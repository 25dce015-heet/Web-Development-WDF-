<?php
require_once __DIR__ . "/db.php";

$users = [];
$error = "";

if ($conn) {
    $result = mysqli_query($conn, "SELECT id, name, username, email, password_hash, role, created_at FROM users ORDER BY id DESC");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        mysqli_free_result($result);
    } else {
        $error = mysqli_error($conn);
    }
} else {
    $error = "Database not connected. Please make sure MySQL is running in XAMPP.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 9 - Registered Users</title>
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
            max-width: 950px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }
        .header h1 {
            color: #007bff;
            font-size: 22px;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 10px 12px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 13px;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f9fbfd;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-admin {
            background-color: #cce5ff;
            color: #004085;
        }
        .badge-student {
            background-color: #d4edda;
            color: #155724;
        }
        .hash-code {
            font-family: monospace;
            font-size: 11px;
            background: #f1f1f1;
            padding: 2px 4px;
            border-radius: 3px;
            color: #333;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Practical 9: Database User Records</h1>
        <a href="register.php" class="btn">+ New User Registration</a>
    </div>

    <?php if (!empty($error)): ?>
        <p style="color:red; margin-bottom:15px;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <p style="margin-bottom:15px; color:#555; font-size:14px;">
        Passwords below are stored as bcrypt hashes created by <code>password_hash()</code>. Plain text passwords are never stored.
    </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Password Hash</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr><td colspan="7" style="text-align:center; color:#888;">No registered users found yet.</td></tr>
            <?php else: ?>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?php echo $u["id"]; ?></td>
                        <td><strong><?php echo htmlspecialchars($u["name"]); ?></strong></td>
                        <td><?php echo htmlspecialchars($u["username"]); ?></td>
                        <td><?php echo htmlspecialchars($u["email"]); ?></td>
                        <td>
                            <span class="badge <?php echo $u["role"] === 'admin' ? 'badge-admin' : 'badge-student'; ?>">
                                <?php echo htmlspecialchars(ucfirst($u["role"])); ?>
                            </span>
                        </td>
                        <td><span class="hash-code"><?php echo htmlspecialchars(substr($u["password_hash"], 0, 28)) . "..."; ?></span></td>
                        <td><?php echo htmlspecialchars($u["created_at"]); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
