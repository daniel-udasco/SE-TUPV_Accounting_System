<?php
// api.php - Basic API endpoint for handling requests
header('Content-Type: application/json');
require_once 'db.php';

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Process new transaction (Mock implementation)
    if ($action === 'create_transaction') {
        $userId = $input['user_id'] ?? 1; // Default to user 1 for prototype
        $desc = $input['description'] ?? 'Transaction';
        $type = $input['type'] ?? 'fee';
        $amount = $input['amount'] ?? 0;
        $method = $input['method'] ?? 'gcash';
        $subjectIds = $input['subject_ids'] ?? [];
        
        $refNo = 'TRX-' . rand(100000, 999999);
        
        try {
            $pdo->beginTransaction();
            
            // Insert transaction as 'completed'
            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, reference_no, description, transaction_type, amount, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, 'completed')");
            $stmt->execute([$userId, $refNo, $desc, $type, $amount, $method]);
            $txnId = $pdo->lastInsertId();
            
            // If summer class, decrement slots and insert enrollments
            if ($type === 'summer_class' && !empty($subjectIds)) {
                $updateSubj = $pdo->prepare("UPDATE summer_subjects SET available_slots = GREATEST(0, available_slots - 1) WHERE id = ?");
                $insertEnroll = $pdo->prepare("INSERT INTO enrollments (user_id, subject_id, transaction_id, status) VALUES (?, ?, ?, 'enrolled')");
                
                foreach ($subjectIds as $subjId) {
                    $updateSubj->execute([$subjId]);
                    $insertEnroll->execute([$userId, $subjId, $txnId]);
                }
            }
            
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'reference_no' => $refNo,
                'message' => 'Transaction created successfully'
            ]);
        } catch (\PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo json_encode(['error' => 'Failed to create transaction: ' . $e->getMessage()]);
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch transaction history
    if ($action === 'get_transactions') {
        $userId = $_GET['user_id'] ?? 1;
        
        try {
            $stmt = $pdo->prepare("SELECT reference_no, description, transaction_type, amount, payment_method, status, DATE_FORMAT(transaction_date, '%b %d, %Y') as date FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC");
            $stmt->execute([$userId]);
            $transactions = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'data' => $transactions]);
        } catch (\PDOException $e) {
            echo json_encode(['error' => 'Failed to fetch transactions']);
        }
        exit;
    }
}

echo json_encode(['error' => 'Invalid action']);
?>
