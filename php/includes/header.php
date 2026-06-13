<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// User details from session
$isLoggedIn = isset($_SESSION['user']);
$userFirstName = $_SESSION['user']['first_name'] ?? 'Guest';
$userLastName = $_SESSION['user']['last_name'] ?? '';
$userCourse = $_SESSION['user']['course'] ?? '';
$userYear = $_SESSION['user']['year_level'] ?? '';
$userYearFormatted = '';
if ($userYear !== '') {
    switch ((int)$userYear) {
        case 1: $userYearFormatted = '1st Year'; break;
        case 2: $userYearFormatted = '2nd Year'; break;
        case 3: $userYearFormatted = '3rd Year'; break;
        case 4: $userYearFormatted = '4th Year'; break;
        default: $userYearFormatted = "Master's Year"; break;
    }
}
$initials = substr($userFirstName, 0, 1) . substr($userLastName, 0, 1);
if (empty($initials)) $initials = 'G';
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$basePath = $basePath === '' ? '.' : $basePath;
$styleVersion = filemtime(__DIR__ . '/../../css/style.css');
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TUPV Accounting Office</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($basePath); ?>/css/style.css?v=<?php echo $styleVersion; ?>">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="wrapper">
    <?php include __DIR__ . '/sidebar.php'; ?>
    
    <main class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="theme-toggle" id="themeToggle" title="Toggle Dark Mode">
                    <i class="ph ph-moon"></i>
                </button>
                <div>
                    <div class="topbar-kicker">TUP Visayas</div>
                    <div class="topbar-title">Accounting Office Portal</div>
                </div>
            </div>
            
            <?php if($isLoggedIn): ?>
            <div class="profile-dropdown-wrapper">
                <div class="user-profile" id="profileToggle" style="cursor: pointer;">
                    <div class="user-copy" style="text-align: right;">
                        <div style="font-weight: 750; font-size: 0.9rem;"><?php echo htmlspecialchars($userFirstName . ' ' . $userLastName); ?></div>
                        <div style="color: var(--text-muted); font-size: 0.75rem;"><?php echo htmlspecialchars($userCourse . ' - ' . $userYearFormatted); ?></div>
                    </div>
                    <div class="avatar"><?php echo htmlspecialchars($initials); ?></div>
                </div>
                <div class="profile-dropdown" id="profileDropdown">
                    <a href="profile.php"><i class="ph ph-user"></i> Profile</a>
                    <a href="logout.php"><i class="ph ph-sign-out"></i> Logout</a>
                </div>
            </div>
            <?php else: ?>
            <div class="user-profile">
                <a href="login.php" class="btn btn-outline">Login</a>
            </div>
            <?php endif; ?>
        </header>
        
        <!-- Content Area -->
        <div class="content-area">
