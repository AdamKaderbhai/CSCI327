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

$sql = "SELECT Store_ID, Address, Phone FROM STORE";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Store Info</title>
    <style>
        body { font-family: Arial; padding: 40px; background-color: #f0f2f5; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #007BFF; color: white; }
        a.button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
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

<h2>Store Information</h2>

<?php if ($result && $result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Store ID</th>
            <th>Address</th>
            <th>Phone</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['Store_ID']) ?></td>
                <td><?= htmlspecialchars($row['Address']) ?></td>
                <td><?= htmlspecialchars($row['Phone']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No store information found.</p>
<?php endif; ?>

<a href="admin_dashboard.php" class="button">← Back to Dashboard</a>

</body>
</html>

<?php
$conn->close();
?>
