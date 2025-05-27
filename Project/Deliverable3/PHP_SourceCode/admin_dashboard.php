<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 3px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
        }
        a.button {
            display: block;
            margin: 10px 0;
            padding: 12px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
        a.button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>🛠️ Administrative Functions Menu</h2>
    <a class="button" href="add_movie_copy.php">➕ Add a Movie Copy</a>
    <a class="button" href="admin_search_movie.php">🔍 Search a Movie and Check Status</a>
    <a class="button" href="admin_search_player.php">📀 Search a Player and Check Status</a>
    <a class="button" href="add_user.php">👥 Add New Customer or Admin</a>
    <a class="button" href="store_info.php">🏪 Store Info</a>
    <a class="button" href="top_renters.php">🏆 Top 10 Frequent Renters</a>
    <a class="button" href="top_rented_store.php">🎥 Top 10 Most Rented in Store</a>
    <a class="button" href="popular_movies.php">🌟 Top 10 Popular Movies</a>
    <a class="button" href="average_charges.php">💰 Avg Charges Per Customer</a>
    <a class="button" href="index.php">🚪 Quit (Log Out)</a>
</div>
</body>
</html>
