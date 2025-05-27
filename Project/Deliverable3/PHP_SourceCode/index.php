<!-- index.php -->
<!DOCTYPE html>
<html>
<head>
    <title>VideoStore Login</title>
    <style>
        body { font-family: Arial; text-align: center; padding: 50px; }
        .container { margin-top: 100px; }
        button {
            padding: 15px 30px;
            font-size: 18px;
            margin: 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to AlphaBhatta VideoStore</h1>
        <p>Please choose your login type:</p>
        <form action="member_login.php" method="get">
    <button type="submit">Member Login</button>
</form>

<form action="admin_login.php" method="get">
    <button type="submit">Admin Login</button>
</form>
    </div>
</body>
</html>
