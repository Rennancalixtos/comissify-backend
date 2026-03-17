<?php

namespace App\Livewire\Dashboard;

use App\Models\UserTelegramGroup;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TelegramChannels extends Component
{
    public ?string $connectionCode    = null;
    public ?int    $codeExpiresAt     = null;   // Unix timestamp
    public bool    $showAddPanel      = false;

    public ?string $flashMsg          = null;
    public ?string $error             = null;

    // Template editing
    public ?int    $editingGroupId    = null;
    public string  $templateDraft    = '';
    public bool    $templateSaved    = false;

    // Disconnect confirmation
    public ?int    $confirmDisconnectId = null;

    public function mount(TelegramService $telegram): void
    {
        $code = $telegram->getActiveCode(Auth::id());
        if ($code) {
            $this->connectionCode = $code;
            $this->codeExpiresAt  = $telegram->getCodeExpiresAt($code);
            $this->showAddPanel   = true;
        }
    }

    public function getGroupsProperty()
    {
        return UserTelegramGroup::where('user_id', Auth::id())->latest()->get();
    }

    public function generateCode(TelegramService $telegram): void
    {
        $code                 = $telegram->generateConnectionCode(Auth::id());
        $this->connectionCode = $code;
        $this->codeExpiresAt  = $telegram->getCodeExpiresAt($code);
        $this->showAddPanel   = true;
        $this->flashMsg       = null;
        $this->error          = null;
    }

    public function showAddPanel(): void
    {
        $this->showAddPanel = true;
        $this->flashMsg     = null;
        $this->error        = null;
    }

    public function hideAddPanel(): void
    {
        $this->showAddPanel = false;
    }

    /** Called by wire:poll when a connection code is active — detects new group. */
    public function checkConnection(TelegramService $telegram): void
    {
        if (!$this->connectionCode) return;

        // If the code no longer exists in cache, it was consumed by the webhook
        $stillActive = $telegram->getActiveCode(Auth::id());
        if (!$stillActive && $this->groups->isNotEmpty()) {
            $this->connectionCode = null;
            $this->codeExpiresAt  = null;
            $this->showAddPanel   = false;
            $this->flashMsg       = '✅ Grupo conectado com sucesso!';
        }
    }

    public function disconnectGroup(int $id): void
    {
        if ($this->confirmDisconnectId !== $id) {
            $this->confirmDisconnectId = $id;
            return;
        }

        UserTelegramGroup::where('id', $id)->where('user_id', Auth::id())->delete();

        $this->confirmDisconnectId = null;
        $this->flashMsg            = 'Grupo desconectado.';
    }

    public function cancelDisconnect(): void
    {
        $this->confirmDisconnectId = null;
    }

    public function startEditTemplate(int $id, TelegramService $telegram): void
    {
        $group = UserTelegramGroup::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$group) return;

        $this->editingGroupId = $id;
        $this->templateDraft  = $group->message_template ?? $telegram->defaultTemplate();
        $this->templateSaved  = false;
    }

    public function saveTemplate(): void
    {
        if (!$this->editingGroupId) return;

        UserTelegramGroup::where('id', $this->editingGroupId)
            ->where('user_id', Auth::id())
            ->update(['message_template' => $this->templateDraft]);

        $this->templateSaved  = true;
        $this->editingGroupId = null;
        $this->flashMsg       = 'Template salvo.';
    }

    public function cancelEditTemplate(): void
    {
        $this->editingGroupId = null;
        $this->templateDraft  = '';
    }

    public function resetTemplate(TelegramService $telegram): void
    {
        $this->templateDraft = $telegram->defaultTemplate();
    }

    public function render()
    {
        return view('livewire.dashboard.telegram-channels')
            ->layout('layouts.app', ['title' => 'Canais de Envio']);
    }
}
