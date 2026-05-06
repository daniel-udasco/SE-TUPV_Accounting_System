<?php
session_start();
session_unset();
session_destroy();

// If there's a remember_me cookie, you might want to unset it here
if (isset($_COOKIE['remember_me'])) {
    setcookie('remember_me', '', time() - 3600, '/');
}

header("Location: login.php");
exit;
?>
