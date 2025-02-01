<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>Store - UnicornMC</title>
        <meta name="description" content="Support UnicornMC by purchasing ranks and perks from our store.">
        <meta property="og:title" content="Store - UnicornMC">
        <meta property="og:description" content="Support UnicornMC by purchasing ranks and perks from our store.">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <!-- Favicons -->
        <link rel="icon" type="image/png" href="/favicon/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg" />
        <link rel="shortcut icon" href="/favicon/favicon.ico" />
        <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png" />
        <meta name="apple-mobile-web-app-title" content="UnicornMC" />
        <link rel="manifest" href="/favicon/site.webmanifest" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tsparticles@2.12.0/tsparticles.bundle.min.js"></script>
        @vite(['resources/css/styles.css', 'resources/css/store.css'])
    </head>
    <body>
        <div id="tsparticles"></div>
        <div class="bg-overlay"></div>
        <div class="content">
            <a href="/" class="back-button">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Return Home
            </a>

            <main class="store-container">
                @if (session('success'))
                    <div class="alert alert-success animate__animated animate__fadeIn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <div class="alert-content">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-error animate__animated animate__fadeIn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <div class="alert-content">
                            {{ session('error') }}
                        </div>
                    </div>
                @endif
                
                <h1 class="store-title">Store</h1>
                
                <div class="store-categories">
                    <button class="category-btn active" data-category="ranks">Ranks</button>
                    <button class="category-btn" data-category="money">Money</button>
                    <button class="category-btn" data-category="plots">Plots</button>
                </div>

                <div class="store-items">
                    <!-- Ranks -->
                    <div class="store-section active" data-category="ranks">
                        <div class="store-grid">
                            @foreach(config('store.ranks') as $rankId => $rank)
                                <div class="store-item">
                                    <div class="item-header {{ strtolower($rankId) }}" style="background: linear-gradient(45deg, {{ $rank['color'] }}66, {{ $rank['color'] }})">
                                        <h3>{{ $rank['display_name'] }}</h3>
                                        <span class="price">${{ number_format($rank['price'], 2) }}</span>
                                    </div>
                                    <div class="item-content">
                                        <p class="rank-description">{{ $rank['display_name'] }} rank - {{ $rank['description'] }}</p>
                                        
                                        <div class="features-container">
                                            <h4>Package Includes:</h4>
                                            <ul class="perks-list">
                                                @foreach($rank['features']['display_features'] as $feature)
                                                    <li>
                                                        <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <polyline points="20 6 9 17 4 12"></polyline>
                                                        </svg>
                                                        {{ $feature }}
                                                    </li>
                                                @endforeach
                                                <li>
                                                    <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="20 6 9 17 4 12"></polyline>
                                                    </svg>
                                                    Instant Delivery
                                                </li>
                                                <li>
                                                    <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="20 6 9 17 4 12"></polyline>
                                                    </svg>
                                                    24/7 Support
                                                </li>
                                            </ul>
                                        </div>

                                        <button 
                                            class="buy-btn" 
                                            onclick="window.location.href='/order?rank={{ $rankId }}&price={{ $rank['price'] }}'"
                                        >
                                            Purchase
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Money -->
                    <div class="store-section" data-category="money">
                        <div class="store-grid">
                            <div class="store-item">
                                <div class="item-header" style="background: linear-gradient(45deg, #32CD32, #228B22)">
                                    <h3>1M Coins</h3>
                                    <span class="price">$5.00</span>
                                </div>
                                <div class="item-content">
                                    <p class="rank-description">Purchase 1,000,000 in-game coins for your Minecraft adventure!</p>
                                    
                                    <div class="features-container">
                                        <h4>Package Includes:</h4>
                                        <ul class="perks-list">
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                1,000,000 In-Game Coins
                                            </li>
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                Instant Delivery
                                            </li>
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                24/7 Support
                                            </li>
                                        </ul>
                                    </div>
                                    <button class="buy-btn" 
                                        onclick="window.location.href='{{ route('order.create', ['rank' => 'money_1m', 'price' => 5.00]) }}'"
                                    >
                                        Purchase
                                    </button>
                                </div>
                            </div>

                            <div class="store-item">
                                <div class="item-header" style="background: linear-gradient(45deg, #32CD32, #228B22)">
                                    <h3>2M Coins</h3>
                                    <span class="price">$10.00</span>
                                </div>
                                <div class="item-content">
                                    <p class="rank-description">Purchase 2,000,000 in-game coins for your Minecraft adventure!</p>
                                    
                                    <div class="features-container">
                                        <h4>Package Includes:</h4>
                                        <ul class="perks-list">
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                2,000,000 In-Game Coins
                                            </li>
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                Instant Delivery
                                            </li>
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                24/7 Support
                                            </li>
                                        </ul>
                                    </div>
                                    <button class="buy-btn" onclick="window.location.href='/order?rank=money_2m&price=10'">
                                        Purchase
                                    </button>
                                </div>
                            </div>

                            <div class="store-item">
                                <div class="item-header" style="background: linear-gradient(45deg, #32CD32, #228B22)">
                                    <h3>3M Coins</h3>
                                    <span class="price">$15.00</span>
                                </div>
                                <div class="item-content">
                                    <p class="rank-description">Purchase 3,000,000 in-game coins for your Minecraft adventure!</p>
                                    
                                    <div class="features-container">
                                        <h4>Package Includes:</h4>
                                        <ul class="perks-list">
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                3,000,000 In-Game Coins
                                            </li>
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                Instant Delivery
                                            </li>
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                24/7 Support
                                            </li>
                                        </ul>
                                    </div>
                                    <button class="buy-btn" onclick="window.location.href='/order?rank=money_3m&price=15'">
                                        Purchase
                                    </button>
                                </div>
                            </div>

                            <div class="store-item">
                                <div class="item-header" style="background: linear-gradient(45deg, #32CD32, #228B22)">
                                    <h3>5M Coins</h3>
                                    <span class="price">$20.00</span>
                                </div>
                                <div class="item-content">
                                    <p class="rank-description">Purchase 5,000,000 in-game coins for your Minecraft adventure!</p>
                                    
                                    <div class="features-container">
                                        <h4>Package Includes:</h4>
                                        <ul class="perks-list">
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                5,000,000 In-Game Coins
                                            </li>
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                Instant Delivery
                                            </li>
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                24/7 Support
                                            </li>
                                        </ul>
                                    </div>
                                    <button class="buy-btn" onclick="window.location.href='/order?rank=money_5m&price=20'">
                                        Purchase
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Plots -->
                    <div class="store-section" data-category="plots">
                        <div class="store-grid">
                            <!-- Normal Plot -->
                            <div class="store-item">
                                <div class="item-header" style="background: linear-gradient(45deg, #9c27b0, #6a1b9a)">
                                    <h3>Normal Plot</h3>
                                    <span class="price">$2.50</span>
                                </div>
                                <div class="item-content">
                                    <p class="rank-description">Standard Plot - Perfect for starting your build journey!</p>
                                    
                                    <div class="features-container">
                                        <h4>Package Includes:</h4>
                                        <ul class="perks-list">
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                40x40 Plot Size
                                            </li>
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                Standard Plot Protection
                                            </li>
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                Unlimited Availability
                                            </li>
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                Instant Delivery
                                            </li>
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                24/7 Support
                                            </li>
                                        </ul>
                                    </div>
                                    <button class="buy-btn" onclick="window.location.href='{{ route('order.create', ['rank' => 'plot_normal', 'price' => 2.50]) }}'">
                                        Purchase
                                    </button>
                                </div>
                            </div>

                            <!-- VIP I Plot -->
                            <div class="store-item">
                                <div class="item-header" style="background: linear-gradient(45deg, #f44336, #c62828)">
                                    <h3>Plot VIP I</h3>
                                    <span class="price">$12.00</span>
                                </div>
                                <div class="item-content">
                                    <p class="rank-description">Premium VIP I Plot - Limited availability near spawn!</p>
                                    
                                    <div class="features-container">
                                        <h4>Package Includes:</h4>
                                        <ul class="perks-list">
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                107x107 Plot Size
                                            </li>
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                Premium Spawn Location
                                            </li>
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                Limited Edition (Only 12 Available)
                                            </li>
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                Instant Delivery
                                            </li>
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                24/7 Support
                                            </li>
                                        </ul>
                                    </div>
                                    <button class="buy-btn" 
                                        onclick="window.location.href='{{ route('order.create', ['rank' => 'plot_vip1', 'price' => 12.00]) }}'"
                                    >
                                        Purchase
                                    </button>
                                </div>
                            </div>

                            <!-- VIP II Plot -->
                            <div class="store-item">
                                <div class="item-header" style="background: linear-gradient(45deg, #ff9800, #ef6c00)">
                                    <h3>Plot VIP II</h3>
                                    <span class="price">$6.00</span>
                                </div>
                                <div class="item-content">
                                    <p class="rank-description">Premium VIP II Plot - Great location near spawn!</p>
                                    
                                    <div class="features-container">
                                        <h4>Package Includes:</h4>
                                        <ul class="perks-list">
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                107x40 Plot Size
                                            </li>
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                Near Spawn Location
                                            </li>
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                Limited Edition (Only 16 Available)
                                            </li>
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                Instant Delivery
                                            </li>
                                            <li>
                                                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                                24/7 Support
                                            </li>
                                        </ul>
                                    </div>
                                    <button class="buy-btn" onclick="window.location.href='/order?rank=plot_vip2&price=6'">
                                        Purchase
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const categoryBtns = document.querySelectorAll('.category-btn');
                const sections = document.querySelectorAll('.store-section');

                categoryBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        const category = btn.dataset.category;
                        
                        // Update buttons
                        categoryBtns.forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        
                        // Update sections
                        sections.forEach(section => {
                            if (section.dataset.category === category) {
                                section.classList.add('active');
                            } else {
                                section.classList.remove('active');
                            }
                        });
                    });
                });
            });
        </script>

        <footer class="footer">
            <span>Copyright © 2023-2025 UnicornMC. All Rights Reserved.</span>
        </footer>

        @vite(['resources/js/store.js'])
    </body>
</html> 