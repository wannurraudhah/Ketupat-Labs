<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\BadgeController;

// Home Page
Route::get('/', function () {
    $badgeCount = DB::table('badges')->count();
    $categoryCount = DB::table('badge_categories')->count();
    $studentCount = DB::table('users')->count();
    
    return view('welcome', compact('badgeCount', 'categoryCount', 'studentCount'));
});

// Badge Routes
Route::prefix('badges')->name('badges.')->group(function () {
    // Public
    Route::get('/', [BadgeController::class, 'index'])->name('index');
    Route::get('/category/{category}', [BadgeController::class, 'byCategory'])->name('category');

    // Protected (require login)
    Route::middleware(['auth'])->group(function () {
        Route::get('/my', [BadgeController::class, 'userBadges'])->name('my');
        Route::post('/redeem', [BadgeController::class, 'redeem'])->name('redeem');
        Route::post('/earn-points', [BadgeController::class, 'earnBadgePoints'])->name('earn-points');
        Route::post('/log-activity', [BadgeController::class, 'logActivity'])->name('log-activity');
    });
});

// Demo route redirects to main badges page
Route::get('/demo/badges', function () {
    return redirect()->route('badges.index');
})->name('demo.badges');

// Achievement Routes
Route::prefix('achievements')->name('achievements.')->group(function () {
    Route::get('/', [AchievementController::class, 'index'])->name('index');
    Route::post('/update-progress', [AchievementController::class, 'updateProgress'])->name('update-progress');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::resource('badges', Admin\BadgeController::class);
    Route::post('/award-badge', [BadgeController::class, 'award'])->name('badges.award');
});
