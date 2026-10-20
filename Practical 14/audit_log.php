<?php
require_once __DIR__ . "/auth_check.php";
require_once __DIR__ . "/db.php";

if ($_SESSION["role"] !== "admin") {
    header("Location: index.php");
    exit();
}

$logs = [];
if ($conn) {
    $res = mysqli_query($conn, "SELECT * FROM audit_logs ORDER BY id DESC");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $logs[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 14 - System Audit Logs</title>
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
            max-width: 1050px;
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
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #007bff;
            font-size: 24px;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 5px;
            font-size: 14px;
            text-decoration: none;
            background: #6c757d;
            color: white;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 10px 12px;
            text-align: left;
            border: 1px solid #eee;
            font-size: 13px;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
        }
        tr:hover { background-color: #f9fbfd; }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-login { background: #cce5ff; color: #004085; }
        .badge-create { background: #d4edda; color: #155724; }
        .badge-update { background: #fff3cd; color: #856404; }
        .badge-register { background: #d1ecf1; color: #0c5460; }
        .badge-manual { background: #e2e3e5; color: #383d41; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div>
            <h1>System Audit Logs</h1>
            <p style="color:#666; font-size:13px;">Full Traceability of Administrator & Student Events</p>
        </div>
        <a href="index.php" class="btn">&larr; Return to Dashboard</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Action</th>
                <th>Details</th>
                <th>Client IP</th>
                <th>Recorded At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)): ?>
                <tr><td colspan="6" style="text-align:center; color:#888;">No audit logs found.</td></tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td>#<?php echo $log["id"]; ?></td>
                        <td><strong><?php echo htmlspecialchars($log["username"]); ?></strong></td>
                        <td>
                            <?php
                            $actionClass = "badge-manual";
                            if ($log["action"] === "LOGIN") $actionClass = "badge-login";
                            elseif (strpos($log["action"], "CREATE") !== false) $actionClass = "badge-create";
                            elseif (strpos($log["action"], "UPDATE") !== false) $actionClass = "badge-update";
                            elseif (strpos($log["action"], "REGISTER") !== false) $actionClass = "badge-register";
                            ?>
                            <span class="badge <?php echo $actionClass; ?>"><?php echo htmlspecialchars($log["action"]); ?></span>
                        </td>
                        <td><?php echo htmlspecialchars($log["details"]); ?></td>
                        <td><code><?php echo htmlspecialchars($log["ip_address"]); ?></code></td>
                        <td><?php echo htmlspecialchars($log["created_at"]); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
