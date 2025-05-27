<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: member_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$movie_id = $_GET['movie_id'] ?? null;

if (!$movie_id) {
    die("Invalid access.");
}

// Fetch movie title
$title = "Unknown Movie";
$stmt = $conn->prepare("SELECT Title FROM MOVIE WHERE Movie_ID = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$stmt->bind_result($title);
$stmt->fetch();
$stmt->close();

// Step 1: If GET request, show format selection form
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <title>Select Format</title>
        <style>
            body {
                font-family: Arial;
                display: flex;
                height: 100vh;
                align-items: center;
                justify-content: center;
                background-color: #f8f9fa;
            }
            .card {
                text-align: center;
                padding: 40px;
                border: 1px solid #ddd;
                background: white;
                border-radius: 10px;
                box-shadow: 0px 3px 10px rgba(0,0,0,0.1);
            }
            button {
                padding: 10px 25px;
                background-color: rgb(30, 63, 182);
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
            }
            button:hover {
                background-color: rgb(19, 38, 122);
            }
        </style>
    </head>
    <body>
        <div class='card'>
            <h2>Select Format for <em>$title</em></h2>
            <form method='POST' action='checkout_handler.php?movie_id=$movie_id'>
                <label><input type='radio' name='format' value='DVD' required> DVD</label><br><br>
                <label><input type='radio' name='format' value='BLU-RAY'> Blu-Ray</label><br><br>
                <button type='submit'>Checkout</button>
            </form>
        </div>
    </body>
    </html>
    ";
    exit();
}

// Step 2: If POST request, process the checkout
$format = $_POST['format'] ?? null;
$success = false;

if ($format) {
    $diskQuery = "
        SELECT D.Object_ID 
        FROM DISK D
        WHERE D.Movie_ID = ? 
        AND D.Type = ?
        AND D.Object_ID NOT IN (
            SELECT Object_ID FROM TRANSACTION WHERE End_Date IS NULL
        )
        AND D.Object_ID NOT IN (
            SELECT Object_ID FROM RESERVATION 
            WHERE CURDATE() BETWEEN Desired_Start_Date AND Desired_End_Date
        )
        LIMIT 1
    ";
    $stmt = $conn->prepare($diskQuery);
    $stmt->bind_param("is", $movie_id, $format);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $object_id = $row['Object_ID'];

        $insert = $conn->prepare("INSERT INTO TRANSACTION (User_ID, Object_ID, Type, Start_Date) VALUES (?, ?, 'DISK', CURDATE())");
        $insert->bind_param("ii", $user_id, $object_id);
        $success = $insert->execute();
        $insert->close();
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout Confirmation</title>
    <style>
        body {
            font-family: Arial;
            display: flex;
            height: 100vh;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        .card {
            text-align: center;
            padding: 40px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 3px 10px rgba(0,0,0,0.1);
        }
        .success {
            color: green;
            font-weight: bold;
            font-size: 18px;
        }
        .fail {
            color: red;
            font-weight: bold;
            font-size: 18px;
        }
        a.button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 25px;
            background-color:rgb(30, 63, 182);
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a.button:hover {
            background-color:rgb(19, 38, 122);
        }
    </style>
</head>
<body>
    <div class="card">
        <?php if ($success): ?>
            <p class="success">✅ Successfully checked out <strong><?= htmlspecialchars($title) ?></strong> in <strong><?= strtoupper($format) ?></strong> format!</p>
        <?php else: ?>
            <p class="fail">❌ No available copies of <strong><?= htmlspecialchars($title) ?></strong> in <strong><?= strtoupper($format) ?></strong> format.</p>
        <?php endif; ?>
        <a href="member_dashboard.php" class="button">🔙 Back to Dashboard</a>
    </div>
</body>
</html>
