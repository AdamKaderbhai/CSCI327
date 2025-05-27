<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: member_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "
    SELECT T.Trans_ID, T.Object_ID, T.Type, T.Start_Date, T.End_Date,
       CASE 
           WHEN T.Type = 'DISK' THEN M.Title
           WHEN T.Type = 'PLAYER' THEN CONCAT('Player (', P.Features, ')')
           ELSE 'Unknown'
       END AS Title,
       CASE 
           WHEN T.Type = 'DISK' THEN D.Type
           WHEN T.Type = 'PLAYER' THEN 'PLAYER'
           ELSE 'N/A'
       END AS Format
FROM TRANSACTION T
JOIN STORE_OBJECT SO ON T.Object_ID = SO.Object_ID
LEFT JOIN DISK D ON D.Object_ID = SO.Object_ID
LEFT JOIN MOVIE M ON D.Movie_ID = M.Movie_ID
LEFT JOIN PLAYER P ON P.Object_ID = SO.Object_ID
WHERE T.User_ID = ? AND T.End_Date IS NULL

";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Rentals</title>
    <style>
        body { font-family: Arial; padding: 40px; background-color: #f9f9f9; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #007BFF; color: white; }
        a.button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color:rgb(49, 148, 234);
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        a.button:hover {
            background-color:rgb(16, 76, 122);
        }
    </style>
</head>
<body>

<h2>My Rentals</h2>

<?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Transaction ID</th>
            <th>Title</th>
            <th>Type</th>
            <th>Start Date</th>
            <th>End Date</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['Trans_ID']) ?></td>
                <td><?= htmlspecialchars($row['Title'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($row['Format']) ?></td>
                <td><?= htmlspecialchars($row['Start_Date']) ?></td>
                <td><?= htmlspecialchars($row['End_Date'] ?? '—') ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No rentals found.</p>
<?php endif; ?>

<a href="member_dashboard.php" class="button">← Back to Dashboard</a>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>

