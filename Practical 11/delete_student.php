<?php
require_once __DIR__ . "/db.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

if ($id > 0 && $conn) {
    $stmt = mysqli_prepare($conn, "DELETE FROM students WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: index.php?msg=deleted");
        exit();
    }
    mysqli_stmt_close($stmt);
}

header("Location: index.php?msg=error");
exit();
?>
