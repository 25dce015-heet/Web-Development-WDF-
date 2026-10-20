<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 9 - Secure User Registration</title>
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
            max-width: 500px;
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
        .nav-links {
            text-align: center;
            margin-bottom: 20px;
        }
        .nav-links a {
            display: inline-block;
            margin: 0 5px;
            padding: 6px 14px;
            background: #e9ecef;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
        }
        .nav-links a:hover {
            background: #007bff;
            color: white;
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
        input, select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
        }
        input:focus, select:focus {
            border-color: #007bff;
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
        .error-hint {
            color: #dc3545;
            font-size: 12px;
            margin-top: 4px;
            display: none;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>StudentHub Portal</h1>
        <p>Practical 9: Secure Registration (Duplicate Email Check & Password Hashing)</p>
    </div>

    <div class="nav-links">
        <a href="register.php">Register Page</a>
        <a href="users.php">View Users in DB</a>
    </div>

    <form id="regForm" action="process_register.php" method="POST" onsubmit="return validateForm()">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" placeholder="Enter your full name" required>
        </div>

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Choose a username" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="example@charusat.edu.in" required>
        </div>

        <div class="form-group">
            <label for="role">User Role</label>
            <select id="role" name="role" required>
                <option value="student">Student</option>
                <option value="admin">Administrator</option>
            </select>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="At least 8 characters" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat your password" required>
            <div id="matchError" class="error-hint">Passwords do not match!</div>
        </div>

        <button type="submit">Create Secure Account</button>
    </form>
</div>

<script>
function validateForm() {
    var pass = document.getElementById("password").value;
    var confirmPass = document.getElementById("confirm_password").value;
    var matchError = document.getElementById("matchError");

    if (pass.length < 8) {
        alert("Password must be at least 8 characters long.");
        return false;
    }

    if (pass !== confirmPass) {
        matchError.style.display = "block";
        return false;
    } else {
        matchError.style.display = "none";
    }

    return true;
}
</script>

</body>
</html>
