<?php
require_once 'php/includes/admin_auth.php';

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'update_student') {
            $userId = (int)($_POST['user_id'] ?? 0);
            $course = trim($_POST['course'] ?? '');
            $yearLevel = $_POST['year_level'] === '' ? null : (int)$_POST['year_level'];
            $eligible = isset($_POST['is_eligible_summer_class']) ? 1 : 0;
            $scholar = isset($_POST['is_ched_scholar']) ? 1 : 0;

            $stmt = $pdo->prepare("
                UPDATE users
                SET course = ?, year_level = ?, is_eligible_summer_class = ?, is_ched_scholar = ?
                WHERE id = ?
            ");
            $stmt->execute([$course, $yearLevel, $eligible, $scholar, $userId]);
            $successMsg = "Student profile updated.";
        }

        if ($action === 'update_product') {
            $productId = (int)($_POST['product_id'] ?? 0);
            $price = (float)($_POST['price'] ?? 0);
            $stock = (int)($_POST['stock_quantity'] ?? 0);

            $stmt = $pdo->prepare("UPDATE products SET price = ?, stock_quantity = ? WHERE id = ?");
            $stmt->execute([$price, $stock, $productId]);
            $successMsg = "Material item updated.";
        }

        if ($action === 'update_subject') {
            $subjectId = (int)($_POST['subject_id'] ?? 0);
            $fee = (float)($_POST['fee'] ?? 0);
            $totalSlots = max(0, (int)($_POST['total_slots'] ?? 0));
            $availableSlots = max(0, (int)($_POST['available_slots'] ?? 0));
            $availableSlots = min($availableSlots, $totalSlots);

            $stmt = $pdo->prepare("UPDATE summer_subjects SET fee = ?, total_slots = ?, available_slots = ? WHERE id = ?");
            $stmt->execute([$fee, $totalSlots, $availableSlots, $subjectId]);
            $successMsg = "Summer subject updated.";
        }
    } catch (\PDOException $e) {
        $errorMsg = "Unable to save changes.";
    }
}

$search = trim($_GET['search'] ?? '');
$selectedUserId = (int)($_GET['student'] ?? 0);
$students = [];
$selectedStudent = null;
$studentTransactions = [];
$studentFeedbacks = [];
$products = [];
$subjects = [];

try {
    $query = "
        SELECT u.*,
            COUNT(t.id) AS transaction_count,
            COALESCE(SUM(t.amount), 0) AS total_amount,
            COALESCE(SUM(t.status = 'pending'), 0) AS pending_count
        FROM users u
        LEFT JOIN transactions t ON t.user_id = u.id
        WHERE 1 = 1
    ";
    $params = [];
    if ($search !== '') {
        $query .= " AND (u.student_id LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.course LIKE ?)";
        $term = '%' . $search . '%';
        array_push($params, $term, $term, $term, $term);
    }
    $query .= " GROUP BY u.id ORDER BY u.last_name, u.first_name LIMIT 120";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $students = $stmt->fetchAll();

    if ($selectedUserId <= 0 && !empty($students)) {
        $selectedUserId = (int)$students[0]['id'];
    }

    if ($selectedUserId > 0) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$selectedUserId]);
        $selectedStudent = $stmt->fetch();

        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC LIMIT 12");
        $stmt->execute([$selectedUserId]);
        $studentTransactions = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT * FROM feedbacks WHERE user_id = ? ORDER BY created_at DESC LIMIT 8");
        $stmt->execute([$selectedUserId]);
        $studentFeedbacks = $stmt->fetchAll();
    }

    $products = $pdo->query("SELECT * FROM products ORDER BY id ASC")->fetchAll();
    $subjects = $pdo->query("SELECT * FROM summer_subjects ORDER BY subject_code ASC LIMIT 16")->fetchAll();
} catch (\PDOException $e) {
    $errorMsg = $errorMsg ?: "Unable to load student records.";
}

