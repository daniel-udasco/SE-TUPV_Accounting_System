<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$hideSidebarPages = ['login.php'];

if (!in_array($currentPage, $hideSidebarPages)):
?>
<style>
    .sidebar { width: 260px; background-color: var(--bg-card); border-right: 1px solid #e9ecef; display: flex; flex-direction: column; box-shadow: var(--shadow-sm); z-index: 10; }
    .sidebar-header { padding: 1.5rem; border-bottom: 1px solid #e9ecef; display: flex; align-items: center; gap: 0.75rem; color: var(--tup-maroon); font-family: var(--font-heading); font-weight: 700; font-size: 1.15rem; line-height: 1.2; }
    .sidebar-logo-placeholder { width: 35px; height: 35px; background-color: var(--tup-maroon); border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.8rem; }
    .sidebar-menu { list-style: none; padding: 1rem 0; flex: 1; overflow-y: auto; }
    .sidebar-menu li a { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.5rem; color: var(--text-main); font-weight: 500; transition: all 0.2s ease; }
    .sidebar-menu li a:hover, .sidebar-menu li.active a { background-color: rgba(128, 0, 0, 0.05); color: var(--tup-maroon); border-right: 3px solid var(--tup-maroon); }
    .sidebar-menu i { font-size: 1.25rem; }
    .sidebar-footer { padding: 1rem 1.5rem; border-top: 1px solid #e9ecef; }
    
    @media (max-width: 767px) {
        .sidebar { position: fixed; left: -260px; top: 0; bottom: 0; transition: left 0.3s ease; }
        .sidebar.open { left: 0; }
    }
</style>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo-placeholder">LOGO</div>
        <div>TUPV<br><span style="font-size: 0.9rem; font-weight: 500;">Accounting Office</span></div>
    </div>
    <ul class="sidebar-menu">
        <?php if($isLoggedIn): ?>
        <li class="<?= $currentPage == 'home.php' ? 'active' : '' ?>"><a href="home.php"><i class="ph ph-squares-four"></i> Dashboard</a></li>
        <li class="<?= $currentPage == 'school_fees.php' ? 'active' : '' ?>"><a href="school_fees.php"><i class="ph ph-money"></i> School Fees</a></li>
        <li class="<?= $currentPage == 'summer_class.php' ? 'active' : '' ?>"><a href="summer_class.php"><i class="ph ph-sun"></i> Summer Class</a></li>
        <li class="<?= $currentPage == 'shop.php' ? 'active' : '' ?>"><a href="shop.php"><i class="ph ph-shopping-bag"></i> Browse Shop</a></li>
        <li class="<?= $currentPage == 'transactions.php' ? 'active' : '' ?>"><a href="transactions.php"><i class="ph ph-clock-counter-clockwise"></i> Transaction History</a></li>
        <li class="<?= $currentPage == 'feedback.php' ? 'active' : '' ?>"><a href="feedback.php"><i class="ph ph-chat-teardrop-text"></i> Feedback</a></li>
        <?php endif; ?>
        <li class="<?= $currentPage == 'info.php' ? 'active' : '' ?>"><a href="info.php"><i class="ph ph-info"></i> Info Board</a></li>
    </ul>
    
    <?php if($isLoggedIn): ?>
    <div class="sidebar-footer">
        <a href="logout.php" class="btn btn-outline" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
            <i class="ph ph-sign-out"></i> Logout
        </a>
    </div>
    <?php endif; ?>
</aside>
<?php endif; ?>
