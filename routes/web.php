<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\OrderController;
use App\Http\Middleware\VerifyHoneypot;

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

// CSRF token refresh route
Route::get('/csrf-token', function () {
    return response()->json([
        'token' => csrf_token()
    ]);
})->middleware('web');

// Discord interaction endpoint
Route::post('/discord/interaction', function (Request $request) {
    if ($request->input('type') === 1) {
        return response()->json(['type' => 1]);
    }

    if ($request->input('type') === 3) {
        $customId = $request->input('data.custom_id');
        // Handle approve/reject logic here
    }

    return response()->json(['type' => 1]);
})->middleware('api');

