<?php
$host = "localhost";
$user = "root"; // default for XAMPP
$password = ""; // default for XAMPP
$database = "AlphaBhatta_VideoStore"; // your DB name

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
