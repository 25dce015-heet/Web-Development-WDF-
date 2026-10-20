<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET["switch_role"])) {
    $targetRole = $_GET["switch_role"] === "student" ? "student" : "admin";
    $_SESSION["role"] = $targetRole;
    if ($targetRole === "admin") {
        $_SESSION["username"] = "admin";
        $_SESSION["name"] = "Administrator";
    } else {
        $_SESSION["username"] = "bhavya";
        $_SESSION["name"] = "Bhavya Patel";
    }
}

if (!isset($_SESSION["role"])) {
    $_SESSION["role"] = "admin";
    $_SESSION["username"] = "admin";
    $_SESSION["name"] = "Administrator";
}
?>
