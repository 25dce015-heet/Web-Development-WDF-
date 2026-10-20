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
            ('AI & Data Science Summit', '2026-11-10', '10:00:00', 'Auditorium FTE', 'Technical', 'Industry expert keynote and hands-on session on AI frameworks.', 'default.png', 'Upcoming'),
            ('Frontend Frameworks Bootcamp', '2026-11-25', '14:00:00', 'Lab 204', 'Workshop', 'Fast-paced masterclass covering React, Vue, and modern JavaScript.', 'default.png', 'Upcoming'),
            ('University Coding League', '2026-12-01', '09:00:00', 'Computer Center', 'Technical', 'Inter-college competitive programming tournament.', 'default.png', 'Upcoming')";
            mysqli_query($conn, $seedSql);
        }
    }
}
?>
