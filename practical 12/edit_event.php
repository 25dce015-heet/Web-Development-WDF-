<?php
require_once __DIR__ . "/db.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
if ($id <= 0) {
    header("Location: index.php");
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT * FROM events WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$event = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$event) {
    header("Location: index.php?msg=error");
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $event_title = isset($_POST["event_title"]) ? trim($_POST["event_title"]) : "";
    $event_date = isset($_POST["event_date"]) ? trim($_POST["event_date"]) : "";
    $event_time = isset($_POST["event_time"]) ? trim($_POST["event_time"]) : "";
    $venue = isset($_POST["venue"]) ? trim($_POST["venue"]) : "";
    $category = isset($_POST["category"]) ? trim($_POST["category"]) : "Technical";
    $description = isset($_POST["description"]) ? trim($_POST["description"]) : "";
    $status = isset($_POST["status"]) ? trim($_POST["status"]) : "Upcoming";

    if (empty($event_title)) {
        $errors[] = "Event Title is required.";
    }
    if (empty($event_date)) {
        $errors[] = "Event Date is required.";
    }
    if (empty($event_time)) {
        $errors[] = "Event Time is required.";
    }
    if (empty($venue)) {
        $errors[] = "Venue is required.";
    }

    $posterFilename = $event["poster"];

    if (isset($_FILES["poster"]) && $_FILES["poster"]["error"] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES["poster"];
        if ($file["error"] !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading poster image.";
        } else {
            $allowedExtensions = ["jpg", "jpeg", "png", "webp"];
            $allowedMimes = ["image/jpeg", "image/png", "image/webp"];
            $fileExtension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
            $fileSize = $file["size"];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file["tmp_name"]);
            finfo_close($finfo);

            if (!in_array($fileExtension, $allowedExtensions) || !in_array($mimeType, $allowedMimes)) {
                $errors[] = "Invalid poster file type. Only JPG, PNG, and WEBP images are allowed.";
            }

            if ($fileSize > 2 * 1024 * 1024) {
                $errors[] = "Poster image file size exceeds 2MB.";
            }

            if (empty($errors)) {
                $newFilename = "poster_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $fileExtension;
                $targetPath = __DIR__ . "/uploads/" . $newFilename;
                if (move_uploaded_file($file["tmp_name"], $targetPath)) {
                    if ($event["poster"] !== "default.png" && file_exists(__DIR__ . "/uploads/" . $event["poster"])) {
                        unlink(__DIR__ . "/uploads/" . $event["poster"]);
                    }
                    $posterFilename = $newFilename;
                } else {
                    $errors[] = "Failed to save uploaded poster file.";
                }
            }
        }
    }

    if (empty($errors)) {
        $updateStmt = mysqli_prepare($conn, "UPDATE events SET event_title = ?, event_date = ?, event_time = ?, venue = ?, category = ?, description = ?, poster = ?, status = ? WHERE id = ?");
        mysqli_stmt_bind_param($updateStmt, "ssssssssi", $event_title, $event_date, $event_time, $venue, $category, $description, $posterFilename, $status, $id);
        if (mysqli_stmt_execute($updateStmt)) {
            mysqli_stmt_close($updateStmt);
            header("Location: index.php?msg=updated");
            exit();
        } else {
            $errors[] = "Update error: " . mysqli_error($conn);
            mysqli_stmt_close($updateStmt);
        }
    }

    $event["event_title"] = $event_title;
    $event["event_date"] = $event_date;
    $event["event_time"] = $event_time;
    $event["venue"] = $venue;
    $event["category"] = $category;
    $event["description"] = $description;
    $event["status"] = $status;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - Practical 12</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background-color: #f4f6f9;
            padding: 30px 15px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #ffc107;
            font-size: 22px;
        }
        .btn-back {
            padding: 6px 14px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 13px;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        ul { margin-left: 20px; }
        .form-group {
            margin-bottom: 16px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            font-size: 14px;
            color: #333;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        .row-2 {
            display: flex;
            gap: 15px;
        }
        .row-2 .form-group {
            flex: 1;
        }
        .current-poster {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 6px;
        }
        .current-poster img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Edit Event Details</h1>
        <a href="index.php" class="btn-back">&larr; Back to Events</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert-danger">
            <strong>Errors Encountered:</strong>
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?php echo htmlspecialchars($e); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="edit_event.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label for="event_title">Event Title</label>
            <input type="text" id="event_title" name="event_title" value="<?php echo htmlspecialchars($event['event_title']); ?>" required>
        </div>

        <div class="row-2">
            <div class="form-group">
                <label for="event_date">Event Date</label>
                <input type="date" id="event_date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
            </div>
            <div class="form-group">
                <label for="event_time">Event Time</label>
                <input type="time" id="event_time" name="event_time" value="<?php echo htmlspecialchars($event['event_time']); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="venue">Venue</label>
            <input type="text" id="venue" name="venue" value="<?php echo htmlspecialchars($event['venue']); ?>" required>
        </div>

        <div class="row-2">
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category">
                    <option value="Technical" <?php echo $event['category'] === 'Technical' ? 'selected' : ''; ?>>Technical</option>
                    <option value="Seminar" <?php echo $event['category'] === 'Seminar' ? 'selected' : ''; ?>>Seminar</option>
                    <option value="Workshop" <?php echo $event['category'] === 'Workshop' ? 'selected' : ''; ?>>Workshop</option>
                    <option value="Cultural & Tech" <?php echo $event['category'] === 'Cultural & Tech' ? 'selected' : ''; ?>>Cultural & Tech</option>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="Upcoming" <?php echo $event['status'] === 'Upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                    <option value="Completed" <?php echo $event['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Event Description</label>
            <textarea id="description" name="description"><?php echo htmlspecialchars($event['description']); ?></textarea>
        </div>

        <div class="current-poster">
            <img src="uploads/<?php echo htmlspecialchars($event['poster']); ?>" alt="Current Poster">
            <div>
                <strong>Current Poster:</strong> <?php echo htmlspecialchars($event['poster']); ?><br>
                <small style="color:#666;">Upload a new image below if you want to replace it.</small>
            </div>
        </div>

        <div class="form-group">
            <label for="poster">Replace Poster Image (Optional, Max 2MB)</label>
            <input type="file" id="poster" name="poster" accept=".jpg,.jpeg,.png,.webp">
        </div>

        <button type="submit">Update Event</button>
    </form>
</div>

</body>
</html>
