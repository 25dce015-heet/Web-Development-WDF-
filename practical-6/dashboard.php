<?php

session_start();


if (!isset($_SESSION["student_email"])) {

    header(
        "Location: login.html"
    );

    exit;

}


$name =
    $_SESSION["student_name"];

$email =
    $_SESSION["student_email"];

$course =
    $_SESSION["course"];

$year =
    $_SESSION["year"];

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        StudentHub | Dashboard
    </title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>


<body>

<header>

    <h1>
        StudentHub Dashboard
    </h1>

    <p>
        Welcome,
        <?php
        echo htmlspecialchars($name);
        ?>
    </p>

    <nav>

        <a href="index.html">
            Home
        </a>

        <a href="courses.html">
            Courses
        </a>

        <a href="attendance.html">
            Attendance
        </a>

        <a href="assignments.html">
            Assignments
        </a>

        <a href="results.html">
            Results
        </a>

        <a href="logout.php">
            Logout
        </a>

    </nav>

</header>


<main>

<section>

    <h2>
        Student Information
    </h2>

    <p>

        <strong>Name:</strong>

        <?php
        echo htmlspecialchars($name);
        ?>

    </p>


    <p>

        <strong>Email:</strong>

        <?php
        echo htmlspecialchars($email);
        ?>

    </p>


    <p>

        <strong>Course:</strong>

        <?php
        echo htmlspecialchars($course);
        ?>

    </p>


    <p>

        <strong>Year:</strong>

        <?php
        echo htmlspecialchars($year);
        ?>

    </p>

</section>


<section>

    <h2>
        Dashboard Modules
    </h2>


    <div class="cards">

        <div class="card">

            <h3>
                📚 Courses
            </h3>

            <a href="courses.html">
                Open
            </a>

        </div>


        <div class="card">

            <h3>
                📊 Attendance
            </h3>

            <a href="attendance.html">
                Open
            </a>

        </div>


        <div class="card">

            <h3>
                📝 Assignments
            </h3>

            <a href="assignments.html">
                Open
            </a>

        </div>


        <div class="card">

            <h3>
                🏆 Results
            </h3>

            <a href="results.html">
                Open
            </a>

        </div>

    </div>

</section>

</main>


<footer>

    <p>
        © 2026 StudentHub Portal
    </p>

</footer>

</body>

</html>