<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Posts
Route::resource('posts', PostController::class);

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

require __DIR__.'/auth.php';
