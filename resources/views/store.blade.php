<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>Store - UnicornMC</title>
        <meta name="description" content="Support UnicornMC by purchasing ranks and perks from our store.">
        <meta property="og:title" content="Store - UnicornMC">
        <meta property="og:description" content="Support UnicornMC by purchasing ranks and perks from our store.">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
        <link rel="preload" href="/images/minecraft-bg.jpg" as="image">
        @vite(['resources/css/styles.css', 'resources/css/store.css'])
    </head>
    <body>
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
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                <h1 class="store-title">Store</h1>
                
                <div class="store-categories">
                    <button class="category-btn active" data-category="ranks">Ranks</button>
                    <button class="category-btn" data-category="crates">Crates</button>
                    <button class="category-btn" data-category="cosmetics">Cosmetics</button>
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
                                        <p class="rank-description">{{ $rank['description'] }}</p>
                                        
                                        <div class="features-container">
                                            <h4>Features:</h4>
                                            <ul class="perks-list">
                                                @foreach($rank['features']['display_features'] as $feature)
                                                    <li>
                                                        <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <polyline points="20 6 9 17 4 12"></polyline>
                                                        </svg>
                                                        {{ $feature }}
                                                    </li>
                                                @endforeach
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

                    <!-- Crates -->
                    <div class="store-section" data-category="crates">
                        <div class="store-grid">
                            <!-- Add crate items here -->
                            <div class="coming-soon">
                                <h3>Coming Soon!</h3>
                                <p>Exciting crates are being prepared...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Cosmetics -->
                    <div class="store-section" data-category="cosmetics">
                        <div class="store-grid">
                            <!-- Add cosmetic items here -->
                            <div class="coming-soon">
                                <h3>Coming Soon!</h3>
                                <p>Amazing cosmetics are on the way...</p>
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
    </body>
</html> 