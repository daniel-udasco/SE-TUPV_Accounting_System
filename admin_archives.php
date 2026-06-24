<?php
require_once 'php/includes/admin_auth.php';

$typeFilter = $_GET['type'] ?? 'all';
$statusFilter = $_GET['status'] ?? 'completed';
$fromDate = $_GET['from'] ?? date('Y-m-01');
$toDate = $_GET['to'] ?? date('Y-m-d');
$transactions = [];
$summary = ['count' => 0, 'total' => 0];

$query = "
    SELECT t.*, u.student_id, u.first_name, u.last_name, u.course
    FROM transactions t
    JOIN users u ON u.id = t.user_id
    WHERE DATE(t.transaction_date) BETWEEN ? AND ?
";
$params = [$fromDate, $toDate];

if (in_array($typeFilter, ['fee', 'materials', 'summer_class'], true)) {
    $query .= " AND t.transaction_type = ?";
    $params[] = $typeFilter;
}

if (in_array($statusFilter, ['pending', 'completed', 'failed'], true)) {
    $query .= " AND t.status = ?";
    $params[] = $statusFilter;
}

$query .= " ORDER BY t.transaction_date ASC, t.reference_no ASC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $transactions = $stmt->fetchAll();
    foreach ($transactions as $txn) {
        $summary['count']++;
        $summary['total'] += (float)$txn['amount'];
    }
} catch (\PDOException $e) {
    // Printable page still loads with empty state.
}

require_once 'php/includes/admin_header.php';
?>

<div class="page-header print-hidden">
    <div>
        <span class="eyebrow admin-eyebrow"><i class="ph ph-printer"></i> Archive records</span>
        <h1>Print Transaction Lists</h1>
        <p class="text-muted">Generate clean transaction lists for Accounting Office filing and PDF export.</p>
    </div>
    <button type="button" class="btn btn-primary admin-btn" onclick="window.print()"><i class="ph ph-printer"></i> Print / Save PDF</button>
</div>

<div class="filters-bar admin-filter print-hidden">
    <form action="admin_archives.php" method="GET">
        <div class="filter-group">
            <label for="from">From</label>
            <input type="date" id="from" name="from" class="form-control" value="<?php echo htmlspecialchars($fromDate); ?>">
        </div>
        <div class="filter-group">
            <label for="to">To</label>
            <input type="date" id="to" name="to" class="form-control" value="<?php echo htmlspecialchars($toDate); ?>">
        </div>
        <div class="filter-group">
            <label for="type">Type</label>
            <select id="type" name="type" class="form-control">
                <option value="all" <?php echo $typeFilter === 'all' ? 'selected' : ''; ?>>All Types</option>
                <option value="fee" <?php echo $typeFilter === 'fee' ? 'selected' : ''; ?>>School Fees</option>
                <option value="materials" <?php echo $typeFilter === 'materials' ? 'selected' : ''; ?>>Materials</option>
                <option value="summer_class" <?php echo $typeFilter === 'summer_class' ? 'selected' : ''; ?>>Summer Class</option>
            </select>
        </div>
        <div class="filter-group">
            <label for="status">Status</label>
            <select id="status" name="status" class="form-control">
                <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="failed" <?php echo $statusFilter === 'failed' ? 'selected' : ''; ?>>Failed</option>
            </select>
        </div>
        <button type="submit" class="btn btn-outline">Generate</button>
    </form>
</div>

<section class="archive-sheet">
    <div class="archive-heading">
        <img src="assets/TUPVAS_logo.svg" alt="TUPVAS Logo">
        <div>
            <h1>Technological University of the Philippines Visayas</h1>
            <h2>Accounting Office Transaction Archive</h2>
            <p><?php echo date('F d, Y', strtotime($fromDate)); ?> to <?php echo date('F d, Y', strtotime($toDate)); ?></p>
        </div>
    </div>

    <div class="archive-summary">
        <div><span>Records</span><strong><?php echo number_format($summary['count']); ?></strong></div>
        <div><span>Total Amount</span><strong>&#8369; <?php echo number_format($summary['total'], 2); ?></strong></div>
        <div><span>Type</span><strong><?php echo $typeFilter === 'all' ? 'All' : ucwords(str_replace('_', ' ', $typeFilter)); ?></strong></div>
        <div><span>Status</span><strong><?php echo $statusFilter === 'all' ? 'All' : ucfirst($statusFilter); ?></strong></div>
    </div>

    <table class="archive-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Reference</th>
                <th>Student</th>
                <th>Description</th>
                <th>Type</th>
                <th>Method</th>
                <th>Status</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $txn): ?>
            <tr>
                <td><?php echo date('Y-m-d', strtotime($txn['transaction_date'])); ?></td>
                <td class="mono"><?php echo htmlspecialchars($txn['reference_no']); ?></td>
                <td><?php echo htmlspecialchars($txn['last_name'] . ', ' . $txn['first_name']); ?><br><span class="mono"><?php echo htmlspecialchars($txn['student_id']); ?></span></td>
                <td><?php echo htmlspecialchars($txn['description']); ?></td>
                <td><?php echo ucwords(str_replace('_', ' ', $txn['transaction_type'])); ?></td>
                <td><?php echo strtoupper($txn['payment_method']); ?></td>
                <td><?php echo ucfirst($txn['status']); ?></td>
                <td>&#8369; <?php echo number_format((float)$txn['amount'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="archive-signatures">
        <div>
            <span>Prepared by</span>
            <strong><?php echo htmlspecialchars($_SESSION['admin']['first_name'] . ' ' . $_SESSION['admin']['last_name']); ?></strong>
        </div>
        <div>
            <span>Verified by</span>
            <strong>Accounting Office Head</strong>
        </div>
    </div>
</section>

<?php require_once 'php/includes/admin_footer.php'; ?>
