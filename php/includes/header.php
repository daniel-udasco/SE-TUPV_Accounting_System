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
$initials = substr($userFirstName, 0, 1) . substr($userLastName, 0, 1);
if (empty($initials)) $initials = 'G';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TUPV Accounting Office</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        /* Base Dark Mode variables */
        [data-theme="dark"] {
            --bg-color: #121212;
            --bg-card: #1e1e1e;
            --text-main: #f8f9fa;
            --text-muted: #adb5bd;
            --tup-maroon: #b30000;
        }
        
        .wrapper { display: flex; min-height: 100vh; flex-direction: column; }
        @media (min-width: 768px) { .wrapper { flex-direction: row; } }
        
        .main-content { flex: 1; display: flex; flex-direction: column; width: 100%; min-width: 0; }
        
        .topbar { background-color: var(--bg-card); padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e9ecef; }
        .user-profile { display: flex; align-items: center; gap: 0.75rem; }
        .avatar { width: 40px; height: 40px; background-color: var(--tup-maroon); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; }
        
        .theme-toggle { background: none; border: none; font-size: 1.5rem; color: var(--text-main); cursor: pointer; padding: 0.5rem; border-radius: 50%; transition: background 0.2s; display: flex; align-items: center; justify-content: center; }
        .theme-toggle:hover { background: rgba(128,0,0,0.1); }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include 'sidebar.php'; ?>
    
    <main class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <div>
                <button class="theme-toggle" id="themeToggle" title="Toggle Dark Mode">
                    <i class="ph ph-moon"></i>
                </button>
            </div>
            
            <?php if($isLoggedIn): ?>
            <div class="user-profile">
                <div style="text-align: right;">
                    <div style="font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($userFirstName . ' ' . $userLastName); ?></div>
                    <div style="color: var(--text-muted); font-size: 0.75rem;"><?php echo htmlspecialchars($userCourse . ' - Year ' . $userYear); ?></div>
                </div>
                <div class="avatar"><?php echo htmlspecialchars($initials); ?></div>
            </div>
            <?php else: ?>
            <div class="user-profile">
                <a href="login.php" class="btn btn-outline" style="padding: 0.4rem 1rem; font-size: 0.85rem;">Login</a>
            </div>
            <?php endif; ?>
        </header>
        
        <!-- Content Area -->
        <div class="content-area">
