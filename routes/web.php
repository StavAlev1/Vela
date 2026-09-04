<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CommentController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/posts', [PostsController::class, 'index']);

Route::resource('/posts', PostController::class);

Route::delete('posts/{post}/force', [PostController::class, 'forceDestroy'])
    ->name('posts.force-destroy')
    ->withTrashed(); // allows route model binding to find soft-deleted posts too

Route::patch('posts/{post}/restore', [PostController::class, 'restore'])
    ->name('posts.restore')
    ->withTrashed();

Route::get('posts/trashed/list', [PostController::class, 'trashed'])
    ->name('posts.trashed');

// Nested under posts — comment creation always happens in the context of a specific post
Route::post('posts/{post}/comments', [CommentController::class, 'store'])
    ->name('comments.store');

// Flat — deletion just needs the comment itself, works for both Post and Video comments
Route::delete('comments/{comment}', [CommentController::class, 'destroy'])
    ->name('comments.destroy');

Route::get('/contact', [ContactController::class, 'index']);
