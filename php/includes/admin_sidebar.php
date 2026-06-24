<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$adminPages = [
    'admin_dashboard.php' => ['Dashboard', 'ph-gauge'],
    'admin_transactions.php' => ['Transactions', 'ph-receipt'],
    'admin_students.php' => ['Student Profiles', 'ph-student'],
    'admin_feedback.php' => ['Feedbacks', 'ph-chat-centered-text'],
    'admin_archives.php' => ['Print Archives', 'ph-printer'],
    'admin_info.php' => ['Info Board', 'ph-info'],
];
?>
<aside class="sidebar admin-sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo-placeholder" style="padding: 2px;">
            <img src="assets/TUPVAS_logo.svg" alt="TUPVAS Logo" style="width: 100%; height: 100%; object-fit: contain; display: block;">
        </div>
        <div class="brand-name">TUPV<span class="brand-subtitle">Admin Accounting</span></div>
    </div>
    <ul class="sidebar-menu">
        <?php foreach ($adminPages as $page => [$label, $icon]): ?>
        <li class="<?= $currentPage == $page ? 'active' : '' ?>">
            <a href="<?php echo $page; ?>"><i class="ph <?php echo $icon; ?>"></i> <?php echo $label; ?></a>
        </li>
        <?php endforeach; ?>
        <li><a href="info.php"><i class="ph ph-arrow-square-out"></i> Public Info Board</a></li>
    </ul>

    <div class="sidebar-footer">
        <a href="admin_logout.php" class="btn btn-outline" style="width: 100%;">
            <i class="ph ph-sign-out"></i> Logout
        </a>
    </div>
</aside>
