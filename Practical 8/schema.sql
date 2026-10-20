CREATE DATABASE IF NOT EXISTS studenthub_db;
USE studenthub_db;

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mobile VARCHAR(15) NOT NULL,
    course VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_title VARCHAR(150) NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    venue VARCHAR(100) NOT NULL,
    category VARCHAR(50) DEFAULT 'Technical',
    description TEXT,
    poster VARCHAR(255) DEFAULT 'default.png',
    status VARCHAR(20) DEFAULT 'Upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'Confirmed',
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

INSERT INTO students (student_id, name, email, mobile, course, year) VALUES
('25DCE025', 'Bhavya Patel', '25dce025@charusat.edu.in', '9876543210', 'BCA', 2),
('25DCE026', 'Aarav Shah', '25dce026@charusat.edu.in', '9876543211', 'B.Tech CSE', 2),
('25DCE027', 'Diya Joshi', '25dce027@charusat.edu.in', '9876543212', 'B.Tech IT', 3),
('25DCE028', 'Kavya Sharma', '25dce028@charusat.edu.in', '9876543213', 'BCA', 1),
('25DCE029', 'Rohan Mehta', '25dce029@charusat.edu.in', '9876543214', 'BBA', 2);

INSERT INTO events (event_title, event_date, event_time, venue, category, description, status) VALUES
('Web Development Hackathon', '2026-11-15', '09:30:00', 'Lab 301, FTE Building', 'Technical', '24-hour coding challenge for web development frameworks.', 'Upcoming'),
('AI & Machine Learning Seminar', '2026-11-20', '11:00:00', 'Auditorium A', 'Seminar', 'Expert talk on practical generative AI and deep learning applications.', 'Upcoming'),
('Annual Tech Fest CodeCraft', '2026-12-05', '10:00:00', 'Central Campus Ground', 'Cultural & Tech', 'University annual technical symposium and coding contests.', 'Upcoming');

INSERT INTO registrations (student_id, event_id, status) VALUES
(1, 1, 'Confirmed'),
(1, 2, 'Confirmed'),
(2, 1, 'Confirmed'),
(3, 2, 'Confirmed'),
(4, 3, 'Confirmed');
