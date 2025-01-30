<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class DiscordWebhook
{
    protected $webhookUrl;
    protected $timeout = 10; // seconds

    public function __construct()
    {
        $this->webhookUrl = Config::get('services.discord.webhook_url');
        
        if (empty($this->webhookUrl)) {
            Log::error('Discord webhook URL is not configured');
            throw new \Exception('Discord webhook URL is not configured');
        }
    }

    public function sendOrderNotification($order)
    {
        try {
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
                'image' => [
                    'url' => $order['proof_url']
                ],
                'footer' => [
                    'text' => 'UnicornMC Store • Order needs verification'
                ],
                'timestamp' => now()
            ];

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'User-Agent' => 'UnicornMC-Store/1.0',
                ])
                ->post($this->webhookUrl, [
                    'content' => "New order received!",
                    'embeds' => [$embed]
                ]);

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
} 