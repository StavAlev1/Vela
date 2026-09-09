<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
});

// SEO / syndication — public, unauthenticated
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/feed', [FeedController::class, 'index'])->name('feed');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Posts
// ->withTrashed(['show']) lets the trashed-posts page link straight to a
// deleted post's normal show page (read-only there — see PostController)
// instead of needing a separate preview view.
Route::resource('posts', PostController::class)->withTrashed(['show']);

Route::get('posts/trashed/list', [PostController::class, 'trashed'])
    ->name('posts.trashed');

Route::patch('posts/{post}/restore', [PostController::class, 'restore'])
    ->name('posts.restore')
    ->withTrashed();

Route::delete('posts/{post}/force', [PostController::class, 'forceDestroy'])
    ->name('posts.force-destroy')
    ->withTrashed();

// Comments
Route::post('posts/{post}/comments', [CommentController::class, 'store'])
    ->name('comments.store');

Route::delete('comments/{comment}', [CommentController::class, 'destroy'])
    ->name('comments.destroy');

// Contact
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::patch('users/{user}/promote', [UserController::class, 'promoteToEditor'])->name('users.promote');
    Route::patch('users/{user}/demote', [UserController::class, 'demoteToUser'])->name('users.demote');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::resource('categories', CategoryController::class)->except(['show']);

    Route::get('activity', [ActivityLogController::class, 'index'])->name('activity.index');
});

require __DIR__ . '/auth.php';
