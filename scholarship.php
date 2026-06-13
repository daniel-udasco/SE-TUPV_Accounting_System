<?php
require_once 'php/includes/auth.php';
require_once 'php/db.php';
require_once 'php/includes/header.php';

// Fetch scholar status fresh from database
$userId = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT is_ched_scholar FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
$isScholar = $user && $user['is_ched_scholar'] == 1;
?>

<div class="page-header">
    <div>
        <span class="eyebrow"><i class="ph ph-graduation-cap"></i> Grants & Assistance</span>
        <h1>CHED Scholarship Program</h1>
        <p class="text-muted">Review requirements and official payroll disbursement schedules for scholars.</p>
    </div>
</div>

<?php if ($isScholar): ?>
    <div class="eligibility-check approved" style="margin-bottom: 2rem;">
        <i class="ph-fill ph-check-circle" style="font-size: 1.8rem;"></i>
        <div>
            <h3 style="color: inherit; margin-bottom: 0.25rem;">Welcome, CHED Scholar!</h3>
            <p style="color: inherit; opacity: 0.9; margin: 0;">You are currently recognized as an active grantee of the CHED Scholarship Program for the current term.</p>
        </div>
    </div>

    <div class="panel" style="margin-bottom: 2rem;">
        <div class="panel-header">
            <h3><i class="ph ph-list-numbers"></i> Submission Requirements</h3>
        </div>
        <div class="panel-body">
            <p class="text-muted" style="margin-bottom: 1rem;">To claim your stipend, please submit physical copies of the following documents to the Accounting Office:</p>
            <ul style="list-style: none; padding: 0; display: flex; flex-direction: column; gap: 0.75rem;">
                <li style="display: flex; gap: 0.5rem; align-items: flex-start; font-size: 0.92rem;">
                    <i class="ph ph-check-square" style="color: var(--tup-maroon); font-size: 1.25rem; margin-top: 0.1rem;"></i>
                    <span><strong>a. Photocopy of School ID</strong> (or any VALID ID duly signed)</span>
                </li>
                <li style="display: flex; gap: 0.5rem; align-items: flex-start; font-size: 0.92rem;">
                    <i class="ph ph-check-square" style="color: var(--tup-maroon); font-size: 1.25rem; margin-top: 0.1rem;"></i>
                    <span><strong>b. Notarized Special Power of Attorney (SPA next of kin)</strong></span>
                </li>
                <li style="display: flex; gap: 0.5rem; align-items: flex-start; font-size: 0.92rem;">
                    <i class="ph ph-check-square" style="color: var(--tup-maroon); font-size: 1.25rem; margin-top: 0.1rem;"></i>
                    <span><strong>c. Valid ID of representative</strong> (present original and submit photocopy of ID)</span>
                </li>
                <li style="display: flex; gap: 0.5rem; align-items: flex-start; font-size: 0.92rem;">
                    <i class="ph ph-check-square" style="color: var(--tup-maroon); font-size: 1.25rem; margin-top: 0.1rem;"></i>
                    <span><strong>d. TUPV ID (or any VALID ID) of the grantee</strong> (photocopy only)</span>
                </li>
            </ul>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h3><i class="ph ph-calendar"></i> Important Dates</h3>
        </div>
        <div class="panel-body">
            <div class="schedule-row" style="font-size: 1rem; padding: 0.5rem 0;">
                <span><strong>Stipend Payroll Release Date</strong></span>
                <strong style="color: var(--tup-maroon); font-size: 1.1rem;"><i class="ph ph-calendar-blank"></i> December 5, 2026</strong>
            </div>
            <p class="text-muted" style="margin-top: 0.75rem; font-size: 0.85rem;">Stipends will be distributed via Landbank cash cards or direct over-the-counter payroll at the business office on the scheduled date. Make sure to bring your requirements.</p>
        </div>
    </div>

<?php else: ?>
    <div class="eligibility-check denied" style="margin-top: 2rem;">
        <i class="ph-fill ph-warning-circle" style="font-size: 1.8rem;"></i>
        <div>
            <h3 style="color: inherit; margin-bottom: 0.25rem;">Access Denied</h3>
            <p style="color: inherit; opacity: 0.9; margin: 0;">You are not currently enrolled in the CHED Scholarship Program. If you believe this is an error, please coordinate with the Scholarship Unit or Registrar.</p>
        </div>
    </div>
<?php endif; ?>

<?php
require_once 'php/includes/footer.php';
?>
