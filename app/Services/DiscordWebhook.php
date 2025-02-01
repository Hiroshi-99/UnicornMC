<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class DiscordWebhook
{
    protected $webhookUrl;
    protected $imgurClientId;
    protected $imgurClientSecret;
    protected $timeout = 10; // seconds

    public function __construct()
    {
        $this->webhookUrl = Config::get('services.discord.webhook_url');
        $this->imgurClientId = Config::get('services.imgur.client_id');
        
        if (empty($this->webhookUrl)) {
            Log::error('Discord webhook URL is not configured');
            throw new \Exception('Discord webhook URL is not configured');
        }

        if (empty($this->imgurClientId)) {
            Log::error('Imgur client ID is not configured');
            throw new \Exception('Imgur client ID is not configured');
        }
    }

    protected function uploadToImgur($imageUrl)
    {
        try {
            // Get the relative path from the URL
            $relativePath = str_replace('/storage/', '', parse_url($imageUrl, PHP_URL_PATH));
            
            // Get the absolute path to the file in the storage/app/public directory
            $absolutePath = storage_path('app/public/' . $relativePath);
            
            // Add debug logging
            Log::debug('Attempting to access file', [
                'original_url' => $imageUrl,
                'relative_path' => $relativePath,
                'absolute_path' => $absolutePath,
                'exists' => file_exists($absolutePath)
            ]);

            // Check if file exists
            if (!file_exists($absolutePath)) {
                throw new \Exception('Image file not found: ' . $absolutePath);
            }

            // Get the image content directly from the file system
            $imageContent = file_get_contents($absolutePath);
            if (!$imageContent) {
                throw new \Exception('Could not read image content');
            }
            
            // Convert image content to base64
            $base64Image = base64_encode($imageContent);

            // Use Http client with SSL verification disabled
            $response = Http::withOptions([
                'verify' => false // Disable SSL verification
            ])->withHeaders([
                'Authorization' => 'Client-ID ' . $this->imgurClientId,
                'Content-Type' => 'application/json'
            ])->post('https://api.imgur.com/3/upload', [
                'image' => $base64Image,
                'type' => 'base64',
                'name' => 'proof_image_' . time() . '.jpg',
                'title' => 'Order Proof',
                'description' => 'Payment proof for UnicornMC order'
            ]);

            if (!$response->successful()) {
                Log::error('Imgur upload failed', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                    'url' => $imageUrl,
                    'storage_path' => $absolutePath
                ]);
                throw new \Exception('Imgur upload failed: ' . $response->status());
            }

            $data = $response->json();
            
            Log::info('Imgur upload successful', [
                'original_url' => $imageUrl,
                'storage_path' => $absolutePath,
                'imgur_url' => $data['data']['link'] ?? null,
                'response' => $data
            ]);

            return $data['data']['link'] ?? null;
        } catch (\Exception $e) {
            Log::error('Imgur upload error: ' . $e->getMessage(), [
                'url' => $imageUrl,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function sendOrderNotification($order)
    {
        try {
            $productType = ucfirst($order['product_type'] ?? 'Rank');
            
            // Get the image file path
            $relativePath = str_replace('/storage/', '', parse_url($order['proof_url'], PHP_URL_PATH));
            $absolutePath = storage_path('app/public/' . $relativePath);

            if (!file_exists($absolutePath)) {
                throw new \Exception('Image file not found: ' . $absolutePath);
            }

            // Create a cleaner embed
            $embed = [
                'title' => '🛍️ New Purchase Order',
                'description' => "```diff\n+ New order has been submitted to UnicornMC Store!\n+ Please check and process this order.```",
                'color' => $this->getColorByType($order['product_type']),
                'fields' => [
                    [
                        'name' => '👤 Username',
                        'value' => "```\n{$order['username']}```",
                        'inline' => true
                    ],
                    [
                        'name' => '🎭 Discord',
                        'value' => $order['discord'] ? 
                            "```\n{$order['discord']}```" : 
                            "```\nNot provided```",
                        'inline' => true
                    ],
                    [
                        'name' => '🎮 Platform',
                        'value' => $order['platform'] === 'java' ? 
                            "```\n☕ Java Edition```" : 
                            "```\n📱 Bedrock Edition```",
                        'inline' => true
                    ],
                    [
                        'name' => '🏷️ Product',
                        'value' => "```\n{$order['product_name']}```",
                        'inline' => true
                    ],
                    [
                        'name' => '💰 Price',
                        'value' => "```\n$" . number_format($order['price'], 2) . "```",
                        'inline' => true
                    ],
                    [
                        'name' => '📝 Order Details',
                        'value' => "```yaml\nType: {$productType}\nPlatform: " . 
                            ($order['platform'] === 'java' ? '☕ Java' : '📱 Bedrock') . 
                            "\nStatus: ⏳ Pending\nOrder ID: " . substr(md5(uniqid()), 0, 8) . "```",
                        'inline' => false
                    ]
                ],
                'footer' => [
                    'text' => '🦄 UnicornMC Store • Order System'
                ],
                'timestamp' => now()
            ];

            // Prepare the multipart request with role mention
            $response = Http::withOptions([
                'verify' => false
            ])->attach(
                'file',
                file_get_contents($absolutePath),
                'payment_proof.jpg',
                ['Content-Type' => 'image/jpeg']
            )->post($this->webhookUrl, [
                'content' => '🔔 **New Order Alert!** <@&1334942311695777903>',
                'payload_json' => json_encode([
                    'embeds' => [$embed]
                ])
            ]);

            if (!$response->successful()) {
                Log::error('Discord webhook failed', [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                throw new \Exception('Discord webhook failed: ' . $response->status());
            }

            Log::info('Order notification sent successfully', [
                'username' => $order['username'],
                'product' => $order['product_name']
            ]);

        } catch (\Exception $e) {
            Log::error('Discord webhook failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function getColorByType($type)
    {
        return match($type) {
            'money' => 0x2ECC71,  // Emerald Green
            'plot' => 0x9B59B6,   // Amethyst Purple
            default => 0xE91E63    // Pink
        };
    }

    public function sendMessage($message, $files = [])
    {
        try {
            $data = [
                'content' => $message,
            ];

            if (empty($files)) {
                return Http::withOptions([
                    'verify' => false
                ])->post($this->webhookUrl, $data);
            }

            // Handle file attachments
            $response = Http::withOptions([
                'verify' => false
            ])->attach(
                'file',
                file_get_contents($files[0]),
                basename($files[0])
            )->post($this->webhookUrl, [
                'payload_json' => json_encode($data)
            ]);

            if (!$response->successful()) {
                Log::error('Failed to send message', [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                throw new \Exception('Failed to send message: ' . $response->status());
            }

            return $response;

        } catch (\Exception $e) {
            Log::error('Send message failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
} 