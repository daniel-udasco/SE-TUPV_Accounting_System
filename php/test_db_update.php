<?php
require 'db.php';
try {
    $hash1 = password_hash('Udasco', PASSWORD_DEFAULT);
    $hash2 = password_hash('password', PASSWORD_DEFAULT);
    
    $pdo->exec("UPDATE users SET password_hash = '$hash1' WHERE student_id = 'TUPV-23-0050'");
    $pdo->exec("UPDATE users SET password_hash = '$hash2' WHERE student_id = 'TUPV-23-0051'");
    
    echo "Successfully forced password hashes to 'Udasco' and 'password'.\n";
    
    // Also remove this script so it's clean
    unlink(__FILE__);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
