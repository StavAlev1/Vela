<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
        });

        // Backfill slugs for any posts created before this column existed
        // (including soft-deleted ones), guarding against duplicates.
        // Uses the query builder directly rather than Eloquent so this
        // migration never depends on the current shape of the Post model.
        $posts = DB::table('posts')->orderBy('id')->get(['id', 'title']);
        $usedSlugs = [];

        foreach ($posts as $post) {
            $base = Str::slug($post->title) ?: 'post';
            $slug = $base;
            $suffix = 1;

            while (in_array($slug, $usedSlugs, true)) {
                $slug = "{$base}-{$suffix}";
                $suffix++;
            }

            $usedSlugs[] = $slug;

            DB::table('posts')->where('id', $post->id)->update(['slug' => $slug]);
        }

        Schema::table('posts', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
