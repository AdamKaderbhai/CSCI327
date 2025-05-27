<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Calculate average charge paid per customer
$query = "
    SELECT M.Name, ROUND(AVG(D.Rent_per_day), 2) AS AvgCharge
    FROM MEMBER M
    JOIN TRANSACTION T ON M.User_ID = T.User_ID
    JOIN DISK D ON T.Object_ID = D.Object_ID
    GROUP BY M.User_ID
    ORDER BY AvgCharge DESC
    LIMIT 10
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Average Charge Paid Per Customer</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; padding: 30px; }
        .container { max-width: 700px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 12px; text-align: left; }
        th { background-color: #007BFF; color: white; }
        a.button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: gray;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a.button:hover { background-color: #5a6268; }
    </style>
</head>
<body>
<div class="container">
    <h2>Average Charge Paid Per Customer</h2>
    <table>
        <tr>
            <th>Member Name</th>
            <th>Avg. Charge Paid ($)</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['Name']) ?></td>
                <td>$<?= htmlspecialchars($row['AvgCharge']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <a href="admin_dashboard.php" class="button">← Back to Dashboard</a>
</div>
</body>
</html>

<?php $conn->close(); ?>

