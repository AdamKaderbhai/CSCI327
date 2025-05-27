<?php
session_start();
require_once("db.php"); // make sure this path is correct

if (!isset($_SESSION['user_id'])) {
    header("Location: member_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get the member name
$stmt = $conn->prepare("SELECT Name FROM MEMBER WHERE User_ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_assoc();
$name = $member ? $member['Name'] : 'Member';

$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Member Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f3;
            text-align: center;
            padding-top: 50px;
        }
        .container {
            background-color: white;
            padding: 40px;
            display: inline-block;
            border-radius: 10px;
            box-shadow: 0 0 10px #aaa;
        }
        h2 {
            margin-bottom: 30px;
        }
        .menu {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .menu a {
            text-decoration: none;
            background-color: #007BFF;
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .menu a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
    <h2>Welcome, <?= htmlspecialchars($name) ?></h2>
        <div class="menu">
            <a href="search_movie.php">🔍 Search a Movie</a>
            <a href="checkout_movie.php">🎬 Rent a Movie</a>
            <a href="reserve_movie.php">📌 Reserve a Movie</a>
            <a href="view_rentals.php">📜 View My Rentals</a>
            <a href="view_reservations.php">📄 View My Reservations</a>
            <a href="return_movie.php">↩️ Return Movie</a>
            <a href="checkout_player.php">📀 Rent Player</a>
            <a href="compute_charges.php">💰 Compute Charges</a>
            <a href="index.php">🚪 Logout</a>
        </div>
    </div>

</body>
</html>

