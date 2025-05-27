<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: member_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$movie_id = $_GET['movie_id'] ?? null;
$format = $_POST['format'] ?? null;
$today = date("Y-m-d");
$endDate = date("Y-m-d", strtotime("+3 days")); // reservation length is 3 days

// Get movie title
$title = "Unknown Movie";
$stmt = $conn->prepare("SELECT Title FROM MOVIE WHERE Movie_ID = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$stmt->bind_result($title);
$stmt->fetch();
$stmt->close();

$success = false;

// Step 1: Ask for format selection
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Select Format to Reserve</title>
    <style>
        body { font-family: Arial; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f2f5; }
        .form-card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        select, button { padding: 10px; margin-top: 15px; width: 100%; }
    </style>
</head>
<body>
    <div class="form-card">
        <h2>Reserve "<?= htmlspecialchars($title) ?>"</h2>
        <form method="POST">
            <label for="format">Choose Format:</label>
            <select name="format" required>
                <option value="DVD">DVD</option>
                <option value="BLU-RAY">Blu-Ray</option>
            </select>
            <button type="submit">Reserve</button>
        </form>
    </div>
</body>
</html>
<?php
    exit();
}

// Step 2: Handle reservation request
if ($movie_id && $format) {
    $query = "
        SELECT D.Object_ID, D.Type
        FROM DISK D
        LEFT JOIN TRANSACTION T ON D.Object_ID = T.Object_ID AND T.End_Date IS NULL
        LEFT JOIN RESERVATION R ON D.Object_ID = R.Object_ID 
            AND CURDATE() BETWEEN R.Desired_Start_Date AND R.Desired_End_Date
        WHERE D.Movie_ID = ? AND D.Type = ? 
        AND T.Object_ID IS NULL 
        AND R.Object_ID IS NULL
        LIMIT 1
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $movie_id, $format);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $object_id = $row['Object_ID'];
        $format = $row['Type'];

        $insert = $conn->prepare("
            INSERT INTO RESERVATION (User_ID, Object_ID, Reserved_Date, Desired_Start_Date, Desired_End_Date)
            VALUES (?, ?, CURDATE(), ?, ?)
        ");
        $insert->bind_param("isss", $user_id, $object_id, $today, $endDate);
        $success = $insert->execute();
        $insert->close();
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reservation Confirmation</title>
    <style>
        body { font-family: Arial; display: flex; align-items: center; justify-content: center; height: 100vh; background-color: #f8f9fa; }
        .card {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 3px 10px rgba(0,0,0,0.1);
        }
        .success { color: green; font-weight: bold; font-size: 18px; }
        .fail { color: red; font-weight: bold; font-size: 18px; }
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 25px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .button:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div class="card">
        <?php if ($success): ?>
            <p class="success">✅ Successfully reserved <strong><?= htmlspecialchars($title) ?></strong> in <strong><?= strtoupper($format) ?></strong> format!</p>
        <?php else: ?>
            <p class="fail">❌ No available copies of <strong><?= htmlspecialchars($title) ?></strong> in <strong><?= strtoupper($format) ?></strong> format to reserve.</p>
        <?php endif; ?>
        <a href="member_dashboard.php" class="button">🔙 Back to Dashboard</a>
    </div>
</body>
</html>
