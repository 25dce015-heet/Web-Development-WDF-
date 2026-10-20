<?php
$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

$fullName = isset($_POST["fullName"]) ? trim($_POST["fullName"]) : "";
$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$mobile = isset($_POST["mobile"]) ? trim($_POST["mobile"]) : "";
$course = isset($_POST["course"]) ? trim($_POST["course"]) : "";
$year = isset($_POST["year"]) ? trim($_POST["year"]) : "";
$gender = isset($_POST["gender"]) ? trim($_POST["gender"]) : "";
$message = isset($_POST["message"]) ? trim($_POST["message"]) : "";

$fullName = htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8');
$email = filter_var($email, FILTER_SANITIZE_EMAIL);
$mobile = htmlspecialchars($mobile, ENT_QUOTES, 'UTF-8');
$course = htmlspecialchars($course, ENT_QUOTES, 'UTF-8');
$year = htmlspecialchars($year, ENT_QUOTES, 'UTF-8');
$gender = htmlspecialchars($gender, ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

if (empty($fullName)) {
    $errors[] = "Full Name is required.";
} elseif (!preg_match("/^[a-zA-Z\s]{3,50}$/", $fullName)) {
    $errors[] = "Full Name must contain only letters and spaces (minimum 3 characters).";
}

if (empty($email)) {
    $errors[] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please provide a valid email address.";
}

if (empty($mobile)) {
    $errors[] = "Mobile number is required.";
} elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {
    $errors[] = "Mobile number must be exactly 10 digits.";
}

$allowedCourses = ["BCA", "BBA", "B.Tech CSE", "B.Tech IT", "B.Com"];
if (empty($course) || !in_array($course, $allowedCourses)) {
    $errors[] = "Please select a valid course.";
}

$allowedYears = ["1", "2", "3", "4"];
if (empty($year) || !in_array($year, $allowedYears)) {
    $errors[] = "Please select a valid year.";
}

$allowedGenders = ["male", "female", "other"];
if (empty($gender) || !in_array($gender, $allowedGenders)) {
    $errors[] = "Please select a valid gender.";
}

if (empty($message)) {
    $errors[] = "Message cannot be empty.";
}

$submittedAt = date("Y-m-d H:i:s");

if (empty($errors)) {
    $csvFile = __DIR__ . "/contacts.csv";
    $csvRow = [$fullName, $email, $mobile, $course, $year, $gender, $message, $submittedAt];
    $fp = fopen($csvFile, "a");
    if ($fp !== false) {
        fputcsv($fp, $csvRow);
        fclose($fp);
    }

    $jsonFile = __DIR__ . "/contacts.json";
    $currentData = [];
    if (file_exists($jsonFile)) {
        $jsonContent = file_get_contents($jsonFile);
        $decoded = json_decode($jsonContent, true);
        if (is_array($decoded)) {
            $currentData = $decoded;
        }
    }

    $newEntry = [
        "full_name" => $fullName,
        "email" => $email,
        "mobile" => $mobile,
        "course" => $course,
        "year" => $year,
        "gender" => $gender,
        "message" => $message,
        "submitted_at" => $submittedAt
    ];

    $currentData[] = $newEntry;
    file_put_contents($jsonFile, json_encode($currentData, JSON_PRETTY_PRINT));

    $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 7 - Processing Result</title>
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
            max-width: 600px;
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
            margin-bottom: 5px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
        }
        .data-table td {
            padding: 10px;
            border: 1px solid #eee;
            font-size: 14px;
        }
        .data-table td:first-child {
            font-weight: bold;
            width: 35%;
            background-color: #fafafa;
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
            padding: 12px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }
        .btn-primary {
            background-color: #007bff;
            color: #fff;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
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
            <h2>Success!</h2>
            <p>Your registration details have been validated and saved successfully into CSV and JSON files.</p>
        </div>

        <h3>Submitted Information:</h3>
        <table class="data-table">
            <tr><td>Full Name</td><td><?php echo $fullName; ?></td></tr>
            <tr><td>Email</td><td><?php echo $email; ?></td></tr>
            <tr><td>Mobile</td><td><?php echo $mobile; ?></td></tr>
            <tr><td>Course</td><td><?php echo $course; ?></td></tr>
            <tr><td>Year</td><td><?php echo $year; ?></td></tr>
            <tr><td>Gender</td><td><?php echo ucfirst($gender); ?></td></tr>
            <tr><td>Message</td><td><?php echo nl2br($message); ?></td></tr>
            <tr><td>Submitted At</td><td><?php echo $submittedAt; ?></td></tr>
        </table>

        <div class="btn-group">
            <a href="index.php" class="btn btn-secondary">Submit Another</a>
            <a href="view.php" class="btn btn-primary">View All Records</a>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            <h2>Validation Errors Encountered:</h2>
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?php echo $err; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="btn-group">
            <a href="javascript:history.back()" class="btn btn-primary">Go Back & Fix Errors</a>
            <a href="index.php" class="btn btn-secondary">Reset Form</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
