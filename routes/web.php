<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\ContactController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/posts', [PostsController::class, 'index']);

Route::resource('/posts', PostsController::class);

Route::get('/contact', [ContactController::class, 'index']);

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');