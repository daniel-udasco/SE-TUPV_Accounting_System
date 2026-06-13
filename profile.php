<?php
require_once 'php/includes/auth.php';
require_once 'php/db.php';
require_once 'php/includes/header.php';

// Fetch latest details
$userId = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
// Format Year Level descriptive string
$yearFormatted = '';
switch ((int)$user['year_level']) {
    case 1: $yearFormatted = '1st Year'; break;
    case 2: $yearFormatted = '2nd Year'; break;
    case 3: $yearFormatted = '3rd Year'; break;
    case 4: $yearFormatted = '4th Year'; break;
    default: $yearFormatted = "Master's Year"; break;
}
?>

<div class="page-header">
    <div>
        <span class="eyebrow"><i class="ph ph-user"></i> Student Account</span>
        <h1>My Profile</h1>
        <p class="text-muted">Review your official enrollment information and portal statuses below.</p>
    </div>
</div>

<div class="form-shell" style="grid-template-columns: minmax(0, 1.2fr) 350px;">
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Student Details Panel -->
        <div class="panel">
            <div class="panel-header">
                <h3><i class="ph ph-student"></i> Registration Information</h3>
            </div>
            <div class="panel-body" style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                    <div>
                        <span class="text-muted" style="font-size: 0.8rem; display: block;">Full Name</span>
                        <strong style="font-size: 1rem;"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>
                    </div>
                    <div>
                        <span class="text-muted" style="font-size: 0.8rem; display: block;">Student ID</span>
                        <strong style="font-size: 1rem;" class="mono"><?php echo htmlspecialchars($user['student_id']); ?></strong>
                    </div>
                    <div>
                        <span class="text-muted" style="font-size: 0.8rem; display: block;">Course</span>
                        <strong style="font-size: 1rem;"><?php echo htmlspecialchars($user['course']); ?></strong>
                    </div>
                    <div>
                        <span class="text-muted" style="font-size: 0.8rem; display: block;">Year Level</span>
                        <strong style="font-size: 1rem;"><?php echo htmlspecialchars($yearFormatted); ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- System & Accounting Statuses Panel -->
        <div class="panel">
            <div class="panel-header">
                <h3><i class="ph ph-shield-check"></i> Accounting & Eligibility Statuses</h3>
            </div>
            <div class="panel-body" style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0;">
                    <div>
                        <strong style="display: block;">CHED Scholarship Program</strong>
                        <span class="text-muted" style="font-size: 0.8rem;">Grants access to CHED scholar payroll schedules and checklists.</span>
                    </div>
                    <div>
                        <?php if($user['is_ched_scholar'] == 1): ?>
                            <span class="slots-badge available" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">Active Scholar</span>
                        <?php else: ?>
                            <span class="slots-badge full" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">Regular Student</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <hr style="border: 0; border-top: 1px solid var(--line);">

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0;">
                    <div>
                        <strong style="display: block;">Summer Term Application</strong>
                        <span class="text-muted" style="font-size: 0.8rem;">Determines eligibility to pay and enroll in summer bridging courses.</span>
                    </div>
                    <div>
                        <?php if($user['is_eligible_summer_class'] == 1): ?>
                            <span class="slots-badge available" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">Eligible</span>
                        <?php else: ?>
                            <span class="slots-badge full" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">Not Eligible</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <aside>
        <!-- Account Metadata -->
        <div class="panel">
            <div class="panel-header">
                <h3><i class="ph ph-fingerprint"></i> Portal Account</h3>
            </div>
            <div class="panel-body" style="display: flex; flex-direction: column; gap: 0.85rem;">
                <div>
                    <span class="text-muted" style="font-size: 0.78rem; display: block;">Account Registered</span>
                    <strong style="font-size: 0.88rem;"><?php echo date('F d, Y', strtotime($user['created_at'])); ?></strong>
                </div>
                <div>
                    <span class="text-muted" style="font-size: 0.78rem; display: block;">Portal Role</span>
                    <strong style="font-size: 0.88rem;">Student Access</strong>
                </div>
                <hr style="border: 0; border-top: 1px solid var(--line);">
                <a href="https://ers.tup.edu.ph/aims/students/forgot.php" target="_blank" class="btn btn-primary" style="width: 100%; margin-bottom: 0.5rem;"><i class="ph ph-key"></i> Change Password</a>
                <a href="logout.php" class="btn btn-outline" style="width: 100%;"><i class="ph ph-sign-out"></i> Sign Out of Account</a>
            </div>
        </div>
    </aside>
</div>

<?php
require_once 'php/includes/footer.php';
?>
