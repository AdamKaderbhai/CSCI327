<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: member_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

$query = "
    SELECT 
        R.Reservation_ID, 
        R.Object_ID, 
        R.Reserved_Date,
        R.Desired_Start_Date,
        R.Desired_End_Date,
        M.Title
    FROM RESERVATION R
    JOIN DISK D ON R.Object_ID = D.Object_ID
    JOIN MOVIE M ON D.Movie_ID = M.Movie_ID
    WHERE R.User_ID = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Reservations</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f9f9f9;
            padding: 40px;
            text-align: center;
        }
        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        a.button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a.button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<h2>📋 My Reservations</h2>

<?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Reservation ID</th>
            <th>Title</th>
            <th>Reserved Date</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): 
            $status = "Upcoming";
            if ($today >= $row['Desired_Start_Date'] && $today <= $row['Desired_End_Date']) {
                $status = "Active";
            } elseif ($today > $row['Desired_End_Date']) {
                $status = "Expired";
            }
        ?>
        <tr>
            <td><?= $row['Reservation_ID'] ?></td>
            <td><?= htmlspecialchars($row['Title']) ?></td>
            <td><?= $row['Reserved_Date'] ?></td>
            <td><?= $row['Desired_Start_Date'] ?></td>
            <td><?= $row['Desired_End_Date'] ?></td>
            <td><?= $status ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No reservations found.</p>
<?php endif; ?>

<a href="member_dashboard.php" class="button">🔙 Back to Dashboard</a>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
