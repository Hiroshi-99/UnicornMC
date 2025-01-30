<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\DiscordWebhook;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('home');
});

Route::get('/store', function () {
    return view('store');
});

Route::get('/order', function (Request $request) {
    return view('order', [
        'rank' => $request->query('rank'),
        'price' => $request->query('price')
    ]);
})->middleware(\App\Http\Middleware\ValidateOrderParameters::class);

Route::post('/order/submit', function (Request $request) {
    try {
        // Validate inputs
        $validated = $request->validate([
            'username' => 'required|string|max:16',
            'proof' => 'required|image|max:5120', // 5MB max
            'rank' => 'required|string',
            'price' => 'required|numeric'
        ]);

        // Store the image
        $path = $request->file('proof')->store('proofs', 'public');
        
        // Create order data
        $order = [
            'username' => $validated['username'],
            'rank' => $validated['rank'],
            'price' => $validated['price'],
            'proof_url' => config('app.url') . '/storage/' . $path
        ];

        // Send Discord notification
        $discord = new DiscordWebhook();
        $discord->sendOrderNotification($order);

        return redirect('/store')->with('success', 'Order submitted successfully! We will process it shortly.');

    } catch (\Exception $e) {
        Log::error('Order submission failed: ' . $e->getMessage());
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Sorry, there was an error processing your order. Please try again.');
    }
})->name('order.store');

// Add this temporary route
Route::get('/db-test', function () {
    try {
        DB::connection()->getPdo();
        return "Database connection successful. Connected to database: " . DB::connection()->getDatabaseName();
    } catch (\Exception $e) {
        return "Database connection failed: " . $e->getMessage();
    }
});

