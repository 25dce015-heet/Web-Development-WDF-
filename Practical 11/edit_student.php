<?php
require_once __DIR__ . "/db.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
if ($id <= 0) {
    header("Location: index.php");
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$student) {
    header("Location: index.php?msg=error");
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_id = isset($_POST["student_id"]) ? trim($_POST["student_id"]) : "";
    $name = isset($_POST["name"]) ? trim($_POST["name"]) : "";
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $mobile = isset($_POST["mobile"]) ? trim($_POST["mobile"]) : "";
    $course = isset($_POST["course"]) ? trim($_POST["course"]) : "";
    $year = isset($_POST["year"]) ? trim($_POST["year"]) : "";

    if (empty($student_id)) {
        $errors[] = "Student ID is required.";
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
    if (empty($year)) {
        $errors[] = "Please select a year.";
    }

    if (empty($errors)) {
        $checkStmt = mysqli_prepare($conn, "SELECT id FROM students WHERE (student_id = ? OR email = ?) AND id != ? LIMIT 1");
        mysqli_stmt_bind_param($checkStmt, "ssi", $student_id, $email, $id);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);
        if (mysqli_stmt_num_rows($checkStmt) > 0) {
            $errors[] = "Another student already exists with this Student ID or Email.";
        }
        mysqli_stmt_close($checkStmt);
    }

    if (empty($errors)) {
        $updateStmt = mysqli_prepare($conn, "UPDATE students SET student_id = ?, name = ?, email = ?, mobile = ?, course = ?, year = ? WHERE id = ?");
        mysqli_stmt_bind_param($updateStmt, "sssssii", $student_id, $name, $email, $mobile, $course, $year, $id);
        if (mysqli_stmt_execute($updateStmt)) {
            mysqli_stmt_close($updateStmt);
            header("Location: index.php?msg=updated");
            exit();
        } else {
            $errors[] = "Failed to update record: " . mysqli_error($conn);
            mysqli_stmt_close($updateStmt);
        }
    }

    $student["student_id"] = $student_id;
    $student["name"] = $name;
    $student["email"] = $email;
    $student["mobile"] = $mobile;
    $student["course"] = $course;
    $student["year"] = $year;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - Practical 11</title>
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
            color: #ffc107;
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
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Edit Student Record</h1>
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

    <form method="POST" action="edit_student.php?id=<?php echo $id; ?>">
        <div class="form-group">
            <label for="student_id">Student ID / Roll No</label>
            <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>" required>
        </div>

        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="mobile">Mobile Number</label>
            <input type="tel" id="mobile" name="mobile" value="<?php echo htmlspecialchars($student['mobile']); ?>" required>
        </div>

        <div class="form-group">
            <label for="course">Course</label>
            <select id="course" name="course" required>
                <option value="BCA" <?php echo $student['course'] === 'BCA' ? 'selected' : ''; ?>>BCA</option>
                <option value="B.Tech CSE" <?php echo $student['course'] === 'B.Tech CSE' ? 'selected' : ''; ?>>B.Tech CSE</option>
                <option value="B.Tech IT" <?php echo $student['course'] === 'B.Tech IT' ? 'selected' : ''; ?>>B.Tech IT</option>
                <option value="BBA" <?php echo $student['course'] === 'BBA' ? 'selected' : ''; ?>>BBA</option>
                <option value="B.Com" <?php echo $student['course'] === 'B.Com' ? 'selected' : ''; ?>>B.Com</option>
            </select>
        </div>

        <div class="form-group">
            <label for="year">Year</label>
            <select id="year" name="year" required>
                <option value="1" <?php echo $student['year'] == 1 ? 'selected' : ''; ?>>1st Year</option>
                <option value="2" <?php echo $student['year'] == 2 ? 'selected' : ''; ?>>2nd Year</option>
                <option value="3" <?php echo $student['year'] == 3 ? 'selected' : ''; ?>>3rd Year</option>
                <option value="4" <?php echo $student['year'] == 4 ? 'selected' : ''; ?>>4th Year</option>
            </select>
        </div>

        <button type="submit">Update Student Record</button>
    </form>
</div>

</body>
</html>
