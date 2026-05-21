<?php

namespace App\Console\Commands;

use App\Domain\Blog\Models\BlogPost;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    protected $signature = 'blog:publish-scheduled';

    protected $description = 'Flip scheduled posts whose published_at is in the past to published status.';

    public function handle(): void
    {
        $count = BlogPost::scheduled()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->update(['status' => BlogPost::STATUS_PUBLISHED]);

        $this->info("Published {$count} scheduled post(s).");
    }
}
