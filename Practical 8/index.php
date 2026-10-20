<?php
require_once __DIR__ . "/db.php";

$init_message = "";
if (isset($_POST["init_db"])) {
    try {
        $rootPdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
        $sql = file_get_contents(__DIR__ . "/schema.sql");
        $rootPdo->exec($sql);
        $init_message = "Database schema initialized successfully!";
        require __DIR__ . "/db.php";
    } catch (PDOException $e) {
        $init_message = "Initialization failed: " . $e->getMessage();
    }
}

$students_count = 0;
$events_count = 0;
$registrations_count = 0;
$tables_ready = false;

if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM students");
        $students_count = $stmt->fetchColumn();

        $stmt = $pdo->query("SELECT COUNT(*) FROM events");
        $events_count = $stmt->fetchColumn();

        $stmt = $pdo->query("SELECT COUNT(*) FROM registrations");
        $registrations_count = $stmt->fetchColumn();

        $tables_ready = true;
    } catch (Exception $e) {
        $tables_ready = false;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 8 - Database Connectivity & Schema Test</title>
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
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
        }
        .header h1 {
            color: #007bff;
            font-size: 24px;
            margin-bottom: 6px;
        }
        .header p {
            color: #666;
            font-size: 14px;
        }
        .status-box {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 15px;
        }
        .status-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        .card {
            background: #f8f9fa;
            border: 1px solid #e2e8f0;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .card h3 {
            font-size: 15px;
            color: #555;
            margin-bottom: 10px;
        }
        .card .count {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
        }
        .actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-secondary {
            background-color: #28a745;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>StudentHub Portal</h1>
        <p>Practical 8: MySQL Schema Design, ER Model, PDO Connectivity, and Prepared Statements</p>
    </div>

    <?php if (!empty($init_message)): ?>
        <div class="status-box status-success">
            <?php echo htmlspecialchars($init_message); ?>
        </div>
    <?php endif; ?>

    <?php if ($pdo): ?>
        <div class="status-box status-success">
            <strong>Connection Status: SUCCESSFUL</strong><br>
            Connected to MySQL database '<strong>studenthub_db</strong>' on host '<strong>localhost</strong>' using PDO Prepared Statements engine.
        </div>

        <?php if ($tables_ready): ?>
            <div class="cards-grid">
                <div class="card">
                    <h3>Students</h3>
                    <div class="count"><?php echo $students_count; ?></div>
                    <small>Records Seeded</small>
                </div>
                <div class="card">
                    <h3>Events</h3>
                    <div class="count"><?php echo $events_count; ?></div>
                    <small>Records Seeded</small>
                </div>
                <div class="card">
                    <h3>Registrations</h3>
                    <div class="count"><?php echo $registrations_count; ?></div>
                    <small>Records Seeded</small>
                </div>
            </div>

            <div class="actions">
                <a href="view_tables.php" class="btn btn-primary">View Seeded Database Records</a>
                <form method="POST" style="display:inline;">
                    <button type="submit" name="init_db" class="btn btn-secondary" onclick="return confirm('Re-run database schema import?');">Re-Seed Database</button>
                </form>
            </div>
        <?php else: ?>
            <div class="status-box status-error">
                <strong>Database tables not created yet!</strong> Click the button below to initialize tables and insert seed data from schema.sql.
            </div>
            <div class="actions">
                <form method="POST">
                    <button type="submit" name="init_db" class="btn btn-primary">Initialize Schema & Seed Data</button>
                </form>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="status-box status-error">
            <strong>Connection Failed!</strong><br>
            <?php echo htmlspecialchars(isset($db_connection_error) ? $db_connection_error : "Could not connect to MySQL server."); ?><br><br>
            <em>Please start the MySQL module in your XAMPP Control Panel.</em>
        </div>

        <div class="actions">
            <form method="POST">
                <button type="submit" name="init_db" class="btn btn-primary">Retry Database Connection & Setup</button>
            </form>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
