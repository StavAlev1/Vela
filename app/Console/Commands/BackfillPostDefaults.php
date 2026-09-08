<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackfillPostDefaults extends Command
{
    /**
     * php artisan posts:backfill-defaults              fill both columns
     * php artisan posts:backfill-defaults --only=category
     * php artisan posts:backfill-defaults --only=image
     * php artisan posts:backfill-defaults --dry-run     preview only, writes nothing
     *
     * Safe to re-run: it only ever touches posts where the column is
     * currently NULL, so posts you've already set by hand are never
     * overwritten, and a partially-failed run can just be run again.
     */
    protected $signature = 'posts:backfill-defaults
        {--dry-run : Show what would change without writing anything}
        {--only= : Limit to "category" or "image" (default: both)}';

    protected $description = 'Fill in missing category_id and featured_image on existing posts that don\'t have them yet.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $only = $this->option('only');

        if ($only && ! in_array($only, ['category', 'image'], true)) {
            $this->error('--only must be "category" or "image".');

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

    private function backfillImages(bool $dryRun): void
    {
        $posts = Post::whereNull('featured_image')->get();

        if ($posts->isEmpty()) {
            $this->info('Every post already has a featured image. Nothing to backfill there.');

            return;
        }

        $this->info("Fetching a stock photo for {$posts->count()} post(s) from picsum.photos (needs internet access)...");
        $bar = $this->output->createProgressBar($posts->count());
        $bar->start();
        $failures = [];

        foreach ($posts as $post) {
            if ($dryRun) {
                $this->line("");
                $this->line("  #{$post->id} \"{$post->title}\" -> would download a stock photo");
                $bar->advance();

                continue;
            }

            // Seeded by post id so re-running the command fetches the same
            // photo for the same post rather than a different one each time.
            $response = Http::timeout(15)->get("https://picsum.photos/seed/post-{$post->id}/800/450");

            if (!$response->successful()) {
                $failures[] = $post->id;
                $bar->advance();

                continue;
            }

            $filename = 'posts/' . Str::random(40) . '.jpg';
            Storage::disk('public')->put($filename, $response->body());

            $post->update(['featured_image' => $filename]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if (! empty($failures)) {
            $this->warn(
                'Could not fetch an image for post ID(s): ' . implode(', ', $failures) .
                '. Check your internet connection and re-run the command — it will only retry posts still missing an image.'
            );
        }
    }
}
