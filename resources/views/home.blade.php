<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- Info meta tags, important for social media + SEO -->
        <title>UnicornMC - Official Website</title>
        <meta name="description" content="UnicornMC is an awesome Minecraft server. Join us at unicornmc.club">
        <meta property="og:title" content="UnicornMC - Official Website">
        <meta property="og:site_name" content="UnicornMC">
        <meta property="og:description" content="UnicornMC is an awesome Minecraft server. Join us at unicornmc.club">
        <meta property="og:image" content="/images/logo.png">
        <meta property="og:url" content="https://store.unicornmc.club">
        <meta name="twitter:card" content="summary_large_image">
        
        <!-- Favicons -->
        <link rel="icon" type="image/png" href="/favicon/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg" />
        <link rel="shortcut icon" href="/favicon/favicon.ico" />
        <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png" />
        <meta name="apple-mobile-web-app-title" content="UnicornMC" />
        <link rel="manifest" href="/favicon/site.webmanifest" />
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/tsparticles@2.12.0/tsparticles.bundle.min.js"></script>
        @vite(['resources/css/styles.css', 'resources/js/main.js'])
    </head>
    <body>
        <div id="tsparticles"></div>
    
        <div class="container">
            <div class="logo">
                <img src="/images/logo.png" alt="UnicornMC logo">
            </div>
    
            <div class="items">
                <a href="https://discord.gg/PBzbEhsJpg" class="item">
                    <div>
                        <img src="/images/discord-icon.png" alt="Minecraft forums icon" class="img">
                        <p class="subtitle">chat on our</p>
                        <p class="title">Discord</p>
                    </div>
                </a>
    
                <a href="/store" class="item">
                    <div>
                        <img src="/images/store-icon.png" alt="Minecraft store icon" class="img">
                        <p class="subtitle">donate on our</p>
                        <p class="title">Store</p>
                    </div>
                </a>
    
                <a href="https://www.youtube.com/@JJ-vg1kz" class="item">
                    <div>
                        <img src="/images/vote-icon.png" alt="Minecraft voting icon" class="img">
                        <p class="subtitle">support us by subscribe</p>
                        <p class="title">YouTube</p>
                    </div>
                </a>
    
            </div>
    
            <div class="playercount">
                <p>Join <span id="sip" data-ip="unicornmc.club"></span> other players on <span id="ip" class="copy-ip">unicornmc.club</span></p>
            </div>
        </div>
    
        <script src="/js/firefly.js" type="text/javascript"></script>
        <script src="/js/main.js" type="text/javascript"></script>
        <footer class="footer">
            <span>Copyright © 2023-2025 UnicornMC. All Rights Reserved.</span>
        </footer>
    </body>
</html>
