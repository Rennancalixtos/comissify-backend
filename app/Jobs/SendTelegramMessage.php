<?php

namespace App\Jobs;

use App\Models\UserAffiliateLink;
use App\Models\UserTelegramGroup;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTelegramMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 30;

    public function __construct(
        public readonly int $groupId,
        public readonly int $linkId,
    ) {}

    public function handle(TelegramService $telegram): void
    {
        $group = UserTelegramGroup::find($this->groupId);
        $link  = UserAffiliateLink::find($this->linkId);

        if (!$group || !$link) {
            Log::warning('SendTelegramMessage: group or link not found', [
                'group_id' => $this->groupId,
                'link_id'  => $this->linkId,
            ]);
            return;
        }

        $template = $group->message_template ?? $telegram->defaultTemplate();
        $message  = $telegram->resolveTemplate($template, $link);

        $sent = $telegram->sendMessage($group->chat_id, $message);

        if (!$sent) {
            Log::warning('SendTelegramMessage: failed to send', [
                'group_id' => $this->groupId,
                'chat_id'  => $group->chat_id,
            ]);
            $this->fail('Falha ao enviar para ' . $group->chat_title);
        }
    }
}
