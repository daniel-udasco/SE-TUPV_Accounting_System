<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isAdminLoggedIn = isset($_SESSION['admin']);
$adminFirstName = $_SESSION['admin']['first_name'] ?? 'Staff';
$adminLastName = $_SESSION['admin']['last_name'] ?? '';
$adminRole = $_SESSION['admin']['role'] ?? 'Accounting Staff';
$adminInitials = substr($adminFirstName, 0, 1) . substr($adminLastName, 0, 1);
if ($adminInitials === '') {
    $adminInitials = 'AO';
}

$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$basePath = $basePath === '' ? '.' : $basePath;
$styleVersion = filemtime(__DIR__ . '/../../css/style.css');
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - TUPV Accounting Office</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($basePath); ?>/css/style.css?v=<?php echo $styleVersion; ?>">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="admin-body">
<div class="wrapper admin-wrapper">
    <?php include __DIR__ . '/admin_sidebar.php'; ?>

    <main class="main-content">
        <header class="topbar admin-topbar">
            <div class="topbar-left">
                <button class="theme-toggle" id="themeToggle" title="Toggle Dark Mode">
                    <i class="ph ph-moon"></i>
                </button>
                <div>
                    <div class="topbar-kicker">Accounting Staff</div>
                    <div class="topbar-title">Administrative Console</div>
                </div>
            </div>

            <?php if($isAdminLoggedIn): ?>
            <div class="profile-dropdown-wrapper">
                <div class="user-profile" id="profileToggle" style="cursor: pointer;">
                    <div class="user-copy" style="text-align: right;">
                        <div style="font-weight: 750; font-size: 0.9rem;"><?php echo htmlspecialchars($adminFirstName . ' ' . $adminLastName); ?></div>
                        <div style="color: var(--text-muted); font-size: 0.75rem;"><?php echo htmlspecialchars($adminRole); ?></div>
                    </div>
                    <div class="avatar admin-avatar"><?php echo htmlspecialchars($adminInitials); ?></div>
                </div>
                <div class="profile-dropdown" id="profileDropdown">
                    <a href="admin_info.php"><i class="ph ph-info"></i> Info Board</a>
                    <a href="admin_logout.php"><i class="ph ph-sign-out"></i> Logout</a>
                </div>
            </div>
            <?php endif; ?>
        </header>

        <div class="content-area admin-content">
