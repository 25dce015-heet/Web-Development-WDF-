<?php

session_start();


if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    die("Invalid request.");

}


$email = trim(
    $_POST["email"] ?? ""
);

$password =
    $_POST["password"] ?? "";


if ($email === "" || $password === "") {

    die("
        <h2>Login Failed</h2>

        <p>
            Please enter email and password.
        </p>

        <a href='login.html'>
            Go Back
        </a>
    ");

}


$file = "data/students.json";


if (!file_exists($file)) {

    die("
        <h2>
            No registered students found.
        </h2>

        <a href='register.html'>
            Register
        </a>
    ");

}


$students = json_decode(

    file_get_contents($file),

    true

);


if (!is_array($students)) {

    $students = [];

}


foreach ($students as $student) {

    if (
        $student["email"] === $email
        &&
        password_verify(
            $password,
            $student["password"]
        )
    ) {

        $_SESSION["student_name"] =
            $student["name"];

        $_SESSION["student_email"] =
            $student["email"];

        $_SESSION["course"] =
            $student["course"];

        $_SESSION["year"] =
            $student["year"];

        header(
            "Location: dashboard.php"
        );

        exit;

    }

}


echo "

<!DOCTYPE html>

<html>

<head>

<title>Login Failed</title>

<link
    rel='stylesheet'
    href='css/style.css'
>

</head>

<body>

<main>

<section>

<h2>
Invalid Login
</h2>

<p>
Email or password is incorrect.
</p>

<a
    href='login.html'
    class='btn'
>
Try Again
</a>

</section>

</main>

</body>

</html>

";

?>