<?php
require 'db.php';
try {
    $hash1 = password_hash('Udasco', PASSWORD_DEFAULT);
    $hash2 = password_hash('password', PASSWORD_DEFAULT);
    
    $pdo->exec("UPDATE users SET password_hash = '$hash1' WHERE student_id = 'TUPV-23-0050'");
    $pdo->exec("UPDATE users SET password_hash = '$hash2' WHERE student_id = 'TUPV-23-0051'");
    
    echo "SUCCESS_PASSWORD_FORCE_UPDATE_V2";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
