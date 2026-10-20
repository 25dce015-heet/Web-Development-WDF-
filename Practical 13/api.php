<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . "/db.php";

$action = isset($_GET["action"]) ? trim($_GET["action"]) : "";

if (!$conn) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Database connection failure."]);
    exit();
}

$rawInput = file_get_contents("php://input");
$body = json_decode($rawInput, true);
if (!is_array($body)) {
    $body = $_POST;
}

if ($action === "list") {
    $query = "SELECT * FROM events ORDER BY event_date ASC";
    $result = mysqli_query($conn, $query);
    $events = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $events[] = $row;
        }
        mysqli_free_result($result);
    }
    echo json_encode(["success" => true, "data" => $events]);
    exit();
}

if ($action === "get") {
    $id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
    if ($id <= 0) {
        echo json_encode(["success" => false, "error" => "Invalid event ID."]);
        exit();
    }
    $stmt = mysqli_prepare($conn, "SELECT * FROM events WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $event = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if ($event) {
        echo json_encode(["success" => true, "data" => $event]);
    } else {
        echo json_encode(["success" => false, "error" => "Event not found."]);
    }
    exit();
}

if ($action === "create" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $event_title = isset($body["event_title"]) ? trim($body["event_title"]) : "";
    $event_date = isset($body["event_date"]) ? trim($body["event_date"]) : "";
    $event_time = isset($body["event_time"]) ? trim($body["event_time"]) : "";
    $venue = isset($body["venue"]) ? trim($body["venue"]) : "";
    $category = isset($body["category"]) ? trim($body["category"]) : "Technical";
    $description = isset($body["description"]) ? trim($body["description"]) : "";
    $status = isset($body["status"]) ? trim($body["status"]) : "Upcoming";

    if (empty($event_title) || empty($event_date) || empty($event_time) || empty($venue)) {
        echo json_encode(["success" => false, "error" => "Please fill in all required fields."]);
        exit();
    }

    $poster = "default.png";
    $stmt = mysqli_prepare($conn, "INSERT INTO events (event_title, event_date, event_time, venue, category, description, poster, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssssss", $event_title, $event_date, $event_time, $venue, $category, $description, $poster, $status);

    if (mysqli_stmt_execute($stmt)) {
        $newId = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        echo json_encode(["success" => true, "message" => "Event created successfully.", "id" => $newId]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
        mysqli_stmt_close($stmt);
    }
    exit();
}

if ($action === "update" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $id = isset($_GET["id"]) ? intval($_GET["id"]) : (isset($body["id"]) ? intval($body["id"]) : 0);
    if ($id <= 0) {
        echo json_encode(["success" => false, "error" => "Invalid event ID for update."]);
        exit();
    }

    $event_title = isset($body["event_title"]) ? trim($body["event_title"]) : "";
    $event_date = isset($body["event_date"]) ? trim($body["event_date"]) : "";
    $event_time = isset($body["event_time"]) ? trim($body["event_time"]) : "";
    $venue = isset($body["venue"]) ? trim($body["venue"]) : "";
    $category = isset($body["category"]) ? trim($body["category"]) : "Technical";
    $description = isset($body["description"]) ? trim($body["description"]) : "";
    $status = isset($body["status"]) ? trim($body["status"]) : "Upcoming";

    if (empty($event_title) || empty($event_date) || empty($event_time) || empty($venue)) {
        echo json_encode(["success" => false, "error" => "All required fields must be filled."]);
        exit();
    }

    $stmt = mysqli_prepare($conn, "UPDATE events SET event_title = ?, event_date = ?, event_time = ?, venue = ?, category = ?, description = ?, status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssssssi", $event_title, $event_date, $event_time, $venue, $category, $description, $status, $id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        echo json_encode(["success" => true, "message" => "Event updated successfully."]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
        mysqli_stmt_close($stmt);
    }
    exit();
}

if ($action === "delete" && $_SERVER["REQUEST_METHOD"] === "POST") {
    $id = isset($_GET["id"]) ? intval($_GET["id"]) : (isset($body["id"]) ? intval($body["id"]) : 0);
    if ($id <= 0) {
        echo json_encode(["success" => false, "error" => "Invalid ID for deletion."]);
        exit();
    }

    $stmt = mysqli_prepare($conn, "DELETE FROM events WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        echo json_encode(["success" => true, "message" => "Event deleted successfully."]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
        mysqli_stmt_close($stmt);
    }
    exit();
}

echo json_encode(["success" => false, "error" => "Invalid action requested."]);
?>
