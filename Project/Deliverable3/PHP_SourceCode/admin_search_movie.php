<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$movieDetails = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $object_id = $_POST['object_id'];

    // Basic details
    $query = "
        SELECT D.Object_ID, D.Type, M.Title
        FROM DISK D
        JOIN MOVIE M ON D.Movie_ID = M.Movie_ID
        WHERE D.Object_ID = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $object_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $movieDetails = $result->fetch_assoc();

        // Check if it's checked out
        $checkoutQuery = "SELECT U.Name, T.Start_Date FROM TRANSACTION T JOIN MEMBER U ON T.User_ID = U.User_ID WHERE T.Object_ID = ? AND T.End_Date IS NULL";
        $stmt = $conn->prepare($checkoutQuery);
        $stmt->bind_param("i", $object_id);
        $stmt->execute();
        $checkoutResult = $stmt->get_result();

        if ($checkoutResult->num_rows > 0) {
            $checkoutInfo = $checkoutResult->fetch_assoc();
            $movieDetails['Status'] = 'Checked Out';
            $movieDetails['User'] = $checkoutInfo['Name'];
            $movieDetails['Date'] = $checkoutInfo['Start_Date'];
        } else {
            // Check if it's reserved
            $reserveQuery = "SELECT U.Name, R.Desired_Start_Date, R.Desired_End_Date FROM RESERVATION R JOIN MEMBER U ON R.User_ID = U.User_ID WHERE R.Object_ID = ? AND CURDATE() BETWEEN R.Desired_Start_Date AND R.Desired_End_Date";
            $stmt = $conn->prepare($reserveQuery);
            $stmt->bind_param("i", $object_id);
            $stmt->execute();
            $reserveResult = $stmt->get_result();

            if ($reserveResult->num_rows > 0) {
                $reserveInfo = $reserveResult->fetch_assoc();
                $movieDetails['Status'] = 'Reserved';
                $movieDetails['User'] = $reserveInfo['Name'];
                $movieDetails['Date'] = $reserveInfo['Desired_Start_Date'] . ' to ' . $reserveInfo['Desired_End_Date'];
            } else {
                $movieDetails['Status'] = 'Available';
            }
        }
    } else {
        $error = "No movie found with Object ID $object_id.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Movie by Object ID</title>
    <style>
        body { font-family: Arial; padding: 30px; background: #f8f9fa; }
        .container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        label, input { display: block; width: 100%; margin-bottom: 15px; }
        input[type="number"] { padding: 10px; }
        button { padding: 10px 20px; background: #007BFF; color: white; border: none; border-radius: 4px; }
        button:hover { background: #0056b3; }
        .result { margin-top: 20px; }
        .back { margin-top: 20px; display: inline-block; background: #6c757d; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; }
        .back:hover { background: #5a6268; }
    </style>
</head>
<body>
<div class="container">
    <h2>Admin Movie Search by Object ID</h2>
    <form method="POST">
        <label for="object_id">Enter Object ID:</label>
        <input type="number" name="object_id" required>
        <button type="submit">Search</button>
    </form>

    <?php if ($movieDetails): ?>
        <div class="result">
            <h3>Result:</h3>
            <p><strong>Title:</strong> <?= htmlspecialchars($movieDetails['Title']) ?></p>
            <p><strong>Format:</strong> <?= htmlspecialchars($movieDetails['Type']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($movieDetails['Status']) ?></p>
            <?php if (isset($movieDetails['User'])): ?>
                <p><strong>User:</strong> <?= htmlspecialchars($movieDetails['User']) ?></p>
                <p><strong>Date:</strong> <?= htmlspecialchars($movieDetails['Date']) ?></p>
            <?php endif; ?>
        </div>
    <?php elseif ($error): ?>
        <p style="color:red;">❌ <?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <a href="admin_dashboard.php" class="back">← Back to Dashboard</a>
</div>
</body>
</html>
