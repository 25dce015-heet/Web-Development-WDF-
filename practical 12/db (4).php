<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "studenthub_db";

$conn = @mysqli_connect($host, $user, $pass, $db);

if ($conn) {
    $createSql = "CREATE TABLE IF NOT EXISTS events (
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
    )";
    mysqli_query($conn, $createSql);

    $check = mysqli_query($conn, "SELECT COUNT(*) AS total FROM events");
    if ($check) {
        $row = mysqli_fetch_assoc($check);
        if ($row["total"] == 0) {
            $seedSql = "INSERT INTO events (event_title, event_date, event_time, venue, category, description, poster, status) VALUES
            ('Web Development Hackathon', '2026-11-15', '09:30:00', 'Lab 301, FTE Building', 'Technical', '24-hour coding challenge for web development frameworks.', 'default.png', 'Upcoming'),
            ('AI & Machine Learning Seminar', '2026-11-20', '11:00:00', 'Auditorium A', 'Seminar', 'Expert talk on practical generative AI and deep learning applications.', 'default.png', 'Upcoming'),
            ('Annual Tech Fest CodeCraft', '2026-12-05', '10:00:00', 'Central Campus Ground', 'Cultural & Tech', 'University annual technical symposium and coding contests.', 'default.png', 'Upcoming'),
            ('Cyber Security Awareness Workshop', '2026-10-10', '14:00:00', 'Seminar Hall B', 'Workshop', 'Practical session on web security, XSS, and SQL injection prevention.', 'default.png', 'Completed')";
            mysqli_query($conn, $seedSql);
        }
    }
}
?>
