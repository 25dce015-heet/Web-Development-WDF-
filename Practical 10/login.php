<?php
session_start();

if (isset($_SESSION["user_id"]) && isset($_SESSION["role"])) {
    if ($_SESSION["role"] === "admin") {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit();
}

$rememberedUser = "";
if (isset($_COOKIE["remember_user"])) {
    $rememberedUser = htmlspecialchars($_COOKIE["remember_user"]);
}

$errorMessage = "";
$successMessage = "";

if (isset($_GET["error"])) {
    if ($_GET["error"] === "invalid") {
        $errorMessage = "Invalid username/email or password.";
    } elseif ($_GET["error"] === "timeout") {
        $errorMessage = "Your session expired due to inactivity. Please log in again.";
    } elseif ($_GET["error"] === "unauthorized") {
        $errorMessage = "You must log in first to access that page.";
    } elseif ($_GET["error"] === "role_mismatch") {
        $errorMessage = "Access restricted: you do not have permission for that area.";
    }
}

if (isset($_GET["logged_out"])) {
    $successMessage = "You have been logged out successfully.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 10 - Secure Login</title>
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
            max-width: 440px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
        }
        .header h1 {
            color: #007bff;
            font-size: 24px;
            margin-bottom: 6px;
        }
        .header p {
            color: #666;
            font-size: 13px;
        }
        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
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
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
        }
        input:focus {
            border-color: #007bff;
        }
        .remember-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #555;
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
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
        .demo-box {
            margin-top: 25px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px dashed #ced4da;
            border-radius: 6px;
            font-size: 12px;
            color: #495057;
        }
        .demo-box h4 {
            margin-bottom: 6px;
            color: #333;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>StudentHub Portal</h1>
        <p>Practical 10: Secure Login & Role-Based Access Control</p>
    </div>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($successMessage); ?>
        </div>
    <?php endif; ?>

    <form action="process_login.php" method="POST">
        <div class="form-group">
            <label for="identifier">Username or Email</label>
            <input type="text" id="identifier" name="identifier" value="<?php echo $rememberedUser; ?>" placeholder="Username or email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required>
        </div>

        <div class="remember-group">
            <input type="checkbox" id="remember_me" name="remember_me" <?php echo !empty($rememberedUser) ? 'checked' : ''; ?>>
            <label for="remember_me" style="margin-bottom:0; font-weight:normal; cursor:pointer;">Remember username on this browser</label>
        </div>

        <button type="submit">Log In</button>
    </form>

    <div class="demo-box">
        <h4>Demo Accounts (Auto-Seeded):</h4>
        <p><strong>Student:</strong> <code>bhavya</code> &bull; Password: <code>Student@123</code></p>
        <p><strong>Admin:</strong> <code>admin</code> &bull; Password: <code>Admin@123</code></p>
    </div>
</div>

</body>
</html>
