<?php
require_once __DIR__ . "/db.php";

$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";
$courseFilter = isset($_GET["course"]) ? trim($_GET["course"]) : "";
$yearFilter = isset($_GET["year"]) ? trim($_GET["year"]) : "";

$query = "SELECT * FROM students WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR student_id LIKE ? OR email LIKE ? OR mobile LIKE ?)";
    $like = "%" . $search . "%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "ssss";
}

if (!empty($courseFilter)) {
    $query .= " AND course = ?";
    $params[] = $courseFilter;
    $types .= "s";
}

if (!empty($yearFilter)) {
    $query .= " AND year = ?";
    $params[] = $yearFilter;
    $types .= "i";
}

$query .= " ORDER BY id ASC";

$students = [];
if ($conn) {
    if (!empty($params)) {
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($res)) {
            $students[] = $row;
        }
        mysqli_stmt_close($stmt);
    } else {
        $res = mysqli_query($conn, $query);
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $students[] = $row;
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
    <title>Practical 11 - Student Management CRUD</title>
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
        .filter-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            align-items: center;
        }
        .filter-form input, .filter-form select {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            outline: none;
        }
        .filter-form input[type="text"] {
            flex: 2;
            min-width: 200px;
        }
        .filter-form select {
            flex: 1;
            min-width: 130px;
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
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .btn-sm {
            padding: 4px 10px;
            font-size: 12px;
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
            color: #495057;
        }
        tr:hover {
            background-color: #f9fbfd;
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
            <h1>Student Management CRUD</h1>
            <p style="color:#666; font-size:13px;">Practical 11: MySQLi Database CRUD with Search and Filter</p>
        </div>
        <a href="add_student.php" class="btn btn-success">+ Add New Student</a>
    </div>

    <?php if (isset($_GET["msg"])): ?>
        <?php if ($_GET["msg"] === "added"): ?>
            <div class="alert alert-success">Student record created successfully!</div>
        <?php elseif ($_GET["msg"] === "updated"): ?>
            <div class="alert alert-success">Student details updated successfully!</div>
        <?php elseif ($_GET["msg"] === "deleted"): ?>
            <div class="alert alert-success">Student record deleted successfully!</div>
        <?php elseif ($_GET["msg"] === "error"): ?>
            <div class="alert alert-danger">An error occurred while executing the operation.</div>
        <?php endif; ?>
    <?php endif; ?>

    <form method="GET" action="index.php" class="filter-form">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name, roll no, or email...">

        <select name="course">
            <option value="">All Courses</option>
            <option value="BCA" <?php echo $courseFilter === 'BCA' ? 'selected' : ''; ?>>BCA</option>
            <option value="B.Tech CSE" <?php echo $courseFilter === 'B.Tech CSE' ? 'selected' : ''; ?>>B.Tech CSE</option>
            <option value="B.Tech IT" <?php echo $courseFilter === 'B.Tech IT' ? 'selected' : ''; ?>>B.Tech IT</option>
            <option value="BBA" <?php echo $courseFilter === 'BBA' ? 'selected' : ''; ?>>BBA</option>
            <option value="B.Com" <?php echo $courseFilter === 'B.Com' ? 'selected' : ''; ?>>B.Com</option>
        </select>

        <select name="year">
            <option value="">All Years</option>
            <option value="1" <?php echo $yearFilter === '1' ? 'selected' : ''; ?>>Year 1</option>
            <option value="2" <?php echo $yearFilter === '2' ? 'selected' : ''; ?>>Year 2</option>
            <option value="3" <?php echo $yearFilter === '3' ? 'selected' : ''; ?>>Year 3</option>
            <option value="4" <?php echo $yearFilter === '4' ? 'selected' : ''; ?>>Year 4</option>
        </select>

        <button type="submit" class="btn btn-primary">Filter</button>
        <?php if (!empty($search) || !empty($courseFilter) || !empty($yearFilter)): ?>
            <a href="index.php" class="btn btn-secondary">Reset</a>
        <?php endif; ?>
    </form>

    <div style="margin-bottom:10px; font-size:14px; color:#555;">
        Showing <strong><?php echo count($students); ?></strong> student records
    </div>

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
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($students)): ?>
                <tr><td colspan="8" style="text-align:center; padding:20px; color:#888;">No student records found matching your criteria.</td></tr>
            <?php else: ?>
                <?php foreach ($students as $s): ?>
                    <tr>
                        <td><?php echo $s["id"]; ?></td>
                        <td><strong><?php echo htmlspecialchars($s["student_id"]); ?></strong></td>
                        <td><?php echo htmlspecialchars($s["name"]); ?></td>
                        <td><?php echo htmlspecialchars($s["email"]); ?></td>
                        <td><?php echo htmlspecialchars($s["mobile"]); ?></td>
                        <td><?php echo htmlspecialchars($s["course"]); ?></td>
                        <td><?php echo htmlspecialchars($s["year"]); ?></td>
                        <td class="actions-cell">
                            <a href="edit_student.php?id=<?php echo $s['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_student.php?id=<?php echo $s['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete <?php echo htmlspecialchars(addslashes($s['name'])); ?>?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
