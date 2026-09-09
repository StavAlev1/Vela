<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Keep the trash tidy on its own — permanently remove posts that have
// been sitting there for 30+ days, without anyone needing to remember
// to run posts:prune-trashed by hand. --force skips the confirmation
// prompt, which is required for a command running unattended.
//
// Note: this only fires if something is actually invoking Laravel's
// scheduler once a minute on this machine (e.g. `php artisan schedule:work`
// while developing, or a real cron/Task Scheduler entry running
// `php artisan schedule:run` in production) — the schedule below just
// describes *when*, it doesn't make that happen by itself.
Schedule::command('posts:prune-trashed --force')->daily();
