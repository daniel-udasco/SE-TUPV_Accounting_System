<?php
require_once 'php/includes/admin_auth.php';

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedbackId = (int)($_POST['feedback_id'] ?? 0);
    $status = $_POST['status'] ?? 'read';
    if ($feedbackId > 0 && in_array($status, ['new', 'read', 'resolved'], true)) {
        try {
            $stmt = $pdo->prepare("UPDATE feedbacks SET status = ? WHERE id = ?");
            $stmt->execute([$status, $feedbackId]);
            $successMsg = "Feedback status updated.";
        } catch (\PDOException $e) {
            $errorMsg = "Unable to update feedback.";
        }
    }
}

$statusFilter = $_GET['status'] ?? 'all';
$feedbacks = [];

try {
    $query = "
        SELECT f.*, u.student_id, u.first_name, u.last_name, u.course
        FROM feedbacks f
        JOIN users u ON u.id = f.user_id
        WHERE 1 = 1
    ";
    $params = [];
    if (in_array($statusFilter, ['new', 'read', 'resolved'], true)) {
        $query .= " AND f.status = ?";
        $params[] = $statusFilter;
    }
    $query .= " ORDER BY FIELD(f.status, 'new', 'read', 'resolved'), f.created_at DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $feedbacks = $stmt->fetchAll();
} catch (\PDOException $e) {
    $errorMsg = $errorMsg ?: "Unable to load feedback.";
}

require_once 'php/includes/admin_header.php';
?>

<div class="page-header">
    <div>
        <span class="eyebrow admin-eyebrow"><i class="ph ph-chat-centered-text"></i> Student voice</span>
        <h1>Feedbacks</h1>
        <p class="text-muted">Review student concerns and mark each item as read or resolved.</p>
    </div>
</div>

<?php if ($successMsg): ?><div class="alert alert-success"><i class="ph-fill ph-check-circle"></i> <?php echo htmlspecialchars($successMsg); ?></div><?php endif; ?>
<?php if ($errorMsg): ?><div class="alert alert-danger"><i class="ph-fill ph-warning-circle"></i> <?php echo htmlspecialchars($errorMsg); ?></div><?php endif; ?>

<div class="filters-bar admin-filter">
    <form action="admin_feedback.php" method="GET">
        <div class="filter-group">
            <label for="status">Status</label>
            <select id="status" name="status" class="form-control" onchange="this.form.submit()">
                <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All Feedback</option>
                <option value="new" <?php echo $statusFilter === 'new' ? 'selected' : ''; ?>>New</option>
                <option value="read" <?php echo $statusFilter === 'read' ? 'selected' : ''; ?>>Read</option>
                <option value="resolved" <?php echo $statusFilter === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
            </select>
        </div>
        <noscript><button class="btn btn-outline" type="submit">Filter</button></noscript>
    </form>
</div>

<div class="feedback-admin-grid">
    <?php foreach ($feedbacks as $feedback): ?>
    <article class="panel feedback-admin-card">
        <div class="panel-header">
            <div>
                <span class="status <?php echo $feedback['status']; ?>"><?php echo ucfirst($feedback['status']); ?></span>
                <h2 style="font-size: 1.05rem; margin-top: 0.55rem;"><?php echo htmlspecialchars($feedback['subject']); ?></h2>
                <p class="text-muted" style="font-size: 0.84rem;">
                    <?php echo htmlspecialchars($feedback['first_name'] . ' ' . $feedback['last_name']); ?> ·
                    <span class="mono"><?php echo htmlspecialchars($feedback['student_id']); ?></span> ·
                    <?php echo date('M d, Y g:i A', strtotime($feedback['created_at'])); ?>
                </p>
            </div>
        </div>
        <div class="panel-body">
            <p><?php echo nl2br(htmlspecialchars($feedback['message'])); ?></p>
            <form action="admin_feedback.php?<?php echo htmlspecialchars(http_build_query($_GET)); ?>" method="POST" class="feedback-action-row">
                <input type="hidden" name="feedback_id" value="<?php echo (int)$feedback['id']; ?>">
                <select name="status" class="form-control compact-control">
                    <option value="new" <?php echo $feedback['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                    <option value="read" <?php echo $feedback['status'] === 'read' ? 'selected' : ''; ?>>Read</option>
                    <option value="resolved" <?php echo $feedback['status'] === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                </select>
                <button class="btn btn-primary admin-btn compact-btn" type="submit"><i class="ph ph-check"></i> Update</button>
            </form>
        </div>
    </article>
    <?php endforeach; ?>
</div>

<?php require_once 'php/includes/admin_footer.php'; ?>
