<?php

namespace App\Console\Commands;

use App\Models\Comment;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PruneOrphanedComments extends Command
{
    /**
     * Comments are attached to their parent (Post, Video, ...) through a
     * polymorphic relation — commentable_type + commentable_id — rather
     * than a real foreign key, because Laravel can't enforce a FK across
     * several possible parent tables at once. That means nothing in the
     * database stops a comment from outliving its parent: PostController's
     * forceDestroy(), for example, permanently deletes a Post but never
     * touches its comments. This command finds and removes those leftovers.
     *
     * php artisan comments:prune-orphaned              lists + prompts before deleting
     * php artisan comments:prune-orphaned --dry-run     lists only, deletes nothing
     * php artisan comments:prune-orphaned --force       deletes without prompting
     */
    protected $signature = 'comments:prune-orphaned
        {--dry-run : List what would be deleted without deleting anything}
        {--force : Skip the confirmation prompt}';

    protected $description = 'Delete comments whose parent post/video has been permanently deleted, leaving them orphaned.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $types = Comment::query()->select('commentable_type')->distinct()->pluck('commentable_type');

        if ($types->isEmpty()) {
            $this->info('No comments exist yet. Nothing to check.');

            return self::SUCCESS;
        }

        $orphanedIds = [];

        foreach ($types as $type) {
            if (! class_exists($type) || ! is_subclass_of($type, Model::class)) {
                $this->warn("Skipping commentable_type \"{$type}\" — its model class no longer exists in the codebase.");

                continue;
            }

            $query = $type::query();

            // A soft-deleted parent (Post uses SoftDeletes) still has a row
            // in the table, so its comments are NOT orphaned — only a
            // parent that's truly gone (force-deleted) counts. Only add
            // withTrashed() for models that actually use the trait (Video
            // doesn't), so this stays correct for every commentable type.
            if (in_array(SoftDeletes::class, class_uses_recursive($type), true)) {
                $query->withTrashed();
            }

            $existingIds = $query->pluck('id')->all();

            $orphaned = Comment::where('commentable_type', $type)
                ->whereNotIn('commentable_id', $existingIds)
                ->get(['id', 'commentable_id', 'body']);

            foreach ($orphaned as $comment) {
                $orphanedIds[] = $comment->id;

                $preview = Str::limit($comment->body, 60);
                $shortType = class_basename($type);
                $this->line("  #{$comment->id} on {$shortType}#{$comment->commentable_id} -> \"{$preview}\"");
            }
        }

        if (empty($orphanedIds)) {
            $this->info('No orphaned comments found — everything checks out.');

            return self::SUCCESS;
        }

        $count = count($orphanedIds);
        $this->newLine();

        if ($dryRun) {
            $this->comment("Dry run — {$count} orphaned comment(s) listed above, nothing deleted.");

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm("Delete these {$count} orphaned comment(s)? This cannot be undone.")) {
            $this->comment('Cancelled — nothing was deleted.');

            return self::SUCCESS;
        }

        Comment::whereIn('id', $orphanedIds)->delete();

        $this->info("Deleted {$count} orphaned comment(s).");

        return self::SUCCESS;
    }
}
