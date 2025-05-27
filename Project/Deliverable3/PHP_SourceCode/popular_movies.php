<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get most frequently rented movies
$query = "
    SELECT M.Title, COUNT(T.Trans_ID) AS Rentals
    FROM MOVIE M
    JOIN DISK D ON M.Movie_ID = D.Movie_ID
    JOIN TRANSACTION T ON D.Object_ID = T.Object_ID
    GROUP BY M.Movie_ID
    ORDER BY Rentals DESC
    LIMIT 10
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Popular Movies</title>
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
    <h2>Top 10 Popular Movies</h2>
    <table>
        <tr>
            <th>Movie Title</th>
            <th>Total Rentals</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['Title']) ?></td>
                <td><?= htmlspecialchars($row['Rentals']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <a href="admin_dashboard.php" class="button">← Back to Dashboard</a>
</div>
</body>
</html>

<?php $conn->close(); ?>
