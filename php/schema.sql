-- TUP Visayas Accounting System Database Schema

CREATE DATABASE IF NOT EXISTS tupv_accounting;
USE tupv_accounting;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    course VARCHAR(50),
    year_level INT,
    is_eligible_summer_class BOOLEAN DEFAULT TRUE,
    is_ched_scholar BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Transactions Table
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reference_no VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NOT NULL,
    transaction_type ENUM('fee', 'materials', 'summer_class') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('gcash', 'bank', 'otc') NOT NULL,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Products/Materials Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT DEFAULT 0
);

-- Summer Class Subjects Table
CREATE TABLE IF NOT EXISTS summer_subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(20) NOT NULL,
    subject_title VARCHAR(100) NOT NULL,
    fee DECIMAL(10, 2) NOT NULL,
    total_slots INT NOT NULL,
    available_slots INT NOT NULL
);

-- Enrollments Table
CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject_id INT NOT NULL,
    transaction_id INT,
    status ENUM('enrolled', 'dropped', 'pending_payment') DEFAULT 'pending_payment',
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (subject_id) REFERENCES summer_subjects(id),
    FOREIGN KEY (transaction_id) REFERENCES transactions(id)
);

-- Feedbacks Table
CREATE TABLE IF NOT EXISTS feedbacks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'resolved') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert Mock Data
-- Password hashes generated for the passwords "Udasco" and "Password"
INSERT IGNORE INTO users (student_id, password_hash, first_name, last_name, course, year_level, is_eligible_summer_class, is_ched_scholar) VALUES
('TUPV-23-0050', '$2y$10$qOZ2ecNqusd9xpA9aAcL9uRf1JONHEf2mBGmo14btwmsrMMUN41be', 'Daniel', 'Udasco', 'BET T09-A', NULL, TRUE, TRUE),
('TUPV-23-0051', '$2y$10$GgezO5HrjvOWcxgr5BN3CenNCuMyu9X7ZTmmsbPmfpflmNbfmg/Da', 'John', 'Doe', 'BSIT', 2, FALSE, FALSE),
('TUPV-23-0052', '$2y$10$Z0/bXQrgZnHqGyY3o6KvFOQyviCfUInxai0s8h82TipTapCYAESqq', 'Rex Orly', 'Mallorca', 'BET T09-A', NULL, TRUE, FALSE),
('TUPV-23-0053', '$2y$10$bZERtD.U8atHZuVSSF.zOeuyI3vJSJ0Obb2kCz7/997m8iz1PCW92', 'Hazel Mae', 'Jalandoni', 'BET T09-A', NULL, TRUE, FALSE),
('TUPV-23-0054', '$2y$10$8xkZvBhBykhIme4Ba/Jv7ewe9BaFzwh38EsMP4/kBXO3MFnuE0eFq', 'Rica', 'Galagate', 'BET T09-A', NULL, TRUE, TRUE),
('TUPV-23-0055', '$2y$10$MHTFN3ux4RnPt91rVBCC9O3sh7OZD.6XW2Ra3W0/PJ/c9qiuFokuW', 'Sidney', 'Monillo', 'BET T09-A', NULL, TRUE, FALSE),
('TUPV-23-0056', '$2y$10$BRGMxu8ankh947KmjBW.P.HIuIGjJd.JwAbBo0AY2zawezQuwkoRu', 'Francis Roi', 'Buenacosa', 'BET T09-A', NULL, TRUE, TRUE),
('TUPV-23-0057', '$2y$10$qRtp0d71QpZe0RgckUyHAuTmr1Ks0Cy7m//IVSMm05lR06e3YWrT2', 'Harold', 'Perez', 'BET T09-A', NULL, FALSE, FALSE),
('TUPV-23-0058', '$2y$10$ZJxHVMr6wDJzsSa3pXzQU.fwKx6hE1hi6cwqQ/SbWxggXltjJXX7u', 'Desiree', 'Valois', 'BET T09-A', NULL, TRUE, FALSE),
('TUPV-23-0059', '$2y$10$XQ91dI9Et3I8wjQBFA0AbOEHjrVzVONTVtiz6xSMsK/bHzSb.yJv.', 'Tovi', 'Macalipsay', 'BET T09-A', NULL, FALSE, FALSE),
('TUPV-23-0060', '$2y$10$yXQxsYpgnjo0ebOa5q5/gOd7VOL6LjrjVDu5QZaSgS4J8PrfaqXX6', 'Jared', 'Antiporda', 'BET T05-B', NULL, FALSE, FALSE); -- password: password

INSERT IGNORE INTO products (name, price, stock_quantity) VALUES
('PE Uniform (Set)', 600.00, 100),
('College Uniform Textile', 180.00, 150),
('University Lanyard', 100.00, 200),
('SIT Uniform', 380.00, 120);

INSERT IGNORE INTO summer_subjects (subject_code, subject_title, fee, total_slots, available_slots) VALUES
('MATH 101', 'Calculus 1', 1500.00, 30, 15),
('IT 202', 'Data Structures and Algorithms', 1200.00, 25, 5),
('PHY 101', 'Physics for Engineers', 1800.00, 40, 0);
