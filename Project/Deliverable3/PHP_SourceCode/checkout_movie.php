<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: member_login.php");
    exit();
}

$query = "
    SELECT 
        M.Movie_ID, M.Title, M.Genre, M.Director,
        SUM(CASE WHEN D.Type = 'DVD' AND T.Object_ID IS NULL THEN 1 ELSE 0 END) AS DVD_Available,
        SUM(CASE WHEN D.Type = 'BLU-RAY' AND T.Object_ID IS NULL THEN 1 ELSE 0 END) AS Bluray_Available
    FROM MOVIE M
    LEFT JOIN DISK D ON M.Movie_ID = D.Movie_ID
    LEFT JOIN TRANSACTION T ON D.Object_ID = T.Object_ID AND T.End_Date IS NULL
    GROUP BY M.Movie_ID, M.Title, M.Genre, M.Director
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout a Movie</title>
    <style>
        body { font-family: Arial; padding: 30px; background: #f0f0f0; }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        a.button {
            text-decoration: none;
            padding: 6px 12px;
            background-color: #28a745;
            color: white;
            border-radius: 4px;
        }
        a.button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<h2>Available Movies for Checkout</h2>

<table>
    <tr>
        <th>Title</th>
        <th>Genre</th>
        <th>Director</th>
        <th>DVD Available</th>
        <th>Blu-Ray Available</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['Title']) ?></td>
            <td><?= htmlspecialchars($row['Genre']) ?></td>
            <td><?= htmlspecialchars($row['Director']) ?></td>
            <td><?= $row['DVD_Available'] ?></td>
            <td><?= $row['Bluray_Available'] ?></td>
            <td>
                <?php if ($row['DVD_Available'] > 0 || $row['Bluray_Available'] > 0): ?>
                    <a href="checkout_handler.php?movie_id=<?= $row['Movie_ID'] ?>" class="button">Checkout</a>
                <?php else: ?>
                    <span style="color: red;">Not Available</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<br>
<a href="member_dashboard.php" style="text-decoration:none; font-weight:bold;">🔙 Back to Dashboard</a>


</body>
</html>



