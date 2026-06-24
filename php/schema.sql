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
    processed_by INT NULL,
    admin_note TEXT NULL,
    processed_at TIMESTAMP NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Accounting Staff Table
CREATE TABLE IF NOT EXISTS accounting_staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    role VARCHAR(80) NOT NULL DEFAULT 'Accounting Staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Public Office Info Board Settings
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
('TUPV-23-0052', '$2y$10$Z0/bXQrgZnHqGyY3o6KvFOQyviCfUInxai0s8h82TipTapCYAESqq', 'Rex Orly', 'Mallorca', 'BET T09-A', NULL, TRUE, TRUE),
('TUPV-23-0053', '$2y$10$bZERtD.U8atHZuVSSF.zOeuyI3vJSJ0Obb2kCz7/997m8iz1PCW92', 'Hazel Mae', 'Jalandoni', 'BET T09-A', NULL, TRUE, TRUE),
('TUPV-23-0054', '$2y$10$8xkZvBhBykhIme4Ba/Jv7ewe9BaFzwh38EsMP4/kBXO3MFnuE0eFq', 'Rica', 'Galagate', 'BET T09-A', NULL, TRUE, TRUE),
('TUPV-23-0055', '$2y$10$MHTFN3ux4RnPt91rVBCC9O3sh7OZD.6XW2Ra3W0/PJ/c9qiuFokuW', 'Sidney', 'Monillo', 'BET T09-A', NULL, TRUE, FALSE),
('TUPV-23-0056', '$2y$10$BRGMxu8ankh947KmjBW.P.HIuIGjJd.JwAbBo0AY2zawezQuwkoRu', 'Francis Roi', 'Buenacosa', 'BET T09-A', NULL, TRUE, TRUE),
('TUPV-23-0057', '$2y$10$qRtp0d71QpZe0RgckUyHAuTmr1Ks0Cy7m//IVSMm05lR06e3YWrT2', 'Harold', 'Perez', 'BET T09-A', NULL, FALSE, FALSE),
('TUPV-23-0058', '$2y$10$ZJxHVMr6wDJzsSa3pXzQU.fwKx6hE1hi6cwqQ/SbWxggXltjJXX7u', 'Desiree', 'Valois', 'BET T09-A', NULL, TRUE, FALSE),
('TUPV-23-0059', '$2y$10$XQ91dI9Et3I8wjQBFA0AbOEHjrVzVONTVtiz6xSMsK/bHzSb.yJv.', 'Tovi', 'Macalipsay', 'BET T09-A', NULL, FALSE, FALSE),
('TUPV-23-0060', '$2y$10$yXQxsYpgnjo0ebOa5q5/gOd7VOL6LjrjVDu5QZaSgS4J8PrfaqXX6', 'Jared', 'Antiporda', 'BET T05-B', NULL, FALSE, FALSE);

-- Accounting staff usernames are first names and passwords are last names.
INSERT IGNORE INTO accounting_staff (username, password_hash, first_name, last_name, role) VALUES
('celeste', '$2y$10$mVgUk8QOyCvoLleN2Nt9n.E8wXrfU1QrRamLFcQniR/Im6EBFdR4y', 'Celeste', 'Delumpa', 'Accountant III / Head, Accounting Office'),
('jazer', '$2y$10$2N6FgZlMjsTLiFjLpWkJ4O4Oft4fiHPz1lDa4WSPzQSb/UNUP0Uqq', 'Jazer', 'Frias', 'Accountant II'),
('jorjet', '$2y$10$Zkm6Q8FQ0O6GIjJhHiTxqO.hRqFQJWZets0qh/xLFPnadNPYI5nx2', 'Jorjet', 'Abad', 'Administrative Aide VI'),
('romena', '$2y$10$8wCSr0oVrOt.uu5NQPIRIOJk9dprH8mdWl.NhYmjViHxK7ElG2QRW', 'Romena', 'Esidenio', 'Administrative Aide III');

