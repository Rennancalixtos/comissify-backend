<?php

namespace App\Livewire\Admin;

use App\Jobs\DownloadVideoJob;
use App\Models\Account;
use App\Models\PostQueue;
use App\Models\Video;
use Livewire\Component;
use Livewire\WithPagination;

class Queue extends Component
{
    use WithPagination;

    public string $status    = '';
    public string $accountId = '';
    public bool   $showForm       = false;
    public bool   $confirmDelete  = false;
    public ?int   $deleteId       = null;

    // Form fields
    public ?int   $videoId       = null;
    public ?int   $formAccountId = null;
    public string $title         = '';
    public string $description   = '';
    public string $hashtags      = '';
    public string $affiliateLink = '';
    public int    $priority      = 5;
    public string $scheduledAt   = '';

    // Inline editing state: keyed by queue item id
    public array  $editingTitle       = [];
    public array  $editingDescription = [];

    protected $queryString = ['status', 'accountId'];

    protected function rules(): array
    {
        return [
            'videoId'       => 'required|exists:videos,id',
            'formAccountId' => 'required|exists:accounts,id',
            'title'         => 'required|string|max:100',
            'description'   => 'nullable|string',
            'hashtags'      => 'nullable|string',
            'affiliateLink' => 'nullable|url',
            'priority'      => 'required|integer|min:1|max:10',
            'scheduledAt'   => 'nullable|date',
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['videoId', 'formAccountId', 'title', 'description', 'hashtags', 'affiliateLink', 'scheduledAt']);
        $this->priority = 5;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        PostQueue::create([
            'video_id'       => $this->videoId,
            'account_id'     => $this->formAccountId,
            'title'          => $this->title,
            'description'    => $this->description,
            'hashtags'       => array_filter(array_map('trim', explode(',', $this->hashtags))),
            'affiliate_link' => $this->affiliateLink,
            'priority'       => $this->priority,
            'status'         => 'pending',
            'scheduled_at'   => $this->scheduledAt ?: null,
        ]);

        $this->showForm = false;
        session()->flash('success', 'Item adicionado à fila.');
    }

    // ─── Inline edit ─────────────────────────────────────────────────────────

    public function saveField(int $id, string $field, string $value): void
    {
        $allowed = ['title', 'description', 'affiliate_link'];
        if (! in_array($field, $allowed)) {
            return;
        }

        if ($field === 'title') {
            $value = mb_substr(trim($value), 0, 100);
        }

        PostQueue::findOrFail($id)->update([$field => $value]);
    }

    // ─── Status actions ──────────────────────────────────────────────────────

    public function markAsPosted(int $id): void
    {
        $item = PostQueue::findOrFail($id);
        $item->markAsPosted('manual');

        $item->account?->increment('posts_today');
        $item->account?->update(['last_post_at' => now()]);

        session()->flash('success', 'Marcado como postado.');
    }

    public function rejectAndRequeue(int $id): void
    {
        $item = PostQueue::with('video.product')->findOrFail($id);

        // Rejeitar vídeo atual
        $item->video?->update([
            'validation_status' => 'invalid',
            'rejection_reason'  => 'Rejeitado manualmente pelo admin.',
        ]);

        $item->update(['status' => 'cancelled']);

        // Disparar novo download para o mesmo produto
        if ($item->video?->product_id) {
            DownloadVideoJob::dispatch($item->video->product_id);
            session()->flash('success', 'Vídeo rejeitado. Novo download iniciado.');
        } else {
            session()->flash('success', 'Vídeo rejeitado.');
        }
    }

    public function cancelItem(int $id): void
    {
        PostQueue::findOrFail($id)->update(['status' => 'cancelled']);
    }

    public function retryItem(int $id): void
    {
        PostQueue::findOrFail($id)->update(['status' => 'pending', 'error_message' => null]);
    }

    public function confirmDeleteItem(int $id): void
    {
        $this->deleteId      = $id;
        $this->confirmDelete = true;
    }

    public function deleteItem(): void
    {
        PostQueue::findOrFail($this->deleteId)->delete();
        $this->confirmDelete = false;
        $this->deleteId      = null;
        session()->flash('success', 'Item removido da fila.');
    }

    public function render()
    {
        $queue = PostQueue::with(['video.product', 'account'])
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->accountId, fn ($q) => $q->where('account_id', $this->accountId))
            ->orderBy('priority')
            ->orderBy('scheduled_at')
            ->paginate(10);

        $accounts = Account::active()->get();
        $videos   = Video::valid()->with('product')->get();

        return view('livewire.admin.queue', compact('queue', 'accounts', 'videos'))
            ->layout('layouts.admin', ['title' => 'Fila de Postagem']);
    }
}
