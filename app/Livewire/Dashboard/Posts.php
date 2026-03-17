<?php

namespace App\Livewire\Dashboard;

use App\Models\SystemPrice;
use App\Models\UserAffiliateLink;
use App\Models\UserPost;
use App\Models\UserTelegramGroup;
use App\Services\PinterestService;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Posts extends Component
{
    public ?int    $selectedLinkId = null;
    public string  $activeTab      = '';   // 'text'|'image'|'video'|'pinterest'

    // Pinterest search
    public string  $pinterestQuery = '';
    public array   $pinterestPins  = [];
    public ?string $pinterestError = null;
    public bool    $pinterestLoading = false;

    // General
    public ?string $flashMsg  = null;
    public ?string $error     = null;

    public function mount(): void
    {
    }


    public function getLinksProperty()
    {
        return UserAffiliateLink::where('user_id', Auth::id())
            ->withCount('posts')
            ->latest()
            ->get();
    }

    public function getSelectedLinkProperty(): ?UserAffiliateLink
    {
        if (!$this->selectedLinkId) return null;
        return UserAffiliateLink::find($this->selectedLinkId);
    }

    public function getPricesProperty(): array
    {
        return [
            'text'      => SystemPrice::get('post_text_cost'),
            'image'     => SystemPrice::get('post_image_cost'),
            'video'     => SystemPrice::get('post_video_cost'),
            'pinterest' => SystemPrice::get('post_pinterest_cost'),
        ];
    }

    public function getPostsForLinkProperty(): array
    {
        if (!$this->selectedLinkId) return [];
        return UserPost::where('affiliate_link_id', $this->selectedLinkId)
            ->latest()
            ->get()
            ->toArray();
    }


    public function selectLink(int $linkId): void
    {
        $this->selectedLinkId = $linkId;
        $this->activeTab      = '';
        $this->pinterestPins  = [];
        $this->pinterestQuery = '';
        $this->error          = null;
        $this->flashMsg       = null;
    }

    public function clearProduct(): void
    {
        $this->selectedLinkId = null;
        $this->activeTab      = '';
        $this->pinterestPins  = [];
        $this->pinterestQuery = '';
        $this->error          = null;
        $this->flashMsg       = null;
    }

    public function selectTab(string $tab): void
    {
        $this->activeTab     = $tab;
        $this->pinterestPins = [];
        $this->error         = null;

        if ($tab === 'pinterest' && $this->selectedLink) {
            $this->pinterestQuery = $this->selectedLink->product_name;
        }
    }

    public function clearTab(): void
    {
        $this->activeTab     = '';
        $this->pinterestPins = [];
        $this->error         = null;
        $this->flashMsg      = null;
    }

    public function createPost(string $type): void
    {
        $user  = Auth::user();
        $link  = $this->selectedLink;
        $cost  = $this->prices[$type] ?? 0;

        if (!$link) {
            $this->error = 'Selecione um produto.';
            return;
        }

        if ($user->tokenBalance() < $cost) {
            $this->error = 'Saldo insuficiente. Necessário R$ ' . number_format($cost, 2, ',', '.') . ' em créditos.';
            return;
        }

        $user->ensureTokenWallet()->debit(
            $cost,
            "Post {$type} — {$link->product_name}",
            "post:{$type}:{$link->id}"
        );

        $post = UserPost::create([
            'user_id'          => $user->id,
            'affiliate_link_id'=> $link->id,
            'type'             => $type,
            'status'           => 'pending',
            'credits_charged'  => $cost,
        ]);

        // For now: immediately mock the result (no queue)
        $this->processMock($post, $link, $type);

        $this->flashMsg = 'Conteúdo gerado com sucesso!';
        $this->error    = null;
    }

    public function searchPinterest(): void
    {
        $user = Auth::user();
        $link = $this->selectedLink;
        $cost = $this->prices['pinterest'];

        if (!$link) {
            $this->error = 'Selecione um produto primeiro.';
            return;
        }

        if (empty(trim($this->pinterestQuery))) {
            $this->pinterestQuery = $link->product_name;
        }

        if ($user->tokenBalance() < $cost) {
            $this->error = 'Saldo insuficiente. Necessário R$ ' . number_format($cost, 2, ',', '.') . ' em créditos.';
            return;
        }

        $this->pinterestLoading = true;
        $this->pinterestError   = null;

        $service = app(PinterestService::class);
        $result  = $service->searchPins($this->pinterestQuery, 12);

        $this->pinterestLoading = false;

        if (isset($result['error'])) {
            $this->pinterestError = $result['error'];
            return;
        }

        // Debit only after successful search
        $user->ensureTokenWallet()->debit(
            $cost,
            "Busca Pinterest — {$this->pinterestQuery}",
            "post:pinterest:{$link->id}"
        );

        UserPost::create([
            'user_id'           => $user->id,
            'affiliate_link_id' => $link->id,
            'type'              => 'pinterest',
            'status'            => 'completed',
            'credits_charged'   => $cost,
            'result'            => ['pins' => $result['pins'], 'query' => $this->pinterestQuery, 'cursor' => $result['cursor'] ?? null],
        ]);

        $this->pinterestPins = $result['pins'];
    }


    public function getTelegramGroupsProperty()
    {
        return UserTelegramGroup::where('user_id', Auth::id())->get();
    }

    public function sendToTelegram(int $postId, int $groupId): void
    {
        $user  = Auth::user();
        $post  = UserPost::find($postId);
        $group = UserTelegramGroup::where('id', $groupId)->where('user_id', $user->id)->first();

        if (!$post || $post->user_id !== $user->id || !$group) {
            $this->error = 'Não foi possível enviar. Verifique a conexão.';
            return;
        }

        $link = UserAffiliateLink::find($post->affiliate_link_id);
        if (!$link) {
            $this->error = 'Produto não encontrado.';
            return;
        }

        $telegram = app(TelegramService::class);
        $template = $group->message_template ?? $telegram->defaultTemplate();
        $result   = $post->result ?? [];
        $sent     = false;

        if ($post->type === 'image' && !empty($result['image_url'])) {
            $caption = $telegram->resolveTemplate($template, $link);
            $sent    = $telegram->sendPhoto($group->chat_id, $result['image_url'], $caption);
        } else {
            if ($post->type === 'text' && !empty($result['title'])) {
                $caption = '<b>' . htmlspecialchars($result['title']) . '</b>'
                         . "\n\n" . ($result['description'] ?? '')
                         . "\n\n" . '<a href="' . $link->affiliate_link . '">Comprar agora →</a>';
            } else {
                $caption = $telegram->resolveTemplate($template, $link);
            }
            $sent = $telegram->sendMessage($group->chat_id, $caption);
        }

        if ($sent) {
            $this->flashMsg = 'Enviado para ' . $group->chat_title . '!';
            $this->error    = null;
        } else {
            $this->error = 'Falha ao enviar. Verifique se o bot está no grupo.';
        }
    }

    private function processMock(UserPost $post, UserAffiliateLink $link, string $type): void
    {
        $result = match ($type) {
            'text' => [
                'title'       => "🔥 {$link->product_name} — Melhor oferta!",
                'description' => "Aproveite essa oportunidade incrível! Adquira {$link->product_name} com comissão garantida. Link de afiliado: {$link->affiliate_link}",
                'image_url'   => $link->image_url,
            ],
            'image' => [
                'image_url' => "https://via.placeholder.com/800x800/EE4D2D/ffffff?text=" . urlencode(substr($link->product_name, 0, 20)),
                'prompt'    => "Imagem promocional para {$link->product_name}",
            ],
            'video' => [
                'video_url' => 'https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4',
                'duration'  => 28,
            ],
            default => [],
        };

        $post->update(['status' => 'completed', 'result' => $result]);
    }


    public function render()
    {
        return view('livewire.dashboard.posts')
            ->layout('layouts.app', ['title' => 'Criador']);
    }
}
