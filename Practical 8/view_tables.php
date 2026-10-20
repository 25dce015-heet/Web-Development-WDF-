<?php
require_once __DIR__ . "/db.php";

$students = [];
$events = [];
$registrations = [];

if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM students ORDER BY id ASC");
        $stmt->execute();
        $students = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT * FROM events ORDER BY id ASC");
        $stmt->execute();
        $events = $stmt->fetchAll();

        $stmt = $pdo->prepare("
            SELECT r.id, s.name AS student_name, s.student_id, e.event_title, r.registration_date, r.status
            FROM registrations r
            JOIN students s ON r.student_id = s.id
            JOIN events e ON r.event_id = e.id
            ORDER BY r.id ASC
        ");
        $stmt->execute();
        $registrations = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 8 - Database Records</title>
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
        h2 {
            font-size: 17px;
            color: #333;
            margin: 25px 0 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
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
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Practical 8: Normalized Database Records (PDO)</h1>
        <a href="index.php" class="btn">&larr; Connection Test</a>
    </div>

    <?php if (isset($error)): ?>
        <p style="color:red; margin-bottom: 15px;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <h2>Table: students (<?php echo count($students); ?>)</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Course</th>
                <th>Year</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $s): ?>
                <tr>
                    <td><?php echo $s["id"]; ?></td>
                    <td><strong><?php echo htmlspecialchars($s["student_id"]); ?></strong></td>
                    <td><?php echo htmlspecialchars($s["name"]); ?></td>
                    <td><?php echo htmlspecialchars($s["email"]); ?></td>
                    <td><?php echo htmlspecialchars($s["mobile"]); ?></td>
                    <td><?php echo htmlspecialchars($s["course"]); ?></td>
                    <td><?php echo htmlspecialchars($s["year"]); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Table: events (<?php echo count($events); ?>)</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Event Title</th>
                <th>Date</th>
                <th>Time</th>
                <th>Venue</th>
                <th>Category</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $e): ?>
                <tr>
                    <td><?php echo $e["id"]; ?></td>
                    <td><strong><?php echo htmlspecialchars($e["event_title"]); ?></strong></td>
                    <td><?php echo htmlspecialchars($e["event_date"]); ?></td>
                    <td><?php echo htmlspecialchars($e["event_time"]); ?></td>
                    <td><?php echo htmlspecialchars($e["venue"]); ?></td>
                    <td><?php echo htmlspecialchars($e["category"]); ?></td>
                    <td><span class="badge"><?php echo htmlspecialchars($e["status"]); ?></span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Table: registrations (Normalized Foreign Key Join) (<?php echo count($registrations); ?>)</h2>
    <table>
        <thead>
            <tr>
                <th>Reg ID</th>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Event Title</th>
                <th>Registration Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registrations as $r): ?>
                <tr>
                    <td><?php echo $r["id"]; ?></td>
                    <td><?php echo htmlspecialchars($r["student_id"]); ?></td>
                    <td><?php echo htmlspecialchars($r["student_name"]); ?></td>
                    <td><?php echo htmlspecialchars($r["event_title"]); ?></td>
                    <td><?php echo htmlspecialchars($r["registration_date"]); ?></td>
                    <td><span class="badge"><?php echo htmlspecialchars($r["status"]); ?></span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
