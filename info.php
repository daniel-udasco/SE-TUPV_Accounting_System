<?php
require_once 'php/includes/admin_bootstrap.php';

$officeInfo = [
    'office_name' => 'TUPV Accounting Office',
    'location' => 'Admin Building, Ground Floor, Room 101, TUP Visayas, Capt. Sabi St., Brgy. Zone 12, Talisay City.',
    'operating_hours' => 'Monday to Friday, 8:00 AM to 5:00 PM. Closed during weekends and declared holidays.',
    'services' => 'Fee assessment, payment confirmation, student receipts, summer class payments, and university materials.',
    'email' => 'accountingtupv@gmail.com',
    'phone' => '(034) 445 2177',
    'facebook_url' => 'https://www.facebook.com/tupvbusinessoffice',
    'messenger_url' => 'https://www.messenger.com/tupvbusinessoffice/',
];

try {
    $row = $pdo->query("SELECT * FROM office_info WHERE id = 1")->fetch();
    if ($row) {
        $officeInfo = array_merge($officeInfo, $row);
    }
} catch (\PDOException $e) {
    // Keep defaults visible if the database is unavailable.
}

require_once 'php/includes/header.php';
?>

<section class="dashboard-hero">
    <div class="hero-copy">
        <span class="eyebrow"><i class="ph ph-info"></i> Information Board</span>
        <h1><?php echo htmlspecialchars($officeInfo['office_name']); ?></h1>
        <p>Find office hours, location details, official contact channels, and the current staff directory for student transactions.</p>
        <div class="hero-actions">
            <a href="#contact" class="btn btn-primary"><i class="ph ph-phone-call"></i> Contact Office</a>
            <?php if (!$isLoggedIn): ?>
            <a href="login.php" class="btn btn-outline"><i class="ph ph-sign-in"></i> Student Login</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="hero-visual">
        <img src="assets/info-office-exterior.jpg" alt="Office Exterior" style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
</section>

<section class="info-grid">
    <article class="info-card">
        <i class="ph ph-map-pin"></i>
        <h3>Office Location</h3>
        <p><?php echo htmlspecialchars($officeInfo['location']); ?></p>
    </article>
    <article class="info-card">
        <i class="ph ph-clock"></i>
        <h3>Operating Hours</h3>
        <p><?php echo htmlspecialchars($officeInfo['operating_hours']); ?></p>
    </article>
    <article class="info-card">
        <i class="ph ph-receipt"></i>
        <h3>Services</h3>
        <p><?php echo htmlspecialchars($officeInfo['services']); ?></p>
    </article>
</section>

<section class="info-feature">
    <div class="image-placeholder">
        <img src="assets/info-office-interior.jpg" alt="Accounting Office Interior" style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
    <div class="section-content">
        <span class="eyebrow"><i class="ph ph-buildings"></i> Visit</span>
        <h2>Campus Accounting Counter</h2>
        <p><?php echo htmlspecialchars($officeInfo['services']); ?></p>
        <div style="margin-top: 1rem;">
            <div class="schedule-row"><span>Monday - Friday</span><strong>8:00 AM - 5:00 PM</strong></div>
            <div class="schedule-row" style="margin-top: 0.6rem;"><span>Saturday - Sunday</span><strong style="color: var(--tup-maroon);">Closed</strong></div>
        </div>
    </div>
</section>

<section class="info-feature" id="contact">
    <div class="section-content">
        <span class="eyebrow"><i class="ph ph-headset"></i> Contact</span>
        <h2>Official Channels</h2>
        <p>Use official channels for payment concerns, receipt follow-ups, and account questions.</p>
        <div class="contact-list">
            <a href="mailto:<?php echo htmlspecialchars($officeInfo['email']); ?>"><i class="ph-fill ph-envelope-simple"></i> <?php echo htmlspecialchars($officeInfo['email']); ?></a>
            <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $officeInfo['phone']); ?>"><i class="ph-fill ph-phone"></i> <?php echo htmlspecialchars($officeInfo['phone']); ?></a>
            <a href="<?php echo htmlspecialchars($officeInfo['facebook_url']); ?>" target="_blank"><i class="ph-fill ph-facebook-logo"></i> TUPV Business Office Official Page</a>
            <a href="<?php echo htmlspecialchars($officeInfo['messenger_url']); ?>" target="_blank"><i class="ph-fill ph-messenger-logo"></i> Message us on Messenger</a>
        </div>
    </div>
    <div class="image-placeholder">
        <img src="assets/info-office-closeup.jpg" alt="Office Support Team" style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
</section>

<section style="margin-top: 1.25rem;">
    <div class="page-header">
        <div>
            <span class="eyebrow"><i class="ph ph-users-three"></i> Staff Directory</span>
            <h2>Board of Staff</h2>
        </div>
    </div>
    <div class="staff-tree-container">
        <!-- Level 1: Campus Director -->
        <div class="tree-node">
            <div class="profile-card">
                <div class="profile-img">
                    <img src="assets/staff1.jpg" alt="Eric A. Malo-oy, Ph.D.">
                </div>
                <div class="profile-name">Eric A. Malo-oy, Ph.D.</div>
                <div class="profile-title">Campus Director</div>
            </div>
        </div>
        
        <div class="tree-line"></div>
        
        <!-- Level 2: Chief Administrative Officer -->
        <div class="tree-node">
            <div class="profile-card">
                <div class="profile-img">
                    <img src="assets/staff2.jpg" alt="Lainie Mae L. Bala-an, DPA">
                </div>
                <div class="profile-name">Lainie Mae L. Bala-an, DPA</div>
                <div class="profile-title">Chief Administrative Officer</div>
            </div>
        </div>
        
        <div class="tree-line"></div>
        
        <!-- Level 3: Accounting Head -->
        <div class="tree-node">
            <div class="profile-card">
                <div class="profile-img">
                    <img src="assets/staff3.jpg" alt="Celeste Grace B. Delumpa, CPA">
                </div>
                <div class="profile-name">Celeste Grace B. Delumpa, CPA</div>
                <div class="profile-title">Accountant III / Head, Accounting Office</div>
            </div>
        </div>
        
        <div class="tree-children">
            <!-- Level 4: Line spread to three staffs -->
            <div class="tree-node child">
                <div class="profile-card">
                    <div class="profile-img">
                        <img src="assets/staff4.jpg" alt="Jazer John A. Frias, CPA">
                    </div>
                    <div class="profile-name">Jazer John A. Frias, CPA</div>
                    <div class="profile-title">Accountant II</div>
                </div>
            </div>
            <div class="tree-node child">
                <div class="profile-card">
                    <div class="profile-img">
                        <img src="assets/staff4-a.jpg" alt="Jorjet D. Abad">
                    </div>
                    <div class="profile-name">Jorjet D. Abad</div>
                    <div class="profile-title">Administrative Aide VI</div>
                </div>
            </div>
            <div class="tree-node child">
                <div class="profile-card">
                    <div class="profile-img">
                        <img src="assets/staff4-b.jpg" alt="Romena G. Esidenio">
                    </div>
                    <div class="profile-name">Romena G. Esidenio</div>
                    <div class="profile-title">Administrative Aide III</div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'php/includes/footer.php'; ?>
