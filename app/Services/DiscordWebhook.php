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

            $response = Http::withHeaders([
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
            // Upload image to Imgur first
            $imgurUrl = null;
            if (!empty($order['proof_url'])) {
                try {
                    $imgurUrl = $this->uploadToImgur($order['proof_url']);
                } catch (\Exception $e) {
                    // If Imgur upload fails, fall back to original URL
                    Log::warning('Imgur upload failed, falling back to original URL', [
                        'original_url' => $order['proof_url']
                    ]);
                    $imgurUrl = $order['proof_url'];
                }
            }

            $embed = [
                'title' => '🛒 New Store Order #' . ($order['id'] ?? 'N/A'),
                'description' => "A new order has been placed and is pending verification!",
                'color' => hexdec('7fe7ee'),
                'fields' => [
                    [
                        'name' => '🏆 Rank',
                        'value' => $order['rank'],
                        'inline' => true
                    ],
                    [
                        'name' => '💰 Price',
                        'value' => '$' . number_format($order['price'], 2),
                        'inline' => true
                    ],
                    [
                        'name' => '👤 Username',
                        'value' => $order['username'],
                        'inline' => true
                    ],
                    [
                        'name' => '⏰ Order Time',
                        'value' => now()->format('Y-m-d H:i:s'),
                        'inline' => true
                    ],
                    [
                        'name' => '🌐 IP Address',
                        'value' => $order['ip_address'] ?? 'Unknown',
                        'inline' => true
                    ]
                ],
                'thumbnail' => [
                    'url' => 'https://mc-heads.net/avatar/' . urlencode($order['username'])
                ],
                'footer' => [
                    'text' => 'UnicornMC Store • Order needs verification'
                ],
                'timestamp' => now()
            ];

            // Add image to embed only if we have an Imgur URL
            if ($imgurUrl) {
                $embed['image'] = [
                    'url' => $imgurUrl
                ];
            }

            $data = [
                'content' => "New order received!",
                'embeds' => [$embed]
            ];

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'User-Agent' => 'UnicornMC-Store/1.0',
                ])
                ->post($this->webhookUrl, $data);

            if (!$response->successful()) {
                Log::error('Discord webhook failed with status: ' . $response->status(), [
                    'response' => $response->body(),
                    'order' => $order
                ]);
                throw new \Exception('Discord webhook request failed: ' . $response->status());
            }

            Log::info('Discord webhook sent successfully', ['order' => $order]);
            return $response;

        } catch (\Exception $e) {
            Log::error('Discord webhook error: ' . $e->getMessage(), [
                'order' => $order,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function sendMessage($message, $files = [])
    {
        $data = [
            'content' => $message,
        ];

        // Add files/images if present
        if (!empty($files)) {
            $multipart = [];
            
            // Add the message content as a part
            $multipart[] = [
                'name' => 'payload_json',
                'contents' => json_encode($data),
            ];

            // Add each file as a part
            foreach ($files as $index => $file) {
                $multipart[] = [
                    'name' => "file$index",
                    'contents' => fopen($file, 'r'),
                    'filename' => basename($file)
                ];
            }

            // Send with multipart form data
            return Http::attach(
                'files[]', 
                file_get_contents($files[0]), 
                basename($files[0])
            )->post($this->webhookUrl, $multipart);
        }

        // Send regular message without files
        return Http::post($this->webhookUrl, $data);
    }
} 