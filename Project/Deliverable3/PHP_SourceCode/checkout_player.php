<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: member_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the user already has a player rented
$check = $conn->prepare("SELECT 1 FROM TRANSACTION WHERE User_ID = ? AND Type = 'PLAYER' AND End_Date IS NULL");
$check->bind_param("i", $user_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "<p style='color:red; text-align:center; font-weight:bold;'>❌ You already have a player checked out. Return it before checking out another one.</p>";
    echo "<div style='text-align:center;'><a href='member_dashboard.php'>← Back to Dashboard</a></div>";
    $check->close();
    $conn->close();
    exit();
}
$check->close();

// Fetch available players
$query = "
    SELECT P.Object_ID
    FROM PLAYER P
    LEFT JOIN TRANSACTION T ON P.Object_ID = T.Object_ID AND T.Type = 'PLAYER' AND T.End_Date IS NULL
    WHERE T.Object_ID IS NULL
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout Player</title>
    <style>
        body { font-family: Arial; padding: 40px; background-color: #f8f8f8; }
        table { width: 50%; margin: auto; border-collapse: collapse; margin-top: 30px; }
        th, td { padding: 12px; border: 1px solid #ccc; text-align: center; }
        th { background-color: #007BFF; color: white; }
        a.button {
            display: inline-block;
            padding: 8px 15px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        a.button:hover { background-color: #0056b3; }
        h2 { text-align: center; }
    </style>
</head>
<body>
<h2>Available Players for Checkout</h2>
<?php if ($result->num_rows > 0): ?>
<table>
    <tr>
        <th>Player Object ID</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['Object_ID']) ?></td>
            <td><a class="button" href="checkout_player_handler.php?object_id=<?= $row['Object_ID'] ?>">Checkout</a></td>
        </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
<p style="text-align:center;">No players available for checkout at the moment.</p>
<?php endif; ?>

<div style="text-align:center; margin-top: 20px;">
    <a href="member_dashboard.php" class="button">← Back to Dashboard</a>
</div>
</body>
</html>

<?php $conn->close(); ?>
