<?php
require_once __DIR__ . "/db.php";

$errors = [];
$student_id = "";
$name = "";
$email = "";
$mobile = "";
$course = "";
$year = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_id = isset($_POST["student_id"]) ? trim($_POST["student_id"]) : "";
    $name = isset($_POST["name"]) ? trim($_POST["name"]) : "";
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $mobile = isset($_POST["mobile"]) ? trim($_POST["mobile"]) : "";
    $course = isset($_POST["course"]) ? trim($_POST["course"]) : "";
    $year = isset($_POST["year"]) ? trim($_POST["year"]) : "";

    if (empty($student_id)) {
        $errors[] = "Student ID / Roll Number is required.";
    }
    if (empty($name) || strlen($name) < 2) {
        $errors[] = "Student Name must be at least 2 characters.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email address is required.";
    }
    if (empty($mobile) || !preg_match("/^[0-9]{10}$/", $mobile)) {
        $errors[] = "Mobile number must be 10 digits.";
    }
    if (empty($course)) {
        $errors[] = "Please select a course.";
    }
    if (empty($year) || !in_array($year, ["1", "2", "3", "4"])) {
        $errors[] = "Please select a valid year.";
    }

    if (empty($errors)) {
        $checkStmt = mysqli_prepare($conn, "SELECT id FROM students WHERE student_id = ? OR email = ? LIMIT 1");
        mysqli_stmt_bind_param($checkStmt, "ss", $student_id, $email);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);
        if (mysqli_stmt_num_rows($checkStmt) > 0) {
            $errors[] = "A student with this Student ID or Email already exists.";
        }
        mysqli_stmt_close($checkStmt);
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO students (student_id, name, email, mobile, course, year) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssssi", $student_id, $name, $email, $mobile, $course, $year);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: index.php?msg=added");
            exit();
        } else {
            $errors[] = "Database insert error: " . mysqli_error($conn);
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student - Practical 11</title>
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
            font-size: 22px;
        }
        .btn-back {
            padding: 6px 14px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 13px;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        ul {
            margin-left: 20px;
        }
        .form-group {
            margin-bottom: 16px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            font-size: 14px;
            color: #333;
        }
        input, select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
        }
        input:focus, select:focus {
            border-color: #007bff;
        }
        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }
        button[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Add New Student</h1>
        <a href="index.php" class="btn-back">&larr; Back to List</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert-danger">
            <strong>Errors Encountered:</strong>
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?php echo htmlspecialchars($e); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="add_student.php">
        <div class="form-group">
            <label for="student_id">Student ID / Roll No</label>
            <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>" placeholder="e.g. 25DCE030" required>
        </div>

        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" placeholder="Student name" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="student@charusat.edu.in" required>
        </div>

        <div class="form-group">
            <label for="mobile">Mobile Number</label>
            <input type="tel" id="mobile" name="mobile" value="<?php echo htmlspecialchars($mobile); ?>" placeholder="10-digit mobile" required>
        </div>

        <div class="form-group">
            <label for="course">Course</label>
            <select id="course" name="course" required>
                <option value="">-- Select Course --</option>
                <option value="BCA" <?php echo $course === 'BCA' ? 'selected' : ''; ?>>BCA</option>
                <option value="B.Tech CSE" <?php echo $course === 'B.Tech CSE' ? 'selected' : ''; ?>>B.Tech CSE</option>
                <option value="B.Tech IT" <?php echo $course === 'B.Tech IT' ? 'selected' : ''; ?>>B.Tech IT</option>
                <option value="BBA" <?php echo $course === 'BBA' ? 'selected' : ''; ?>>BBA</option>
                <option value="B.Com" <?php echo $course === 'B.Com' ? 'selected' : ''; ?>>B.Com</option>
            </select>
        </div>

        <div class="form-group">
            <label for="year">Year</label>
            <select id="year" name="year" required>
                <option value="">-- Select Year --</option>
                <option value="1" <?php echo $year == 1 ? 'selected' : ''; ?>>1st Year</option>
                <option value="2" <?php echo $year == 2 ? 'selected' : ''; ?>>2nd Year</option>
                <option value="3" <?php echo $year == 3 ? 'selected' : ''; ?>>3rd Year</option>
                <option value="4" <?php echo $year == 4 ? 'selected' : ''; ?>>4th Year</option>
            </select>
        </div>

        <button type="submit">Save Student Record</button>
    </form>
</div>

</body>
</html>
