<?php
require_once 'php/includes/header.php';
?>

<style>
    .page-header { margin-bottom: 3rem; text-align: center; }
    .page-header h1 { font-size: 2.5rem; color: var(--tup-maroon); }
    
    .section-container { margin-bottom: 4rem; display: flex; flex-direction: column; gap: 2rem; align-items: center; }
    @media(min-width: 900px) {
        .section-container { flex-direction: row; align-items: stretch; }
        .section-container:nth-child(even) { flex-direction: row-reverse; }
    }
    
    .section-image { flex: 1; min-height: 300px; background-color: #ddd; border-radius: var(--border-radius-lg); display: flex; align-items: center; justify-content: center; color: #888; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; }
    .section-content { flex: 1; padding: 2rem; background: var(--bg-card); border-radius: var(--border-radius-lg); box-shadow: var(--shadow-sm); display: flex; flex-direction: column; justify-content: center; }
    .section-content h2 { color: var(--tup-maroon); display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; }
    
    /* Org Chart Styles */
    .org-chart { text-align: center; width: 100%; margin-top: 2rem; }
    .org-node { display: flex; flex-direction: column; align-items: center; margin-bottom: 2rem; position: relative; }
    .org-node::after { content: ''; position: absolute; bottom: -2rem; left: 50%; width: 2px; height: 2rem; background-color: var(--tup-maroon); }
    .org-node:last-child::after { display: none; }
    
    .org-row { display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap; }
    .org-row .org-node::after { display: none; }
    
    .profile-card { background: var(--bg-card); padding: 1.5rem; border-radius: var(--border-radius-lg); box-shadow: var(--shadow-sm); width: 220px; border-top: 4px solid var(--tup-maroon); }
    .profile-img { width: 80px; height: 80px; background-color: #eee; border-radius: 50%; margin: 0 auto 1rem auto; display: flex; align-items: center; justify-content: center; color: #aaa; font-size: 2rem; }
    .profile-name { font-weight: 700; font-size: 1.1rem; }
    .profile-title { font-size: 0.85rem; color: var(--text-muted); }
</style>

<div class="page-header">
    <h1>Information Board</h1>
    <p class="text-muted">Important details and contact information for the TUP Visayas Accounting Office.</p>
</div>

<!-- Office Location Section -->
<div class="section-container">
    <div class="section-image">[Office Building Image Placeholder]</div>
    <div class="section-content">
        <h2><i class="ph ph-map-pin"></i> Office Location</h2>
        <p>The Accounting Office is centrally located within the campus to ensure easy access for all students and faculty.</p>
        <div style="margin-top: 1rem; padding: 1.5rem; background-color: var(--bg-color); border-radius: var(--border-radius-md); border-left: 4px solid var(--tup-maroon);">
            <strong>Admin Building, Ground Floor, Room 101</strong><br>
            Technological University of the Philippines Visayas<br>
            Capt. Sabi St., Brgy. Zone 12<br>
            Talisay City, Negros Occidental
        </div>
    </div>
</div>

<!-- Operating Hours Section -->
<div class="section-container">
    <div class="section-image">[Office Interior Image Placeholder]</div>
    <div class="section-content">
        <h2><i class="ph ph-clock"></i> Operating Hours</h2>
        <p>We are open during regular business hours to assist you with your financial transactions and inquiries.</p>
        <ul style="list-style-type: none; padding-left: 0; margin-top: 1rem;">
            <li style="display: flex; justify-content: space-between; padding: 1rem 0; border-bottom: 1px solid #eee;">
                <span>Monday - Friday</span> 
                <strong>8:00 AM - 5:00 PM</strong>
            </li>
            <li style="display: flex; justify-content: space-between; padding: 1rem 0;">
                <span>Saturday - Sunday</span> 
                <strong style="color: var(--tup-maroon);">Closed</strong>
            </li>
        </ul>
        <p class="text-muted" style="font-size: 0.85rem; margin-top: 1rem;">Note: The office is closed during national and local holidays.</p>
    </div>
</div>

<!-- Contact Us Section -->
<div class="section-container">
    <div class="section-image">[Contact/Support Team Image Placeholder]</div>
    <div class="section-content">
        <h2><i class="ph ph-phone-call"></i> Contact Us</h2>
        <p>If you have any questions, feel free to reach out to us through our official channels.</p>
        
        <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1.5rem;">
            <a href="mailto:accountingtupv@gmail.com" style="display: flex; align-items: center; gap: 1rem; color: var(--text-main);">
                <i class="ph-fill ph-envelope-simple" style="color: var(--tup-maroon); font-size: 1.5rem;"></i> accountingtupv@gmail.com
            </a>
            <a href="tel:0344452177" style="display: flex; align-items: center; gap: 1rem; color: var(--text-main);">
                <i class="ph-fill ph-phone" style="color: var(--tup-maroon); font-size: 1.5rem;"></i> (034) 445 2177
            </a>
            <a href="https://www.facebook.com/tupvbusinessoffice" target="_blank" style="display: flex; align-items: center; gap: 1rem; color: var(--text-main);">
                <i class="ph-fill ph-facebook-logo" style="color: #1877F2; font-size: 1.5rem;"></i> TUPV Business Office Official Page
            </a>
            <a href="https://www.messenger.com/tupvbusinessoffice/" target="_blank" style="display: flex; align-items: center; gap: 1rem; color: var(--text-main);">
                <i class="ph-fill ph-messenger-logo" style="color: #00B2FF; font-size: 1.5rem;"></i> Message us on Messenger
            </a>
        </div>
    </div>
</div>

<!-- Board of Staff Section -->
<div style="margin-top: 4rem; margin-bottom: 4rem;">
    <h2 style="text-align: center; color: var(--tup-maroon); margin-bottom: 2rem;"><i class="ph ph-users-three"></i> Board of Staff</h2>
    
    <div class="org-chart">
        <!-- Campus Director -->
        <div class="org-node">
            <div class="profile-card">
                <div class="profile-img"><i class="ph ph-user"></i></div>
                <div class="profile-name">Eric A. Malinao, Ph.D.</div>
                <div class="profile-title">Campus Director</div>
            </div>
        </div>
        
        <!-- Chief Administrative Officer -->
        <div class="org-node">
            <div class="profile-card">
                <div class="profile-img"><i class="ph ph-user"></i></div>
                <div class="profile-name">[Name Here]</div>
                <div class="profile-title">Chief Administrative Officer</div>
            </div>
        </div>
        
        <!-- Head of Accounting -->
        <div class="org-node">
            <div class="profile-card">
                <div class="profile-img"><i class="ph ph-user"></i></div>
                <div class="profile-name">Mrs. Jane Doe, CPA</div>
                <div class="profile-title">Head of Accounting</div>
            </div>
        </div>
        
        <!-- Accountants Row -->
        <div class="org-row">
            <div class="org-node">
                <div class="profile-card">
                    <div class="profile-img"><i class="ph ph-user"></i></div>
                    <div class="profile-name">Accountant 1</div>
                    <div class="profile-title">Assessment Officer</div>
                </div>
            </div>
            <div class="org-node">
                <div class="profile-card">
                    <div class="profile-img"><i class="ph ph-user"></i></div>
                    <div class="profile-name">Accountant 2</div>
                    <div class="profile-title">Cashier</div>
                </div>
            </div>
            <div class="org-node">
                <div class="profile-card">
                    <div class="profile-img"><i class="ph ph-user"></i></div>
                    <div class="profile-name">Accountant 3</div>
                    <div class="profile-title">Records Officer</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'php/includes/footer.php'; ?>
