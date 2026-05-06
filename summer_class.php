<?php
require_once 'php/includes/auth.php';
require_once 'php/db.php';
require_once 'php/includes/header.php';

$isEligible = $_SESSION['user']['is_eligible_summer_class'] == 1;

// Fetch Summer Subjects
$subjects = [];
if ($isEligible) {
    try {
        $stmt = $pdo->query("SELECT * FROM summer_subjects ORDER BY subject_code ASC");
        $subjects = $stmt->fetchAll();
    } catch (\PDOException $e) {
        // Error fetching
    }
}
?>
<style>
    .page-header { margin-bottom: 2rem; }
    
    .eligibility-check { padding: 1.5rem; border-radius: 0; margin-bottom: 2rem; display: flex; align-items: flex-start; gap: 1rem; border: 1px solid transparent; }
    .eligibility-check.approved { background-color: #e8f4fd; border-color: #b6d4fe; color: #084298; }
    .eligibility-check.denied { background-color: #f8d7da; border-color: #f5c2c7; color: #842029; }
    .eligibility-check i { font-size: 2rem; }
    
    .subject-table-wrapper { background: var(--bg-card); border: 1px solid #e9ecef; border-radius: 0; overflow: hidden; margin-bottom: 2rem; }
    table.compact-table { width: 100%; border-collapse: collapse; }
    table.compact-table th, table.compact-table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #e9ecef; }
    table.compact-table th { background-color: #f8f9fa; font-size: 0.85rem; text-transform: uppercase; color: var(--text-muted); }
    table.compact-table tr.selected { background-color: rgba(128, 0, 0, 0.05); }
    
    .slots-badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; display: inline-block; }
    .slots-badge.available { background: #d1e7dd; color: #0f5132; }
    .slots-badge.full { background: #f8d7da; color: #842029; }

    .summary-box { background-color: var(--bg-card); border: 1px solid var(--tup-maroon); padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; }
    
    .checkout-modal { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; }
    .checkout-content { background: var(--bg-card); padding: 3rem; max-width: 500px; width: 90%; text-align: center; border-top: 5px solid var(--tup-maroon); }
</style>

<div class="page-header">
    <h1>Summer Class Application</h1>
    <p class="text-muted">Apply and enroll in subjects available for the upcoming summer term.</p>
</div>

<?php if ($isEligible): ?>
    <div class="eligibility-check approved">
        <i class="ph-fill ph-check-circle" style="color: #0d6efd;"></i>
        <div>
            <h4 style="margin-bottom: 0.25rem;">Eligibility Status: Approved</h4>
            <p style="font-size: 0.9rem; margin: 0;">You are eligible to enroll in summer classes. Please select your subjects below.</p>
        </div>
    </div>

    <div class="subject-table-wrapper">
        <table class="compact-table">
            <thead>
                <tr>
                    <th style="width: 50px;">Select</th>
                    <th>Code</th>
                    <th>Descriptive Title</th>
                    <th>Fee</th>
                    <th>Slots Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($subjects as $subj): 
                    $isFull = $subj['available_slots'] <= 0;
                ?>
                <tr class="<?php echo $isFull ? 'text-muted' : ''; ?>">
                    <td>
                        <input type="checkbox" class="subject-cb" value="<?php echo $subj['fee']; ?>" data-id="<?php echo $subj['id']; ?>" <?php echo $isFull ? 'disabled' : ''; ?> onchange="calculateTotal()">
                    </td>
                    <td style="font-weight: 600; color: <?php echo $isFull ? '#adb5bd' : 'var(--tup-maroon)'; ?>;"><?php echo htmlspecialchars($subj['subject_code']); ?></td>
                    <td><?php echo htmlspecialchars($subj['subject_title']); ?></td>
                    <td>₱ <?php echo number_format($subj['fee'], 2); ?></td>
                    <td>
                        <?php if($isFull): ?>
                            <span class="slots-badge full">Full</span>
                        <?php else: ?>
                            <span class="slots-badge available"><?php echo $subj['available_slots']; ?> / <?php echo $subj['total_slots']; ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="summary-box">
        <div>
            <span style="display: block; font-size: 0.9rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">Total Assessment</span>
            <strong style="font-size: 2rem; color: var(--tup-maroon); line-height: 1;" id="totalAssessment">₱ 0.00</strong>
        </div>
        <button class="btn btn-primary" id="btnEnroll" disabled onclick="showCheckoutModal()" style="padding: 1rem 3rem; font-size: 1.1rem; border-radius: 0; text-transform: uppercase;">Proceed to Payment</button>
    </div>

    <!-- Simulation Checkout Modal -->
    <div class="checkout-modal" id="checkoutModal">
        <div class="checkout-content">
            <h2 style="margin-bottom: 1rem; color: var(--text-main);">Confirm Enrollment Payment</h2>
            <p class="text-muted" style="margin-bottom: 2rem;">You are about to process a payment of <strong id="modalTotalAmount"></strong> for Summer Classes via GCash.</p>
            
            <button type="button" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem; padding: 1rem; border-radius: 0; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; background-color: var(--gcash-blue); border: none;" onclick="processSimulation()">
                <i class="ph ph-device-mobile"></i> Pay with GCash Simulation
            </button>
            <button type="button" class="btn btn-outline" style="width: 100%; padding: 1rem; border-radius: 0; font-size: 1.1rem;" onclick="closeCheckoutModal()">Cancel</button>
        </div>
    </div>

    <script>
        let currentTotal = 0;
        
        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.subject-cb:checked').forEach(cb => {
                total += parseFloat(cb.value);
                cb.closest('tr').classList.add('selected');
            });
            document.querySelectorAll('.subject-cb:not(:checked)').forEach(cb => {
                cb.closest('tr').classList.remove('selected');
            });
            
            currentTotal = total;
            document.getElementById('totalAssessment').innerText = '₱ ' + total.toFixed(2);
            document.getElementById('modalTotalAmount').innerText = '₱ ' + total.toFixed(2);
            document.getElementById('btnEnroll').disabled = total === 0;
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
                        description: 'Summer Class Enrollment',
                        type: 'summer_class',
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

<?php else: ?>
    <div class="eligibility-check denied">
        <i class="ph-fill ph-warning-circle" style="color: #dc3545;"></i>
        <div>
            <h4 style="margin-bottom: 0.25rem;">Eligibility Status: Not Eligible</h4>
            <p style="font-size: 0.9rem; margin: 0;">You are currently not eligible to apply for summer classes. This may be due to missing prerequisites or failing grades. Please contact the registrar for more information.</p>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'php/includes/footer.php'; ?>
