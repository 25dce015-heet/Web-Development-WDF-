<?php
$jsonFile = __DIR__ . "/contacts.json";
$records = [];
if (file_exists($jsonFile)) {
    $content = file_get_contents($jsonFile);
    $data = json_decode($content, true);
    if (is_array($data)) {
        $records = $data;
    }
}

$csvFile = __DIR__ . "/contacts.csv";
$csvRecords = [];
if (file_exists($csvFile)) {
    $fp = fopen($csvFile, "r");
    if ($fp !== false) {
        $header = fgetcsv($fp);
        while (($row = fgetcsv($fp)) !== false) {
            $csvRecords[] = $row;
        }
        fclose($fp);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 7 - Stored Records</title>
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
            max-width: 1000px;
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
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }
        .header h1 {
            color: #007bff;
            font-size: 24px;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        h2 {
            font-size: 18px;
            color: #333;
            margin: 20px 0 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 10px 12px;
            text-align: left;
            border: 1px solid #e0e0e0;
            font-size: 13px;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        tr:nth-child(even) {
            background-color: #fdfdfd;
        }
        tr:hover {
            background-color: #f1f7fd;
        }
        .empty-msg {
            color: #888;
            font-style: italic;
            padding: 15px 0;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Stored Form Submissions</h1>
        <a href="index.php" class="btn">+ New Submission</a>
    </div>

    <h2>Records Loaded from contacts.json (<?php echo count($records); ?>)</h2>
    <?php if (empty($records)): ?>
        <p class="empty-msg">No JSON records found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Gender</th>
                    <th>Message</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $r): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r["full_name"]); ?></td>
                        <td><?php echo htmlspecialchars($r["email"]); ?></td>
                        <td><?php echo htmlspecialchars($r["mobile"]); ?></td>
                        <td><?php echo htmlspecialchars($r["course"]); ?></td>
                        <td><?php echo htmlspecialchars($r["year"]); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($r["gender"])); ?></td>
                        <td><?php echo htmlspecialchars($r["message"]); ?></td>
                        <td><?php echo htmlspecialchars($r["submitted_at"]); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h2>Records Loaded from contacts.csv (<?php echo count($csvRecords); ?>)</h2>
    <?php if (empty($csvRecords)): ?>
        <p class="empty-msg">No CSV records found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Gender</th>
                    <th>Message</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($csvRecords as $r): ?>
                    <tr>
                        <?php foreach ($r as $val): ?>
                            <td><?php echo htmlspecialchars($val); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
