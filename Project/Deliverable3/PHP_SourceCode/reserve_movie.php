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
    <title>Reserve a Movie</title>
    <style>
        body { font-family: Arial; padding: 30px; background: #f7f7f7; }
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
            background-color:rgb(21, 67, 193);
            color: white;
        }
        a.button {
            text-decoration: none;
            padding: 6px 12px;
            background-color:rgb(0, 0, 0);
            color: white;
            border-radius: 4px;
        }
        a.button:hover {
            background-color:rgb(30, 43, 196);
        }
    </style>
</head>
<body>

<h2>Reserve a Movie</h2>

<table>
    <tr>
        <th>Title</th>
        <th>Genre</th>
        <th>Director</th>
        <th>DVD Available</th>
        <th>Blu-Ray Available</th>
        <th>Reserve</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['Title']) ?></td>
            <td><?= htmlspecialchars($row['Genre']) ?></td>
            <td><?= htmlspecialchars($row['Director']) ?></td>
            <td><?= $row['DVD_Available'] ?></td>
            <td><?= $row['Bluray_Available'] ?></td>
            <td>
                <a href="reserve_handler.php?movie_id=<?= $row['Movie_ID'] ?>" class="button">Reserve</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<br>
<a href="member_dashboard.php" style="text-decoration:none; font-weight:bold;">🔙 Back to Dashboard</a>


</body>
</html>

