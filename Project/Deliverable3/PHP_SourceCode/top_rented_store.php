<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Top stores based on number of rentals across their objects
$query = "
    SELECT S.Address, COUNT(T.Trans_ID) AS TotalRentals
    FROM STORE S
    JOIN STORE_OBJECT SO ON S.Store_ID = SO.Store_ID
    JOIN TRANSACTION T ON SO.Object_ID = T.Object_ID
    GROUP BY S.Store_ID
    ORDER BY TotalRentals DESC
    LIMIT 10
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Top Rented Stores</title>
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
    <h2>Top 10 Stores by Rentals</h2>
    <table>
        <tr>
            <th>Store Address</th>
            <th>Total Rentals</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['Address']) ?></td>
                <td><?= htmlspecialchars($row['TotalRentals']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <a href="admin_dashboard.php" class="button">← Back to Dashboard</a>
</div>
</body>
</html>

<?php $conn->close(); ?>
