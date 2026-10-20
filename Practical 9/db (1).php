<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "studenthub_db";

$conn = @mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    $db_error = mysqli_connect_error();
}
?>
