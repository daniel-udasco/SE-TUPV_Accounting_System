<?php
require_once 'php/includes/auth.php';
require_once 'php/db.php';

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $userId = $_SESSION['user']['id'];

    if (!empty($subject) && !empty($message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO feedbacks (user_id, subject, message) VALUES (?, ?, ?)");
            $stmt->execute([$userId, $subject, $message]);
            $successMsg = "Thank you! Your feedback has been submitted successfully to the Accounting Office.";
        } catch (\PDOException $e) {
            $errorMsg = "An error occurred while submitting your feedback. Please try again later.";
        }
    } else {
        $errorMsg = "Please fill in all fields.";
    }
}

require_once 'php/includes/header.php';
?>

<style>
    .page-header { margin-bottom: 2rem; }
    
    .feedback-form-container { background: var(--bg-card); border-radius: 0; padding: 3rem; box-shadow: var(--shadow-sm); border: 1px solid #e9ecef; border-top: 5px solid var(--tup-maroon); max-width: 700px; margin: 0 auto; }
    
    .form-group { margin-bottom: 1.5rem; }
    .form-label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-main); }
    .form-control { width: 100%; padding: 0.75rem; border: 1px solid #ced4da; border-radius: 0; font-family: var(--font-body); background: var(--bg-color); color: var(--text-main); }
    .form-control:focus { outline: none; border-color: var(--tup-maroon); }
    
    textarea.form-control { resize: vertical; min-height: 150px; }
    
    .alert { padding: 1rem; margin-bottom: 2rem; border-radius: 0; }
    .alert-success { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
    .alert-danger { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
</style>

<div class="page-header text-center">
    <h1>Submit Feedback</h1>
    <p class="text-muted">We value your input. Let us know how we can improve our services.</p>
</div>

<div class="feedback-form-container">
    <?php if ($successMsg): ?>
        <div class="alert alert-success"><i class="ph-fill ph-check-circle"></i> <?php echo htmlspecialchars($successMsg); ?></div>
    <?php endif; ?>
    
    <?php if ($errorMsg): ?>
        <div class="alert alert-danger"><i class="ph-fill ph-warning-circle"></i> <?php echo htmlspecialchars($errorMsg); ?></div>
    <?php endif; ?>

    <form action="feedback.php" method="POST">
        <div class="form-group">
            <label for="subject" class="form-label">Subject / Topic</label>
            <input type="text" id="subject" name="subject" class="form-control" placeholder="e.g. Inquiry about Summer Class Fees" required>
        </div>
        
        <div class="form-group">
            <label for="message" class="form-label">Your Message or Suggestion</label>
            <textarea id="message" name="message" class="form-control" placeholder="Please provide specific details..." required></textarea>
        </div>
        
        <div style="text-align: right; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem; border-radius: 0; text-transform: uppercase; font-weight: 600;">
                <i class="ph ph-paper-plane-right"></i> Submit Feedback
            </button>
        </div>
    </form>
</div>

<?php require_once 'php/includes/footer.php'; ?>
