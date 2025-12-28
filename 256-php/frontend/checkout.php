<?php
$gameId = $_GET['game_id'] ?? null;

if (!$gameId) {
    header('Location: not-found.php');
    exit();
}

session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_data'])) {
    header('Location: login.php?redirect=checkout');
    exit;
}

$userId = $_SESSION['user_id'];

require_once __DIR__ . '/../api/db.php';

$stmt = $db->prepare("select g.id,g.name,g.description,g.price, g.logo_path, g.genre, u.username as developer from games g, users u where g.developer_id = u.id and g.id=?");
$stmt->execute([$gameId]);
$game = $stmt->fetch(PDO::FETCH_ASSOC) ?? null;

if (!$game) {
    header('Location: not-found.php');
    exit();
}

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

    <div id="loadingState" class="checkout-container">
        <div style="text-align: center; padding: 4rem; color: #888;">
            <i class="fa-solid fa-spinner fa-spin" style="font-size: 3rem; color: #4CAF50;"></i>
            <p style="margin-top: 1rem;">Verifying authentication...</p>
        </div>
    </div>

    <div id="checkoutContent" class="checkout-container" style="display: none;">
        <div class="checkout-header">
            <h1><i class="fa-solid fa-shopping-cart"></i> Checkout</h1>
            <p>Complete your purchase</p>
        </div>

        <div class="checkout-content">
            <div class="checkout-left">
                <div class="checkout-section">
                    <h3 class="section-title"><i class="fa-solid fa-gamepad"></i> Game Details</h3>
                    <div class="game-checkout-card">
                        <img src="<?php echo htmlspecialchars($game['logo_path']); ?>"
                            alt="<?php echo htmlspecialchars($game['name']); ?>">
                        <div class="game-checkout-info">
                            <h4><?php echo htmlspecialchars($game['name']); ?></h4>
                            <p class="game-genre"><i class="fa-solid fa-tag"></i> <?php echo htmlspecialchars($game['genre']); ?></p>
                            <p class="game-developer"><i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($game['developer']); ?></p>
                            <p class="game-description"><?php echo htmlspecialchars($game['description']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="checkout-section">
                    <h3 class="section-title"><i class="fa-solid fa-credit-card"></i> Payment Method</h3>
                    <div class="payment-methods">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="wallet" checked>
                            <div class="payment-option-content">
                                <i class="fa-solid fa-wallet"></i>
                                <span>Steam Wallet</span>
                            </div>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="credit_card" disabled>
                            <div class="payment-option-content">
                                <i class="fa-solid fa-credit-card"></i>
                                <span><b>(On Construction)</b>Credit Card</span>
                            </div>
                        </label>
                    </div>
                </div>

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
        const userId = <?= json_encode($userId) ?>;
        const total = <?= json_encode($total) ?>;
        const gameId = <?= json_encode($game["id"]) ?>;

        async function checkAuthentication() {
            try {
                const response = await fetch('/api/health', {
                    method: 'GET',
                    credentials: 'include'
                });

                if (!response.ok) {
                    window.location.href = 'login.php?redirect=checkout&game_id=<?php echo $gameId; ?>';
                    return;
                }

                const data = await response.json();

                if (!data.success || !data.data || !data.data.user) {
                    window.location.href = 'login.php?redirect=checkout&game_id=<?php echo $gameId; ?>';
                    return;
                }

                document.getElementById('loadingState').style.display = 'none';
                document.getElementById('checkoutContent').style.display = 'block';

                if (data.data.user && data.data.user.email) {
                    document.getElementById('email').value = data.data.user.email;
                }
            } catch (error) {
                console.error('Authentication check failed:', error);
                window.location.href = 'login.php?redirect=checkout&game_id=<?php echo $gameId; ?>';
            }
        }

        checkAuthentication();

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

        document.getElementById('cardNumber')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });

        document.getElementById('expiryDate')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });

        document.getElementById('completePurchaseBtn')?.addEventListener('click', function() {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const email = document.getElementById('email').value;
            const country = document.getElementById('country').value;

            if (!email || !country) {
                alert('Please fill in all required fields');
                return;
            }

            if (confirm('Complete your purchase for $<?php echo number_format($total, 2); ?>?')) {
                window.location.href = `buy.php?game_id=${gameId}&payment=${total}&user_id=${userId}`;
            }
        });
    </script>
</body>

</html>