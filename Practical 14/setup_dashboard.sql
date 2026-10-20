CREATE DATABASE IF NOT EXISTS studenthub_db;
USE studenthub_db;

CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    action VARCHAR(50) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45) DEFAULT '127.0.0.1',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO audit_logs (username, action, details, ip_address) VALUES
('admin', 'LOGIN', 'Admin successfully logged in from localhost.', '127.0.0.1'),
('admin', 'STUDENT_CREATE', 'Added student Bhavya Patel (25DCE025).', '127.0.0.1'),
('admin', 'EVENT_CREATE', 'Published event Web Development Hackathon.', '127.0.0.1'),
('admin', 'EVENT_UPDATE', 'Updated venue for AI & Machine Learning Seminar.', '127.0.0.1'),
('bhavya', 'EVENT_REGISTER', 'Student registered for Hackathon 2026.', '127.0.0.1');
