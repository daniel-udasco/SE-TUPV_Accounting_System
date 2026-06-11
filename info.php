<?php
require_once 'php/includes/header.php';
?>

<section class="dashboard-hero">
    <div class="hero-copy">
        <span class="eyebrow"><i class="ph ph-info"></i> Information Board</span>
        <h1>TUPV Accounting Office</h1>
        <p>Find office hours, location details, official contact channels, and the current staff directory for student transactions.</p>
        <div class="hero-actions">
            <a href="#contact" class="btn btn-primary"><i class="ph ph-phone-call"></i> Contact Office</a>
            <a href="login.php" class="btn btn-outline"><i class="ph ph-sign-in"></i> Student Login</a>
        </div>
    </div>
    <div class="hero-visual">
        <span class="placeholder-badge"><i class="ph ph-image"></i> Office exterior photo placeholder</span>
    </div>
</section>

<section class="info-grid">
    <article class="info-card">
        <i class="ph ph-map-pin"></i>
        <h3>Office Location</h3>
        <p>Admin Building, Ground Floor, Room 101, TUP Visayas, Capt. Sabi St., Brgy. Zone 12, Talisay City.</p>
    </article>
    <article class="info-card">
        <i class="ph ph-clock"></i>
        <h3>Operating Hours</h3>
        <p>Monday to Friday, 8:00 AM to 5:00 PM. Closed during weekends and declared holidays.</p>
    </article>
    <article class="info-card">
        <i class="ph ph-receipt"></i>
        <h3>Services</h3>
        <p>Fee assessment, payment confirmation, student receipts, summer class payments, and university materials.</p>
    </article>
</section>

<section class="info-feature">
    <div class="image-placeholder">
        <span class="placeholder-badge">Accounting office interior placeholder</span>
    </div>
    <div class="section-content">
        <span class="eyebrow"><i class="ph ph-buildings"></i> Visit</span>
        <h2>Campus Accounting Counter</h2>
        <p>The Accounting Office serves students, faculty, and campus staff for financial transactions and official payment records.</p>
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
            <a href="mailto:accountingtupv@gmail.com"><i class="ph-fill ph-envelope-simple"></i> accountingtupv@gmail.com</a>
            <a href="tel:0344452177"><i class="ph-fill ph-phone"></i> (034) 445 2177</a>
            <a href="https://www.facebook.com/tupvbusinessoffice" target="_blank"><i class="ph-fill ph-facebook-logo"></i> TUPV Business Office Official Page</a>
            <a href="https://www.messenger.com/tupvbusinessoffice/" target="_blank"><i class="ph-fill ph-messenger-logo"></i> Message us on Messenger</a>
        </div>
    </div>
    <div class="image-placeholder">
        <span class="placeholder-badge">Support team photo placeholder</span>
    </div>
</section>

<section style="margin-top: 1.25rem;">
    <div class="page-header">
        <div>
            <span class="eyebrow"><i class="ph ph-users-three"></i> Staff Directory</span>
            <h2>Board of Staff</h2>
        </div>
    </div>
    <div class="staff-grid">
        <div class="profile-card">
            <div class="profile-img"><i class="ph ph-user"></i></div>
            <div class="profile-name">Eric A. Malinao, Ph.D.</div>
            <div class="profile-title">Campus Director</div>
        </div>
        <div class="profile-card">
            <div class="profile-img"><i class="ph ph-user"></i></div>
            <div class="profile-name">[Name Here]</div>
            <div class="profile-title">Chief Administrative Officer</div>
        </div>
        <div class="profile-card">
            <div class="profile-img"><i class="ph ph-user"></i></div>
            <div class="profile-name">Mrs. Jane Doe, CPA</div>
            <div class="profile-title">Head of Accounting</div>
        </div>
        <div class="profile-card">
            <div class="profile-img"><i class="ph ph-user"></i></div>
            <div class="profile-name">Accountant 1</div>
            <div class="profile-title">Assessment Officer</div>
        </div>
        <div class="profile-card">
            <div class="profile-img"><i class="ph ph-user"></i></div>
            <div class="profile-name">Accountant 2</div>
            <div class="profile-title">Cashier</div>
        </div>
    </div>
</section>

<?php require_once 'php/includes/footer.php'; ?>
