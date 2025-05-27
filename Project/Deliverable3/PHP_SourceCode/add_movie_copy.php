<?php

session_start();
require_once("db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle form submission
$success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $movie_id = $_POST['movie_id'];
    $store_id = $_POST['store_id'];
    $format = $_POST['format'];

    // Insert into DISK table
    $stmt = $conn->prepare("INSERT INTO DISK (Movie_ID, Type) VALUES (?, ?)");
    $stmt->bind_param("is", $movie_id, $format);
    if ($stmt->execute()) {
        $object_id = $stmt->insert_id;
        // Now insert into STORE_OBJECT
        $stmt2 = $conn->prepare("INSERT INTO STORE_OBJECT (Object_ID, Store_ID) VALUES (?, ?)");
        $stmt2->bind_param("ii", $object_id, $store_id);
        $success = $stmt2->execute();
        $stmt2->close();
    }
    if (!isset($movie_title) || !isset($store_address)) {
        echo "<p style='color:red; text-align:center;'>Something went wrong. Could not fetch movie/store information.</p>";
        echo "<p style='text-align:center;'><a href='add_movie_copy.php'>← Try again</a></p>";
        exit();
    }
    $stmt->close();
}

// Get movie and store data
$movies = $conn->query("SELECT Movie_ID, Title FROM MOVIE");
$stores = $conn->query("SELECT Store_ID, Address FROM STORE");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Movie Copy</title>
    <style>
        body { font-family: Arial; padding: 40px; background-color: #f2f2f2; }
        form { background: white; padding: 30px; border-radius: 8px; max-width: 500px; margin: auto; }
        label, select, button { display: block; width: 100%; margin-bottom: 15px; }
        select, button { padding: 10px; }
        button {
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
        }
        button:hover { background-color: #0056b3; }
        .message {
            text-align: center;
            font-weight: bold;
            color: green;
        }
        a.button {
            display: inline-block;
            text-align: center;
            margin-top: 20px;
            padding: 10px 25px;
            background-color: gray;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">Add Movie Copy</h2>

<?php if ($success): ?>
    <p class="message">✅ Successfully added a new <?= htmlspecialchars($format) ?> copy!</p>
<?php endif; ?>

<form method="POST" action="add_movie_copy_handler.php">
    <label for="movie_id">Select Movie:</label>
    <select name="movie_id" required>
        <option value="">-- Select --</option>
        <?php while ($row = $movies->fetch_assoc()): ?>
            <option value="<?= $row['Movie_ID'] ?>"><?= htmlspecialchars($row['Title']) ?></option>
        <?php endwhile; ?>
    </select>

    <label for="store_id">Select Store:</label>
    <select name="store_id" required>
        <option value="">-- Select --</option>
        <?php while ($row = $stores->fetch_assoc()): ?>
            <option value="<?= $row['Store_ID'] ?>"><?= htmlspecialchars($row['Address']) ?></option>
        <?php endwhile; ?>
    </select>

    <label for="format">Format:</label>
    <select name="format" required>
        <option value="DVD">DVD</option>
        <option value="BLU-RAY">Blu-Ray</option>
    </select>

    <button type="submit">Add Copy</button>
</form>


<div style="text-align:center;">
    <a href="admin_dashboard.php" class="button">← Back to Dashboard</a>
</div>

</body>
</html>

<?php $conn->close(); ?>
