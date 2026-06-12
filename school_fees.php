<?php
require_once 'php/includes/auth.php';
require_once 'php/includes/header.php';
?>

<div class="page-header">
    <div>
        <span class="eyebrow"><i class="ph ph-credit-card"></i> Fee checkout</span>
        <h1>Pay School Fees</h1>
        <p class="text-muted">Select an approved fee, enter the assessed amount, and choose how you want to pay.</p>
    </div>
    <a href="transactions.php?type=fees" class="btn btn-outline"><i class="ph ph-clock-counter-clockwise"></i> School Fees History</a>
</div>

<form id="paymentForm" class="form-shell">
    <div class="panel">
        <div class="form-section">
            <h3><i class="ph ph-list-checks"></i> Fee Type</h3>
            <div class="form-group">
                <label for="feeType" class="form-label">Approved payment category</label>
                <select id="feeType" class="form-control" required>
                    <option value="" disabled selected>Select a fee to pay...</option>
                    <option value="establish_id">Establish ID</option>
                    <option value="id_replacement">ID Replacement</option>
                    <option value="graduation_fee">Graduation Fee</option>
                    <option value="transcript">Transcript of Records</option>
                    <option value="other">Other Approved Fees</option>
                </select>
            </div>
        </div>

        <div class="form-section">
            <h3><i class="ph ph-currency-circle-dollar"></i> Amount</h3>
            <div class="form-group">
                <label for="amount" class="form-label">Amount to pay (PHP)</label>
                <input type="number" id="amount" class="form-control" placeholder="e.g. 150" min="50" required>
            </div>
        </div>

        <div class="form-section">
            <h3><i class="ph ph-contactless-payment"></i> Payment Method</h3>
            <div class="payment-methods">
                <button type="button" class="payment-method selected" id="method-gcash" onclick="selectMethod('gcash')">
                    <i class="ph ph-device-mobile"></i>
                    <span>GCash</span>
                </button>
                <button type="button" class="payment-method" id="method-bank" onclick="selectMethod('bank')">
                    <i class="ph ph-bank"></i>
                    <span>Bank Transfer</span>
                </button>
                <button type="button" class="payment-method" id="method-otc" onclick="selectMethod('otc')">
                    <i class="ph ph-storefront"></i>
                    <span>Over-the-Counter</span>
                </button>
            </div>
            <input type="hidden" id="selectedMethod" value="gcash">
        </div>
    </div>

    <aside class="summary-box">
        <span class="eyebrow"><i class="ph ph-receipt"></i> Summary</span>
        <div class="summary-row"><span>Subtotal</span> <span id="summarySubtotal">&#8369; 0.00</span></div>
        <div class="summary-row"><span>Convenience Fee</span> <span>&#8369; 15.00</span></div>
        <div class="summary-total"><span>Total</span> <span id="summaryTotal">&#8369; 15.00</span></div>
        <button type="button" class="btn btn-primary" id="btnProceed" style="width: 100%; margin-top: 1rem;" disabled onclick="showCheckoutModal()">
            Proceed to Payment <i class="ph ph-arrow-right"></i>
        </button>
    </aside>
</form>

<div class="checkout-modal" id="checkoutModal">
    <div class="checkout-content">
        <h2>Confirm Payment</h2>
        <p class="text-muted">You are about to process <strong id="modalTotalAmount"></strong> via <strong id="modalMethod">GCash</strong>.</p>
        <div class="checkout-actions">
            <button type="button" class="btn btn-primary" onclick="processSimulation()">
                <i class="ph ph-check-circle"></i> Confirm and Pay Simulation
            </button>
            <button type="button" class="btn btn-outline" onclick="closeCheckoutModal()">Cancel</button>
        </div>
    </div>
</div>

<script>
    let currentTotal = 0;

    function selectMethod(method) {
        document.querySelectorAll('.payment-method').forEach(el => el.classList.remove('selected'));
        document.getElementById('method-' + method).classList.add('selected');
        document.getElementById('selectedMethod').value = method;

        let methodText = 'GCash';
        if (method === 'bank') methodText = 'Bank Transfer';
        if (method === 'otc') methodText = 'Over-the-Counter';
        document.getElementById('modalMethod').innerText = methodText;
    }

    document.getElementById('amount').addEventListener('input', function(e) {
        let amount = parseFloat(e.target.value) || 0;
        let total = amount > 0 ? amount + 15 : 0;
        currentTotal = total;

        document.getElementById('summarySubtotal').innerText = '\u20B1 ' + amount.toFixed(2);
        document.getElementById('summaryTotal').innerText = '\u20B1 ' + total.toFixed(2);
        document.getElementById('modalTotalAmount').innerText = '\u20B1 ' + total.toFixed(2);

        document.getElementById('btnProceed').disabled = amount <= 0;
    });

    function showCheckoutModal() {
        document.getElementById('checkoutModal').style.display = 'flex';
    }

    function closeCheckoutModal() {
        document.getElementById('checkoutModal').style.display = 'none';
    }

    async function processSimulation() {
        const feeType = document.getElementById('feeType').options[document.getElementById('feeType').selectedIndex].text;
        const method = document.getElementById('selectedMethod').value;

        try {
            const response = await fetch('php/api.php?action=create_transaction', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    user_id: <?php echo $_SESSION['user']['id']; ?>,
                    description: feeType,
                    type: 'fee',
                    amount: currentTotal,
                    method: method
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
