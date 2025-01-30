<?php

return [
    'ranks' => [
        'VIP' => [
            'display_name' => 'VIP',
            'price' => 5.00,
            'color' => '#FCFF3D',
            'description' => 'Start your journey with essential perks',
            'features' => [
                'chat' => [
                    'prefix' => '§a[VIP]',
                    'color' => true,
                    'format' => true,
                ],
                'perks' => [
                    'homes' => 2,
                    'daily_rewards' => 2,
                    'particles' => ['hearts', 'smoke'],
                ],
                'display_features' => [
                    'VIP Kit Access',
                    '1+ Plot',
                    'MinePvP-Area Access',
                    'Discord VIP Role',
                ]
            ],
        ],
        'PRO' => [
            'display_name' => 'PRO',
            'price' => 10.00,
            'color' => '#3DEFFF',
            'description' => 'Enhanced privileges with flight access',
            'features' => [
                'chat' => [
                    'prefix' => '§b[PRO]',
                    'color' => true,
                    'format' => true,
                    'emotes' => true,
                ],
                'perks' => [
                    'homes' => 4,
                    'daily_rewards' => 3,
                    'particles' => ['hearts', 'smoke', 'flame', 'portal'],
                    'pro_servers' => true,
                ],
                'display_features' => [
                    'PRO Kit Access',
                    '2+ Plots',
                    'MinePvP-Area Access',
                    'Discord VIP Role',
                    '/cfly Command',
                ]
            ],
        ],
        'ELITE' => [
            'display_name' => 'ELITE',
            'price' => 15.00,
            'color' => '#47FF3D',
            'description' => 'Premium features with extra plots',
            'features' => [
                'chat' => [
                    'prefix' => '§6[ELITE]',
                    'color' => true,
                    'format' => true,
                    'emotes' => true,
                    'custom_join_msg' => true,
                ],
                'perks' => [
                    'homes' => 6,
                    'daily_rewards' => 4,
                    'particles' => ['all'],
                    'pro_servers' => true,
                    'priority_queue' => 2,
                ],
                'display_features' => [
                    'ELITE Kit Access',
                    '3+ Plots',
                    'MinePvP-Area Access',
                    'Discord VIP Role',
                    '/cfly Command',
                ]
            ],
        ],
        'EPIC' => [
            'display_name' => 'EPIC',
            'price' => 20.00,
            'color' => '#FC1212',
            'description' => 'Epic perks with maximum plots',
            'features' => [
                'chat' => [
                    'prefix' => '§d[EPIC]',
                    'color' => true,
                    'format' => true,
                    'emotes' => true,
                    'custom_join_msg' => true,
                    'custom_death_msg' => true,
                ],
                'perks' => [
                    'homes' => 8,
                    'daily_rewards' => 5,
                    'particles' => ['all'],
                    'pro_servers' => true,
                    'priority_queue' => 3,
                    'private_island' => true,
                ],
                'display_features' => [
                    'EPIC Kit Access',
                    '4+ Plots',
                    'MinePvP-Area Access',
                    'Discord VIP Role',
                    '/cfly Command',
                ]
            ],
        ],
        'LEGEND' => [
            'display_name' => 'LEGEND',
            'price' => 25.00,
            'color' => '#FF3FA7',
            'description' => 'Ultimate privileges with flight bypass',
            'features' => [
                'chat' => [
                    'prefix' => '§c[LEGEND]',
                    'color' => true,
                    'format' => true,
                    'emotes' => true,
                    'custom_join_msg' => true,
                    'custom_death_msg' => true,
                    'rainbow_chat' => true,
                ],
                'perks' => [
                    'homes' => 12,
                    'daily_rewards' => 6,
                    'particles' => ['all', 'exclusive'],
                    'pro_servers' => true,
                    'priority_queue' => 1,
                    'private_island' => true,
                    'private_world' => true,
                    'monthly_items' => true,
                    'priority_support' => true,
                ],
                'display_features' => [
                    'LEGEND Kit Access',
                    '6+ Plots',
                    'MinePvP-Area Access',
                    'Discord VIP Role',
                    '/cfly Command + Bypass',
                ]
            ],
        ],
    ],
]; 