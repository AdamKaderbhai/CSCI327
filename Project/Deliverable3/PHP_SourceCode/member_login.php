<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Member Login - AlphaBhatta VideoStore</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 100px;
        }
        input {
            padding: 10px;
            width: 250px;
            margin: 10px;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <h2>Member Login</h2>

    <form method="post" action="member_authenticate.php">
        <input type="number" name="user_id" placeholder="User ID" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>

</body>
</html>