require_once 'php/includes/admin_header.php';
?>

<div class="page-header">
    <div>
        <span class="eyebrow admin-eyebrow"><i class="ph ph-student"></i> Student records</span>
        <h1>Student Profiles</h1>
        <p class="text-muted">View account history, update eligibility, and manage student-facing materials and subjects.</p>
    </div>
</div>

<?php if ($successMsg): ?><div class="alert alert-success"><i class="ph-fill ph-check-circle"></i> <?php echo htmlspecialchars($successMsg); ?></div><?php endif; ?>
<?php if ($errorMsg): ?><div class="alert alert-danger"><i class="ph-fill ph-warning-circle"></i> <?php echo htmlspecialchars($errorMsg); ?></div><?php endif; ?>

<div class="filters-bar admin-filter">
    <form action="admin_students.php" method="GET">
        <div class="filter-group filter-grow">
            <label for="search">Find Student</label>
            <input type="search" id="search" name="search" class="form-control" value="<?php echo htmlspecialchars($search); ?>" placeholder="Student ID, name, or course">
        </div>
        <button type="submit" class="btn btn-outline">Search</button>
    </form>
</div>

<div class="admin-two-column student-admin-grid">
    <div class="table-container">
        <div class="table-header">
            <div>
                <h2 style="font-size: 1.05rem;">Student Directory</h2>
                <p class="text-muted" style="font-size: 0.86rem;">Select a student to view their transaction profile.</p>
            </div>
        </div>
        <div class="student-list">
            <?php foreach ($students as $student): ?>
            <a class="student-row <?php echo (int)$student['id'] === $selectedUserId ? 'active' : ''; ?>" href="admin_students.php?<?php echo htmlspecialchars(http_build_query(['search' => $search, 'student' => $student['id']])); ?>">
                <div>
                    <strong><?php echo htmlspecialchars($student['last_name'] . ', ' . $student['first_name']); ?></strong>
                    <span class="mono"><?php echo htmlspecialchars($student['student_id']); ?> · <?php echo htmlspecialchars($student['course'] ?? 'No course'); ?></span>
                </div>
                <div style="text-align: right;">
                    <strong>&#8369; <?php echo number_format((float)$student['total_amount'], 2); ?></strong>
                    <span><?php echo number_format((int)$student['pending_count']); ?> pending</span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="stacked-panels">
        <?php if ($selectedStudent): ?>
        <div class="panel">
            <div class="panel-header">
                <div>
                    <h2 style="font-size: 1.05rem;"><?php echo htmlspecialchars($selectedStudent['first_name'] . ' ' . $selectedStudent['last_name']); ?></h2>
                    <p class="text-muted mono" style="font-size: 0.86rem;"><?php echo htmlspecialchars($selectedStudent['student_id']); ?></p>
                </div>
                <a href="admin_transactions.php?search=<?php echo urlencode($selectedStudent['student_id']); ?>" class="btn btn-outline">Open Transactions</a>
            </div>
            <div class="panel-body">
                <form action="admin_students.php?<?php echo htmlspecialchars(http_build_query(['search' => $search, 'student' => $selectedUserId])); ?>" method="POST" class="admin-form-grid two">
                    <input type="hidden" name="action" value="update_student">
                    <input type="hidden" name="user_id" value="<?php echo (int)$selectedStudent['id']; ?>">
                    <div class="form-group">
                        <label class="form-label" for="course">Course</label>
                        <input class="form-control" id="course" name="course" value="<?php echo htmlspecialchars($selectedStudent['course'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="year_level">Year Level</label>
                        <select class="form-control" id="year_level" name="year_level">
                            <option value="">Not set</option>
                            <?php for ($year = 1; $year <= 5; $year++): ?>
                            <option value="<?php echo $year; ?>" <?php echo (int)($selectedStudent['year_level'] ?? 0) === $year ? 'selected' : ''; ?>><?php echo $year; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <label class="checkbox-row">
                        <input type="checkbox" name="is_eligible_summer_class" <?php echo (int)$selectedStudent['is_eligible_summer_class'] === 1 ? 'checked' : ''; ?>>
                        Eligible for summer class
                    </label>
                    <label class="checkbox-row">
                        <input type="checkbox" name="is_ched_scholar" <?php echo (int)$selectedStudent['is_ched_scholar'] === 1 ? 'checked' : ''; ?>>
                        CHED scholar
                    </label>
                    <div>
                        <button class="btn btn-primary admin-btn" type="submit"><i class="ph ph-floppy-disk"></i> Save Profile</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h2 style="font-size: 1.05rem;">Recent Student Transactions</h2>
            </div>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($studentTransactions as $txn): ?>
                        <tr>
                            <td class="mono"><?php echo htmlspecialchars($txn['reference_no']); ?></td>
                            <td><?php echo htmlspecialchars($txn['description']); ?></td>
                            <td>&#8369; <?php echo number_format((float)$txn['amount'], 2); ?></td>
                            <td><span class="status <?php echo $txn['status']; ?>"><?php echo ucfirst($txn['status']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h2 style="font-size: 1.05rem;">Student Feedback</h2>
            </div>
            <div class="panel-body compact-list">
                <?php if (empty($studentFeedbacks)): ?><p class="text-muted">No feedback submitted by this student.</p><?php endif; ?>
                <?php foreach ($studentFeedbacks as $feedback): ?>
                <div class="compact-list-item">
                    <strong><?php echo htmlspecialchars($feedback['subject']); ?></strong>
                    <span><?php echo ucfirst($feedback['status']); ?> · <?php echo date('M d, Y', strtotime($feedback['created_at'])); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="admin-two-column" style="margin-top: 1.25rem;">
    <div class="panel" id="materials">
        <div class="panel-header">
            <div>
                <h2 style="font-size: 1.05rem;">Materials Inventory</h2>
                <p class="text-muted" style="font-size: 0.86rem;">Edit shop prices and available stock.</p>
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr><th>Item</th><th>Price</th><th>Stock</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <form action="admin_students.php#materials" method="POST">
                            <input type="hidden" name="action" value="update_product">
                            <input type="hidden" name="product_id" value="<?php echo (int)$product['id']; ?>">
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><input type="number" class="form-control compact-control" name="price" min="0" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>"></td>
                            <td><input type="number" class="form-control compact-control" name="stock_quantity" min="0" value="<?php echo (int)$product['stock_quantity']; ?>"></td>
                            <td><button class="btn btn-primary admin-btn compact-btn" type="submit">Save</button></td>
                        </form>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 style="font-size: 1.05rem;">Summer Subject Slots</h2>
                <p class="text-muted" style="font-size: 0.86rem;">First 16 records shown for quick edits.</p>
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr><th>Code</th><th>Fee</th><th>Total</th><th>Available</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($subjects as $subject): ?>
                    <tr>
                        <form action="admin_students.php" method="POST">
                            <input type="hidden" name="action" value="update_subject">
                            <input type="hidden" name="subject_id" value="<?php echo (int)$subject['id']; ?>">
                            <td><strong><?php echo htmlspecialchars($subject['subject_code']); ?></strong></td>
                            <td><input type="number" class="form-control compact-control" name="fee" min="0" step="0.01" value="<?php echo htmlspecialchars($subject['fee']); ?>"></td>
                            <td><input type="number" class="form-control compact-control" name="total_slots" min="0" value="<?php echo (int)$subject['total_slots']; ?>"></td>
                            <td><input type="number" class="form-control compact-control" name="available_slots" min="0" value="<?php echo (int)$subject['available_slots']; ?>"></td>
                            <td><button class="btn btn-primary admin-btn compact-btn" type="submit">Save</button></td>
                        </form>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'php/includes/admin_footer.php'; ?>
