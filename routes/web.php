<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ContactController;

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

Route::get('/contact', [ContactController::class, 'index']);
