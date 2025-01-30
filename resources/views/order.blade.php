<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>Complete Order - UnicornMC</title>
        <meta name="description" content="Complete your purchase on UnicornMC store.">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
        @vite(['resources/css/styles.css', 'resources/css/store.css', 'resources/css/order.css'])
    </head>
    <body>
        <a href="/store" class="back-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back to Store
        </a>

        <main class="order-container">
            <h1 class="order-title">Complete Your Order</h1>
            
            <div class="order-form-container">
                @if (session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif
                <form id="orderForm" class="order-form" method="POST" action="{{ route('order.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="rank" value="{{ $rank }}" readonly>
                    <input type="hidden" name="price" value="{{ $price }}" readonly>

                    <div class="form-group">
                        <label for="username">Minecraft Username</label>
                        <input type="text" id="username" name="username" required 
                               placeholder="Enter your Minecraft username">
                    </div>

                    <div class="form-group">
                        <label>Payment Proof</label>
                        <div class="payment-info">
                            <img src="/images/jjqr.jpg" alt="Payment QR Code" class="payment-qr">
                            <div class="payment-details">
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

                    <button type="submit" class="submit-btn">Complete Purchase</button>
                </form>
            </div>
        </main>

        <footer class="footer">
            <span>Copyright © 2023-2025 UnicornMC. All Rights Reserved.</span>
        </footer>

        @vite(['resources/js/order.js'])
    </body>
</html> 