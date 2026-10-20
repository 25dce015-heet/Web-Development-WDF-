<?php
require_once __DIR__ . "/db.php";

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: register.php");
    exit();
}

if (!$conn) {
    die("Database connection failed. Please ensure MySQL is running.");
}

$createTableSql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $createTableSql);

$name = isset($_POST["name"]) ? trim($_POST["name"]) : "";
$username = isset($_POST["username"]) ? trim($_POST["username"]) : "";
$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";
$confirm_password = isset($_POST["confirm_password"]) ? $_POST["confirm_password"] : "";
$role = isset($_POST["role"]) ? trim($_POST["role"]) : "student";

if (empty($name) || strlen($name) < 2) {
    $errors[] = "Full Name must be at least 2 characters.";
}

if (empty($username) || !preg_match("/^[a-zA-Z0-9_]{3,20}$/", $username)) {
    $errors[] = "Username must be 3-20 characters and contain only letters, numbers, or underscores.";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please provide a valid email address.";
}

if (empty($password) || strlen($password) < 8) {
    $errors[] = "Password must be at least 8 characters long.";
}

if ($password !== $confirm_password) {
    $errors[] = "Password and Confirm Password do not match.";
}

$allowedRoles = ["student", "admin"];
if (!in_array($role, $allowedRoles)) {
    $role = "student";
}

if (empty($errors)) {
    $checkStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? LIMIT 1");
    mysqli_stmt_bind_param($checkStmt, "s", $email);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);
    if (mysqli_stmt_num_rows($checkStmt) > 0) {
        $errors[] = "An account with this email address already exists.";
    }
    mysqli_stmt_close($checkStmt);
}

if (empty($errors)) {
    $userStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? LIMIT 1");
    mysqli_stmt_bind_param($userStmt, "s", $username);
    mysqli_stmt_execute($userStmt);
    mysqli_stmt_store_result($userStmt);
    if (mysqli_stmt_num_rows($userStmt) > 0) {
        $errors[] = "This username is already taken. Please choose another.";
    }
    mysqli_stmt_close($userStmt);
}

if (empty($errors)) {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $insertStmt = mysqli_prepare($conn, "INSERT INTO users (name, username, email, password_hash, role) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($insertStmt, "sssss", $name, $username, $email, $password_hash, $role);

    if (mysqli_stmt_execute($insertStmt)) {
        $success = true;
    } else {
        $errors[] = "Failed to create account: " . mysqli_error($conn);
    }
    mysqli_stmt_close($insertStmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 9 - Registration Status</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background-color: #f4f6f9;
            padding: 30px 15px;
        }
        .container {
            max-width: 550px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        ul {
            margin-left: 20px;
            margin-top: 10px;
        }
        li {
            margin-bottom: 6px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .details-table td {
            padding: 9px 12px;
            border: 1px solid #e9ecef;
            font-size: 14px;
        }
        .details-table td:first-child {
            font-weight: bold;
            background: #f8f9fa;
            width: 35%;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            flex: 1;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

<div class="container">
    <?php if ($success): ?>
        <div class="alert alert-success">
            <h2>Account Created Successfully!</h2>
            <p>User details were securely verified, password was hashed using <code>password_hash()</code>, and the record was inserted into MySQL.</p>
        </div>

        <h3>Registered Account Details:</h3>
        <table class="details-table">
            <tr><td>Name</td><td><?php echo htmlspecialchars($name); ?></td></tr>
            <tr><td>Username</td><td><?php echo htmlspecialchars($username); ?></td></tr>
            <tr><td>Email</td><td><?php echo htmlspecialchars($email); ?></td></tr>
            <tr><td>Role</td><td><?php echo htmlspecialchars(ucfirst($role)); ?></td></tr>
            <tr><td>Password Storage</td><td><code><?php echo substr($password_hash, 0, 25); ?>... (Hashed)</code></td></tr>
        </table>

        <div class="btn-group">
            <a href="register.php" class="btn btn-secondary">Register Another</a>
            <a href="users.php" class="btn btn-primary">View Database Users</a>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            <h2>Registration Failed</h2>
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="btn-group">
            <a href="javascript:history.back()" class="btn btn-primary">Go Back & Correct</a>
            <a href="register.php" class="btn btn-secondary">Start Over</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
