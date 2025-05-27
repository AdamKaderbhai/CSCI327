<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: member_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['object_id'])) {
    die("Invalid access.");
}

$object_id = $_GET['object_id'];

// Double-check: Has the user already rented a player?
$check = $conn->prepare("SELECT 1 FROM TRANSACTION WHERE User_ID = ? AND Type = 'PLAYER' AND End_Date IS NULL");
$check->bind_param("i", $user_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $check->close();
    $conn->close();
    echo "<p style='color:red; text-align:center; font-weight:bold;'>❌ You already have a player checked out. Return it first.</p>";
    echo "<div style='text-align:center;'><a href='member_dashboard.php'>← Back to Dashboard</a></div>";
    exit();
}
$check->close();

// Insert the transaction
$insert = $conn->prepare("INSERT INTO TRANSACTION (User_ID, Object_ID, Type, Start_Date) VALUES (?, ?, 'PLAYER', CURDATE())");
$insert->bind_param("ii", $user_id, $object_id);
$success = $insert->execute();
$insert->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Player Checkout Confirmation</title>
    <style>
        body {
            font-family: Arial;
            display: flex;
            height: 100vh;
            align-items: center;
            justify-content: center;
            background-color: #f8f8f8;
        }
        .card {
            background: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0px 2px 10px rgba(0,0,0,0.1);
        }
        .success { color: green; font-weight: bold; font-size: 18px; }
        .fail { color: red; font-weight: bold; font-size: 18px; }
        a.button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 25px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a.button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
<div class="card">
    <?php if ($success): ?>
        <p class="success">✅ You have successfully checked out Player (Object ID: <strong><?= htmlspecialchars($object_id) ?></strong>).</p>
    <?php else: ?>
        <p class="fail">❌ Failed to check out player. Please try again.</p>
    <?php endif; ?>
    <a href="member_dashboard.php" class="button">← Back to Dashboard</a>
</div>
</body>
</html>
