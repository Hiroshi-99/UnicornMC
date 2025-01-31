<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Services\DiscordWebhook;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validate inputs
            $validated = $request->validate([
                'username' => 'required|string|max:16',
                'platform' => 'required|in:java,bedrock',
                'proof' => 'required|image|max:5120', // 5MB max
                'rank' => 'required|string',
                'price' => 'required|numeric'
            ]);

            // Get all available products
            $ranks = config('store.ranks', []);
            $money = config('store.money', []);
            $plots = config('store.plots', []);
            $allProducts = array_merge($ranks, $money, $plots);

            // Validate product exists and price matches
            if (!isset($allProducts[$validated['rank']]) || 
                abs($allProducts[$validated['rank']]['price'] - floatval($validated['price'])) > 0.01) {
                return back()->with('error', 'Invalid order parameters');
            }

            // Store the image
            $path = $request->file('proof')->store('proofs', 'public');
            
            // Format username based on platform
            $username = $validated['username'];
            if ($validated['platform'] === 'bedrock') {
                $username = '.' . ltrim($username, '.'); // Add dot prefix if not already present
            }
            
            // Create order data
            $order = [
                'username' => $username,
                'platform' => $validated['platform'],
                'rank' => $validated['rank'],
                'price' => $validated['price'],
                'proof_url' => config('app.url') . '/storage/' . $path,
                'product_name' => $allProducts[$validated['rank']]['display_name'],
                'product_type' => $allProducts[$validated['rank']]['type'] ?? 'rank'
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
    }

    public function create(Request $request)
    {
        $rank = $request->query('rank');
        $price = $request->query('price');

        // Get all available products
        $ranks = config('store.ranks', []);
        $money = config('store.money', []);
        $plots = config('store.plots', []);
        
        // Combine all products
        $allProducts = array_merge($ranks, $money, $plots);

        if (!isset($allProducts[$rank])) {
            return redirect()->route('store')->with('error', 'Invalid rank selected');
        }

        $rankData = $allProducts[$rank];
        
        // Validate price matches
        if (abs($rankData['price'] - floatval($price)) > 0.01) {
            return redirect()->route('store')->with('error', 'Invalid price');
        }

        return view('order', [
            'rank' => $rank,
            'price' => $price
        ]);
    }
} 