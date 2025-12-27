<?php
// Get game ID from URL parameter
$gameId = $_GET['game_id'] ?? null;

// If no game ID provided, redirect to store
if (!$gameId) {
    header('Location: index.php');
    exit();
}

// Sample game data - will be replaced with API call later
// In production, fetch game details from API using $gameId
$sampleGames = [
    30 => [
        "id" => 30,
        "name" => "Super Smash Bros. Melee",
        "description" => "Super Smash Bros. Melee includes all playable characters from the first game and adds characters from franchises such as Fire Emblem. Its major focus is the multiplayer mode.",
        "price" => 59.99,
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co21yv.jpg",
        "genre" => "Fighting",
        "developer" => "HAL Laboratory"
    ],
    38 => [
        "id" => 38,
        "name" => "Mass Effect 2",
        "description" => "It is time to bring together your greatest allies and recruit the galaxy's fighting elite to continue the resistance against the invading Reapers.",
        "price" => 19.99,
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co20ac.jpg",
        "genre" => "Shooter",
        "developer" => "BioWare"
    ],
    37 => [
        "id" => 37,
        "name" => "Red Dead Redemption 2",
        "description" => "Red Dead Redemption 2 is the epic tale of outlaw Arthur Morgan and the infamous Van der Linde gang, on the run across America at the dawn of the modern age.",
        "price" => 59.99,
        "logo_path" => "https://images.igdb.com/igdb/image/upload/t_cover_big/co1q1f.jpg",
        "genre" => "Shooter",
        "developer" => "Rockstar Games"
    ]
];

// Get game data
$game = $sampleGames[$gameId] ?? null;

// If game not found, redirect to store
if (!$game) {
    header('Location: index.php');
    exit();
}

// Calculate tax and total (10% tax rate)
$subtotal = $game['price'];
$tax = $subtotal * 0.10;
$total = $subtotal + $tax;

$activePage = 'checkout';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo htmlspecialchars($game['name']); ?></title>
    <link rel="stylesheet" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/checkout.css">
