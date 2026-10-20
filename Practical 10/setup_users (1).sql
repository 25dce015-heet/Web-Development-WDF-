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
('bhavya', '25dce025@charusat.edu.in', 'Bhavya Patel', '$2y$10$wE0v2g6177xW1Lh0X5u1uODW3r8e6n7u5m0u3s9a5w2e1r4t7y0u1', 'student'),
('admin', 'admin@charusat.edu.in', 'Administrator', '$2y$10$tM6n5b4v3c2x1z0l9k8j7h6g5f4d3s2a1q0w9e8r7t6y5u4i3o2p1', 'admin');
