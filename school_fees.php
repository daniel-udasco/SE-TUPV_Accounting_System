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
                    <option value="id_renewal_replacement">ID Renewal/Replacement</option>
                    <option value="graduation_fee">Graduation Fee</option>
                    <option value="masteral_fee">Masteral Fee</option>
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

        <!-- Document Attachment Section (only shown for Graduation, Masteral, and Other Approved Fees) -->
        <div class="form-section" id="attachmentSection" style="display: none;">
            <h3><i class="ph ph-paperclip"></i> Support Attachment</h3>
            <div class="form-group">
                <label for="attachmentInput" class="form-label">Attach clearance / approval document image</label>
                <input type="file" id="attachmentInput" class="form-control" accept="image/*">
                <p class="text-muted" style="font-size: 0.8rem; margin-top: 0.25rem;">Please upload a screenshot or photo of your clearance or office approval before proceeding.</p>
            </div>
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
        <p class="text-muted">You are about to process <strong id="modalTotalAmount"></strong> for school fees.</p>
        <div class="checkout-actions" style="display: flex; flex-direction: column; gap: 0.5rem; width: 100%;">
            <button type="button" class="btn btn-payment" onclick="processSimulation('gcash')">
                <i class="ph ph-device-mobile"></i> Pay with GCash Simulation
            </button>
            <button type="button" class="btn btn-payment" onclick="processSimulation('bank')" style="background-color: var(--tup-gray); color: white;">
                <i class="ph ph-bank"></i> Pay with Bank Transfer Simulation
            </button>
            <button type="button" class="btn btn-outline" onclick="closeCheckoutModal()">Cancel</button>
        </div>
    </div>
</div>

<script>
    let currentTotal = 0;
    const feeTypeSelect = document.getElementById('feeType');
    const amountInput = document.getElementById('amount');
    const attachmentSection = document.getElementById('attachmentSection');
    const attachmentInput = document.getElementById('attachmentInput');
    const btnProceed = document.getElementById('btnProceed');

    function calculateTotal() {
        let amount = parseFloat(amountInput.value) || 0;
        let total = amount > 0 ? amount + 15 : 0;
        currentTotal = total;

        document.getElementById('summarySubtotal').innerText = '\u20B1 ' + amount.toFixed(2);
        document.getElementById('summaryTotal').innerText = '\u20B1 ' + total.toFixed(2);
        document.getElementById('modalTotalAmount').innerText = '\u20B1 ' + total.toFixed(2);
    }

    function validateForm() {
        const amount = parseFloat(amountInput.value) || 0;
        const val = feeTypeSelect.value;
        let valid = amount > 0 && val !== "";

        if (val === 'graduation_fee' || val === 'masteral_fee' || val === 'other') {
            if (!attachmentInput.files || attachmentInput.files.length === 0) {
                valid = false;
            }
        }

        btnProceed.disabled = !valid;
    }

    feeTypeSelect.addEventListener('change', function() {
        const val = this.value;
        if (val === 'establish_id' || val === 'id_renewal_replacement') {
            amountInput.value = 100;
            amountInput.readOnly = true;
        } else {
            amountInput.readOnly = false;
            if (amountInput.value == 100) {
                amountInput.value = '';
            }
        }
        
        if (val === 'graduation_fee' || val === 'masteral_fee' || val === 'other') {
            attachmentSection.style.display = 'block';
            attachmentInput.required = true;
        } else {
            attachmentSection.style.display = 'none';
            attachmentInput.required = false;
            attachmentInput.value = ''; // clear file if switched back
        }
        
        calculateTotal();
        validateForm();
    });

    amountInput.addEventListener('input', function() {
        calculateTotal();
        validateForm();
    });

    attachmentInput.addEventListener('change', function() {
        validateForm();
    });

    function showCheckoutModal() {
        document.getElementById('checkoutModal').style.display = 'flex';
    }

    function closeCheckoutModal() {
        document.getElementById('checkoutModal').style.display = 'none';
    }

    async function processSimulation(method) {
        const feeType = feeTypeSelect.options[feeTypeSelect.selectedIndex].text;

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
