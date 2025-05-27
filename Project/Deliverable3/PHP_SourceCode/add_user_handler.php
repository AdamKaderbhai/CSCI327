<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST["role"];
    $name = $_POST["name"];
    $password = $_POST["password"];

    if ($role === "MEMBER") {
        $address = $_POST["address"];

        // Get next available User_ID
        $result = $conn->query("SELECT MAX(User_ID) AS max_id FROM MEMBER");
        $row = $result->fetch_assoc();
        $next_id = $row['max_id'] + 1;

        $stmt = $conn->prepare("INSERT INTO MEMBER (Member_ID, User_ID, Name, Address, Password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $next_id, $next_id, $name, $address, $password);
        $stmt->execute();
        $stmt->close();

        $message = "The <strong>member</strong> <em>" . htmlspecialchars($name) . "</em> has been successfully added into the system.";

    } elseif ($role === "ADMIN") {
        $email = $_POST["email"];

        // Get next available Admin_ID
        $result = $conn->query("SELECT MAX(Admin_ID) AS max_id FROM ADMIN");
        $row = $result->fetch_assoc();
        $next_id = $row['max_id'] + 1;

        $stmt = $conn->prepare("INSERT INTO ADMIN (Admin_ID, Email, Password, Name) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $next_id, $email, $password, $name);
        $stmt->execute();
        $stmt->close();

        $message = "The <strong>admin</strong> <em>" . htmlspecialchars($name) . "</em> has been successfully added into the system.";
    } else {
        $message = "Invalid role selected.";
    }

    $conn->close();
} else {
    header("Location: add_user.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Added</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f4f4f4;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
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
    <h2>✅ <?= $message ?></h2>
    <a href="admin_dashboard.php" class="button">← Back to Dashboard</a>
</div>

</body>
</html>

