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
    
    'money' => [
        'money_1m' => [
            'display_name' => '1M Coins',
            'price' => 5.00,
            'type' => 'money',
            'amount' => 1000000,
            'description' => 'Purchase 1,000,000 in-game coins',
            'features' => [
                'display_features' => [
                    '1,000,000 In-Game Coins',
                    'Instant Delivery',
                    '24/7 Support'
                ]
            ]
        ],
        'money_2m' => [
            'display_name' => '2M Coins',
            'price' => 10,
            'type' => 'money',
            'amount' => 2000000,
            'description' => 'Purchase 2,000,000 in-game coins',
            'features' => [
                'display_features' => [
                    '2,000,000 In-Game Coins',
                    'Instant Delivery',
                    '24/7 Support'
                ]
            ]
        ],
        'money_3m' => [
            'display_name' => '3M Coins',
            'price' => 15,
            'type' => 'money',
            'amount' => 3000000,
            'description' => 'Purchase 3,000,000 in-game coins',
            'features' => [
                'display_features' => [
                    '3,000,000 In-Game Coins',
                    'Instant Delivery',
                    '24/7 Support'
                ]
            ]
        ],
        'money_5m' => [
            'display_name' => '5M Coins',
            'price' => 20,
            'type' => 'money',
            'amount' => 5000000,
            'description' => 'Purchase 5,000,000 in-game coins',
            'features' => [
                'display_features' => [
                    '5,000,000 In-Game Coins',
                    'Instant Delivery',
                    '24/7 Support'
                ]
            ]
        ]
    ],
    
    'plots' => [
        'plot_vip1' => [
            'display_name' => 'Plot VIP I',
            'price' => 12.00,
            'type' => 'plot',
            'size' => '107x107',
            'description' => 'Premium VIP I Plot - Limited availability near spawn!',
            'features' => [
                'display_features' => [
                    '107x107 Plot Size',
                    'Premium Spawn Location',
                    'Limited Edition (Only 12 Available)',
                    'Instant Delivery',
                    '24/7 Support'
                ]
            ]
        ],
        'plot_vip2' => [
            'display_name' => 'Plot VIP II',
            'price' => 6,
            'type' => 'plot',
            'size' => '107x40',
            'description' => 'Premium VIP II Plot - Great location near spawn!',
            'features' => [
                'display_features' => [
                    '107x40 Plot Size',
                    'Near Spawn Location',
                    'Limited Edition (Only 16 Available)',
                    'Instant Delivery',
                    '24/7 Support'
                ]
            ]
        ],
        'plot_normal' => [
            'display_name' => 'Normal Plot',
            'price' => 2.5,
            'type' => 'plot',
            'size' => '40x40',
            'description' => 'Standard Plot - Perfect for starting your build journey!',
            'features' => [
                'display_features' => [
                    '40x40 Plot Size',
                    'Standard Plot Protection',
                    'Unlimited Availability',
                    'Instant Delivery',
                    '24/7 Support'
                ]
            ]
        ]
    ]
]; 