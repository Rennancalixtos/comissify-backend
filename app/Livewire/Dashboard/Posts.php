<?php

namespace App\Livewire\Dashboard;

use App\Models\SystemPrice;
use App\Models\UserAffiliateLink;
use App\Models\UserPost;
use App\Models\UserTelegramGroup;
use App\Services\GeminiService;
use App\Services\PinterestService;
use App\Services\PollinationsService;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Posts extends Component
{
    private const AI_TEXT_FREE_DAILY = 10;

    public ?int    $selectedLinkId   = null;
    public string  $activeTab        = '';   // 'text'|'image'|'video'|'pinterest'

    // Pinterest search
    public string  $pinterestQuery   = '';
    public array   $pinterestPins    = [];
    public ?string $pinterestError   = null;
    public bool    $pinterestLoading = false;

    // General
    public ?string $flashMsg  = null;
    public ?string $error     = null;

    public function mount(): void {}

    // ── Computed ─────────────────────────────────────────────────────────────

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

    public function getTextDailyUsageProperty(): array
    {
        $count     = $this->getDailyTextCount(Auth::id());
        $remaining = max(0, self::AI_TEXT_FREE_DAILY - $count);
        return [
            'count'     => $count,
            'limit'     => self::AI_TEXT_FREE_DAILY,
            'remaining' => $remaining,
            'is_free'   => $remaining > 0,
        ];
    }

    public function getTelegramGroupsProperty()
    {
        return UserTelegramGroup::where('user_id', Auth::id())->get();
    }

    // ── Navigation ───────────────────────────────────────────────────────────

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
        $this->flashMsg      = null;

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

    // ── Content generation ───────────────────────────────────────────────────

    public function createPost(string $type): void
    {
        $user = Auth::user();
        $link = $this->selectedLink;

        if (!$link) {
            $this->error = 'Selecione um produto.';
            return;
        }

        match ($type) {
            'text'  => $this->handleTextPost($user, $link),
            'image' => $this->handleImagePost($user, $link),
            default => $this->handleVideoPost($user, $link, $type),
        };
    }

    private function handleTextPost(mixed $user, UserAffiliateLink $link): void
    {
        $usage  = $this->textDailyUsage;
        $isFree = $usage['is_free'];
        $cost   = $isFree ? 0 : $this->prices['text'];

        if (!$isFree && $user->tokenBalance() < $cost) {
            $this->error = 'Saldo insuficiente. Necessário R$ ' . number_format($cost, 2, ',', '.') . ' em créditos.';
            return;
        }

        if (!$isFree) {
            $user->ensureTokenWallet()->debit($cost, "Texto AI — {$link->product_name}", "post:text:{$link->id}");
        }

        $post = UserPost::create([
            'user_id'           => $user->id,
            'affiliate_link_id' => $link->id,
            'type'              => 'text',
            'status'            => 'pending',
            'credits_charged'   => $cost,
        ]);

        $gemini = app(GeminiService::class);

        if (!$gemini->isConfigured()) {
            $this->processMock($post, $link, 'text');
        } else {
            $result = $gemini->generateSalesText($link->product_name, $link->price_min, $link->commission_rate);

            if (isset($result['error'])) {
                if (!$isFree) {
                    $user->ensureTokenWallet()->credit($cost, "Reembolso Texto AI", "refund:text:{$link->id}");
                }
                $post->update(['status' => 'failed', 'error_message' => $result['error']]);
                $this->error = $result['error'];
                return;
            }

            $result['image_url'] = $link->image_url;
            $post->update(['status' => 'completed', 'result' => $result]);
        }

        $this->incrementDailyTextCount($user->id);

        $remaining = max(0, self::AI_TEXT_FREE_DAILY - $usage['count'] - 1);
        $this->flashMsg = $isFree
            ? "Texto gerado! ({$remaining} " . ($remaining === 1 ? 'gratuito restante' : 'gratuitos restantes') . " hoje)"
            : 'Texto gerado com sucesso!';
        $this->error = null;
    }

    private function handleImagePost(mixed $user, UserAffiliateLink $link): void
    {
        $cost = $this->prices['image'];

        if ($user->tokenBalance() < $cost) {
            $this->error = 'Saldo insuficiente. Necessário R$ ' . number_format($cost, 2, ',', '.') . ' em créditos.';
            return;
        }

        $user->ensureTokenWallet()->debit($cost, "Imagem AI — {$link->product_name}", "post:image:{$link->id}");

        $post = UserPost::create([
            'user_id'           => $user->id,
            'affiliate_link_id' => $link->id,
            'type'              => 'image',
            'status'            => 'pending',
            'credits_charged'   => $cost,
        ]);

        $imageUrl = app(PollinationsService::class)->generate($link->product_name, $user->id);

        if (!$imageUrl) {
            // Reembolsa e marca como falha
            $user->ensureTokenWallet()->credit($cost, "Reembolso Imagem AI", "refund:image:{$link->id}");
            $post->update(['status' => 'failed', 'error_message' => 'Falha ao gerar imagem. Tente novamente.']);
            $this->error = 'Falha ao gerar imagem. Tente novamente.';
            return;
        }

        $post->update([
            'status' => 'completed',
            'result' => ['image_url' => $imageUrl, 'prompt' => $link->product_name],
        ]);

        $this->flashMsg = 'Imagem gerada com sucesso!';
        $this->error    = null;
    }

    private function handleVideoPost(mixed $user, UserAffiliateLink $link, string $type): void
    {
        $cost = $this->prices[$type] ?? 0;

        if ($user->tokenBalance() < $cost) {
            $this->error = 'Saldo insuficiente. Necessário R$ ' . number_format($cost, 2, ',', '.') . ' em créditos.';
            return;
        }

        $user->ensureTokenWallet()->debit($cost, "Post {$type} — {$link->product_name}", "post:{$type}:{$link->id}");

        $post = UserPost::create([
            'user_id'           => $user->id,
            'affiliate_link_id' => $link->id,
            'type'              => $type,
            'status'            => 'pending',
            'credits_charged'   => $cost,
        ]);

        $this->processMock($post, $link, $type);

        $this->flashMsg = 'Conteúdo gerado com sucesso!';
        $this->error    = null;
    }

    // ── Pinterest ─────────────────────────────────────────────────────────────

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

        $result = app(PinterestService::class)->searchPins($this->pinterestQuery, 12);

        $this->pinterestLoading = false;

        if (isset($result['error'])) {
            $this->pinterestError = $result['error'];
            return;
        }

        $user->ensureTokenWallet()->debit($cost, "Busca Pinterest — {$this->pinterestQuery}", "post:pinterest:{$link->id}");

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

    // ── Telegram ──────────────────────────────────────────────────────────────

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

    // ── Daily limit helpers ───────────────────────────────────────────────────

    private function getDailyTextCount(int $userId): int
    {
        return (int) Cache::get("ai_text_count:{$userId}:" . now()->format('Y-m-d'), 0);
    }

    private function incrementDailyTextCount(int $userId): void
    {
        $key     = "ai_text_count:{$userId}:" . now()->format('Y-m-d');
        $current = (int) Cache::get($key, 0);
        Cache::put($key, $current + 1, now()->endOfDay()->addHour());
    }

    // ── Mock fallback (video only) ────────────────────────────────────────────

    private function processMock(UserPost $post, UserAffiliateLink $link, string $type): void
    {
        $result = match ($type) {
            'text' => [
                'title'       => "🔥 {$link->product_name} — Melhor oferta!",
                'description' => "Aproveite essa oportunidade incrível! Adquira {$link->product_name} com comissão garantida.",
                'image_url'   => $link->image_url,
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
