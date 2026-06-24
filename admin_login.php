<?php
session_start();
require_once 'php/includes/admin_bootstrap.php';

if (isset($_SESSION['admin'])) {
    header("Location: admin_dashboard.php");
    exit;
}

$error = '';
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$basePath = $basePath === '' ? '.' : $basePath;
$styleVersion = filemtime(__DIR__ . '/css/style.css');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = strtolower(trim($_POST['username'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    if ($username !== '' && $password !== '') {
        try {
            $stmt = $pdo->prepare("SELECT * FROM accounting_staff WHERE LOWER(username) = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password_hash'])) {
                unset($admin['password_hash']);
                $_SESSION['admin'] = $admin;
                header("Location: admin_dashboard.php");
                exit;
            }

            $error = "Invalid staff username or password.";
        } catch (\PDOException $e) {
            $error = "Database error. Please try again later.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - TUPV Accounting</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($basePath); ?>/css/style.css?v=<?php echo $styleVersion; ?>">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="login-page admin-login-page">
    <nav class="login-nav admin-login-nav">
        <a class="navbar-brand" href="info.php">
            <span class="brand-mark" style="padding: 2px;">
                <img src="assets/TUPVAS_logo.svg" alt="TUPVAS Logo" style="width: 100%; height: 100%; object-fit: contain; display: block;">
            </span>
            <span>TUPV Accounting Office</span>
        </a>
        <div class="navbar-links">
            <a href="login.php">Student Login</a>
            <a href="info.php">Information Board</a>
        </div>
    </nav>

    <main class="login-main">
        <section class="login-copy">
            <span class="eyebrow admin-eyebrow"><i class="ph ph-shield-star"></i> Staff operations console</span>
            <h1>Review payments, manage records, and keep the office board current.</h1>
            <p>Accounting staff can process student transactions, update profiles, prepare printable archives, and respond to submitted feedback.</p>
        </section>

        <section class="login-card admin-login-card">
            <div class="login-header">
                <h2>Admin Login</h2>
                <p>Use your first name as username and last name as password.</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="admin_login.php" method="POST">
                <div class="form-group">
                    <label for="username" class="form-label">First Name</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Enter staff first name" required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Last Name</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter staff last name" required>
                </div>

                <button type="submit" class="btn btn-primary admin-btn" style="width: 100%;">Sign In to Admin</button>
            </form>
        </section>
    </main>
</body>
</html>
