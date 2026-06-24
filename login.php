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
            <span class="brand-mark" style="padding: 2px;">
                <img src="assets/TUPVAS_logo.svg" alt="TUPVAS Logo" style="width: 100%; height: 100%; object-fit: contain; display: block;">
            </span>
            <span>TUPV Accounting Office</span>
        </a>
        <div class="navbar-links">
            <a href="info.php">Information Board</a>
            <a href="info.php#contact">Contact Us</a>
            <a href="admin_login.php">Staff Login</a>
        </div>
    </nav>

    <main class="login-main">
        <section class="login-copy">
            <span class="eyebrow"><i class="ph ph-shield-check"></i> Student accounting portal</span>
            <h1>Payments & Transactions, now in a Digital Platform.</h1>
            <p>Access your student dashboard to settle approved fees, apply for summer class payments, reserve official merchandise, and review your transaction history.</p>
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
                    <input type="text" id="studentId" name="student_id" class="form-control" placeholder="Enter your Student ID" required value="<?php echo htmlspecialchars($_COOKIE['remember_me'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your access key" required>
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
        </section>
    </main>

    <footer class="login-footer" style="padding: 1.25rem 5vw; background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(8px); margin-top: auto;">
        <div style="max-width: 1180px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 1rem; width: 100%; flex-wrap: wrap;">
            <p style="margin: 0; font-size: 0.82rem; color: var(--text-muted);">&copy; 2026 Technological University of the Philippines Visayas. All rights reserved.</p>
            <div class="social-links">
                <a href="https://www.facebook.com/tupvbusinessoffice" target="_blank" title="Facebook"><i class="ph ph-facebook-logo"></i></a>
                <a href="https://www.messenger.com/tupvbusinessoffice/" target="_blank" title="Messenger"><i class="ph ph-messenger-logo"></i></a>
                <a href="mailto:accountingtupv@gmail.com" title="Email"><i class="ph ph-envelope-simple"></i></a>
                <a href="tel:0344452177" title="Phone: (034) 445 2177"><i class="ph ph-phone"></i></a>
            </div>
        </div>
    </footer>
</body>
</html>
