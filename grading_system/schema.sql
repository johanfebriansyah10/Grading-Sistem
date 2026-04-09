-- Create the Database
CREATE DATABASE IF NOT EXISTS grading_system;
USE grading_system;

-- 1. Users Table (for Admin & Teacher Logins)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher') NOT NULL
);

-- Insert a default admin for initial login (password is 'admin123')
INSERT INTO users (name, username, password, role) VALUES 
('System Admin', 'admin', '$2y$10$iRSBtglx5e7BdfvYAZ7U9OklOKbB5KOoy6jRKVo46AgR06HE4Lz7G', 'admin')
ON DUPLICATE KEY UPDATE password = '$2y$10$iRSBtglx5e7BdfvYAZ7U9OklOKbB5KOoy6jRKVo46AgR06HE4Lz7G';

-- 2. Students Table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    class VARCHAR(50) NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL
);

-- 3. Teachers Table
CREATE TABLE IF NOT EXISTS teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    subject VARCHAR(100) NOT NULL
);

-- 4. Subjects Table (Mapel)
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- Insert default subjects
INSERT INTO subjects (name) VALUES 
('Bahasa Indonesia'),
('Matematika'),
('Bahasa Inggris'),
('Ilmu Pengetahuan Alam')
ON DUPLICATE KEY UPDATE name=name;

-- 5. KKM Table (Settings for Risk Threshold)
CREATE TABLE IF NOT EXISTS kkm (
    id INT AUTO_INCREMENT PRIMARY KEY,
    value FLOAT NOT NULL DEFAULT 70
);

-- Insert default KKM value
INSERT INTO kkm (value) VALUES (70)
ON DUPLICATE KEY UPDATE id=id;

-- 6. Grades Table with predictive scores per subject
CREATE TABLE IF NOT EXISTS grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    tugas FLOAT NOT NULL DEFAULT 0,
    uts FLOAT NOT NULL DEFAULT 0,
    uas FLOAT NOT NULL DEFAULT 0,
    attendance FLOAT NOT NULL DEFAULT 0,
    predicted_score FLOAT,
    status VARCHAR(20),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    UNIQUE KEY uni_student_subject (student_id, subject_id)
);
