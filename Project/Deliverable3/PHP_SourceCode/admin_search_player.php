<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$player_id = $status = $rented_by = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $player_id = $_POST['player_id'];

    // Check if the player exists
    $check = $conn->prepare("SELECT Object_ID FROM PLAYER WHERE Object_ID = ?");
    $check->bind_param("i", $player_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $status = "Player ID not found.";
    } else {
        // Check if currently rented
        $query = $conn->prepare("SELECT M.Name FROM TRANSACTION T JOIN MEMBER M ON T.User_ID = M.User_ID WHERE T.Object_ID = ? AND T.Type = 'PLAYER' AND T.End_Date IS NULL");
        $query->bind_param("i", $player_id);
        $query->execute();
        $query->store_result();

        if ($query->num_rows > 0) {
            $query->bind_result($rented_by);
            $query->fetch();
            $status = "Checked out by: $rented_by";
        } else {
            $status = "✅ Player is available";
        }
        $query->close();
    }
    $check->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin: Search Player Status</title>
    <style>
        body { font-family: Arial; padding: 40px; background-color: #f5f5f5; }
        .container { max-width: 500px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        label, input, button { width: 100%; margin-bottom: 15px; padding: 10px; }
        button { background-color: #007BFF; color: white; border: none; border-radius: 4px; }
        button:hover { background-color: #0056b3; }
        .status { text-align: center; font-weight: bold; margin-top: 20px; }
        a.button { display: block; text-align: center; margin-top: 20px; background-color: gray; color: white; padding: 10px; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
<div class="container">
    <h2>🔍 Search Player by Object ID</h2>
    <form method="POST">
        <label for="player_id">Enter Player Object ID:</label>
        <input type="number" name="player_id" required>
        <button type="submit">Search</button>
    </form>

    <?php if ($status): ?>
        <div class="status"><?= htmlspecialchars($status) ?></div>
    <?php endif; ?>

    <a href="admin_dashboard.php" class="button">← Back to Dashboard</a>
</div>
</body>
</html>
