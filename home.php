<?php
require_once 'php/includes/auth.php';
require_once 'php/db.php';
require_once 'php/includes/header.php';

$userId = $_SESSION['user']['id'];
$transactions = [];
$stats = [
    'transaction_count' => 0,
    'total_amount' => 0,
    'pending_count' => 0,
    'completed_count' => 0,
];

try {
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC LIMIT 5");
    $stmt->execute([$userId]);
    $transactions = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT COUNT(*) AS transaction_count, COALESCE(SUM(amount), 0) AS total_amount, SUM(status = 'pending') AS pending_count, SUM(status = 'completed') AS completed_count FROM transactions WHERE user_id = ?");
    $stmt->execute([$userId]);
    $stats = array_merge($stats, $stmt->fetch() ?: []);
} catch (\PDOException $e) {
    // Error fetching dashboard data.
}
?>

<section class="dashboard-hero">
    <div class="hero-copy">
        <span class="eyebrow"><i class="ph ph-sparkle"></i> Student finance workspace</span>
        <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['user']['first_name']); ?>.</h1>
        <p>Manage school fees, summer class payments, official merchandise, and receipts in one accounting office dashboard.</p>
        <div class="hero-actions">
            <a href="school_fees.php" class="btn btn-primary"><i class="ph ph-credit-card"></i> Pay a Fee</a>
            <a href="transactions.php" class="btn btn-outline"><i class="ph ph-receipt"></i> View Receipts</a>
        </div>
    </div>
    <div class="hero-visual">
        <span class="placeholder-badge"><i class="ph ph-image"></i> Accounting office photo placeholder</span>
    </div>
</section>

<section class="metric-grid">
    <div class="metric-card">
        <i class="ph ph-receipt"></i>
        <div class="metric-label">Transactions</div>
        <div class="metric-value"><?php echo number_format((int)$stats['transaction_count']); ?></div>
        <div class="metric-note">All recorded payments</div>
    </div>
    <div class="metric-card">
        <i class="ph ph-wallet"></i>
        <div class="metric-label">Total Processed</div>
        <div class="metric-value">&#8369; <?php echo number_format((float)$stats['total_amount'], 2); ?></div>
        <div class="metric-note">Includes pending items</div>
    </div>
    <div class="metric-card">
        <i class="ph ph-hourglass-medium"></i>
        <div class="metric-label">Pending</div>
        <div class="metric-value"><?php echo number_format((int)$stats['pending_count']); ?></div>
        <div class="metric-note">Awaiting office confirmation</div>
    </div>
    <div class="metric-card">
        <i class="ph ph-check-circle"></i>
        <div class="metric-label">Completed</div>
        <div class="metric-value"><?php echo number_format((int)$stats['completed_count']); ?></div>
        <div class="metric-note">Cleared transactions</div>
    </div>
</section>

<section class="action-grid">
    <article class="action-card">
        <div class="image-placeholder"><span class="placeholder-badge">School fees image</span></div>
        <div class="action-content">
            <h3><i class="ph ph-graduation-cap"></i> School Fees</h3>
            <p>Settle ID, transcript, graduation, and other approved fees through a guided payment form.</p>
            <a href="school_fees.php" class="btn btn-primary">Open Fees</a>
        </div>
    </article>
    <article class="action-card">
        <div class="image-placeholder"><span class="placeholder-badge">Summer class image</span></div>
        <div class="action-content">
            <h3><i class="ph ph-sun-horizon"></i> Summer Class</h3>
            <p>Check eligibility, select available subjects, and generate your summer class assessment.</p>
            <a href="summer_class.php" class="btn btn-primary">Apply Now</a>
        </div>
    </article>
    <article class="action-card">
        <div class="image-placeholder"><span class="placeholder-badge">Merchandise image</span></div>
        <div class="action-content">
            <h3><i class="ph ph-t-shirt"></i> Materials Shop</h3>
            <p>Reserve uniforms, lanyards, and university materials for pickup at the Accounting Office.</p>
            <a href="shop.php" class="btn btn-primary">Browse Shop</a>
        </div>
    </article>
</section>

<div class="table-container">
    <div class="table-header">
        <div>
            <h2 style="font-size: 1.05rem;">Recent Transactions</h2>
            <p class="text-muted" style="font-size: 0.86rem;">Latest activity from your account.</p>
        </div>
        <a href="transactions.php" class="btn btn-outline">View All</a>
    </div>

    <?php if (empty($transactions)): ?>
        <p class="text-muted text-center" style="padding: 2rem 0;">No recent transactions found.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Ref No.</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($transactions as $txn): ?>
                    <tr>
                        <td class="mono"><?php echo htmlspecialchars($txn['reference_no']); ?></td>
                        <td><?php echo htmlspecialchars($txn['description']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($txn['transaction_date'])); ?></td>
                        <td style="font-weight: 750;">&#8369; <?php echo number_format($txn['amount'], 2); ?></td>
                        <td><span class="status <?php echo strtolower($txn['status']); ?>"><?php echo ucfirst($txn['status']); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'php/includes/footer.php'; ?>
