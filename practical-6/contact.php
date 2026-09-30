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

    die("Invalid email address.");

}


if ($message === "") {

    die("Message is required.");

}


if (!is_dir("data")) {

    mkdir(
        "data",
        0777,
        true
    );

}


$file =
    "data/contacts.json";


$contacts = [];


if (file_exists($file)) {

    $contacts =
        json_decode(
            file_get_contents($file),
            true
        );

}


if (!is_array($contacts)) {

    $contacts = [];

}


$contacts[] = [

    "name" =>
        htmlspecialchars($name),

    "email" =>
        htmlspecialchars($email),

    "message" =>
        htmlspecialchars($message),

    "date" =>
        date("Y-m-d H:i:s")

];


file_put_contents(

    $file,

    json_encode(
        $contacts,
        JSON_PRETTY_PRINT
    )

);


echo "

<!DOCTYPE html>

<html>

<head>

<title>
Message Sent
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
Message Sent Successfully!
</h2>

<p>
Thank you,
"
. htmlspecialchars($name)
. ".
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