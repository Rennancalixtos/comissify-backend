<?php

namespace App\Jobs;

use App\Models\PostQueue;
use App\Services\AppiumService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PostVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 600; // Appium automation can be slow

    public function __construct(
        public readonly int $postQueueId,
    ) {}

    /**
     * Queue this job on the "posting" queue (processed by Horizon).
     */
    public function handle(AppiumService $appium): void
    {
        // TODO: Implementation steps
        // 1. Load PostQueue with video and account; abort if not found.
        //
        // 2. Check if scheduled_at has been reached; if not, release the job back
        //    to the queue with delay: $this->release(now()->diffInSeconds($entry->scheduled_at)).
        //
        // 3. Verify the account can still post (canPost() && status === 'active').
        //    If the account is exhausted, reschedule to tomorrow.
        //
        // 4. Verify the video file exists at $entry->video->absolute_path.
        //    If missing, mark PostQueue as 'failed' with reason 'Video file not found'.
        //
        // 5. Mark PostQueue status = 'processing'.
        //
        // 6. Call $appium->postVideo(
        //        account:       $entry->account,
        //        videoPath:     $entry->video->absolute_path,
        //        title:         $entry->title,
        //        description:   $entry->description,
        //        affiliateLink: $entry->affiliate_link,
        //    )
        //    Appium will:
        //      - Open the Shopee app via ADB
        //      - Navigate to the video creation screen
        //      - Upload the video file
        //      - Fill in title, description, and affiliate link
        //      - Submit the post and capture the returned video ID
        //
        // 7a. On success:
        //     - Call $entry->markAsPosted($shopeeVideoId)
        //     - Increment $entry->account->posts_today
        //     - Update $entry->account->last_post_at = now()
        //
        // 7b. On failure:
        //     - Call $entry->markAsFailed($reason)
        //     - Log error details for manual review
        //
        // 8. Log posting result.
    }

    public function queue(): string
    {
        return 'posting';
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('PostVideoJob failed', [
            'post_queue_id' => $this->postQueueId,
            'exception'     => $exception->getMessage(),
        ]);

        PostQueue::find($this->postQueueId)?->markAsFailed(
            'Unhandled exception: '.$exception->getMessage()
        );
    }
}
