<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "studenthub_db";

$conn = @mysqli_connect($host, $user, $pass, $db);

if ($conn) {
    $createSql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        name VARCHAR(100) NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        role VARCHAR(20) DEFAULT 'student',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    mysqli_query($conn, $createSql);

    $checkAdmin = mysqli_query($conn, "SELECT id FROM users WHERE username = 'admin' LIMIT 1");
    if ($checkAdmin && mysqli_num_rows($checkAdmin) === 0) {
        $adminPass = password_hash("Admin@123", PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, name, password_hash, role) VALUES ('admin', 'admin@charusat.edu.in', 'Administrator', ?, 'admin')");
        mysqli_stmt_bind_param($stmt, "s", $adminPass);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    $checkStudent = mysqli_query($conn, "SELECT id FROM users WHERE username = 'bhavya' LIMIT 1");
    if ($checkStudent && mysqli_num_rows($checkStudent) === 0) {
        $studentPass = password_hash("Student@123", PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, name, password_hash, role) VALUES ('bhavya', '25dce025@charusat.edu.in', 'Bhavya Patel', ?, 'student')");
        mysqli_stmt_bind_param($stmt, "s", $studentPass);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}
?>
