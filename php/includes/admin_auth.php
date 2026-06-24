<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/admin_bootstrap.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}
?>
