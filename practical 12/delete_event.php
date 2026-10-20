<?php
require_once __DIR__ . "/db.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($id > 0 && $conn) {
    $stmt = mysqli_prepare($conn, "SELECT poster FROM events WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $event = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($event) {
        if ($event["poster"] !== "default.png" && file_exists(__DIR__ . "/uploads/" . $event["poster"])) {
            unlink(__DIR__ . "/uploads/" . $event["poster"]);
        }

        $delStmt = mysqli_prepare($conn, "DELETE FROM events WHERE id = ?");
        mysqli_stmt_bind_param($delStmt, "i", $id);
        if (mysqli_stmt_execute($delStmt)) {
            mysqli_stmt_close($delStmt);
            header("Location: index.php?msg=deleted");
            exit();
        }
        mysqli_stmt_close($delStmt);
    }
}

header("Location: index.php?msg=error");
exit();
?>
