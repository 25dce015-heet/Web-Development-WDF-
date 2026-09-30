<?php

if (
    $_SERVER["REQUEST_METHOD"]
    !==
    "POST"
) {

    die("Invalid request.");

}


$name =
    trim($_POST["name"] ?? "");


$email =
    trim($_POST["email"] ?? "");


$course =
    trim($_POST["course"] ?? "");


$rating =
    trim($_POST["rating"] ?? "");


$message =
    trim($_POST["message"] ?? "");


if ($name === "") {

    die("Name is required.");

}


if (
    !filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    )
) {

    die("Invalid email.");

}


if ($rating === "") {

    die("Please select a rating.");

}


if (!is_dir("data")) {

    mkdir(
        "data",
        0777,
        true
    );

}


$file =
    "data/feedback.json";


$feedback = [];


if (file_exists($file)) {

    $feedback =
        json_decode(
            file_get_contents($file),
            true
        );

}


if (!is_array($feedback)) {

    $feedback = [];

}


$feedback[] = [

    "name" =>
        htmlspecialchars($name),

    "email" =>
        htmlspecialchars($email),

    "course" =>
        htmlspecialchars($course),

    "rating" =>
        htmlspecialchars($rating),

    "message" =>
        htmlspecialchars($message),

    "date" =>
        date("Y-m-d H:i:s")

];


file_put_contents(

    $file,

    json_encode(
        $feedback,
        JSON_PRETTY_PRINT
    )

);


echo "

<!DOCTYPE html>

<html>

<head>

<title>
Feedback Submitted
</title>

<link
    rel='stylesheet'
    href='css/style.css'
>

</head>

<body>

<main>

<section>

<h2>
Thank You For Your Feedback!
</h2>

<p>
Your feedback has been submitted successfully.
</p>

<a
    href='index.html'
    class='btn'
>
Back to Home
</a>

</section>

</main>

</body>

</html>

";

?>