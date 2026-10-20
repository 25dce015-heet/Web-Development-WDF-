<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "studenthub_db";

$conn = @mysqli_connect($host, $user, $pass, $db);

if ($conn) {
    $createAudit = "CREATE TABLE IF NOT EXISTS audit_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        action VARCHAR(50) NOT NULL,
        details TEXT,
        ip_address VARCHAR(45) DEFAULT '127.0.0.1',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    mysqli_query($conn, $createAudit);

    $checkLogs = mysqli_query($conn, "SELECT COUNT(*) AS total FROM audit_logs");
    if ($checkLogs) {
        $row = mysqli_fetch_assoc($checkLogs);
        if ($row["total"] == 0) {
            $seedLogs = "INSERT INTO audit_logs (username, action, details, ip_address) VALUES
            ('admin', 'LOGIN', 'Admin logged in from local machine.', '127.0.0.1'),
            ('admin', 'STUDENT_CREATE', 'Added student Bhavya Patel (25DCE025).', '127.0.0.1'),
            ('admin', 'EVENT_CREATE', 'Created Web Development Hackathon.', '127.0.0.1'),
            ('admin', 'EVENT_UPDATE', 'Updated venue for AI & Machine Learning Seminar.', '127.0.0.1'),
            ('bhavya', 'EVENT_REGISTER', 'Student registered for Hackathon 2026.', '127.0.0.1')";
            mysqli_query($conn, $seedLogs);
        }
    }
}

function log_audit_action($conn, $username, $action, $details) {
    if (!$conn) return false;
    $ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "127.0.0.1";
    $stmt = mysqli_prepare($conn, "INSERT INTO audit_logs (username, action, details, ip_address) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $username, $action, $details, $ip);
    $res = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $res;
}
?>
