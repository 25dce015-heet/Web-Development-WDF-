<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 7 - PHP Form Processing</title>
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
            text-align: center;
            margin-bottom: 25px;
        }
        .header h1 {
            color: #007bff;
            font-size: 26px;
            margin-bottom: 8px;
        }
        .header p {
            color: #666;
            font-size: 14px;
        }
        .nav-links {
            text-align: center;
            margin-bottom: 20px;
        }
        .nav-links a {
            display: inline-block;
            margin: 0 8px;
            padding: 8px 16px;
            background: #e9ecef;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }
        .nav-links a:hover {
            background: #007bff;
            color: white;
        }
        .form-group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #333;
            font-size: 14px;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select,
        textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #007bff;
        }
        textarea {
            resize: vertical;
            min-height: 90px;
        }
        .radio-group {
            display: flex;
            gap: 15px;
            padding-top: 5px;
        }
        .radio-group label {
            font-weight: normal;
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }
        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>StudentHub Portal</h1>
        <p>Practical 7: PHP Form Processing (Server-Side Validation & CSV/JSON Storage)</p>
    </div>

    <div class="nav-links">
        <a href="index.php">Registration Form</a>
        <a href="view.php">View Stored Records</a>
    </div>

    <form action="process.php" method="POST">
        <div class="form-group">
            <label for="fullName">Full Name</label>
            <input type="text" id="fullName" name="fullName" placeholder="Enter your full name" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="example@charusat.edu.in" required>
        </div>

        <div class="form-group">
            <label for="mobile">Mobile Number</label>
            <input type="tel" id="mobile" name="mobile" placeholder="10-digit mobile number" required>
        </div>

        <div class="form-group">
            <label for="course">Course</label>
            <select id="course" name="course" required>
                <option value="">-- Select Course --</option>
                <option value="BCA">BCA</option>
                <option value="BBA">BBA</option>
                <option value="B.Tech CSE">B.Tech CSE</option>
                <option value="B.Tech IT">B.Tech IT</option>
                <option value="B.Com">B.Com</option>
            </select>
        </div>

        <div class="form-group">
            <label for="year">Year of Study</label>
            <select id="year" name="year" required>
                <option value="">-- Select Year --</option>
                <option value="1">1st Year</option>
                <option value="2">2nd Year</option>
                <option value="3">3rd Year</option>
                <option value="4">4th Year</option>
            </select>
        </div>

        <div class="form-group">
            <label>Gender</label>
            <div class="radio-group">
                <label><input type="radio" name="gender" value="male" required> Male</label>
                <label><input type="radio" name="gender" value="female"> Female</label>
                <label><input type="radio" name="gender" value="other"> Other</label>
            </div>
        </div>

        <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" placeholder="Enter query or feedback" required></textarea>
        </div>

        <button type="submit">Submit Form</button>
    </form>
</div>

</body>
</html>
