<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = $_POST["admin_id"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM ADMIN WHERE ADMIN_ID = ? AND Password = ?");
    $stmt->bind_param("is", $admin_id, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $_SESSION["admin_id"] = $admin_id;      // ✅ Set admin_id correctly
        $_SESSION["is_admin"] = 1;              // ✅ Mark as admin
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "<p>❌ Invalid login. <a href='admin_login.php'>Try again</a></p>";
    }

    $stmt->close();
    $conn->close();
}
?>
