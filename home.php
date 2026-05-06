<?php
require_once 'php/includes/auth.php';
require_once 'php/db.php';
require_once 'php/includes/header.php';

// Fetch Recent Transactions
$userId = $_SESSION['user']['id'];
$transactions = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC LIMIT 5");
    $stmt->execute([$userId]);
    $transactions = $stmt->fetchAll();
} catch (\PDOException $e) {
    // Error fetching
}
?>
<style>
    .welcome-section { margin-bottom: 3rem; }
    .welcome-section h1 { font-size: 2rem; color: var(--text-main); }
    
    .dashboard-actions { display: flex; flex-direction: column; gap: 2rem; margin-bottom: 4rem; }
    
    .action-row { display: flex; flex-direction: column; background: var(--bg-card); border-radius: var(--border-radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); border: 1px solid #e9ecef; }
    @media (min-width: 900px) {
        .action-row { flex-direction: row; align-items: stretch; }
    }
    
    .action-img { flex: 0 0 300px; background-color: #ddd; display: flex; align-items: center; justify-content: center; color: #888; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; min-height: 200px; }
    .action-content { flex: 1; padding: 2.5rem; display: flex; flex-direction: column; justify-content: center; }
    .action-content h3 { color: var(--tup-maroon); font-size: 1.5rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
    .action-content p { color: var(--text-muted); margin-bottom: 1.5rem; font-size: 1.05rem; }
    
    .table-container { background: var(--bg-card); border-radius: var(--border-radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm); border: 1px solid #e9ecef; }
    .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #e9ecef; }
    th { color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; }
    .status { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 500; }
    .status.success, .status.completed { background-color: #d1e7dd; color: #0f5132; }
    .status.pending { background-color: #fff3cd; color: #856404; }
    .status.failed { background-color: #f8d7da; color: #842029; }
</style>

<div class="welcome-section">
    <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['user']['first_name']); ?> 👋</h1>
    <p class="text-muted">Select an action below to proceed with your university transactions.</p>
</div>

<div class="dashboard-actions">
    <!-- School Fees -->
    <div class="action-row">
        <div class="action-img">[School Fees Image]</div>
        <div class="action-content">
            <h3><i class="ph ph-graduation-cap"></i> Pay School Fees</h3>
            <p>Settle your university fees securely online. View required payments and choose your preferred digital payment method.</p>
            <div><a href="school_fees.php" class="btn btn-primary">Proceed to Fees</a></div>
        </div>
    </div>
    
    <!-- Summer Class -->
    <div class="action-row">
        <div class="action-img">[Summer Class Image]</div>
        <div class="action-content">
            <h3><i class="ph ph-sun-horizon"></i> Apply for Summer Class</h3>
            <p>Check your eligibility and enroll in available summer subjects to advance your coursework.</p>
            <div><a href="summer_class.php" class="btn btn-primary">Proceed to Application</a></div>
        </div>
    </div>
    
    <!-- Materials Shop -->
    <div class="action-row">
        <div class="action-img">[Shop Image]</div>
        <div class="action-content">
            <h3><i class="ph ph-t-shirt"></i> Browse Materials Shop</h3>
            <p>Purchase official university uniforms, lanyards, and other required materials directly from the accounting office.</p>
            <div><a href="shop.php" class="btn btn-primary">Proceed to Shop</a></div>
        </div>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h2 style="font-size: 1.25rem; color: var(--text-main);">Recent Transactions</h2>
        <a href="transactions.php" class="btn btn-outline" style="padding: 0.4rem 1rem; font-size: 0.85rem;">View All</a>
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
                        <td style="font-family: monospace;"><?php echo htmlspecialchars($txn['reference_no']); ?></td>
                        <td><?php echo htmlspecialchars($txn['description']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($txn['transaction_date'])); ?></td>
                        <td style="font-weight: 600;">₱ <?php echo number_format($txn['amount'], 2); ?></td>
                        <td><span class="status <?php echo strtolower($txn['status']); ?>"><?php echo ucfirst($txn['status']); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'php/includes/footer.php'; ?>
