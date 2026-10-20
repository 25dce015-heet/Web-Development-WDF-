<?php
require_once __DIR__ . "/db.php";

$categoryFilter = isset($_GET["category"]) ? trim($_GET["category"]) : "";
$statusFilter = isset($_GET["status"]) ? trim($_GET["status"]) : "";

$query = "SELECT * FROM events WHERE 1=1";
$params = [];
$types = "";

if (!empty($categoryFilter)) {
    $query .= " AND category = ?";
    $params[] = $categoryFilter;
    $types .= "s";
}

if (!empty($statusFilter)) {
    $query .= " AND status = ?";
    $params[] = $statusFilter;
    $types .= "s";
}

$query .= " ORDER BY event_date ASC";

$events = [];
if ($conn) {
    if (!empty($params)) {
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($res)) {
            $events[] = $row;
        }
        mysqli_stmt_close($stmt);
    } else {
        $res = mysqli_query($conn, $query);
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $events[] = $row;
            }
            mysqli_free_result($res);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 12 - Event Management CRUD</title>
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
            max-width: 1100px;
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
        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
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
        .filter-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            align-items: center;
        }
        .filter-bar select {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            outline: none;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-sm { padding: 4px 10px; font-size: 12px; }
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
            vertical-align: middle;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        tr:hover { background-color: #f9fbfd; }
        .poster-thumb {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-upcoming {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-completed {
            background-color: #e2e3e5;
            color: #383d41;
        }
        .actions-cell {
            white-space: nowrap;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div>
            <h1>Event Management CRUD</h1>
            <p style="color:#666; font-size:13px;">Practical 12: Event Management with Poster Upload & File Validation</p>
        </div>
        <a href="add_event.php" class="btn btn-success">+ Add New Event</a>
    </div>

    <?php if (isset($_GET["msg"])): ?>
        <?php if ($_GET["msg"] === "added"): ?>
            <div class="alert alert-success">Event and poster uploaded successfully!</div>
        <?php elseif ($_GET["msg"] === "updated"): ?>
            <div class="alert alert-success">Event details and poster updated successfully!</div>
        <?php elseif ($_GET["msg"] === "deleted"): ?>
            <div class="alert alert-success">Event deleted successfully!</div>
        <?php elseif ($_GET["msg"] === "error"): ?>
            <div class="alert alert-danger">An error occurred during event processing.</div>
        <?php endif; ?>
    <?php endif; ?>

    <form method="GET" action="index.php" class="filter-bar">
        <select name="category">
            <option value="">All Categories</option>
            <option value="Technical" <?php echo $categoryFilter === 'Technical' ? 'selected' : ''; ?>>Technical</option>
            <option value="Seminar" <?php echo $categoryFilter === 'Seminar' ? 'selected' : ''; ?>>Seminar</option>
            <option value="Workshop" <?php echo $categoryFilter === 'Workshop' ? 'selected' : ''; ?>>Workshop</option>
            <option value="Cultural & Tech" <?php echo $categoryFilter === 'Cultural & Tech' ? 'selected' : ''; ?>>Cultural & Tech</option>
        </select>

        <select name="status">
            <option value="">All Statuses</option>
            <option value="Upcoming" <?php echo $statusFilter === 'Upcoming' ? 'selected' : ''; ?>>Upcoming</option>
            <option value="Completed" <?php echo $statusFilter === 'Completed' ? 'selected' : ''; ?>>Completed</option>
        </select>

        <button type="submit" class="btn btn-primary">Filter</button>
        <?php if (!empty($categoryFilter) || !empty($statusFilter)): ?>
            <a href="index.php" class="btn btn-secondary">Reset</a>
        <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>Poster</th>
                <th>Title</th>
                <th>Date & Time</th>
                <th>Venue</th>
                <th>Category</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($events)): ?>
                <tr><td colspan="7" style="text-align:center; padding:20px; color:#888;">No events registered yet.</td></tr>
            <?php else: ?>
                <?php foreach ($events as $ev): ?>
                    <tr>
                        <td>
                            <?php 
                            $posterPath = "uploads/" . $ev["poster"];
                            if (!file_exists(__DIR__ . "/" . $posterPath) || empty($ev["poster"])) {
                                $posterPath = "uploads/default.png";
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($posterPath); ?>" alt="Poster" class="poster-thumb">
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($ev["event_title"]); ?></strong>
                            <div style="font-size:12px; color:#777; margin-top:3px; max-width:300px;">
                                <?php echo htmlspecialchars($ev["description"]); ?>
                            </div>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($ev["event_date"]); ?><br>
                            <small style="color:#666;"><?php echo htmlspecialchars($ev["event_time"]); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($ev["venue"]); ?></td>
                        <td><?php echo htmlspecialchars($ev["category"]); ?></td>
                        <td>
                            <span class="badge <?php echo $ev["status"] === 'Upcoming' ? 'badge-upcoming' : 'badge-completed'; ?>">
                                <?php echo htmlspecialchars($ev["status"]); ?>
                            </span>
                        </td>
                        <td class="actions-cell">
                            <a href="edit_event.php?id=<?php echo $ev['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_event.php?id=<?php echo $ev['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete event <?php echo htmlspecialchars(addslashes($ev['event_title'])); ?>?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
