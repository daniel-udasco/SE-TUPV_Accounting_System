<?php
require_once __DIR__ . '/../db.php';

function admin_column_exists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = ?
          AND COLUMN_NAME = ?
    ");
    $stmt->execute([$table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

function admin_table_exists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = ?
    ");
    $stmt->execute([$table]);
    return (int)$stmt->fetchColumn() > 0;
}

function admin_ensure_tables(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS accounting_staff (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            role VARCHAR(80) NOT NULL DEFAULT 'Accounting Staff',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS office_info (
            id INT PRIMARY KEY,
            office_name VARCHAR(120) NOT NULL,
            location TEXT NOT NULL,
            operating_hours VARCHAR(160) NOT NULL,
            services TEXT NOT NULL,
            email VARCHAR(120) NOT NULL,
            phone VARCHAR(80) NOT NULL,
            facebook_url VARCHAR(255) NOT NULL,
            messenger_url VARCHAR(255) NOT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");

    if (!admin_column_exists($pdo, 'transactions', 'processed_by')) {
        $pdo->exec("ALTER TABLE transactions ADD COLUMN processed_by INT NULL AFTER status");
    }

    if (!admin_column_exists($pdo, 'transactions', 'admin_note')) {
        $pdo->exec("ALTER TABLE transactions ADD COLUMN admin_note TEXT NULL AFTER processed_by");
    }

    if (!admin_column_exists($pdo, 'transactions', 'processed_at')) {
        $pdo->exec("ALTER TABLE transactions ADD COLUMN processed_at TIMESTAMP NULL AFTER admin_note");
    }

    $staff = [
        ['Celeste', 'Delumpa', 'Accountant III / Head, Accounting Office'],
        ['Jazer', 'Frias', 'Accountant II'],
        ['Jorjet', 'Abad', 'Administrative Aide VI'],
        ['Romena', 'Esidenio', 'Administrative Aide III'],
    ];

    $insertStaff = $pdo->prepare("
        INSERT IGNORE INTO accounting_staff (username, password_hash, first_name, last_name, role)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($staff as [$firstName, $lastName, $role]) {
        $insertStaff->execute([
            strtolower($firstName),
            password_hash($lastName, PASSWORD_DEFAULT),
            $firstName,
            $lastName,
            $role,
        ]);
    }

    $pdo->exec("
        INSERT IGNORE INTO office_info
            (id, office_name, location, operating_hours, services, email, phone, facebook_url, messenger_url)
        VALUES
            (1,
             'TUPV Accounting Office',
             'Admin Building, Ground Floor, Room 101, TUP Visayas, Capt. Sabi St., Brgy. Zone 12, Talisay City.',
             'Monday to Friday, 8:00 AM to 5:00 PM. Closed during weekends and declared holidays.',
             'Fee assessment, payment confirmation, student receipts, summer class payments, and university materials.',
             'accountingtupv@gmail.com',
             '(034) 445 2177',
             'https://www.facebook.com/tupvbusinessoffice',
             'https://www.messenger.com/tupvbusinessoffice/')
    ");
}

admin_ensure_tables($pdo);
?>
