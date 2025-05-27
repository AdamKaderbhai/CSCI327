<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: member_login.php");
    exit();
}

$searchResults = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $term = trim(strtolower($_POST['search_term']));

    if ($term === "all") {
        $stmt = $conn->prepare("
            SELECT 
                M.Movie_ID, M.Title, M.Genre, M.Director,
                SUM(CASE WHEN D.Type = 'DVD' THEN 1 ELSE 0 END) AS DVD_Count,
                SUM(CASE WHEN D.Type = 'BLU-RAY' THEN 1 ELSE 0 END) AS Bluray_Count
            FROM MOVIE M
            LEFT JOIN DISK D ON M.Movie_ID = D.Movie_ID
            GROUP BY M.Movie_ID, M.Title, M.Genre, M.Director
        ");
    } else {
        $searchTerm = "%" . $term . "%";
        $stmt = $conn->prepare("
            SELECT 
                M.Movie_ID, M.Title, M.Genre, M.Director,
                SUM(CASE WHEN D.Type = 'DVD' THEN 1 ELSE 0 END) AS DVD_Count,
                SUM(CASE WHEN D.Type = 'BLU-RAY' THEN 1 ELSE 0 END) AS Bluray_Count
            FROM MOVIE M
            LEFT JOIN DISK D ON M.Movie_ID = D.Movie_ID
            WHERE M.Title LIKE ? OR M.Genre LIKE ? OR M.Director LIKE ?
            GROUP BY M.Movie_ID, M.Title, M.Genre, M.Director
        ");
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    }

    $stmt->execute();
    $searchResults = $stmt->get_result();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Movies</title>
    <style>
        body {
            font-family: Arial;
            padding: 20px;
            background-color: #f9f9f9;
        }
        form {
            text-align: center;
            margin-bottom: 30px;
        }
        input[type="text"] {
            padding: 10px;
            width: 400px;
            font-size: 16px;
        }
        button {
            padding: 10px 20px;
            margin-left: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            margin: auto;
            width: 90%;
            border-collapse: collapse;
            background-color: white;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
    </style>
</head>
<body>
<body>

<div style= "position: fixed; bottom: 500px; width: 100%; text-align: center;">
    <a href="member_dashboard.php" style="
        background-color:rgb(0, 121, 227);
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
    ">🔙 Back to Dashboard</a>
</div>

</div>

<form method="POST">
    


    <h2 align="center">Search Movies</h2>

    <form method="POST">
        <input type="text" name="search_term" placeholder="Search by Title, Genre, or Director..." required>
        <button type="submit">Search</button>
    </form>

    <?php if (!empty($searchResults) && $searchResults->num_rows > 0): ?>
        <table>
            <tr>
                <th>Title</th>
                <th>Genre</th>
                <th>Director</th>
                <th>DVD Copies</th>
                <th>Blu-Ray Copies</th>
            </tr>
            <?php while ($row = $searchResults->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['Title']) ?></td>
                    <td><?= htmlspecialchars($row['Genre']) ?></td>
                    <td><?= htmlspecialchars($row['Director']) ?></td>
                    <td><?= $row['DVD_Count'] ?></td>
                    <td><?= $row['Bluray_Count'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <p align="center">No movies found.</p>
    <?php endif; ?>



</body>
</html>