</head>
<body>
    <?php include 'components/navbar.php'; ?>

    <!-- Loading state -->
    <div id="loadingState" class="checkout-container">
        <div style="text-align: center; padding: 4rem; color: #888;">
            <i class="fa-solid fa-spinner fa-spin" style="font-size: 3rem; color: #4CAF50;"></i>
            <p style="margin-top: 1rem;">Verifying authentication...</p>
        </div>
    </div>

    <!-- Main content (hidden initially) -->
    <div id="checkoutContent" class="checkout-container" style="display: none;">
        <div class="checkout-header">
            <h1><i class="fa-solid fa-shopping-cart"></i> Checkout</h1>
            <p>Complete your purchase</p>
        </div>

        <div class="checkout-content">
            <div class="checkout-left">
                <!-- Game Details Section -->
                <div class="checkout-section">
                    <h3 class="section-title"><i class="fa-solid fa-gamepad"></i> Game Details</h3>
                    <div class="game-checkout-card">
                        <img src="<?php echo htmlspecialchars($game['logo_path']); ?>" 
                             alt="<?php echo htmlspecialchars($game['name']); ?>"
                             onerror="this.src='https://via.placeholder.com/150x200/2a2a2a/ffffff?text=No+Image'">
                        <div class="game-checkout-info">
                            <h4><?php echo htmlspecialchars($game['name']); ?></h4>
                            <p class="game-genre"><i class="fa-solid fa-tag"></i> <?php echo htmlspecialchars($game['genre']); ?></p>
                            <p class="game-developer"><i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($game['developer']); ?></p>
                            <p class="game-description"><?php echo htmlspecialchars($game['description']); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Section -->
                <div class="checkout-section">
                    <h3 class="section-title"><i class="fa-solid fa-credit-card"></i> Payment Method</h3>
                    <div class="payment-methods">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="credit_card" checked>
                            <div class="payment-option-content">
                                <i class="fa-solid fa-credit-card"></i>
                                <span>Credit Card</span>
                            </div>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="paypal">
                            <div class="payment-option-content">
                                <i class="fa-brands fa-paypal"></i>
                                <span>PayPal</span>
                            </div>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="wallet">
                            <div class="payment-option-content">
                                <i class="fa-solid fa-wallet"></i>
                                <span>Steam Wallet</span>
                            </div>
                        </label>
                    </div>

                    <!-- Credit Card Form (shown by default) -->
                    <div id="creditCardForm" class="payment-form">
                        <div class="form-group">
                            <label for="cardNumber"><i class="fa-solid fa-credit-card"></i> Card Number</label>
                            <input type="text" id="cardNumber" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expiryDate"><i class="fa-solid fa-calendar"></i> Expiry Date</label>
                                    <input type="text" id="expiryDate" class="form-control" placeholder="MM/YY" maxlength="5">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cvv"><i class="fa-solid fa-lock"></i> CVV</label>
                                    <input type="text" id="cvv" class="form-control" placeholder="123" maxlength="4">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cardName"><i class="fa-solid fa-user"></i> Cardholder Name</label>
                            <input type="text" id="cardName" class="form-control" placeholder="John Doe">
                        </div>
                    </div>
                </div>

                <!-- Billing Information Section -->
                <div class="checkout-section">
                    <h3 class="section-title"><i class="fa-solid fa-location-dot"></i> Billing Information</h3>
                    <div class="billing-form">
                        <div class="form-group">
                            <label for="email"><i class="fa-solid fa-envelope"></i> Email</label>
                            <input type="email" id="email" class="form-control" placeholder="your@email.com">
                        </div>
                        <div class="form-group">
                            <label for="country"><i class="fa-solid fa-globe"></i> Country</label>
                            <select id="country" class="form-control">
                                <option value="">Select Country</option>
                                <option value="US">United States</option>
                                <option value="CA">Canada</option>
                                <option value="UK">United Kingdom</option>
                                <option value="AU">Australia</option>
                                <option value="DE">Germany</option>
                                <option value="FR">France</option>
                                <option value="JP">Japan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="zipCode"><i class="fa-solid fa-map-pin"></i> ZIP / Postal Code</label>
                            <input type="text" id="zipCode" class="form-control" placeholder="12345">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="checkout-right">
                <div class="order-summary">
                    <h3 class="summary-title"><i class="fa-solid fa-receipt"></i> Order Summary</h3>
                    
                    <div class="summary-item">
                        <span>Subtotal</span>
                        <span class="summary-price">$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    
                    <div class="summary-item">
                        <span>Tax (10%)</span>
                        <span class="summary-price">$<?php echo number_format($tax, 2); ?></span>
                    </div>
                    
                    <div class="summary-divider"></div>
                    
                    <div class="summary-item summary-total">
                        <span>Total</span>
                        <span class="summary-price">$<?php echo number_format($total, 2); ?></span>
                    </div>
                    
                    <button class="btn btn-purchase" id="completePurchaseBtn">
                        <i class="fa-solid fa-lock"></i> Complete Purchase
                    </button>
                    
                    <a href="index.php" class="btn btn-cancel">
                        <i class="fa-solid fa-arrow-left"></i> Back to Store
                    </a>
                    
                    <div class="secure-checkout">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Secure Checkout</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Check authentication on page load
        async function checkAuthentication() {
            try {
                const response = await fetch('../api/index.php/health', {
                    method: 'GET',
                    credentials: 'include' // Include cookies in the request
                });

                if (!response.ok) {
                    // User not authenticated, redirect to login
                    window.location.href = 'login.php?redirect=checkout&game_id=<?php echo $gameId; ?>';
                    return;
                }

                const data = await response.json();
                
                // Check if user is authenticated from backend
                // You may need to adjust this based on your actual API response
                if (!data.authenticated && !data.user) {
                    window.location.href = 'login.php?redirect=checkout&game_id=<?php echo $gameId; ?>';
                    return;
                }

                // User is authenticated, show checkout content
                document.getElementById('loadingState').style.display = 'none';
                document.getElementById('checkoutContent').style.display = 'block';

                // Populate email if available from user data
                if (data.user && data.user.email) {
                    document.getElementById('email').value = data.user.email;
                }
            } catch (error) {
                console.error('Authentication check failed:', error);
                // On error, redirect to login
                window.location.href = 'login.php?redirect=checkout&game_id=<?php echo $gameId; ?>';
            }
        }

        // Run authentication check immediately
        checkAuthentication();

        // Payment method toggle
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const creditCardForm = document.getElementById('creditCardForm');
                if (this.value === 'credit_card') {
                    creditCardForm.style.display = 'block';
                } else {
                    creditCardForm.style.display = 'none';
                }
            });
        });

        // Card number formatting
        document.getElementById('cardNumber')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });

        // Expiry date formatting
        document.getElementById('expiryDate')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });

        // Complete purchase
        document.getElementById('completePurchaseBtn')?.addEventListener('click', function() {
            // Basic validation
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const email = document.getElementById('email').value;
            const country = document.getElementById('country').value;
            
            if (!email || !country) {
                alert('Please fill in all required fields');
                return;
            }
            
            if (paymentMethod === 'credit_card') {
                const cardNumber = document.getElementById('cardNumber').value;
                const expiryDate = document.getElementById('expiryDate').value;
                const cvv = document.getElementById('cvv').value;
                const cardName = document.getElementById('cardName').value;
                
                if (!cardNumber || !expiryDate || !cvv || !cardName) {
                    alert('Please fill in all card details');
                    return;
                }
            }
            
            // In production, this would make an API call to process the payment
            if (confirm('Complete your purchase for $<?php echo number_format($total, 2); ?>?')) {
                // Simulate successful purchase
                alert('Purchase successful! Game added to your library.');
                window.location.href = 'library.php';
            }
        });
    </script>
</body>
</html>
