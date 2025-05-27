<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();


require_once("db.php"); // Update this path if needed

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST["user_id"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM MEMBER WHERE User_ID = ? AND Password = ?");
    $stmt->bind_param("is", $user_id, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $_SESSION["user_id"] = $user_id;
        header("Location: member_dashboard.php");
        exit();
    } else {
        echo "<p>❌ Invalid login. <a href='member_login.php'>Try again</a></p>";
    }

    $stmt->close();
    $conn->close();
}
?>
