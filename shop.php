<?php
require_once 'php/includes/auth.php';
require_once 'php/db.php';
require_once 'php/includes/header.php';

$products = [];
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id ASC");
    $products = $stmt->fetchAll();
} catch (\PDOException $e) {
    // Error fetching products.
}
?>

<div class="page-header">
    <div class="shop-header">
        <span class="eyebrow"><i class="ph ph-shopping-bag"></i> Official materials</span>
        <h1>TUPV Merch Shop</h1>
        <p class="text-muted">Reserve uniforms, lanyards, and university materials online, then pick them up at the Accounting Office.</p>
    </div>
    <a href="transactions.php?type=materials" class="btn btn-outline"><i class="ph ph-receipt"></i> Orders History</a>
</div>

<form id="shopForm">
    <div class="merch-grid">
        <?php foreach($products as $product): ?>
        <article class="merch-card" data-price="<?php echo $product['price']; ?>" data-id="<?php echo $product['id']; ?>">
            <div class="product-placeholder">
                <span class="placeholder-badge"><?php echo htmlspecialchars($product['name']); ?> image</span>
            </div>
            <div class="merch-details">
                <div class="merch-title"><?php echo htmlspecialchars($product['name']); ?></div>
                <p class="text-muted" style="font-size: 0.86rem; margin-top: 0.25rem;">Stock available: <?php echo number_format((int)$product['stock_quantity']); ?></p>
                <div class="merch-price">&#8369; <?php echo number_format($product['price'], 2); ?></div>

                <div class="qty-control">
                    <button type="button" class="qty-btn" onclick="updateQty(this, -1)" title="Decrease quantity"><i class="ph ph-minus"></i></button>
                    <input type="number" class="qty-input" value="0" min="0" readonly aria-label="Quantity">
                    <button type="button" class="qty-btn" onclick="updateQty(this, 1)" title="Increase quantity"><i class="ph ph-plus"></i></button>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
    </div>

    <div class="cart-summary">
        <div>
            <span class="cart-total-label">Order Total</span>
            <div class="cart-total-value" id="cartTotal">&#8369; 0.00</div>
        </div>
        <button type="button" class="btn btn-primary" id="btnCheckout" disabled onclick="showCheckoutModal()">
            Review and Pay <i class="ph ph-arrow-right"></i>
        </button>
    </div>
</form>

<div class="checkout-modal" id="checkoutModal">
    <div class="checkout-content">
        <h2>Confirm Your Order</h2>
        <p class="text-muted">You are about to place an order for university materials and process the payment simulation.</p>
        <div class="checkout-actions">
            <button type="button" class="btn btn-payment" onclick="processSimulation()">
                <i class="ph ph-device-mobile"></i> Pay with GCash Simulation
            </button>
            <button type="button" class="btn btn-outline" onclick="closeCheckoutModal()">Cancel</button>
        </div>
    </div>
</div>

<script>
    let currentTotal = 0;

    function updateQty(btn, change) {
        const input = btn.parentElement.querySelector('.qty-input');
        let newVal = parseInt(input.value) + change;
        if(newVal < 0) newVal = 0;
        input.value = newVal;
        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.merch-card').forEach(card => {
            const price = parseFloat(card.getAttribute('data-price'));
            const qty = parseInt(card.querySelector('.qty-input').value);
            total += price * qty;
        });
        currentTotal = total;
        document.getElementById('cartTotal').innerText = '\u20B1 ' + total.toFixed(2);
        document.getElementById('btnCheckout').disabled = total === 0;
    }

    function showCheckoutModal() {
        document.getElementById('checkoutModal').style.display = 'flex';
    }

    function closeCheckoutModal() {
        document.getElementById('checkoutModal').style.display = 'none';
    }

    async function processSimulation() {
        try {
            const response = await fetch('php/api.php?action=create_transaction', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    user_id: <?php echo $_SESSION['user']['id']; ?>,
                    description: 'Materials Shop Purchase',
                    type: 'materials',
                    amount: currentTotal,
                    method: 'gcash'
                })
            });
            const result = await response.json();
            if(result.success) {
                alert("Payment Successful! Reference No: " + result.reference_no);
                window.location.href = 'transactions.php';
            } else {
                alert("Payment failed.");
            }
        } catch (e) {
            alert("Error processing payment simulation.");
        }
    }
</script>

<?php require_once 'php/includes/footer.php'; ?>
