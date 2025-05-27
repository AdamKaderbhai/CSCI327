<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: member_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch active rentals (no End_Date)
$sql = "
    SELECT T.Trans_ID, M.Title, T.Type, T.Start_Date 
    FROM TRANSACTION T
    JOIN STORE_OBJECT S ON T.Object_ID = S.Object_ID
    LEFT JOIN DISK D ON T.Object_ID = D.Object_ID
    LEFT JOIN MOVIE M ON D.Movie_ID = M.Movie_ID
    WHERE T.User_ID = ? AND T.End_Date IS NULL
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Return Rentals</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 40px;
            text-align: center;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        a.button {
            background-color:rgb(40, 76, 167);
            padding: 8px 15px;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        a.button:hover {
            background-color:rgb(36, 56, 188);
        }
        .back-btn {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<h2>Return Rented Items</h2>

<?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Transaction ID</th>
            <th>Title</th>
            <th>Type</th>
            <th>Start Date</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['Trans_ID'] ?></td>
                <td><?= htmlspecialchars($row['Title']) ?></td>
                <td><?= $row['Type'] ?></td>
                <td><?= $row['Start_Date'] ?></td>
                <td>
                    <a class="button" href="return_handler.php?trans_id=<?= $row['Trans_ID'] ?>">Return</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No active rentals found.</p>
<?php endif; ?>

<div class="back-btn">
    <a href="member_dashboard.php" class="button">🔙 Back to Dashboard</a>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
