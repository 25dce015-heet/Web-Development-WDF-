CREATE DATABASE IF NOT EXISTS studenthub_db;
USE studenthub_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username, email, name, password_hash, role) VALUES
('bhavya25', '25dce025@charusat.edu.in', 'Bhavya Patel', '$2y$10$w09u7uFv1gNknu4Y4W1J6e5wH8jC7l1gI5W3s5vE3b5z5W8a9e0tO', 'student'),
('admin', 'admin@charusat.edu.in', 'System Administrator', '$2y$10$E8r0p8tX/0C7e9qY0Y8l7.tS7s9t5w7s3r5z9e0w9t7e5w3r1t9y2', 'admin');
