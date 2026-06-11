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
            $successMsg = "Thank you. Your feedback has been submitted to the Accounting Office.";
        } catch (\PDOException $e) {
            $errorMsg = "An error occurred while submitting your feedback. Please try again later.";
        }
    } else {
        $errorMsg = "Please fill in all fields.";
    }
}

require_once 'php/includes/header.php';
?>

<div class="page-header">
    <div>
        <span class="eyebrow"><i class="ph ph-chat-teardrop-text"></i> Support</span>
        <h1>Submit Feedback</h1>
        <p class="text-muted">Send inquiries, suggestions, or transaction concerns to the Accounting Office.</p>
    </div>
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
            <label for="subject" class="form-label">Subject or topic</label>
            <input type="text" id="subject" name="subject" class="form-control" placeholder="e.g. Inquiry about summer class fees" required>
        </div>

        <div class="form-group">
            <label for="message" class="form-label">Message</label>
            <textarea id="message" name="message" class="form-control" placeholder="Include reference numbers or specific details when available." required></textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 1.25rem;">
            <button type="submit" class="btn btn-primary">
                <i class="ph ph-paper-plane-right"></i> Submit Feedback
            </button>
        </div>
    </form>
</div>

<?php require_once 'php/includes/footer.php'; ?>
