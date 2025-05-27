<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: member_login.php");
    exit();
}

if (!isset($_GET['trans_id'])) {
    die("Invalid request.");
}


$transaction_id = $_GET['trans_id'];
$success = false;

// Update the End_Date for the transaction
$stmt = $conn->prepare("UPDATE TRANSACTION SET End_Date = CURDATE() WHERE Trans_ID = ?");
$stmt->bind_param("i", $transaction_id);
$success = $stmt->execute();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Return Confirmation</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .card {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 3px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success {
            font-size: 18px;
            color: green;
            font-weight: bold;
        }
        .fail {
            font-size: 18px;
            color: red;
            font-weight: bold;
        }
        a.button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 25px;
            background-color: rgb(30, 63, 182);
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a.button:hover {
            background-color: rgb(19, 38, 122);
        }
    </style>
</head>
<body>
    <div class="card">
        <?php if ($success): ?>
            <p class="success">✅ Thank you! The item has been successfully returned.</p>
        <?php else: ?>
            <p class="fail">❌ Failed to process return. Please try again.</p>
        <?php endif; ?>
        <a href="member_dashboard.php" class="button">🔙 Back to Dashboard</a>
    </div>
</body>
</html>
