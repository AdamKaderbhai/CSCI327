<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Query top 10 renters by transaction count
$query = "
    SELECT M.Name, COUNT(T.Trans_ID) AS Rentals
    FROM MEMBER M
    JOIN TRANSACTION T ON M.User_ID = T.User_ID
    GROUP BY M.User_ID
    ORDER BY Rentals DESC
    LIMIT 10
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Top 10 Frequent Renters</title>
    <style>
        body { font-family: Arial; background: #f9f9f9; padding: 30px; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 700px; margin: auto; }
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
    <h2>Top 10 Frequent Renters</h2>
    <table>
        <tr>
            <th>Member Name</th>
            <th>Total Rentals</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['Name']) ?></td>
                <td><?= htmlspecialchars($row['Rentals']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <a href="admin_dashboard.php" class="button">← Back to Dashboard</a>
</div>

</body>
</html>

<?php $conn->close(); ?>
