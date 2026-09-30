<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    die("Invalid request.");

}


$name = trim($_POST["name"] ?? "");

$email = trim($_POST["email"] ?? "");

$mobile = trim($_POST["mobile"] ?? "");

$password = $_POST["password"] ?? "";

$course = trim($_POST["course"] ?? "");

$year = trim($_POST["year"] ?? "");

$gender = trim($_POST["gender"] ?? "");

$terms = isset($_POST["terms"]);


$errors = [];


if ($name === "") {

    $errors[] = "Name is required.";

}


if (!filter_var(
    $email,
    FILTER_VALIDATE_EMAIL
)) {

    $errors[] = "Invalid email address.";

}


if (!preg_match(
    "/^[0-9]{10}$/",
    $mobile
)) {

    $errors[] =
        "Mobile number must contain 10 digits.";

}


if (strlen($password) < 6) {

    $errors[] =
        "Password must contain at least 6 characters.";

}


if ($course === "") {

    $errors[] =
        "Please select a course.";

}


if ($year === "") {

    $errors[] =
        "Please select your year.";

}


if ($gender === "") {

    $errors[] =
        "Please select your gender.";

}


if (!$terms) {

    $errors[] =
        "You must accept the terms.";

}


if (!empty($errors)) {

    echo "<h2>Registration Failed</h2>";

    echo "<ul>";

    foreach ($errors as $error) {

        echo "<li>"
            . htmlspecialchars($error)
            . "</li>";

    }

    echo "</ul>";

    echo "<a href='register.html'>Go Back</a>";

    exit;

}


if (!is_dir("data")) {

    mkdir(
        "data",
        0777,
        true
    );

}


$file = "data/students.json";


$students = [];


if (file_exists($file)) {

    $students = json_decode(
        file_get_contents($file),
        true
    );

}


if (!is_array($students)) {

    $students = [];

}


foreach ($students as $student) {

    if ($student["email"] === $email) {

        die("
            <h2>Email already registered.</h2>

            <a href='login.html'>
                Go to Login
            </a>
        ");

    }

}


$student = [

    "name" =>
        htmlspecialchars($name),

    "email" =>
        htmlspecialchars($email),

    "mobile" =>
        htmlspecialchars($mobile),

    "password" =>
        password_hash(
            $password,
            PASSWORD_DEFAULT
        ),

    "course" =>
        htmlspecialchars($course),

    "year" =>
        htmlspecialchars($year),

    "gender" =>
        htmlspecialchars($gender),

    "registered_at" =>
        date("Y-m-d H:i:s")

];


$students[] = $student;


file_put_contents(

    $file,

    json_encode(
        $students,
        JSON_PRETTY_PRINT
    )

);


echo "

<!DOCTYPE html>

<html>

<head>

<title>Registration Successful</title>

<link
    rel='stylesheet'
    href='css/style.css'
>

</head>

<body>

<main>

<section>

<h2>
Registration Successful! 🎉
</h2>

<p>
Welcome,
"
. htmlspecialchars($name)
. ".
</p>

<p>
Your account has been created successfully.
</p>

<a
    href='login.html'
    class='btn'
>
Go to Login
</a>

</section>

</main>

</body>

</html>

";

?>