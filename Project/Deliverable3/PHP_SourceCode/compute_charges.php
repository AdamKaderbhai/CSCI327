<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: member_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all active rentals
$query = "
SELECT 
    T.Trans_ID,
    M.Title,
    T.Type,
    T.Start_Date,
    D.Rent_per_day,
    DAYOFWEEK(T.Start_Date) AS Weekday
FROM TRANSACTION T
LEFT JOIN DISK D ON T.Object_ID = D.Object_ID
LEFT JOIN MOVIE M ON D.Movie_ID = M.Movie_ID
WHERE T.User_ID = ? AND T.End_Date IS NULL
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Compute Charges</title>
    <style>
        body {
            font-family: Arial;
            padding: 40px;
            background-color: #f5f5f5;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .total {
            font-weight: bold;
        }
        .back-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #17a2b8;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
        }
    </style>
</head>
<body>

<h2>💰 Live Charges for Active Rentals</h2>

<?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Transaction ID</th>
            <th>Title</th>
            <th>Type</th>
            <th>Start Date</th>
            <th>Days Rented</th>
            <th>Base Charge</th>
            <th>Weekday Discount</th>
            <th>Total Charge</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()):
            $start = strtotime($row['Start_Date']);
            $today = strtotime(date("Y-m-d"));
            $days = max(1, ($today - $start) / (60*60*24));  // At least 1 day
            $base = $days * $row['Rent_per_day'];
            $discount = in_array($row['Weekday'], [2,3,4,5,6]) ? 0.10 * $base : 0;
            $final = $base - $discount;
            $total += $final;
        ?>
        <tr>
            <td><?= $row['Trans_ID'] ?></td>
            <td><?= $row['Title'] ?? 'N/A' ?></td>
            <td><?= $row['Type'] ?></td>
            <td><?= $row['Start_Date'] ?></td>
            <td><?= round($days) ?></td>
            <td>$<?= number_format($base, 2) ?></td>
            <td>$<?= number_format($discount, 2) ?></td>
            <td>$<?= number_format($final, 2) ?></td>
        </tr>
        <?php endwhile; ?>
        <tr class="total">
            <td colspan="7" align="right">Total Charges:</td>
            <td><strong>$<?= number_format($total, 2) ?></strong></td>
        </tr>
    </table>
<?php else: ?>
    <p>No active rentals to compute charges for.</p>
<?php endif; ?>

<a class="back-btn" href="member_dashboard.php">🔙 Back to Dashboard</a>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
