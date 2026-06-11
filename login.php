<?php
session_start();
require_once 'php/db.php';

if (isset($_SESSION['user'])) {
    header("Location: home.php");
    exit;
}

$error = '';
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$basePath = $basePath === '' ? '.' : $basePath;
$styleVersion = filemtime(__DIR__ . '/css/style.css');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['student_id'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember_me']);

    if (!empty($studentId) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = ?");
            $stmt->execute([$studentId]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                unset($user['password_hash']);
                $_SESSION['user'] = $user;

                if ($remember) {
                    setcookie('remember_me', $studentId, time() + (86400 * 30), "/");
                }

                header("Location: home.php");
                exit;
            } else {
                $error = "Invalid Student ID or Password.";
            }
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
    <title>Login - TUPV Accounting</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($basePath); ?>/css/style.css?v=<?php echo $styleVersion; ?>">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="login-page">
    <nav class="login-nav">
        <a class="navbar-brand" href="info.php">
            <span class="brand-mark">LOGO</span>
            <span>TUPV Accounting Office</span>
        </a>
        <div class="navbar-links">
            <a href="info.php">Information Board</a>
            <a href="info.php#contact">Contact Us</a>
        </div>
    </nav>

    <main class="login-main">
        <section class="login-copy">
            <span class="eyebrow"><i class="ph ph-shield-check"></i> Student accounting portal</span>
            <h1>Payments, receipts, and campus materials in one workspace.</h1>
            <p>Access your student dashboard to settle approved fees, apply for summer class payments, reserve official merchandise, and review your transaction history.</p>
            <div class="login-photo-placeholder image-placeholder">
                <span class="placeholder-badge"><i class="ph ph-image"></i> University and Accounting Office photo placeholder</span>
            </div>
        </section>

        <section class="login-card">
            <div class="login-header">
                <h2>Student Portal Login</h2>
                <p>Use your student credentials to continue.</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="studentId" class="form-label">Student ID Number</label>
                    <input type="text" id="studentId" name="student_id" class="form-control" placeholder="e.g. TUPV-23-0050" required value="<?php echo htmlspecialchars($_COOKIE['remember_me'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>

                <div class="form-group d-flex justify-content-between align-items-center mb-4 gap-2">
                    <label style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.85rem; margin: 0;">
                        <input type="checkbox" id="rememberMe" name="remember_me" <?php echo isset($_COOKIE['remember_me']) ? 'checked' : ''; ?>>
                        Remember me
                    </label>
                    <a href="https://ers.tup.edu.ph/aims/students/forgot.php" target="_blank" style="font-size: 0.85rem;">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Sign In</button>
            </form>
            <div class="text-center mt-3" style="font-size: 0.85rem;">
                Having trouble logging in? <a href="info.php#contact">Contact Support</a>
            </div>
        </section>
    </main>

    <footer class="login-footer">
        &copy; 2026 Technological University of the Philippines Visayas. All rights reserved.
    </footer>
</body>
</html>
