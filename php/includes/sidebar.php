<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$hideSidebarPages = ['login.php'];

if (!in_array($currentPage, $hideSidebarPages)):
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo-placeholder" style="padding: 2px;">
            <img src="assets/TUPVAS_logo.svg" alt="TUPVAS Logo" style="width: 100%; height: 100%; object-fit: contain; display: block;">
        </div>
        <div class="brand-name">TUPV<span class="brand-subtitle">Accounting Office</span></div>
    </div>
    <ul class="sidebar-menu">
        <?php if($isLoggedIn): ?>
        <li class="<?= $currentPage == 'home.php' ? 'active' : '' ?>"><a href="home.php"><i class="ph ph-squares-four"></i> Dashboard</a></li>
        <li class="<?= $currentPage == 'school_fees.php' ? 'active' : '' ?>"><a href="school_fees.php"><i class="ph ph-money"></i> School Fees</a></li>
        <li class="<?= $currentPage == 'summer_class.php' ? 'active' : '' ?>"><a href="summer_class.php"><i class="ph ph-sun"></i> Summer Class</a></li>
        <li class="<?= $currentPage == 'shop.php' ? 'active' : '' ?>"><a href="shop.php"><i class="ph ph-shopping-bag"></i> Browse Shop</a></li>
        <li class="<?= $currentPage == 'scholarship.php' ? 'active' : '' ?>"><a href="scholarship.php"><i class="ph ph-graduation-cap"></i> Scholarships</a></li>
        <li class="<?= $currentPage == 'transactions.php' ? 'active' : '' ?>"><a href="transactions.php"><i class="ph ph-clock-counter-clockwise"></i> Transaction History</a></li>
        <li class="<?= $currentPage == 'feedback.php' ? 'active' : '' ?>"><a href="feedback.php"><i class="ph ph-chat-teardrop-text"></i> Feedback</a></li>
        <li class="<?= $currentPage == 'visit.php' ? 'active' : '' ?>"><a href="visit.php"><i class="ph ph-map-pin"></i> Visit Us</a></li>
        <?php endif; ?>
        <li class="<?= $currentPage == 'info.php' ? 'active' : '' ?>"><a href="info.php"><i class="ph ph-info"></i> Info Board</a></li>
    </ul>
    
    <?php if($isLoggedIn): ?>
    <div class="sidebar-footer">
        <a href="logout.php" class="btn btn-outline" style="width: 100%;">
            <i class="ph ph-sign-out"></i> Logout
        </a>
    </div>
    <?php endif; ?>
</aside>
<?php endif; ?>
