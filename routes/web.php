<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\DiscordWebhook;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\OrderController;
use App\Http\Middleware\VerifyHoneypot;
use Illuminate\Session\Middleware\StartSession;

Route::get('/', function () {
    return view('home');
});

Route::get('/store', function () {
    return view('store');
})->name('store');

Route::get('/order', [OrderController::class, 'create'])
    ->middleware(['web'])
    ->name('order.create');

Route::post('/order', [OrderController::class, 'store'])
    ->middleware([
        'web',
        'throttle:10,1',
        VerifyHoneypot::class
    ])
    ->name('order.store');

// Add this temporary route
Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();
        return "Database connection successful. Connected to database: " . DB::connection()->getDatabaseName();
    } catch (\Exception $e) {
        return "Database connection failed: " . $e->getMessage();
    }
});

// Add this route for CSRF token refresh
Route::get('/csrf-token', function () {
    return response()->json([
        'token' => csrf_token()
    ]);
})->middleware('web');

