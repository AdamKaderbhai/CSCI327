<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New User</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.2);
            width: 400px;
        }
        h2 { text-align: center; }
        label {
            display: block;
            margin-top: 15px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
        }
        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        a.button {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: white;
            background-color: gray;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
        }
    </style>
</head>
<body>

<form method="POST" action="add_user_handler.php">
    <h2>Add New Member or Admin</h2>

    <label for="role">Role:</label>
    <select name="role" id="role" required onchange="toggleFields(this.value)">
        <option value="">-- Select Role --</option>
        <option value="MEMBER">Member</option>
        <option value="ADMIN">Admin</option>
    </select>

    <label for="name">Full Name:</label>
    <input type="text" name="name" required>

    <label for="address">Address:</label>
    <input type="text" name="address" id="address">

    <label for="email">Email (Admin only):</label>
    <input type="email" name="email" id="email">

    <label for="password">Password:</label>
    <input type="password" name="password" required>

    <button type="submit">Add User</button>

    <a class="button" href="admin_dashboard.php">← Back to Dashboard</a>
</form>

<script>
function toggleFields(role) {
    document.getElementById('address').disabled = role !== 'MEMBER';
    document.getElementById('email').disabled = role !== 'ADMIN';
}
</script>

</body>
</html>
