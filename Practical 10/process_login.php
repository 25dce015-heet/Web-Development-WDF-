<?php
require_once __DIR__ . "/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}

if (!$conn) {
    header("Location: login.php?error=invalid");
    exit();
}

$identifier = isset($_POST["identifier"]) ? trim($_POST["identifier"]) : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";
$rememberMe = isset($_POST["remember_me"]);

if (empty($identifier) || empty($password)) {
    header("Location: login.php?error=invalid");
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT id, username, email, name, password_hash, role FROM users WHERE username = ? OR email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ss", $identifier, $identifier);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if ($user && password_verify($password, $user["password_hash"])) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_regenerate_id(true);

    $_SESSION["user_id"] = $user["id"];
    $_SESSION["username"] = $user["username"];
    $_SESSION["name"] = $user["name"];
    $_SESSION["email"] = $user["email"];
    $_SESSION["role"] = $user["role"];
    $_SESSION["last_activity"] = time();

    if ($rememberMe) {
        setcookie("remember_user", $user["username"], time() + (86400 * 7), "/", "", false, true);
    } else {
        if (isset($_COOKIE["remember_user"])) {
            setcookie("remember_user", "", time() - 3600, "/");
        }
    }

    if ($user["role"] === "admin") {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit();
} else {
    header("Location: login.php?error=invalid");
    exit();
}
?>
