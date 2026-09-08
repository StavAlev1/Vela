<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Console\Command;

class BackfillPostDefaults extends Command
{
    /**
     * php artisan posts:backfill-defaults              fill category, image, and views
     * php artisan posts:backfill-defaults --only=category
     * php artisan posts:backfill-defaults --only=image
     * php artisan posts:backfill-defaults --only=views
     * php artisan posts:backfill-defaults --dry-run     preview only, writes nothing
     *
     * Safe to re-run: category/image only ever touch posts where the column
     * is currently NULL, so anything you've set by hand is never overwritten,
     * and a partially-failed run can just be run again. Views is the one
     * exception — see backfillViews() below.
     */
    protected $signature = 'posts:backfill-defaults
        {--dry-run : Show what would change without writing anything}
        {--only= : Limit to "category", "image", or "views" (default: all three)}';

    protected $description = 'Fill in missing category_id, featured_image, and views on existing posts.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $only = $this->option('only');

        if ($only && ! in_array($only, ['category', 'image', 'views'], true)) {
            $this->error('--only must be "category", "image", or "views".');

            return self::FAILURE;
        }

        if ($dryRun) {
            $this->comment('Dry run — no changes will be saved.');
        }

        if (in_array($only, [null, 'category'], true)) {
            $this->backfillCategories($dryRun);
        }

        if (in_array($only, [null, 'image'], true)) {
            $this->backfillImages($dryRun);
        }

        if (in_array($only, [null, 'views'], true)) {
            $this->backfillViews($dryRun);
        }

        return self::SUCCESS;
    }

    private function backfillCategories(bool $dryRun): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->warn('No categories exist yet — run `php artisan db:seed --class=CategorySeeder` first, or create some at /admin/categories. Skipping category backfill.');

            return;
        }

        $posts = Post::whereNull('category_id')->get();

        if ($posts->isEmpty()) {
            $this->info('Every post already has a category. Nothing to backfill there.');

            return;
        }

        $this->info("Assigning a random category to {$posts->count()} post(s)...");
        $bar = $this->output->createProgressBar($posts->count());
        $bar->start();

        foreach ($posts as $post) {
            $category = $categories->random();

            if ($dryRun) {
                $this->line("");
                $this->line("  #{$post->id} \"{$post->title}\" -> {$category->name}");
            } else {
                $post->update(['category_id' => $category->id]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    /**
     * Rather than downloading a photo and storing it on our own disk, this
     * just writes a picsum.photos URL straight into featured_image. Nothing
     * to fetch or store here — the browser loads the image directly from
     * picsum.photos whenever the post is viewed, so this step needs no
     * internet access itself and runs instantly.
     *
     * Post::featured_image_url and the delete-on-forceDelete/replace logic
     * both already know how to handle an external URL vs. a local disk path
     * (see Post::hasExternalImage()), so no other code needed to change.
     */
    private function backfillImages(bool $dryRun): void
    {
        $posts = Post::whereNull('featured_image')->get();

        if ($posts->isEmpty()) {
            $this->info('Every post already has a featured image. Nothing to backfill there.');

            return;
        }

        $this->info("Linking {$posts->count()} post(s) to a stock photo URL on picsum.photos...");
        $bar = $this->output->createProgressBar($posts->count());
        $bar->start();

        foreach ($posts as $post) {
            // Seeded by post id, so the same post always links to the same
            // photo rather than a different one on every page load.
            $url = "https://picsum.photos/seed/post-{$post->id}/800/450";

            if ($dryRun) {
                $this->line("");
                $this->line("  #{$post->id} \"{$post->title}\" -> {$url}");
            } else {
                $post->update(['featured_image' => $url]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->comment('Note: these images now load live from picsum.photos whenever a visitor views the post — your database only stores the URL, no image files were saved. That means the site needs internet access to actually display them, and if picsum.photos is ever down or the URL changes shape, those images will break.');
    }

    /**
     * Gives posts a random view count between 1 and 10,000.
     *
     * Unlike category/image, `views` doesn't have a meaningful "unset" state
     * to detect — it defaults to 0, which is also a perfectly real value for
     * a post nobody's opened yet. So this only touches posts still sitting
     * at exactly 0 (the untouched default), and re-running the command won't
     * re-roll a number it already assigned — but note that's different from
     * category/image, which key off NULL; a post can only be "topped up"
     * once here, since after this runs its views is no longer 0.
     *
     * Sets the attribute directly and saves, rather than mass-assigning
     * through update(['views' => ...]) — `views` isn't (and doesn't need to
     * be) in Post's $fillable, since the normal app code only ever touches
     * it via increment().
     */
    private function backfillViews(bool $dryRun): void
    {
        $posts = Post::where('views', 0)->get();

        if ($posts->isEmpty()) {
            $this->info('Every post already has views recorded. Nothing to backfill there.');

            return;
        }

        $this->info("Assigning a random view count (1-10,000) to {$posts->count()} post(s)...");
        $bar = $this->output->createProgressBar($posts->count());
        $bar->start();

        foreach ($posts as $post) {
            $views = random_int(1, 10000);

            if ($dryRun) {
                $this->line("");
                $this->line("  #{$post->id} \"{$post->title}\" -> {$views} views");
            } else {
                $post->views = $views;
                $post->save();
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }
}
