<?php

use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', function () {
    return view('index');
})->name('index');

// Authentication routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Login redirect route - handles server-side redirect after successful login
Route::get('/login/redirect', function () {
    // Check if user is logged in
    if (session('user_id')) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('login.redirect');

// Protected routes
Route::middleware('auth.check')->group(function () {
    // Dashboard route
    Route::get('/dashboard', function () {
        return view('dashboard.dashboard');
    })->name('dashboard');

    // Forum routes
    Route::get('/forum', function () {
        return view('forum.forum');
    })->name('forum.index');

    Route::get('/forum/create', function () {
        return view('forum.create-forum');
    })->name('forum.create');

    Route::get('/forum/{id}', function ($id) {
        return view('forum.forum-detail', ['id' => $id]);
    })->name('forum.detail');

    Route::get('/forum/{id}/post/create', function ($id) {
        return view('forum.create-post', ['forumId' => $id]);
    })->name('forum.post.create');

    Route::get('/forum/{id}/manage', function ($id) {
        return view('forum.manage-forum', ['id' => $id]);
    })->name('forum.manage');

    Route::get('/forum/search', function () {
        return view('forum.forum-search');
    })->name('forum.search');

    Route::get('/post/{id}', function ($id) {
        return view('forum.post-detail', ['id' => $id]);
    })->name('post.detail');

    Route::get('/post/{id}/comments', function ($id) {
        return view('forum.comment-detail', ['id' => $id]);
    })->name('post.comments');

    // Messaging route
    Route::get('/messaging', function () {
        return view('messaging.messaging');
    })->name('messaging');
});
