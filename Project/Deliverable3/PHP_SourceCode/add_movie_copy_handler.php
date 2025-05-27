<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once("db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $movie_id = $_POST['movie_id'];
    $store_id = $_POST['store_id'];
    $format = $_POST['format'];

    // Fetch movie title and store address
    $stmt1 = $conn->prepare("SELECT Title FROM MOVIE WHERE Movie_ID = ?");
    $stmt1->bind_param("i", $movie_id);
    $stmt1->execute();
    $stmt1->bind_result($movie_title);
    $stmt1->fetch();
    $stmt1->close();

    $stmt2 = $conn->prepare("SELECT Address FROM STORE WHERE Store_ID = ?");
    $stmt2->bind_param("i", $store_id);
    $stmt2->execute();
    $stmt2->bind_result($store_address);
    $stmt2->fetch();
    $stmt2->close();

    // Insert into DISK and STORE_OBJECT
    $stmt = $conn->prepare("INSERT INTO DISK (Movie_ID, Type) VALUES (?, ?)");
    $stmt->bind_param("is", $movie_id, $format);

    if ($stmt->execute()) {
        $object_id = $stmt->insert_id;

        $stmt2 = $conn->prepare("INSERT INTO STORE_OBJECT (Object_ID, Store_ID) VALUES (?, ?)");
        $stmt2->bind_param("ii", $object_id, $store_id);
        $stmt2->execute();
        $stmt2->close();
    }
    $stmt->close();
    $conn->close();
} else {
    header("Location: add_movie_copy.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Copy Added</title>
    <style>
        body {
            font-family: Arial;
            display: flex;
            height: 100vh;
            align-items: center;
            justify-content: center;
            background-color: #f5f5f5;
        }
        .card {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .card h2 {
            color: green;
            margin-bottom: 20px;
        }
        a.button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 25px;
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

<div class="card">
    <h2>✅ You have added <strong><?= htmlspecialchars($movie_title) ?></strong> in <strong><?= htmlspecialchars($format) ?></strong> to <strong><?= htmlspecialchars($store_address) ?></strong>.</h2>
    <a href="admin_dashboard.php" class="button">← Back to Dashboard</a>
</div>

</body>
</html>
