<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Services\DiscordWebhook;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Services\DiscordBot;

class OrderController extends Controller
{
    private const HOURLY_LIMIT = 5;  // Max orders per hour
    private const DAILY_LIMIT = 10;   // Max orders per day

    private $discordBot;

    public function __construct(DiscordBot $discordBot)
    {
        $this->discordBot = $discordBot;
    }

    public function store(Request $request)
    {
        try {
            // Honeypot check
            if ($request->filled('website')) {
                return redirect()->route('store')->with('error', 'Bot activity detected. If you are human, please try again.');
            }

            // Validate inputs with stricter rules
            $validated = $request->validate([
                'username' => [
                    'required',
                    'string',
                    'max:16',
                    'min:3',
                    'regex:/^[a-zA-Z0-9_]+$/',  // Only allow alphanumeric and underscore
                    'not_regex:/^(admin|owner|staff|mod|moderator|administrator)$/i', // Prevent impersonation
                ],
                'platform' => 'required|in:java,bedrock',
                'discord' => [
                    'nullable',
                    'string',
                    'max:37',
                    'regex:/^[a-zA-Z0-9_#]{2,32}$/', // Stricter Discord username validation
                ],
                'proof' => [
                    'required',
                    'image',
                    'max:5120', // 5MB max
                    'mimes:jpeg,png,jpg',
                    function ($attribute, $value, $fail) {
                        if (!$value->isValid()) {
                            $fail('The uploaded file is not a valid image.');
                            return;
                        }
                    }
                ],
                'rank' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) {
                        $ranks = config('store.ranks', []);
                        $money = config('store.money', []);
                        $plots = config('store.plots', []);
                        $allProducts = array_merge($ranks, $money, $plots);
                        
                        if (!isset($allProducts[$value])) {
                            $fail('The selected product is invalid.');
                        }
                    }
                ],
                'price' => [
                    'required',
                    'numeric',
                    'min:0.01',
                    'max:100.00', // Reasonable maximum price
                ],
                'g-recaptcha-response' => ['required', function ($attribute, $value, $fail) {
                    $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                        'secret' => config('services.recaptcha.secret_key'),
                        'response' => $value,
                        'remoteip' => request()->ip(),
                    ]);

                    if (!$response->json('success')) {
                        $fail('The reCAPTCHA verification failed. Please try again.');
                    }
                }],
            ], [
                'username.required' => 'Please enter your Minecraft username.',
                'username.min' => 'Username must be at least 3 characters.',
                'username.max' => 'Username cannot exceed 16 characters.',
                'username.regex' => 'Username can only contain letters, numbers, and underscores.',
                'username.not_regex' => 'This username is not allowed.',
                'platform.required' => 'Please select your Minecraft platform.',
                'platform.in' => 'Please select either Java or Bedrock edition.',
                'discord.regex' => 'Please enter a valid Discord username.',
                'proof.required' => 'Please upload your payment proof.',
                'proof.image' => 'The uploaded file must be an image.',
                'proof.max' => 'The image size cannot exceed 5MB.',
                'proof.mimes' => 'Only JPEG, PNG, and JPG images are allowed.',
                'g-recaptcha-response.required' => 'Please verify that you\'re not a robot by completing the reCAPTCHA.',
            ]);

            // Add session-based order tracking
            $now = now();
            $lastOrderTime = session('last_order_time');
            $dailyOrderCount = session('daily_order_count', 0);
            $hourlyOrderCount = session('hourly_order_count', 0);
            
            // Reset daily count if it's a new day (after midnight)
            if ($lastOrderTime) {
                $lastOrderDate = Carbon::parse($lastOrderTime)->startOfDay();
                $today = $now->copy()->startOfDay();
                
                if ($lastOrderDate->lt($today)) {
                    session(['daily_order_count' => 0]);
                    session(['hourly_order_count' => 0]);
                    session(['last_order_time' => null]);
                }
            }
            
            // Reset hourly count if last order was more than 1 hour ago
            elseif ($lastOrderTime && $now->diffInMinutes($lastOrderTime) >= 60) {
                session(['hourly_order_count' => 0]);
            }
            
            // Check hourly limit
            if ($hourlyOrderCount >= self::HOURLY_LIMIT) {
                $nextHourlyReset = Carbon::parse($lastOrderTime)->addHour();
                $minutesLeft = ceil($now->diffInMinutes($nextHourlyReset));
                return redirect()->route('store')->with('error', 
                    "Hourly order limit reached ({$hourlyOrderCount}/" . self::HOURLY_LIMIT . "). Please try again in {$minutesLeft} minutes."
                );
            }
            
            // Check daily limit
            if ($dailyOrderCount >= self::DAILY_LIMIT) {
                $nextDailyReset = Carbon::parse($lastOrderTime)->addHours(24);
                $hoursLeft = floor($now->diffInHours($nextDailyReset));
                $minutesLeft = ceil($now->diffInMinutes($nextDailyReset) % 60);
                return redirect()->route('store')->with('error', 
                    "Daily order limit reached ({$dailyOrderCount}/" . self::DAILY_LIMIT . "). Please try again in {$hoursLeft}h {$minutesLeft}m."
                );
            }

            // Update counters
            session(['hourly_order_count' => $hourlyOrderCount + 1]);
            session(['daily_order_count' => $dailyOrderCount + 1]);
            session(['last_order_time' => $now]);

            // Get all available products
            $ranks = config('store.ranks', []);
            $money = config('store.money', []);
            $plots = config('store.plots', []);
            $allProducts = array_merge($ranks, $money, $plots);

            // Validate product exists and price matches
            $product = $allProducts[$validated['rank']] ?? null;
            if (!$product || abs($product['price'] - floatval($validated['price'])) > 0.01) {
                return redirect()->route('store')->with('error', 'Invalid order parameters. Please try ordering again.');
            }

            // Format username based on platform
            $username = $validated['username'];
            if ($validated['platform'] === 'bedrock') {
                $username = '.' . ltrim($username, '.'); // Add dot prefix if not already present
            }
            
            // Create order data
            $order = [
                'id' => uniqid('order_'),
                'username' => $username,
                'platform' => $validated['platform'],
                'discord' => $validated['discord'] ?? null,
                'rank' => $validated['rank'],
                'price' => $validated['price'],
                'product_name' => $product['display_name'],
                'product_type' => $product['type'] ?? 'rank',
                'status' => 'pending'
            ];

            // Send Discord notification with file directly
            $this->discordBot->sendOrderNotification($order, $request->file('proof'));

            return redirect('/store')->with('success', 'Order submitted successfully! We will process it shortly.');

        } catch (\Exception $e) {
            Log::error('Order submission failed: ' . $e->getMessage());
            // Extract validation errors if they exist
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                $errors = $e->validator->errors()->all();
                $errorMessage = implode(' ', $errors);
                Log::error('Validation errors: ' . $errorMessage);
                return redirect()
                    ->route('store')
                    ->with('error', 'Order validation failed: ' . $errorMessage);
            }

            $errorMessage = 'An unexpected error occurred. ';
            if (app()->environment('local')) {
                $errorMessage .= $e->getMessage();
            } else {
                $errorMessage .= 'Please try again or contact support if the problem persists.';
            }
            return redirect()->route('store')->with('error', $errorMessage);
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