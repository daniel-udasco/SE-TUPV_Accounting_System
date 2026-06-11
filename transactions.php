<?php
require_once 'php/includes/auth.php';
require_once 'php/db.php';
require_once 'php/includes/header.php';

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
    } elseif ($typeFilter === 'summer_class') {
        $query .= " AND transaction_type = 'summer_class'";
    }
}

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
    // Error fetching transactions.
}
?>

<div class="page-header">
    <div>
        <span class="eyebrow"><i class="ph ph-clock-counter-clockwise"></i> Records</span>
        <h1>Transaction History</h1>
        <p class="text-muted">Track all payment attempts, receipts, and Accounting Office confirmations.</p>
    </div>
    <a href="school_fees.php" class="btn btn-primary"><i class="ph ph-plus"></i> New Payment</a>
</div>

<div class="filters-bar">
    <form action="transactions.php" method="GET">
        <div class="filter-group">
            <label for="sy">School Year</label>
            <select id="sy" name="sy" class="form-control" onchange="this.form.submit()">
                <option value="all" <?php echo $syFilter == 'all' ? 'selected' : ''; ?>>All School Years</option>
                <option value="sy2526" <?php echo $syFilter == 'sy2526' ? 'selected' : ''; ?>>S.Y. 2025-2026</option>
                <option value="sy2425" <?php echo $syFilter == 'sy2425' ? 'selected' : ''; ?>>S.Y. 2024-2025</option>
            </select>
        </div>
        <div class="filter-group">
            <label for="type">Transaction Type</label>
            <select id="type" name="type" class="form-control" onchange="this.form.submit()">
                <option value="all" <?php echo $typeFilter == 'all' ? 'selected' : ''; ?>>All Transaction Types</option>
                <option value="fees" <?php echo $typeFilter == 'fees' ? 'selected' : ''; ?>>School Fees</option>
                <option value="summer_class" <?php echo $typeFilter == 'summer_class' ? 'selected' : ''; ?>>Summer Class</option>
                <option value="materials" <?php echo $typeFilter == 'materials' ? 'selected' : ''; ?>>Materials</option>
            </select>
        </div>
        <noscript><button type="submit" class="btn btn-primary">Filter</button></noscript>
    </form>
</div>

<div class="table-container">
    <div class="table-header">
        <div>
            <h2 style="font-size: 1.05rem;"><?php echo count($transactions); ?> record<?php echo count($transactions) === 1 ? '' : 's'; ?></h2>
            <p class="text-muted" style="font-size: 0.86rem;">Filtered by your selected school year and type.</p>
        </div>
    </div>

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
                    if ($txn['payment_method'] == 'otc') $methodText = 'Over-the-Counter';
                ?>
                <tr>
                    <td class="mono" style="font-weight: 700;"><?php echo htmlspecialchars($txn['reference_no']); ?></td>
                    <td><?php echo htmlspecialchars($txn['description']); ?></td>
                    <td><?php echo date('M d, Y g:i A', strtotime($txn['transaction_date'])); ?></td>
                    <td><?php echo $methodText; ?></td>
                    <td style="font-weight: 800; color: var(--tup-maroon);">&#8369; <?php echo number_format($txn['amount'], 2); ?></td>
                    <td><span class="status <?php echo strtolower($txn['status']); ?>"><?php echo ucfirst($txn['status']); ?></span></td>
                    <td>
                        <a href="#" class="btn btn-outline" style="min-height: 32px; padding: 0.4rem 0.65rem; font-size: 0.78rem;">Receipt</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once 'php/includes/footer.php'; ?>
