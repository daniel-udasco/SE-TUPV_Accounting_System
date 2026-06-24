<?php
require_once 'php/includes/admin_auth.php';

$stats = [
    'transactions' => 0,
    'pending' => 0,
    'completed_amount' => 0,
    'feedback_new' => 0,
    'students' => 0,
];
$recentTransactions = [];
$recentFeedbacks = [];
$lowStock = [];

try {
    $stats = array_merge($stats, $pdo->query("
        SELECT
            COUNT(*) AS transactions,
            COALESCE(SUM(status = 'pending'), 0) AS pending,
            COALESCE(SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END), 0) AS completed_amount
        FROM transactions
    ")->fetch() ?: []);

    $stats['feedback_new'] = (int)$pdo->query("SELECT COUNT(*) FROM feedbacks WHERE status = 'new'")->fetchColumn();
    $stats['students'] = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

    $recentTransactions = $pdo->query("
        SELECT t.*, u.student_id, u.first_name, u.last_name
        FROM transactions t
        JOIN users u ON u.id = t.user_id
        ORDER BY t.transaction_date DESC
        LIMIT 8
    ")->fetchAll();

    $recentFeedbacks = $pdo->query("
        SELECT f.*, u.student_id, u.first_name, u.last_name
        FROM feedbacks f
        JOIN users u ON u.id = f.user_id
        ORDER BY f.created_at DESC
        LIMIT 5
    ")->fetchAll();

    $lowStock = $pdo->query("SELECT * FROM products WHERE stock_quantity <= 25 ORDER BY stock_quantity ASC LIMIT 5")->fetchAll();
} catch (\PDOException $e) {
    // Dashboard remains usable with empty state.
}

require_once 'php/includes/admin_header.php';
?>

<section class="dashboard-hero admin-hero">
    <div class="hero-copy">
        <span class="eyebrow admin-eyebrow"><i class="ph ph-gauge"></i> Admin dashboard</span>
        <h1>Good day, <?php echo htmlspecialchars($_SESSION['admin']['first_name']); ?>.</h1>
        <p>Process payments, audit student account activity, prepare archive-ready lists, and manage the public Accounting Office information board.</p>
        <div class="hero-actions">
            <a href="admin_transactions.php" class="btn btn-primary admin-btn"><i class="ph ph-receipt"></i> Process Transactions</a>
            <a href="admin_archives.php" class="btn btn-outline"><i class="ph ph-printer"></i> Print Archives</a>
        </div>
    </div>
    <div class="admin-command-panel">
        <div class="command-title">Today at a glance</div>
        <div class="command-row"><span>Pending Queue</span><strong><?php echo number_format((int)$stats['pending']); ?></strong></div>
        <div class="command-row"><span>New Feedback</span><strong><?php echo number_format((int)$stats['feedback_new']); ?></strong></div>
        <div class="command-row"><span>Student Records</span><strong><?php echo number_format((int)$stats['students']); ?></strong></div>
    </div>
</section>

<section class="metric-grid">
    <div class="metric-card admin-metric">
        <i class="ph ph-receipt"></i>
        <div class="metric-label">Total Transactions</div>
        <div class="metric-value"><?php echo number_format((int)$stats['transactions']); ?></div>
        <div class="metric-note">All student payment records</div>
    </div>
    <div class="metric-card admin-metric">
        <i class="ph ph-hourglass-medium"></i>
        <div class="metric-label">Pending Review</div>
        <div class="metric-value"><?php echo number_format((int)$stats['pending']); ?></div>
        <div class="metric-note">Needs staff action</div>
    </div>
    <div class="metric-card admin-metric">
        <i class="ph ph-vault"></i>
        <div class="metric-label">Completed Amount</div>
        <div class="metric-value">&#8369; <?php echo number_format((float)$stats['completed_amount'], 2); ?></div>
        <div class="metric-note">Cleared revenue records</div>
    </div>
    <div class="metric-card admin-metric">
        <i class="ph ph-chat-centered-text"></i>
        <div class="metric-label">New Feedback</div>
        <div class="metric-value"><?php echo number_format((int)$stats['feedback_new']); ?></div>
        <div class="metric-note">Unread student concerns</div>
    </div>
</section>

<div class="admin-two-column">
    <div class="table-container">
        <div class="table-header">
            <div>
                <h2 style="font-size: 1.05rem;">Recent Transactions</h2>
                <p class="text-muted" style="font-size: 0.86rem;">Latest payment activity across students.</p>
            </div>
            <a href="admin_transactions.php" class="btn btn-outline">Open Queue</a>
        </div>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Reference</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentTransactions as $txn): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($txn['first_name'] . ' ' . $txn['last_name']); ?><br><span class="text-muted mono" style="font-size: 0.76rem;"><?php echo htmlspecialchars($txn['student_id']); ?></span></td>
                        <td class="mono"><?php echo htmlspecialchars($txn['reference_no']); ?></td>
                        <td><?php echo ucwords(str_replace('_', ' ', $txn['transaction_type'])); ?></td>
                        <td style="font-weight: 800;">&#8369; <?php echo number_format((float)$txn['amount'], 2); ?></td>
                        <td><span class="status <?php echo strtolower($txn['status']); ?>"><?php echo ucfirst($txn['status']); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="stacked-panels">
        <div class="panel">
            <div class="panel-header">
                <div>
                    <h2 style="font-size: 1.05rem;">Feedback Watch</h2>
                    <p class="text-muted" style="font-size: 0.86rem;">Newest student messages.</p>
                </div>
                <a href="admin_feedback.php" class="btn btn-outline">Review</a>
            </div>
            <div class="panel-body compact-list">
                <?php foreach ($recentFeedbacks as $feedback): ?>
                <a href="admin_feedback.php" class="compact-list-item">
                    <strong><?php echo htmlspecialchars($feedback['subject']); ?></strong>
                    <span><?php echo htmlspecialchars($feedback['first_name'] . ' ' . $feedback['last_name']); ?> · <?php echo ucfirst($feedback['status']); ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <h2 style="font-size: 1.05rem;">Low Stock</h2>
                    <p class="text-muted" style="font-size: 0.86rem;">Materials needing attention.</p>
                </div>
                <a href="admin_students.php#materials" class="btn btn-outline">Manage</a>
            </div>
            <div class="panel-body compact-list">
                <?php if (empty($lowStock)): ?>
                    <p class="text-muted">No low-stock materials.</p>
                <?php endif; ?>
                <?php foreach ($lowStock as $product): ?>
                <div class="compact-list-item">
                    <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                    <span><?php echo number_format((int)$product['stock_quantity']); ?> item(s) left</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'php/includes/admin_footer.php'; ?>
