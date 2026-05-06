<?php
require_once 'php/includes/auth.php';
require_once 'php/db.php';
require_once 'php/includes/header.php';

// Handle Filters
$typeFilter = $_GET['type'] ?? 'all';
$syFilter = $_GET['sy'] ?? 'all';

$userId = $_SESSION['user']['id'];
$query = "SELECT * FROM transactions WHERE user_id = ?";
$params = [$userId];

if ($typeFilter !== 'all') {
    if ($typeFilter === 'fees') {
        $query .= " AND transaction_type = 'fee'";
    } elseif ($typeFilter === 'materials') {
        $query .= " AND transaction_type = 'materials'";
    }
}

// Simple mock logic for School Year filtering based on dates
// In a real app, this would use exact date ranges
if ($syFilter === 'sy2526') {
    $query .= " AND transaction_date >= '2025-06-01'";
} elseif ($syFilter === 'sy2425') {
    $query .= " AND transaction_date >= '2024-06-01' AND transaction_date < '2025-06-01'";
}

$query .= " ORDER BY transaction_date DESC";

$transactions = [];
try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $transactions = $stmt->fetchAll();
} catch (\PDOException $e) {
    // Error fetching
}
?>
<style>
    .page-header { margin-bottom: 2rem; }
    
    .filters-bar { display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem; background: var(--bg-card); padding: 1rem; border-radius: 0; border: 1px solid #e9ecef; box-shadow: var(--shadow-sm); }
    .filters-bar form { display: flex; gap: 1rem; flex-wrap: wrap; width: 100%; align-items: center; }
    .filter-group { display: flex; flex-direction: column; gap: 0.25rem; flex: 1; min-width: 200px; }
    .filter-group label { font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); }
    
    .table-container { background: var(--bg-card); border-radius: 0; padding: 1.5rem; box-shadow: var(--shadow-sm); border: 1px solid #e9ecef; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; min-width: 700px; }
    th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #e9ecef; }
    th { color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; background-color: #f8f9fa; }
    
    .status { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; font-weight: 500; display: inline-block; text-align: center; }
    .status.success, .status.completed { background-color: #d1e7dd; color: #0f5132; }
    .status.pending { background-color: #fff3cd; color: #856404; }
    .status.failed { background-color: #f8d7da; color: #842029; }
</style>

<div class="page-header">
    <h1>Transaction History</h1>
    <p class="text-muted">View and track all your past payments and financial records.</p>
</div>

<div class="filters-bar">
    <form action="transactions.php" method="GET">
        <div class="filter-group">
            <label>School Year</label>
            <select name="sy" class="form-control" style="border-radius: 0;" onchange="this.form.submit()">
                <option value="all" <?php echo $syFilter == 'all' ? 'selected' : ''; ?>>All School Years</option>
                <option value="sy2526" <?php echo $syFilter == 'sy2526' ? 'selected' : ''; ?>>S.Y. 2025-2026</option>
                <option value="sy2425" <?php echo $syFilter == 'sy2425' ? 'selected' : ''; ?>>S.Y. 2024-2025</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Transaction Type</label>
            <select name="type" class="form-control" style="border-radius: 0;" onchange="this.form.submit()">
                <option value="all" <?php echo $typeFilter == 'all' ? 'selected' : ''; ?>>All Transaction Types</option>
                <option value="fees" <?php echo $typeFilter == 'fees' ? 'selected' : ''; ?>>School Fees</option>
                <option value="materials" <?php echo $typeFilter == 'materials' ? 'selected' : ''; ?>>Materials</option>
            </select>
        </div>
        <div style="align-self: flex-end;">
            <noscript><button type="submit" class="btn btn-primary">Filter</button></noscript>
        </div>
    </form>
</div>

<div class="table-container">
    <?php if(empty($transactions)): ?>
        <p class="text-center text-muted" style="padding: 3rem 0;">No transactions found matching your criteria.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Ref No.</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($transactions as $txn): 
                    $methodText = 'GCash';
                    if ($txn['payment_method'] == 'bank') $methodText = 'Bank Transfer';
                    if ($txn['payment_method'] == 'otc') $methodText = 'Over-the-Counter Payment';
                ?>
                <tr>
                    <td style="font-family: monospace; font-weight: 500;"><?php echo htmlspecialchars($txn['reference_no']); ?></td>
                    <td><?php echo htmlspecialchars($txn['description']); ?></td>
                    <td><?php echo date('M d, Y g:i A', strtotime($txn['transaction_date'])); ?></td>
                    <td><?php echo $methodText; ?></td>
                    <td style="font-weight: 600; color: var(--tup-maroon);">₱ <?php echo number_format($txn['amount'], 2); ?></td>
                    <td><span class="status <?php echo strtolower($txn['status']); ?>"><?php echo ucfirst($txn['status']); ?></span></td>
                    <td>
                        <a href="#" class="btn btn-outline" style="padding: 0.25rem 0.75rem; font-size: 0.8rem; border-radius: 0;">View Receipt</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once 'php/includes/footer.php'; ?>
