<?php
session_start();
function connectToDatabase()
{
    $servername = "localhost";
    $usernameDB = "root";
    $passwordDB = "";
    $database = "ssrf";

    $conn = new mysqli($servername, $usernameDB, $passwordDB, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

    $conn = connectToDatabase();
    $username = $_SESSION['user']['username'];

    $sql = "SELECT username, avatar FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($username, $avatar);

    $stmt->fetch();
    $stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h2 {
            color: #333;
        }

        img {
            max-width: 200px; 
            height: auto;
            margin-bottom: 20px;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="password"],
        input[type="url"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        a {
            display: block;
            margin-top: 20px;
            text-decoration: none;
            color: #3498db;
        }

        a:hover {
            color: #2980b9;
        }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['user']['username']; ?> (User)</h2>

    <img src="<?php echo $avatar; ?>" alt="User Avatar">

    <form action="uploadavatar.php" method="post">
        <label for="avatar">Upload New Avatar URL:</label>
        <input type="url" name="avatar" required>
        <input type="submit" value="Upload Avatar">
    </form>

    <a href="../logout.php">Logout</a>
</body>
</html>
