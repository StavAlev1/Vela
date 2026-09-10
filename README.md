# Vela

Vela is a small custom CMS built on Laravel — a blog-style platform where **admins**, **editors**, and regular **users** each get a different level of access to write, moderate, and manage content.

## Tech stack

- **Laravel 13** on **PHP 8.4**, SQLite by default (any Laravel-supported database works)
- **Laravel Breeze** for authentication, **spatie/laravel-permission** for roles
- **Tailwind CSS v4** (`@tailwindcss/vite`, config lives in `resources/css/app.css`) + **Alpine.js**, bundled with **Vite**

## Features

**Content**
- Posts with categories, an optional featured image (either a real upload or an external URL), full-text-ish search, and pagination
- A draft/publish workflow — a post isn't public until it's published, and only its author or an admin can preview a draft
- Threaded comments on posts (built on a polymorphic relation, so other content types can reuse it later)
- View counts, de-duplicated per browser session rather than per page load
- Soft-deleted posts land in a Trash view everyone can see, but only the post's owner or an admin can restore, and only an admin can permanently delete

**Discovery & SEO**
- Per-post SEO title/description (falls back sensibly to the post's own title/content when not set), Open Graph tags
- `/sitemap.xml` and `/feed` (RSS) — both only ever include published posts
- Human-readable slugs, auto-generated from the title and guaranteed unique

**Admin & moderation**
- Three roles: **admin** (full control), **editor** (can write and publish), **user** (can read and comment)
- Admin panel for managing users (promote/demote/remove), categories, and a running activity log of who did what
- A dashboard with site-wide stats (post/draft counts, most-viewed posts, recent signups) for admins, and a personal summary for everyone else

**Under the hood**
- Every form submit button disables itself the instant it's clicked, so a fast double-click can't submit the same thing twice
- A handful of custom Artisan commands (see below) for backfilling data, cleaning up orphaned records, and pruning old trash automatically

## Getting started

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate
php artisan db:seed          # optional — see "Seeded accounts" below

php artisan storage:link     # needed for locally-uploaded featured images to be publicly reachable

npm run dev                  # or: npm run build, for a production build
php artisan serve
```

### Seeded accounts

Running `php artisan db:seed` creates two known accounts (plus 20 random users) so you have something to log in with locally:

| Role   | Email               | Password    |
|--------|---------------------|-------------|
| admin  | admin@example.com   | admin12345  |
| editor | editor@example.com  | *(random — use "Forgot password" or reset it via tinker)* |

These are for local development only — don't seed them into a real deployment.

## Custom Artisan commands

| Command | What it does |
|---|---|
| `posts:backfill-defaults` | Fills in missing `category_id`, `featured_image`, and `views` on existing posts (random category, a stock photo URL, and a random view count). Supports `--dry-run` and `--only=category\|image\|views`. |
| `comments:prune-orphaned` | Deletes comments left behind by a post/video that no longer exists. Supports `--dry-run` and `--force`. |
| `posts:prune-trashed` | Permanently deletes posts that have been in the trash for 30+ days (configurable with `--days=N`). Supports `--dry-run` and `--force`. Runs automatically once a day via the scheduler in `routes/console.php` — that only takes effect if something is actually invoking `php artisan schedule:run` every minute (`php artisan schedule:work` while developing locally, or a real cron / Task Scheduler entry in production). |

## Testing

```bash
php artisan test
```

## Good to know

A couple of things exist in the codebase but aren't wired up to anything yet — not bugs, just unfinished:

- **Tags**: the `Tag` model and `post_tag` pivot table are there, but there's no UI yet to actually tag a post or browse by tag.
- **Videos**: a full `Video` model exists (title, url, description) and can already take comments via the same polymorphic relation posts use, but it has no controller, routes, or views — it's set up as a second content type that was started and never finished.
