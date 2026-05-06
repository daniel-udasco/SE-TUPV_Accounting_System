<?php
session_start();
require_once 'php/db.php';

// Redirect if already logged in
if (isset($_SESSION['user'])) {
    header("Location: home.php");
    exit;
}

$error = '';

// Handle Login Submission
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
                // Remove password hash from session data for safety
                unset($user['password_hash']);
                $_SESSION['user'] = $user;

                if ($remember) {
                    setcookie('remember_me', $studentId, time() + (86400 * 30), "/"); // 30 days
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
    <link rel="stylesheet" href="css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; background-color: var(--bg-color); }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 1rem 5%; background-color: var(--tup-maroon); color: var(--text-light); box-shadow: var(--shadow-sm); }
        .navbar-brand { font-family: var(--font-heading); font-size: 1.25rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; }
        .navbar-brand .logo-placeholder { width: 30px; height: 30px; background-color: white; border-radius: 50%; color: var(--tup-maroon); display: flex; align-items: center; justify-content: center; font-size: 0.7rem; }
        .navbar-links a { color: var(--text-light); margin-left: 1.5rem; font-weight: 500; }
        .navbar-links a:hover { color: var(--tup-gray); }
        
        .hero-section { flex: 1; display: flex; align-items: center; justify-content: center; padding: 3rem 5%; background: linear-gradient(135deg, rgba(248, 249, 250, 0.9) 0%, rgba(220, 220, 220, 0.9) 100%); }
        .hero-content { display: flex; flex-direction: row; gap: 4rem; align-items: center; max-width: 1200px; width: 100%; }
        .hero-text { flex: 1; color: #333; }
        .hero-text h1 { font-size: 3rem; line-height: 1.2; margin-bottom: 1rem; color: var(--tup-maroon); }
        .hero-text p { font-size: 1.1rem; margin-bottom: 2rem; max-width: 500px; }
        
        .login-card { flex: 0 0 400px; background-color: var(--bg-card); border-radius: var(--border-radius-lg); padding: 2.5rem; box-shadow: var(--shadow-md); }
        .login-header { text-align: center; margin-bottom: 2rem; }
        .login-header h2 { font-size: 1.5rem; color: var(--text-main); }
        .login-header p { color: var(--text-muted); font-size: 0.9rem; }
        
        .error-message { background-color: #f8d7da; color: #842029; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; font-size: 0.9rem; text-align: center; }

        @media (max-width: 768px) {
            .hero-content { flex-direction: column; text-align: center; }
            .hero-text p { margin: 0 auto 2rem auto; }
            .login-card { width: 100%; flex: none; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="navbar-brand">
            <div class="logo-placeholder">LOGO</div>
            TUPV Accounting Office
        </div>
        <div class="navbar-links">
            <a href="info.php">Information Board</a>
            <a href="info.php#contact">Contact Us</a>
        </div>
    </nav>

    <main class="hero-section">
        <div class="hero-content">
            
            <div class="hero-text">
                <h1>Welcome to TUPV Integrated Accounting</h1>
                <p>Seamless, secure, and fast online financial transactions for the Technological University of the Philippines Visayas community.</p>
                <div class="d-flex gap-3">
                    <a href="info.php" class="btn btn-outline">Learn More</a>
                </div>
            </div>

            <div class="login-card">
                <div class="login-header">
                    <h2>Student Portal Login</h2>
                    <p>Enter your credentials to access your dashboard</p>
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
                    
                    <div class="form-group d-flex justify-content-between align-items-center mb-4">
                        <div style="display: flex; align-items: center; gap: 0.25rem;">
                            <input type="checkbox" id="rememberMe" name="remember_me" <?php echo isset($_COOKIE['remember_me']) ? 'checked' : ''; ?>>
                            <label for="rememberMe" style="font-size: 0.85rem; margin: 0;">Remember me</label>
                        </div>
                        <a href="https://ers.tup.edu.ph/aims/students/forgot.php" target="_blank" style="font-size: 0.85rem;">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Sign In</button>
                </form>
                <div class="text-center mt-3" style="font-size: 0.85rem;">
                    Having trouble logging in? <a href="info.php#contact">Contact Support</a>
                </div>
            </div>
        </div>
    </main>

    <?php 
    // We can't include footer.php because footer.php expects the wrapper layout. 
    // We will just put the footer HTML directly here for the login page to keep it standalone.
    ?>
    <footer style="background-color: #222; color: #ccc; padding: 1.5rem 2rem; text-align: center; font-size: 0.85rem;">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; gap: 1rem; align-items: center;">
            <p>&copy; 2025 Technological University of the Philippines Visayas. All rights reserved.</p>
            <div style="display: flex; gap: 1rem;">
                <a href="https://www.facebook.com/tupvbusinessoffice" target="_blank" style="color: #ccc; font-size: 1.25rem;"><i class="ph ph-facebook-logo"></i></a>
                <a href="https://www.messenger.com/tupvbusinessoffice/" target="_blank" style="color: #ccc; font-size: 1.25rem;"><i class="ph ph-messenger-logo"></i></a>
                <a href="mailto:accountingtupv@gmail.com" style="color: #ccc; font-size: 1.25rem;"><i class="ph ph-envelope-simple"></i></a>
                <a href="tel:0344452177" style="color: #ccc; font-size: 1.25rem;"><i class="ph ph-phone"></i></a>
            </div>
        </div>
    </footer>
</body>
</html>
