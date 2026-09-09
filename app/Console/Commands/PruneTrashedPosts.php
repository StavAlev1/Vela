<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class PruneTrashedPosts extends Command
{
    /**
     * Soft-deleted posts otherwise sit in the trash forever. This command
     * permanently removes ones that have been there past a retention
     * window (30 days by default — roughly "one month"), the same way
     * PostController::forceDestroy() does for a single post: comments are
     * deleted explicitly first (they hang off a polymorphic relation, so
     * there's no real foreign key for the database to cascade on), then
     * forceDelete() runs, which also triggers Post::booted()'s
     * forceDeleted hook to clean up a locally-stored featured image.
     *
     * php artisan posts:prune-trashed                  lists + prompts before deleting
     * php artisan posts:prune-trashed --dry-run          lists only, deletes nothing
     * php artisan posts:prune-trashed --force            deletes without prompting
     * php artisan posts:prune-trashed --days=14          use a 14-day window instead of 30
     */
    protected $signature = 'posts:prune-trashed
        {--days=30 : Only delete posts trashed at least this many days ago}
        {--dry-run : List what would be deleted without deleting anything}
        {--force : Skip the confirmation prompt}';

    protected $description = 'Permanently delete trashed posts (and their comments) older than a given number of days.';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dryRun = (bool) $this->option('dry-run');

        $posts = Post::onlyTrashed()
            ->where('deleted_at', '<=', now()->subDays($days))
            ->get();

        if ($posts->isEmpty()) {
            $this->info("No trashed posts older than {$days} day(s) — nothing to prune.");

            return self::SUCCESS;
        }

        foreach ($posts as $post) {
            $this->line("  #{$post->id} \"{$post->title}\" — trashed {$post->deleted_at->diffForHumans()}");
        }

        $count = $posts->count();
        $this->newLine();

        if ($dryRun) {
            $this->comment("Dry run — {$count} post(s) listed above would be permanently deleted, nothing done.");

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm("Permanently delete these {$count} post(s) and their comments? This cannot be undone.")) {
            $this->comment('Cancelled — nothing was deleted.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($posts as $post) {
            $post->comments()->delete();
            $post->forceDelete();

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Permanently deleted {$count} post(s) trashed more than {$days} day(s) ago.");

        return self::SUCCESS;
    }
}
