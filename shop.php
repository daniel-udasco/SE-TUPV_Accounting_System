<?php
require_once 'php/includes/auth.php';
require_once 'php/db.php';
require_once 'php/includes/header.php';

// Fetch Shop Products
$products = [];
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id ASC");
    $products = $stmt->fetchAll();
} catch (\PDOException $e) {
    // Error fetching
}
?>
<style>
    .shop-header { text-align: center; margin-bottom: 3rem; }
    .shop-header h1 { font-size: 2.5rem; margin-bottom: 1rem; color: var(--text-main); }
    
    .merch-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem; margin-bottom: 3rem; }
    
    .merch-card { background: var(--bg-card); border-radius: 0; box-shadow: var(--shadow-sm); overflow: hidden; border: 1px solid #e9ecef; transition: transform 0.3s ease, box-shadow 0.3s ease; display: flex; flex-direction: column; }
    .merch-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-hover); }
    
    .merch-img-container { width: 100%; aspect-ratio: 1; background-color: #f1f3f5; display: flex; align-items: center; justify-content: center; position: relative; }
    .merch-img-container span { color: #adb5bd; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; }
    
    .merch-details { padding: 1.5rem; text-align: center; flex: 1; display: flex; flex-direction: column; }
    .merch-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-main); }
    .merch-price { color: var(--tup-maroon); font-weight: 600; font-size: 1.15rem; margin-bottom: 1.5rem; }
    
    .qty-control { display: flex; align-items: center; justify-content: center; gap: 1rem; margin-bottom: 1.5rem; margin-top: auto; }
    .qty-btn { background: var(--bg-color); border: 1px solid #dee2e6; width: 35px; height: 35px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; color: var(--text-main); }
    .qty-btn:hover { background: #e9ecef; border-color: #ced4da; }
    .qty-input { width: 50px; text-align: center; border: 1px solid #dee2e6; border-radius: 4px; padding: 0.4rem; font-family: var(--font-body); font-weight: 600; background: var(--bg-card); color: var(--text-main); }

    .cart-summary { background: var(--bg-card); padding: 2rem; border-radius: 0; border: 1px solid var(--tup-maroon); position: sticky; bottom: 2rem; box-shadow: 0 10px 30px rgba(128,0,0,0.15); display: flex; justify-content: space-between; align-items: center; z-index: 100; margin-bottom: 2rem; }
    .cart-total-label { display: block; color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.25rem; }
    .cart-total-value { font-size: 2rem; font-weight: 700; color: var(--tup-maroon); line-height: 1; }
    
    .btn-checkout { padding: 1rem 3rem; font-size: 1.1rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; border-radius: 0; }
    
    /* Simulate a modal for checkout flow */
    .checkout-modal { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; }
    .checkout-content { background: var(--bg-card); padding: 3rem; max-width: 500px; width: 90%; text-align: center; border-top: 5px solid var(--tup-maroon); }
</style>

<div class="shop-header">
    <h1>TUPV Official Merch Shop</h1>
    <p class="text-muted">High-quality university apparel and materials. Secure your items online and pick them up at the Accounting Office.</p>
</div>

<form id="shopForm">
    <div class="merch-grid">
        <?php foreach($products as $product): ?>
        <div class="merch-card" data-price="<?php echo $product['price']; ?>" data-id="<?php echo $product['id']; ?>">
            <div class="merch-img-container">
                <span>[<?php echo htmlspecialchars($product['name']); ?> Img]</span>
            </div>
            <div class="merch-details">
                <div class="merch-title"><?php echo htmlspecialchars($product['name']); ?></div>
                <div class="merch-price">₱ <?php echo number_format($product['price'], 2); ?></div>
                
                <div class="qty-control">
                    <button type="button" class="qty-btn" onclick="updateQty(this, -1)"><i class="ph ph-minus"></i></button>
                    <input type="number" class="qty-input" value="0" min="0" readonly>
                    <button type="button" class="qty-btn" onclick="updateQty(this, 1)"><i class="ph ph-plus"></i></button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="cart-summary">
        <div>
            <span class="cart-total-label">Order Total</span>
            <div class="cart-total-value" id="cartTotal">₱ 0.00</div>
        </div>
        <button type="button" class="btn btn-primary btn-checkout" id="btnCheckout" disabled onclick="showCheckoutModal()">Review & Pay</button>
    </div>
</form>

<!-- Simulation Checkout Modal -->
<div class="checkout-modal" id="checkoutModal">
    <div class="checkout-content">
        <h2 style="margin-bottom: 1rem; color: var(--text-main);">Confirm Your Order</h2>
        <p class="text-muted" style="margin-bottom: 2rem;">You are about to place an order for university materials. Please proceed to payment.</p>
        
        <button type="button" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem; padding: 1rem; border-radius: 0; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; background-color: var(--gcash-blue); border: none;" onclick="processSimulation()">
            <i class="ph ph-device-mobile"></i> Pay with GCash Simulation
        </button>
        <button type="button" class="btn btn-outline" style="width: 100%; padding: 1rem; border-radius: 0; font-size: 1.1rem;" onclick="closeCheckoutModal()">Cancel</button>
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
        document.getElementById('cartTotal').innerText = '₱ ' + total.toFixed(2);
        document.getElementById('btnCheckout').disabled = total === 0;
    }

    function showCheckoutModal() {
        document.getElementById('checkoutModal').style.display = 'flex';
    }

    function closeCheckoutModal() {
        document.getElementById('checkoutModal').style.display = 'none';
    }

    async function processSimulation() {
        // Here we simulate calling an API like PayMongo, but we just call our own api.php
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
