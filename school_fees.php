<?php
require_once 'php/includes/auth.php';
require_once 'php/includes/header.php';
?>
<style>
    .page-header { margin-bottom: 2rem; }
    
    .form-section { margin-bottom: 2rem; }
    .form-section h3 { font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid #e9ecef; padding-bottom: 0.5rem; color: var(--text-main); }

    .summary-box { background-color: var(--bg-card); border: 1px solid var(--tup-maroon); border-radius: 0; padding: 1.5rem; margin-top: 2rem; box-shadow: var(--shadow-sm); }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--text-muted); }
    .summary-total { display: flex; justify-content: space-between; margin-top: 1rem; padding-top: 1rem; border-top: 2px solid #e9ecef; font-weight: 700; font-size: 1.5rem; color: var(--tup-maroon); }

    .payment-methods { display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap; }
    .payment-method { background: var(--bg-card); border: 2px solid #e9ecef; border-radius: 0; padding: 1.5rem 1rem; flex: 1; min-width: 150px; text-align: center; cursor: pointer; transition: all 0.2s ease; color: var(--text-main); }
    .payment-method:hover { border-color: var(--tup-gray); }
    .payment-method.selected { border-color: var(--gcash-blue); background-color: rgba(0, 123, 254, 0.05); font-weight: 600; color: var(--gcash-blue); }

    .checkout-modal { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; }
    .checkout-content { background: var(--bg-card); padding: 3rem; max-width: 500px; width: 90%; text-align: center; border-top: 5px solid var(--tup-maroon); }
</style>

<div class="page-header">
    <h1>Pay School Fees</h1>
    <p class="text-muted">Select the specific fee you need to settle. Note: As a state-funded university, standard tuition is not applicable.</p>
</div>

<div class="card" style="border-radius: 0;">
    <form id="paymentForm">
        <div class="form-section">
            <h3>1. Select Fee Type</h3>
            <div class="form-group">
                <select id="feeType" class="form-control" required style="border-radius: 0;">
                    <option value="" disabled selected>Select a fee to pay...</option>
                    <option value="establish_id">Establish ID</option>
                    <option value="id_replacement">ID Replacement</option>
                    <option value="graduation_fee">Graduation Fee</option>
                    <option value="transcript">Transcript of Records</option>
                    <option value="other">[Placeholder] Other Approved Fees</option>
                </select>
            </div>
        </div>

        <div class="form-section">
            <h3>2. Amount</h3>
            <div class="form-group">
                <label class="form-label">Amount to Pay (PHP)</label>
                <input type="number" id="amount" class="form-control" placeholder="e.g. 150" min="50" required style="border-radius: 0;">
            </div>
        </div>

        <div class="form-section">
            <h3>3. Select Payment Method</h3>
            <div class="payment-methods">
                <div class="payment-method selected" id="method-gcash" onclick="selectMethod('gcash')">
                    <i class="ph ph-device-mobile" style="font-size: 2.5rem; margin-bottom: 0.5rem; color: var(--gcash-blue);"></i><br>
                    GCash
                </div>
                <div class="payment-method" id="method-bank" onclick="selectMethod('bank')">
                    <i class="ph ph-bank" style="font-size: 2.5rem; margin-bottom: 0.5rem;"></i><br>
                    Bank Transfer
                </div>
                <div class="payment-method" id="method-otc" onclick="selectMethod('otc')">
                    <i class="ph ph-storefront" style="font-size: 2.5rem; margin-bottom: 0.5rem;"></i><br>
                    Over-the-Counter
                </div>
            </div>
            <input type="hidden" id="selectedMethod" value="gcash">
        </div>

        <div class="summary-box">
            <div class="summary-row"><span>Subtotal:</span> <span id="summarySubtotal">₱ 0.00</span></div>
            <div class="summary-row"><span>Convenience Fee:</span> <span>₱ 15.00</span></div>
            <div class="summary-total"><span>Total to Pay:</span> <span id="summaryTotal">₱ 15.00</span></div>
        </div>

        <div style="margin-top: 2rem; text-align: right;">
            <button type="button" class="btn btn-primary" id="btnProceed" style="font-size: 1.1rem; padding: 1rem 3rem; border-radius: 0; text-transform: uppercase;" disabled onclick="showCheckoutModal()">Proceed to Payment <i class="ph ph-arrow-right"></i></button>
        </div>
    </form>
</div>

<!-- Simulation Checkout Modal -->
<div class="checkout-modal" id="checkoutModal">
    <div class="checkout-content">
        <h2 style="margin-bottom: 1rem; color: var(--text-main);">Confirm Payment</h2>
        <p class="text-muted" style="margin-bottom: 2rem;">You are about to process a payment of <strong id="modalTotalAmount"></strong> via <strong id="modalMethod">GCash</strong>.</p>
        
        <button type="button" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem; padding: 1rem; border-radius: 0; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; background-color: var(--tup-maroon); border: none;" onclick="processSimulation()">
            <i class="ph ph-check-circle"></i> Confirm & Pay Simulation
        </button>
        <button type="button" class="btn btn-outline" style="width: 100%; padding: 1rem; border-radius: 0; font-size: 1.1rem;" onclick="closeCheckoutModal()">Cancel</button>
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
        
        document.getElementById('summarySubtotal').innerText = '₱ ' + amount.toFixed(2);
        document.getElementById('summaryTotal').innerText = '₱ ' + total.toFixed(2);
        document.getElementById('modalTotalAmount').innerText = '₱ ' + total.toFixed(2);
        
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
