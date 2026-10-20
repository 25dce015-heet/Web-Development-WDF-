<?php
require_once __DIR__ . "/auth_check.php";
require_once __DIR__ . "/db.php";

$currentRole = $_SESSION["role"];
$currentUser = $_SESSION["username"];

if (isset($_POST["test_log"])) {
    log_audit_action($conn, $currentUser, "MANUAL_ACTION", "Admin triggered manual verification action.");
    header("Location: index.php");
    exit();
}

$studentCount = 0;
$eventCount = 0;
$regCount = 0;
$userCount = 0;

$recentRegistrations = [];
$recentLogs = [];

if ($conn) {
    $res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM students");
    if ($res) {
        $studentCount = mysqli_fetch_assoc($res)["total"];
    }

    $res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM events");
    if ($res) {
        $eventCount = mysqli_fetch_assoc($res)["total"];
    }

    $res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM registrations");
    if ($res) {
        $regCount = mysqli_fetch_assoc($res)["total"];
    }

    $res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
    if ($res) {
        $userCount = mysqli_fetch_assoc($res)["total"];
    }

    $regSql = "SELECT r.id, s.name AS student_name, s.student_id, e.event_title, r.registration_date, r.status
               FROM registrations r
               JOIN students s ON r.student_id = s.id
               JOIN events e ON r.event_id = e.id
               ORDER BY r.id DESC LIMIT 5";
    $regRes = mysqli_query($conn, $regSql);
    if ($regRes) {
        while ($row = mysqli_fetch_assoc($regRes)) {
            $recentRegistrations[] = $row;
        }
    }

    $logRes = mysqli_query($conn, "SELECT * FROM audit_logs ORDER BY id DESC LIMIT 5");
    if ($logRes) {
        while ($row = mysqli_fetch_assoc($logRes)) {
            $recentLogs[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 14 - Admin Dashboard & RBAC</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background-color: #f4f6f9;
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 260px;
            background-color: #212529;
            color: white;
            padding: 25px 15px;
            display: flex;
            flex-direction: column;
        }
        .sidebar h2 {
            font-size: 20px;
            color: #007bff;
            margin-bottom: 5px;
        }
        .sidebar .role-badge {
            display: inline-block;
            background: #495057;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            margin-bottom: 25px;
            text-transform: uppercase;
        }
        .nav-menu {
            list-style: none;
            flex: 1;
        }
        .nav-menu li {
            margin-bottom: 8px;
        }
        .nav-menu a {
            display: block;
            padding: 10px 14px;
            color: #ced4da;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            transition: 0.2s;
        }
        .nav-menu a:hover, .nav-menu a.active {
            background-color: #007bff;
            color: white;
        }
        .nav-category {
            font-size: 11px;
            color: #888;
            text-transform: uppercase;
            margin: 15px 0 6px 14px;
            letter-spacing: 0.5px;
        }
        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            background: white;
            padding: 20px 25px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .top-bar h1 {
            font-size: 24px;
            color: #333;
        }
        .top-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .switch-pill {
            padding: 8px 14px;
            border-radius: 20px;
            font-size: 13px;
            text-decoration: none;
            font-weight: bold;
        }
        .switch-student {
            background: #e2e3e5;
            color: #383d41;
        }
        .switch-admin {
            background: #cce5ff;
            color: #004085;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: white;
            padding: 22px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            border-left: 5px solid #007bff;
        }
        .stat-card.green { border-left-color: #28a745; }
        .stat-card.orange { border-left-color: #fd7e14; }
        .stat-card.purple { border-left-color: #6f42c1; }
        .stat-card h3 {
            font-size: 13px;
            color: #6c757d;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .stat-card .number {
            font-size: 28px;
            font-weight: bold;
            color: #212529;
        }
        .content-box {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }
        .content-box h2 {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
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
            color: #495057;
        }
        tr:hover { background-color: #fdfdfd; }
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
        .btn {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
        .btn-primary { background: #007bff; color: white; }
        .btn:hover { opacity: 0.9; }
        .access-denied {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #f5c6cb;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>StudentHub</h2>
    <div class="role-badge">Role: <?php echo strtoupper($currentRole); ?></div>

    <ul class="nav-menu">
        <li class="nav-category">Main Navigation</li>

        <?php if ($currentRole === "admin"): ?>
            <li><a href="index.php" class="active">Dashboard Overview</a></li>
            <li><a href="../practical11/index.php">Manage Students</a></li>
            <li><a href="../practical12/index.php">Manage Events</a></li>
            <li><a href="../practical13/index.html">Async Fetch CRUD</a></li>
            <li><a href="audit_log.php">System Audit Logs</a></li>

            <li class="nav-category">Administrative</li>
            <li><a href="../practical8/view_tables.php">Database Tables</a></li>
            <li><a href="../practical9/users.php">System Users</a></li>
            <li><a href="../practical10/logout.php">Log Out</a></li>
        <?php else: ?>
            <li><a href="index.php" class="active">Student Home</a></li>
            <li><a href="../pages/courses.html">My Courses</a></li>
            <li><a href="../pages/attendance.html">Attendance</a></li>
            <li><a href="../pages/assignment.html">Assignments</a></li>
            <li><a href="../pages/result.html">Exam Results</a></li>
            <li><a href="../pages/contact.html">Help Desk</a></li>
            <li><a href="../practical10/logout.php">Log Out</a></li>
        <?php endif; ?>
    </ul>
</div>

<div class="main-content">
    <div class="top-bar">
        <div>
            <h1>Admin Dashboard (RBAC & Dynamic Menu)</h1>
            <p style="color:#666; font-size:13px; margin-top:4px;">Practical 14: SQL Aggregation, Dynamic Menus & Audit Logging</p>
        </div>
        <div class="top-actions">
            <span>Signed in as: <strong><?php echo htmlspecialchars($_SESSION["name"]); ?></strong></span>
            <?php if ($currentRole === "admin"): ?>
                <a href="index.php?switch_role=student" class="switch-pill switch-student" title="Switch to Student view to test Dynamic Menu">Simulate Student View</a>
            <?php else: ?>
                <a href="index.php?switch_role=admin" class="switch-pill switch-admin" title="Switch back to Admin view">Restore Admin View</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($currentRole !== "admin"): ?>
        <div class="access-denied">
            <h2>Restricted View Active (Student Role)</h2>
            <p style="margin:10px 0;">Notice how the navigation menu on the left dynamically transformed into student-only links.</p>
            <p>Admin statistics, logs, and moderation panels are hidden for non-admin users.</p>
            <a href="index.php?switch_role=admin" class="btn btn-primary" style="margin-top:10px;">Switch Back to Admin Role</a>
        </div>
    <?php else: ?>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Students</h3>
                <div class="number"><?php echo $studentCount; ?></div>
            </div>
            <div class="stat-card green">
                <h3>Total Events</h3>
                <div class="number"><?php echo $eventCount; ?></div>
            </div>
            <div class="stat-card orange">
                <h3>Registrations</h3>
                <div class="number"><?php echo $regCount; ?></div>
            </div>
            <div class="stat-card purple">
                <h3>System Users</h3>
                <div class="number"><?php echo $userCount; ?></div>
            </div>
        </div>

        <div class="content-box">
            <h2>Recent Event Registrations</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Event Title</th>
                        <th>Registration Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentRegistrations)): ?>
                        <tr><td colspan="6" style="text-align:center; color:#888;">No recent registrations found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentRegistrations as $r): ?>
                            <tr>
                                <td><?php echo $r["id"]; ?></td>
                                <td><strong><?php echo htmlspecialchars($r["student_id"]); ?></strong></td>
                                <td><?php echo htmlspecialchars($r["student_name"]); ?></td>
                                <td><?php echo htmlspecialchars($r["event_title"]); ?></td>
                                <td><?php echo htmlspecialchars($r["registration_date"]); ?></td>
                                <td><span class="badge badge-create"><?php echo htmlspecialchars($r["status"]); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="content-box">
            <h2>
                <span>Recent System Audit Logs</span>
                <form method="POST" style="display:inline;">
                    <button type="submit" name="test_log" class="btn btn-primary">+ Record Test Audit Event</button>
                    <a href="audit_log.php" class="btn" style="background:#6c757d; color:white;">View All Logs</a>
                </form>
            </h2>
            <table>
                <thead>
                    <tr>
                        <th>Log ID</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>IP Address</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentLogs)): ?>
                        <tr><td colspan="6" style="text-align:center; color:#888;">No audit logs recorded yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentLogs as $log): ?>
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
    <?php endif; ?>
</div>

</body>
</html>
