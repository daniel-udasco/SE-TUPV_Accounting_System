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
        
        $refNo = 'TRX-' . rand(100000, 999999);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, reference_no, description, transaction_type, amount, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$userId, $refNo, $desc, $type, $amount, $method]);
            
            echo json_encode([
                'success' => true,
                'reference_no' => $refNo,
                'message' => 'Transaction created successfully'
            ]);
        } catch (\PDOException $e) {
            echo json_encode(['error' => 'Failed to create transaction']);
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
