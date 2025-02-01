<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>Complete Order - UnicornMC</title>
        <meta name="description" content="Complete your purchase on UnicornMC store.">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <!-- Favicons -->
        <link rel="icon" type="image/png" href="/favicon/favicon-96x96.png" sizes="96x96" />
        <!-- Loading animation CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
        <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg" />
        <link rel="shortcut icon" href="/favicon/favicon.ico" />
        <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png" />
        <meta name="apple-mobile-web-app-title" content="UnicornMC" />
        <link rel="manifest" href="/favicon/site.webmanifest" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
        @vite(['resources/css/styles.css', 'resources/css/store.css', 'resources/css/order.css'])
        <!-- Add reCAPTCHA -->
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <!-- Add jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Add SameSite attribute to cookies via JS -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const token = document.querySelector('meta[name="csrf-token"]')?.content;
                if (token) {
                    document.cookie = `XSRF-TOKEN=${token}; SameSite=Strict; Secure; Path=/`;
                }
            });
        </script>
    </head>
    <body>
        <!-- Loading Overlay -->
        <div id="loadingOverlay" class="loading-overlay" style="display: none;">
            <div class="loading-content animate__animated animate__fadeIn">
                <div class="spinner"></div>
                <p>Processing your order...</p>
            </div>
        </div>

        <a href="/store" class="back-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back to Store
        </a>

        <main class="order-container">
            <h1 class="order-title">Complete Your Order</h1>
            
            <div class="order-form-container">
                @if ($errors->any() || session('error'))
                    <div class="alert alert-error animate__animated animate__fadeIn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <div class="alert-content">
                            @if(session('error'))
                                <p>{{ session('error') }}</p>
                            @endif
                            @if($errors->any())
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif
                <form id="orderForm" class="order-form" method="POST" action="{{ route('order.store') }}" 
                      enctype="multipart/form-data" onsubmit="return showLoading()">
                    @csrf
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <!-- Honeypot field -->
                    <input type="text" 
                           name="website" 
                           style="display:none !important" 
                           tabindex="-1" 
                           autocomplete="off">

                    <input type="hidden" name="rank" value="{{ $rank }}" readonly>
                    <input type="hidden" name="price" value="{{ $price }}" readonly>

                    <div class="form-group">
                        <label for="username">Minecraft Username</label>
                        <input type="text" id="username" name="username" required 
                               placeholder="Enter your Minecraft username">
                    </div>

                    <div class="form-group">
                        <label for="discord">
                            Discord Username 
                            <span class="optional-label">(Optional)</span>
                        </label>
                        <input type="text" id="discord" name="discord" 
                               placeholder="Enter your Discord username">
                        <span class="help-text">Example: username#1234 or username</span>
                    </div>

                    <div class="form-group">
                        <label id="platform-label">Platform</label>
                        <div class="platform-selector" role="radiogroup" aria-labelledby="platform-label">
                            <label class="platform-option">
                                <input type="radio" name="platform" value="java" required 
                                       aria-label="Java Edition">
                                <span class="platform-content">
                                    <img src="/images/java.png" alt="Java Edition">
                                    <span>Java Edition</span>
                                </span>
                            </label>
                            <label class="platform-option">
                                <input type="radio" name="platform" value="bedrock" required
                                       aria-label="Bedrock Edition">
                                <span class="platform-content">
                                    <img src="/images/bedrock.png" alt="Bedrock Edition">
                                    <span>Bedrock Edition</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Payment Details</label>
                        <div class="payment-info">
                            <div class="payment-qr-container">
                                <img src="/images/jjqr.jpg" alt="KHQR Payment" class="payment-qr" id="qrImage">
                                <div class="qr-overlay">
                                    <span>Click to view</span>
                                </div>
                            </div>
                            <div class="payment-details">
                                <div class="payment-recipient">
                                    <div class="recipient-info">
                                        <p class="info-label">Name:</p>
                                        <p class="recipient-name">Chan Sreyvy</p>
                                    </div>
                                    <p class="payment-type">KHQR Payment</p>
                                    <div class="payment-phone">
                                        <p class="phone-label">Tel:</p>
                                        <p class="phone-number" id="phoneNumber">087855814</p>
                                        <button type="button" class="copy-btn">
                                            <span class="copy-text">Copy</span>
                                            <svg class="copy-icon" viewBox="0 0 24 24" width="16" height="16">
                                                <path d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <p>1. Scan QR code or copy account details</p>
                                <p>2. Complete payment for ${{ $price }}</p>
                                <p>3. Screenshot your payment confirmation</p>
                                <p>4. Upload the screenshot below</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="proof">Upload Payment Proof</label>
                        <div class="upload-area" id="uploadArea">
                            <input type="file" id="proof" name="proof" accept="image/*" required>
                            <div class="upload-placeholder">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="17 8 12 3 7 8"/>
                                    <line x1="12" y1="3" x2="12" y2="15"/>
                                </svg>
                                <span>Click to upload or drag image here</span>
                            </div>
                            <div class="preview-area" style="display: none;">
                                <img id="imagePreview" src="#" alt="Preview">
                                <button type="button" class="remove-image">×</button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="g-recaptcha" 
                             data-sitekey="{{ config('services.recaptcha.site_key') }}"
                             data-theme="dark">
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">Complete Purchase</button>
                </form>
            </div>
        </main>

        <footer class="footer">
            <span>Copyright © 2023-2025 UnicornMC. All Rights Reserved.</span>
        </footer>

        <!-- QR Modal -->
        <div id="qrModal" class="qr-modal">
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <div class="modal-header">
                    <h3>KHQR Payment</h3>
                    <p><span class="modal-label">Name:</span> Chan Sreyvy</p>
                    <p class="modal-phone">Tel: 087855814</p>
                </div>
                <img 
                    src="/images/jjqr.jpg" 
                    alt="KHQR Payment" 
                    class="modal-qr-image"
                    style="min-width: 300px; min-height: 300px;"
                >
            </div>
        </div>

        @vite(['resources/js/order.js'])
    </body>
</html> 