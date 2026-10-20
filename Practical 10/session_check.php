<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeoutDuration = 900;

if (isset($_SESSION["last_activity"])) {
    if ((time() - $_SESSION["last_activity"]) > $timeoutDuration) {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header("Location: login.php?error=timeout");
        exit();
    }
}
$_SESSION["last_activity"] = time();

function require_login() {
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php?error=unauthorized");
        exit();
    }
}

function require_role($expectedRole) {
    require_login();
    if ($_SESSION["role"] !== $expectedRole) {
        if ($_SESSION["role"] === "admin") {
            header("Location: admin_dashboard.php?error=role_mismatch");
        } else {
            header("Location: student_dashboard.php?error=role_mismatch");
        }
        exit();
    }
}
?>
