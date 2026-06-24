<?php
require_once 'php/includes/admin_auth.php';

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'update_transaction') {
            $transactionId = (int)($_POST['transaction_id'] ?? 0);
            $status = $_POST['status'] ?? 'pending';
            $amount = (float)($_POST['amount'] ?? 0);
            $adminNote = trim($_POST['admin_note'] ?? '');
            $allowedStatuses = ['pending', 'completed', 'failed'];

            if ($transactionId > 0 && in_array($status, $allowedStatuses, true) && $amount >= 0) {
                $stmt = $pdo->prepare("
                    UPDATE transactions
                    SET status = ?, amount = ?, admin_note = ?, processed_by = ?, processed_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");
                $stmt->execute([$status, $amount, $adminNote, $_SESSION['admin']['id'], $transactionId]);
                $successMsg = "Transaction updated successfully.";
            }
        }

        if ($action === 'create_transaction') {
            $userId = (int)($_POST['user_id'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $type = $_POST['transaction_type'] ?? 'fee';
            $method = $_POST['payment_method'] ?? 'otc';
            $status = $_POST['status'] ?? 'completed';
            $amount = (float)($_POST['amount'] ?? 0);
            $allowedTypes = ['fee', 'materials', 'summer_class'];
            $allowedMethods = ['gcash', 'bank', 'otc'];
            $allowedStatuses = ['pending', 'completed', 'failed'];

            if ($userId > 0 && $description !== '' && $amount >= 0 && in_array($type, $allowedTypes, true) && in_array($method, $allowedMethods, true) && in_array($status, $allowedStatuses, true)) {
                $refNo = 'ADM-' . date('ymd') . '-' . random_int(1000, 9999);
                $stmt = $pdo->prepare("
                    INSERT INTO transactions
                        (user_id, reference_no, description, transaction_type, amount, payment_method, status, processed_by, admin_note, processed_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)
                ");
                $stmt->execute([$userId, $refNo, $description, $type, $amount, $method, $status, $_SESSION['admin']['id'], 'Created by staff console']);
                $successMsg = "Manual transaction created with reference {$refNo}.";
            } else {
                $errorMsg = "Please complete the manual transaction form.";
            }
        }
    } catch (\PDOException $e) {
        $errorMsg = "Unable to save transaction changes.";
    }
}

$statusFilter = $_GET['status'] ?? 'all';
$typeFilter = $_GET['type'] ?? 'all';
$search = trim($_GET['search'] ?? '');
$transactions = [];
$students = [];

$query = "
    SELECT t.*, u.student_id, u.first_name, u.last_name, s.first_name AS staff_first_name, s.last_name AS staff_last_name
    FROM transactions t
    JOIN users u ON u.id = t.user_id
    LEFT JOIN accounting_staff s ON s.id = t.processed_by
    WHERE 1 = 1
";
$params = [];

if (in_array($statusFilter, ['pending', 'completed', 'failed'], true)) {
    $query .= " AND t.status = ?";
    $params[] = $statusFilter;
}

if (in_array($typeFilter, ['fee', 'materials', 'summer_class'], true)) {
    $query .= " AND t.transaction_type = ?";
    $params[] = $typeFilter;
}

if ($search !== '') {
    $query .= " AND (t.reference_no LIKE ? OR t.description LIKE ? OR u.student_id LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
    $term = '%' . $search . '%';
    array_push($params, $term, $term, $term, $term, $term);
}

$query .= " ORDER BY t.transaction_date DESC LIMIT 150";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $transactions = $stmt->fetchAll();
    $students = $pdo->query("SELECT id, student_id, first_name, last_name FROM users ORDER BY last_name, first_name")->fetchAll();
} catch (\PDOException $e) {
    $errorMsg = $errorMsg ?: "Unable to load transactions.";
}

require_once 'php/includes/admin_header.php';
?>

<div class="page-header">
    <div>
        <span class="eyebrow admin-eyebrow"><i class="ph ph-receipt"></i> Transaction operations</span>
        <h1>Process Transactions</h1>
        <p class="text-muted">Review, correct, complete, fail, or create student accounting records.</p>
    </div>
    <button type="button" class="btn btn-primary admin-btn" onclick="document.getElementById('manualForm').scrollIntoView({behavior: 'smooth'});">
        <i class="ph ph-plus"></i> Manual Entry
    </button>
</div>

<?php if ($successMsg): ?><div class="alert alert-success"><i class="ph-fill ph-check-circle"></i> <?php echo htmlspecialchars($successMsg); ?></div><?php endif; ?>
<?php if ($errorMsg): ?><div class="alert alert-danger"><i class="ph-fill ph-warning-circle"></i> <?php echo htmlspecialchars($errorMsg); ?></div><?php endif; ?>

<div class="filters-bar admin-filter">
    <form action="admin_transactions.php" method="GET">
        <div class="filter-group">
            <label for="status">Status</label>
            <select id="status" name="status" class="form-control" onchange="this.form.submit()">
                <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                <option value="failed" <?php echo $statusFilter === 'failed' ? 'selected' : ''; ?>>Failed</option>
            </select>
        </div>
        <div class="filter-group">
            <label for="type">Type</label>
            <select id="type" name="type" class="form-control" onchange="this.form.submit()">
                <option value="all" <?php echo $typeFilter === 'all' ? 'selected' : ''; ?>>All Types</option>
                <option value="fee" <?php echo $typeFilter === 'fee' ? 'selected' : ''; ?>>School Fees</option>
                <option value="materials" <?php echo $typeFilter === 'materials' ? 'selected' : ''; ?>>Materials</option>
                <option value="summer_class" <?php echo $typeFilter === 'summer_class' ? 'selected' : ''; ?>>Summer Class</option>
            </select>
        </div>
        <div class="filter-group filter-grow">
            <label for="search">Search</label>
            <input type="search" id="search" name="search" class="form-control" value="<?php echo htmlspecialchars($search); ?>" placeholder="Reference, student, description">
        </div>
        <button type="submit" class="btn btn-outline">Filter</button>
    </form>
</div>

<div class="table-container">
    <div class="table-header">
        <div>
            <h2 style="font-size: 1.05rem;"><?php echo count($transactions); ?> transaction<?php echo count($transactions) === 1 ? '' : 's'; ?></h2>
            <p class="text-muted" style="font-size: 0.86rem;">Showing latest matching records.</p>
        </div>
    </div>
    <div style="overflow-x: auto;">
        <table class="admin-edit-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Reference</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Staff Note</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $txn): ?>
                <tr>
                    <form action="admin_transactions.php?<?php echo htmlspecialchars(http_build_query($_GET)); ?>" method="POST">
                        <input type="hidden" name="action" value="update_transaction">
                        <input type="hidden" name="transaction_id" value="<?php echo (int)$txn['id']; ?>">
                        <td>
                            <strong><?php echo htmlspecialchars($txn['first_name'] . ' ' . $txn['last_name']); ?></strong><br>
                            <span class="text-muted mono" style="font-size: 0.76rem;"><?php echo htmlspecialchars($txn['student_id']); ?></span>
                        </td>
                        <td>
                            <span class="mono" style="font-weight: 800;"><?php echo htmlspecialchars($txn['reference_no']); ?></span><br>
                            <span class="text-muted" style="font-size: 0.76rem;"><?php echo date('M d, Y', strtotime($txn['transaction_date'])); ?></span>
                        </td>
                        <td><?php echo htmlspecialchars($txn['description']); ?></td>
                        <td><?php echo ucwords(str_replace('_', ' ', $txn['transaction_type'])); ?></td>
                        <td><input type="number" name="amount" class="form-control compact-control" step="0.01" min="0" value="<?php echo htmlspecialchars($txn['amount']); ?>"></td>
                        <td>
                            <select name="status" class="form-control compact-control">
                                <option value="pending" <?php echo $txn['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="completed" <?php echo $txn['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="failed" <?php echo $txn['status'] === 'failed' ? 'selected' : ''; ?>>Failed</option>
                            </select>
                        </td>
                        <td><textarea name="admin_note" class="form-control compact-note" placeholder="Internal note"><?php echo htmlspecialchars($txn['admin_note'] ?? ''); ?></textarea></td>
                        <td><button type="submit" class="btn btn-primary admin-btn compact-btn"><i class="ph ph-floppy-disk"></i> Save</button></td>
                    </form>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel" id="manualForm" style="margin-top: 1.25rem;">
    <div class="panel-header">
        <div>
            <h2 style="font-size: 1.05rem;">Manual Transaction Entry</h2>
            <p class="text-muted" style="font-size: 0.86rem;">For over-the-counter receipts and staff-created adjustments.</p>
        </div>
    </div>
    <div class="panel-body">
        <form action="admin_transactions.php" method="POST" class="admin-form-grid">
            <input type="hidden" name="action" value="create_transaction">
            <div class="form-group">
                <label class="form-label" for="user_id">Student</label>
                <select class="form-control" id="user_id" name="user_id" required>
                    <option value="" disabled selected>Select student...</option>
                    <?php foreach ($students as $student): ?>
                    <option value="<?php echo (int)$student['id']; ?>"><?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name'] . ' - ' . $student['student_id']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <input class="form-control" id="description" name="description" placeholder="e.g. ID replacement fee" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="transaction_type">Type</label>
                <select class="form-control" id="transaction_type" name="transaction_type" required>
                    <option value="fee">School Fee</option>
                    <option value="materials">Materials</option>
                    <option value="summer_class">Summer Class</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="payment_method">Method</label>
                <select class="form-control" id="payment_method" name="payment_method" required>
                    <option value="otc">Over-the-Counter</option>
                    <option value="gcash">GCash</option>
                    <option value="bank">Bank Transfer</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="manual_status">Status</label>
                <select class="form-control" id="manual_status" name="status" required>
                    <option value="completed">Completed</option>
                    <option value="pending">Pending</option>
                    <option value="failed">Failed</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="amount">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" min="0" step="0.01" required>
            </div>
            <div style="display: flex; align-items: end;">
                <button type="submit" class="btn btn-primary admin-btn"><i class="ph ph-plus"></i> Create Record</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'php/includes/admin_footer.php'; ?>
