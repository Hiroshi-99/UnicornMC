<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class DiscordBot
{
    private $webhookUrl;
    private $botToken;
    private $ownerId;
    private $baseUrl = 'https://discord.com/api/v10';

    public function __construct()
    {
        $this->webhookUrl = config('services.discord.webhook_url');
        $this->botToken = config('services.discord.bot_token');
        $this->ownerId = config('services.discord.owner_id');

        if (empty($this->webhookUrl)) {
            Log::error('Discord webhook URL is not configured');
        }
    }

    public function sendOrderNotification($order, UploadedFile $proofFile)
    {
        try {
            // Format price with currency symbol
            $formattedPrice = '$' . number_format($order['price'], 2);
            
            // Set timezone to GMT+7
            $orderTime = Carbon::now()->setTimezone('Asia/Bangkok');
            
            // Create embed for notifications
            $embed = [
                'fields' => [
                    [
                        'name' => '👤 Username',
                        'value' => "```\n{$order['username']}```",
                        'inline' => true
                    ],
                    [
                        'name' => '🎭 Discord',
                        'value' => !empty($order['discord']) ? 
                            "```\n{$order['discord']}```" : 
                            "Not provided",
                        'inline' => true
                    ],
                    [
                        'name' => '🎮 Platform',
                        'value' => $order['platform'] === 'java' ? 
                            "☕ Java Edition" : 
                            "📱 Bedrock Edition",
                        'inline' => true
                    ],
                    [
                        'name' => '🏷️ Product',
                        'value' => $order['product_name'],
                        'inline' => true
                    ],
                    [
                        'name' => '💰 Price',
                        'value' => $formattedPrice,
                        'inline' => true
                    ],
                    [
                        'name' => '📝 Order Details',
                        'value' => "```yaml\n" .
                            "Type: " . ($order['product_type'] ?? 'Rank') . "\n" .
                            "Platform: " . ($order['platform'] === 'java' ? '☕ Java' : '📱 Bedrock') . "\n" .
                            "Status: ⏳ Pending\n" .
                            "Order ID: " . substr($order['id'], 6) . "\n" .
                            "```",
                        'inline' => false
                    ]
                ],
                'color' => 0x2F3136, // Dark theme color
                'timestamp' => now()->toIso8601String(),
                'footer' => [
                    'text' => '🦄 UnicornMC Store',
                    'icon_url' => config('app.url') . '/favicon/favicon-96x96.png'
                ]
            ];

            // Prepare multipart form data for file upload
            $payload = [
                [
                    'name' => 'payload_json',
                    'contents' => json_encode([
                        'content' => "### 🔔 New Order Alert!",
                        'embeds' => [$embed],
                        'components' => [
                            [
                                'type' => 1,
                                'components' => [
                                    [
                                        'type' => 2,
                                        'style' => 3,
                                        'label' => '✅ Approve',
                                        'custom_id' => 'approve_order_' . $order['id']
                                    ],
                                    [
                                        'type' => 2,
                                        'style' => 4,
                                        'label' => '❌ Reject',
                                        'custom_id' => 'reject_order_' . $order['id']
                                    ]
                                ]
                            ]
                        ]
                    ])
                ],
                [
                    'name' => 'files[0]',
                    'contents' => fopen($proofFile->getPathname(), 'r'),
                    'filename' => 'proof_' . substr($order['id'], 6) . '.' . $proofFile->getClientOriginalExtension()
                ]
            ];

            // Send to webhook with file
            $response = Http::asMultipart()
                ->post($this->webhookUrl, $payload);
            
            if (!$response->successful()) {
                Log::error('Discord webhook failed: ' . $response->body());
                return false;
            }

            // Send DM to owner if configured
            if ($this->botToken && $this->ownerId) {
                // Create DM channel
                $createDmResponse = Http::withHeaders([
                    'Authorization' => 'Bot ' . $this->botToken,
                    'Content-Type' => 'application/json',
                ])->post("{$this->baseUrl}/users/@me/channels", [
                    'recipient_id' => $this->ownerId
                ]);

                if ($createDmResponse->successful()) {
                    $dmChannel = $createDmResponse->json();
                    
                    // Create a simpler embed for DM
                    $dmEmbed = [
                        'fields' => [
                            [
                                'name' => '👤 User Info',
                                'value' => "```\n" .
                                    "Username: {$order['username']}\n" .
                                    (!empty($order['discord']) ? "Discord: {$order['discord']}\n" : "") .
                                    "Platform: " . ($order['platform'] === 'java' ? '☕ Java' : '📱 Bedrock') . 
                                    "```",
                                'inline' => false
                            ],
                            [
                                'name' => '🛍️ Order Info',
                                'value' => "```\n" .
                                    "Product: {$order['product_name']}\n" .
                                    "Price: {$formattedPrice}\n" .
                                    "Order ID: " . substr($order['id'], 6) . "\n" .
                                    "Time: " . $orderTime->format('D, M j g:i A') . " GMT+7" .
                                    "```",
                                'inline' => false
                            ]
                        ],
                        'color' => 0x5865F2, // Discord Blurple for DM
                        'timestamp' => now()->toIso8601String(),
                        'footer' => [
                            'text' => '🦄 UnicornMC Store',
                            'icon_url' => config('app.url') . '/favicon/favicon-96x96.png'
                        ]
                    ];

                    // Send DM with file
                    $dmPayload = $payload;
                    $dmPayload[0]['contents'] = json_encode([
                        'content' => "### 🚨 **New Order Needs Review!**",
                        'embeds' => [$dmEmbed]
                    ]);

                    $dmResponse = Http::withHeaders([
                        'Authorization' => 'Bot ' . $this->botToken
                    ])->asMultipart()
                        ->post("{$this->baseUrl}/channels/{$dmChannel['id']}/messages", $dmPayload);

                    if (!$dmResponse->successful()) {
                        Log::error('Discord DM failed: ' . $dmResponse->body());
                    }
                } else {
                    Log::error('Failed to create DM channel: ' . $createDmResponse->body());
                }
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Discord notification failed: ' . $e->getMessage(), [
                'order_id' => $order['id'] ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function sendOrderUpdate($orderId, $status, $message = null)
    {
        try {
            $embed = [
                'title' => $status === 'approved' ? '✅ Order Approved' : '❌ Order Rejected',
                'description' => $message ?? ($status === 'approved' ? 
                    'Your order has been approved and processed!' : 
                    'Your order has been rejected. Please contact support for more information.'),
                'color' => $status === 'approved' ? 0x2ECC71 : 0xE74C3C,
                'timestamp' => now()->toIso8601String(),
                'footer' => [
                    'text' => '🦄 UnicornMC Store',
                    'icon_url' => config('app.url') . '/favicon/favicon-96x96.png'
                ]
            ];

            $webhookMessage = [
                'embeds' => [$embed]
            ];

            return Http::post($this->webhookUrl, $webhookMessage)->successful();

        } catch (\Exception $e) {
            Log::error('Discord order update failed: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'status' => $status,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function executeCommand($username, $command)
    {
        try {
            Log::info('Executing command for user', [
                'username' => $username,
                'command' => $command
            ]);
            // Send command to Minecraft server via RCON or your preferred method
            return true;
        } catch (\Exception $e) {
            Log::error('Command execution failed: ' . $e->getMessage(), [
                'username' => $username,
                'command' => $command,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get the public URL for an image
     */
    private function getPublicImageUrl($path)
    {
        try {
            // If it's already a full URL, return it
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                return $path;
            }

            // Clean up the path
            $path = str_replace(['/storage/', '//storage/'], '', $path);
            $path = trim($path, '/');
            
            // Check if the file exists
            if (!Storage::disk('public')->exists($path)) {
                Log::error('Payment proof file not found', ['path' => $path]);
                return null;
            }

            // Get the full URL using the APP_URL
            $url = config('app.url') . '/storage/' . $path;

            // Ensure the URL is properly formatted
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                Log::error('Invalid URL generated for payment proof', ['url' => $url]);
                return null;
            }

            return $url;

        } catch (\Exception $e) {
            Log::error('Error generating public URL: ' . $e->getMessage(), [
                'path' => $path,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
} 