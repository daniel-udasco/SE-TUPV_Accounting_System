<?php
require_once 'php/includes/auth.php';
require_once 'php/db.php';
require_once 'php/includes/header.php';

$isEligible = $_SESSION['user']['is_eligible_summer_class'] == 1;
$subjects = [];

if ($isEligible) {
    try {
        $stmt = $pdo->query("SELECT * FROM summer_subjects ORDER BY subject_code ASC");
        $subjects = $stmt->fetchAll();
    } catch (\PDOException $e) {
        // Error fetching summer subjects.
    }
}
?>

<div class="page-header">
    <div>
        <span class="eyebrow"><i class="ph ph-sun-horizon"></i> Summer term</span>
        <h1>Summer Class Application</h1>
        <p class="text-muted">Select available subjects and review your assessment before payment.</p>
    </div>
    <a href="transactions.php?type=summer_class" class="btn btn-outline"><i class="ph ph-receipt"></i> Summer Class History</a>
</div>

<?php if ($isEligible): ?>
    <div class="eligibility-check approved">
        <i class="ph-fill ph-check-circle" style="font-size: 1.5rem;"></i>
        <div>
            <h4>Eligibility Status: Approved</h4>
            <p>You are eligible to enroll in summer classes. Choose one or more open subjects below.</p>
        </div>
    </div>

    <div class="form-shell">
        <div class="subject-table-wrapper">
            <table class="compact-table">
                <thead>
                    <tr>
                        <th style="width: 64px;">Select</th>
                        <th>Code</th>
                        <th>Descriptive Title</th>
                        <th>Fee</th>
                        <th>Slots</th>
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
                        <td style="font-weight: 800; color: <?php echo $isFull ? 'var(--ink-500)' : 'var(--tup-maroon)'; ?>;"><?php echo htmlspecialchars($subj['subject_code']); ?></td>
                        <td><?php echo htmlspecialchars($subj['subject_title']); ?></td>
                        <td>&#8369; <?php echo number_format($subj['fee'], 2); ?></td>
                        <td>
                            <?php if($isFull): ?>
                                <span class="slots-badge full">Full</span>
                            <?php else: ?>
                                <span class="slots-badge available"><?php echo $subj['available_slots']; ?> / <?php echo $subj['total_slots']; ?> open</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <aside class="summary-box">
            <span class="eyebrow"><i class="ph ph-clipboard-text"></i> Assessment</span>
            <div class="summary-row"><span>Selected subjects</span> <span id="subjectCount">0</span></div>
            <div class="summary-total"><span>Total</span> <span id="totalAssessment">&#8369; 0.00</span></div>
            <button class="btn btn-primary" id="btnEnroll" disabled onclick="showCheckoutModal()" style="width: 100%; margin-top: 1rem;" type="button">
                Proceed to Payment
            </button>
        </aside>
    </div>

    <div class="checkout-modal" id="checkoutModal">
        <div class="checkout-content">
            <h2>Confirm Enrollment Payment</h2>
            <p class="text-muted">You are about to process <strong id="modalTotalAmount"></strong> for summer classes via GCash.</p>
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

        function calculateTotal() {
            let total = 0;
            let selected = 0;
            document.querySelectorAll('.subject-cb:checked').forEach(cb => {
                total += parseFloat(cb.value);
                selected += 1;
                cb.closest('tr').classList.add('selected');
            });
            document.querySelectorAll('.subject-cb:not(:checked)').forEach(cb => {
                cb.closest('tr').classList.remove('selected');
            });

            currentTotal = total;
            document.getElementById('subjectCount').innerText = selected;
            document.getElementById('totalAssessment').innerText = '\u20B1 ' + total.toFixed(2);
            document.getElementById('modalTotalAmount').innerText = '\u20B1 ' + total.toFixed(2);
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
        <i class="ph-fill ph-warning-circle" style="font-size: 1.5rem;"></i>
        <div>
            <h4>Eligibility Status: Not Eligible</h4>
            <p>You are currently not eligible to apply for summer classes. Please contact the registrar for more information.</p>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'php/includes/footer.php'; ?>
