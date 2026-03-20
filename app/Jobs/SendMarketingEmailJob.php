<?php

namespace App\Jobs;

use App\Models\MarketingEmail;
use App\Models\User;
use App\Models\UserCredential;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMarketingEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly int $marketingEmailId,
    ) {}

    public function handle(): void
    {
        $record = MarketingEmail::findOrFail($this->marketingEmailId);

        $users = $this->resolveRecipients($record->audience);

        $sent = 0;
        foreach ($users as $user) {
            try {
                Mail::html($record->body, function ($message) use ($user, $record) {
                    $message->to($user->email, $user->name)
                            ->subject($record->subject)
                            ->from(config('mail.from.address'), config('mail.from.name'));
                });
                $sent++;
            } catch (\Throwable $e) {
                Log::warning("SendMarketingEmailJob: falha ao enviar para {$user->email} — {$e->getMessage()}");
            }
        }

        $record->update([
            'recipients_count' => $sent,
            'sent_at'          => now(),
        ]);
    }

    private function resolveRecipients(string $audience)
    {
        return match ($audience) {
            'connected'    => User::whereHas('credential')->get(),
            'zero_balance' => User::whereHas('token', fn($q) => $q->where('balance', '<=', 0))->get(),
            default        => User::all(),
        };
    }
}
