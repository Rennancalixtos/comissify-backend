<?php

namespace App\Livewire\Admin;

use App\Jobs\SendMarketingEmailJob;
use App\Models\MarketingEmail;
use App\Models\User;
use App\Models\UserCredential;
use Livewire\Component;

class Emails extends Component
{
    public string $subject  = '';
    public string $body     = '';
    public string $audience = 'all';
    public bool   $preview  = false;
    public bool   $sending  = false;
    public ?string $successMessage = null;

    protected array $rules = [
        'subject'  => 'required|string|max:255',
        'body'     => 'required|string',
        'audience' => 'required|in:all,connected,zero_balance',
    ];

    public function togglePreview(): void
    {
        $this->preview = !$this->preview;
    }

    public function send(): void
    {
        $this->validate();

        $record = MarketingEmail::create([
            'subject'  => $this->subject,
            'body'     => $this->body,
            'audience' => $this->audience,
        ]);

        SendMarketingEmailJob::dispatch($record->id);

        $this->reset(['subject', 'body', 'audience', 'preview']);
        $this->audience = 'all';
        $this->successMessage = 'E-mail enfileirado para envio com sucesso!';
    }

    public function recipientCount(): int
    {
        return match ($this->audience) {
            'connected'    => UserCredential::count(),
            'zero_balance' => User::whereHas('token', fn($q) => $q->where('balance', '<=', 0))->count(),
            default        => User::count(),
        };
    }

    public function render()
    {
        $history = MarketingEmail::whereNotNull('sent_at')
            ->latest('sent_at')
            ->limit(20)
            ->get();

        return view('livewire.admin.emails', [
            'history'        => $history,
            'recipientCount' => $this->recipientCount(),
        ])->layout('layouts.admin', ['title' => 'E-mails Marketing']);
    }
}
