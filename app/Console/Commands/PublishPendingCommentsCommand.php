<?php

namespace App\Console\Commands;

use App\Models\Comment;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('comments:publish-pending')]
#[Description('Publish existing pending demo comments to approved status without affecting spam comments.')]
class PublishPendingCommentsCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $pendingCount = Comment::where('status', 'pending')->count();

        if ($pendingCount === 0) {
            $this->info('No pending comments found to publish.');

            return self::SUCCESS;
        }

        $updated = Comment::where('status', 'pending')->update(['status' => 'approved']);

        $this->info("Successfully published {$updated} pending comments. Spam comments remain untouched.");

        return self::SUCCESS;
    }
}