INSERT IGNORE INTO office_info (id, office_name, location, operating_hours, services, email, phone, facebook_url, messenger_url) VALUES
(1,
 'TUPV Accounting Office',
 'Admin Building, Ground Floor, Room 101, TUP Visayas, Capt. Sabi St., Brgy. Zone 12, Talisay City.',
 'Monday to Friday, 8:00 AM to 5:00 PM. Closed during weekends and declared holidays.',
 'Fee assessment, payment confirmation, student receipts, summer class payments, and university materials.',
 'accountingtupv@gmail.com',
 '(034) 445 2177',
 'https://www.facebook.com/tupvbusinessoffice',
 'https://www.messenger.com/tupvbusinessoffice/');

INSERT IGNORE INTO products (name, price, stock_quantity) VALUES
('PE Uniform (Set)', 600.00, 100),
('College Uniform Textile', 180.00, 150),
('University Lanyard', 100.00, 200),
('SIT Uniform', 380.00, 120);

INSERT IGNORE INTO summer_subjects (subject_code, subject_title, fee, total_slots, available_slots) VALUES
('MATH1-V', 'Advanced Algebra', 25000.00, 30, 11),
('ELECT112-V', 'Basic Electricity (Direct Current)', 25000.00, 30, 18),
('DRAW111-V', 'Engineering Drawing 1', 25000.00, 30, 13),
('EM111ET-V', 'Engineering Measurement (Metrology)', 25000.00, 30, 10),
('CHEM114-V', 'General Chemistry 1', 25000.00, 30, 15),
('COMP112-V', 'Intro to Computing Environment (Logic Formulation)', 25000.00, 30, 26),
('PATHFIT1-V', 'Movement Competency Training', 25000.00, 30, 27),
('MATH2-V', 'Trigonometry', 25000.00, 30, 12),
('GEC1-V', 'Understanding the Self', 25000.00, 30, 22),
('WSTP1-V', 'Workshop Theory and Practice 1 (Basic Machining)', 25000.00, 30, 25),
('MATH3-V', 'Analytic Geometry with Solid Mensuration', 25000.00, 30, 17),
('ELECT122-V', 'Basic Electricity (Alternating Current)', 25000.00, 30, 20),
('COMP122-V', 'Computer Systems', 25000.00, 30, 26),
('PATHFIT2-V', 'Exercise-based Fitness Activities', 25000.00, 30, 25),
('GEE1-V', 'Gender and Society', 25000.00, 30, 24),
('PHYTECH124-V', 'Mechanics and Fluids', 25000.00, 30, 13),
('PEM122-V', 'Properties of Engineering Materials', 25000.00, 30, 24),
('WSTP2-V', 'Workshop Theory and Practice 2 (Bench Work)', 25000.00, 30, 23),
('ELEX132-V', 'Basic Electronics', 25000.00, 30, 16),
('DRAW132-V', 'Computer Aided Drawing (CAD)', 25000.00, 30, 15),
('COMP132-V', 'Computer Programming', 25000.00, 30, 26),
('CHEM134-V', 'General Chemistry 2', 25000.00, 30, 13),
('PHYTECH134-V', 'Heat, Optics and Electromagnetic', 25000.00, 30, 11),
('GEC4-V', 'Mathematics in the Modern World', 25000.00, 30, 18),
('GEC2-V', 'Readings in Philippine History', 25000.00, 30, 23),
('WSTP3-V', 'Workshop Theory & Practice 3 (Sheet Metal Works)', 25000.00, 30, 27),
('COMP212A-V', 'Computer Programming 2 (Object-Oriented)', 25000.00, 30, 22),
('COMP212-V', 'Computer Workshop 1', 25000.00, 30, 23),
('ELEX212-V', 'Electronics Principles 1', 25000.00, 30, 17),
('COMP212B-V', 'Logic Circuits Design 1', 25000.00, 30, 25),
('GEC5-V', 'Purposive Communication', 25000.00, 30, 27),
('COMP211-V', 'Software Applications 1', 25000.00, 30, 22),
('PATHFIT3-V', 'Sports', 25000.00, 30, 25),
('COMP213-V', 'Systems Analysis and Design', 25000.00, 30, 23),
('GEC3-V', 'The Contemporary World', 25000.00, 30, 22),
('COMP222A-V', 'Computer Programming 3 (Java EE)', 25000.00, 30, 23),
('COMP222-V', 'Computer Workshop 2', 25000.00, 30, 25),
('PATHFIT4-V', 'Dance', 25000.00, 30, 25),
('COMP223-V', 'Data Structures and Algorithm Analysis', 25000.00, 30, 26),
('MATH4-V', 'Differential Calculus', 25000.00, 30, 17),
('ELEX222-V', 'Electronics Principles 2', 25000.00, 30, 19),
('IP-V', 'Industry Preparation', 25000.00, 30, 25),
('COMP222B-V', 'Logic Circuits Design 2', 25000.00, 30, 25),
('GEC7-V', 'Science, Technology and Society', 25000.00, 30, 19),
('GEC6-V', 'Art Appreciation', 25000.00, 30, 27),
('BOSH-V', 'Basic Occupational Safety and Health', 25000.00, 30, 22),
('COMP232B-V', 'Computer Organization with Assembly Language', 25000.00, 30, 26),
('COMP232A-V', 'Computer Programming 4 (VB.Net)', 25000.00, 30, 26),
('COMP232-V', 'Computer Workshop 3', 25000.00, 30, 24),
('COMP232D-V', 'HTML Development', 25000.00, 30, 24),
('MATH5-V', 'Integral Calculus', 25000.00, 30, 15),
('COMP231-V', 'Software Applications 2', 25000.00, 30, 24),
('COMP232C-V', 'Theory of Database', 25000.00, 30, 26),
('COMP312-V', 'Advanced Database', 25000.00, 30, 24),
('COMP312A-V', 'Web Programming', 25000.00, 30, 22),
('COMP312B-V', 'Operating Systems', 25000.00, 30, 23),
('COMP312C-V', 'Microprocessor and Interfacing Systems', 25000.00, 30, 22),
('COMP312D', 'Artificial Intelligence and Neural Networks', 25000.00, 30, 25),
('COMP312E', 'Advanced Computer Aided Design', 25000.00, 30, 27),
('GEC8-V', 'Ethics', 25000.00, 30, 24),
('GEE2A-V', 'Reading Visual Art', 25000.00, 30, 26),
('COMP322-V', 'Robotics and Intelligent Control Systems Engineering 1', 25000.00, 30, 25),
('COMP322A-V', 'Embedded Microcontrollers', 25000.00, 30, 24),
('COMP322B-V', 'Computer Networks', 25000.00, 30, 26),
('FL-V', 'Basic Foreign Language', 25000.00, 30, 22),
('GEE3A-V', 'Philippine Indigenous Communities', 25000.00, 30, 24),
('MATH6-V', 'Statistics', 25000.00, 30, 10),
('MGT1-V', 'Industrial Management', 25000.00, 30, 24),
('RES1-V', 'Methods of Research', 25000.00, 30, 27),
('COMP332-V', 'Robotics and Intelligent Control Systems Engineering 2', 25000.00, 30, 27),
('COMP332A-V', 'Mobile Applications Development', 25000.00, 30, 25),
('COMP332B-V', 'Software Engineering', 25000.00, 30, 27),
('COMP332C-V', 'Network Administration', 25000.00, 30, 26),
('RES2-V', 'Thesis 1: Proposal', 25000.00, 30, 22),
('TE1-V', 'Instrumentation and Process Control', 25000.00, 30, 25),
('TE3-V', 'Environmental Engineering', 25000.00, 30, 25);
