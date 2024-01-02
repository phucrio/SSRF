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
function isImage($url)
{
$headers = get_headers($url, 1);

if (isset($headers['Content-Type'])) {
    $contentType = $headers['Content-Type'];

    if (is_array($contentType)) {
        $contentType = end($contentType);
    }

    $imageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp'];

    return in_array($contentType, $imageTypes);
}

return false;
}


function updateAvatar($conn, $userId, $avatarURL)
{
    $sqlUpdate = "UPDATE users SET avatar = ? WHERE id = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("si", $avatarURL, $userId);

    if ($stmtUpdate->execute()) {

    } else {
        echo "Error updating avatar: " . $stmtUpdate->error;
    }

    $stmtUpdate->close();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $avatarURL = $_POST['avatar'];

    if (isset($_SESSION['user']['id'])) {
        $userId = $_SESSION['user']['id'];


        $conn = connectToDatabase();
        if (isImage($avatarURL)) {
            updateAvatar($conn, $userId, $avatarURL);

            echo '<img src="' . $avatarURL . '" alt="User Avatar">';
            echo '<p>Avatar uploaded successfully!</p>';
        } else {
            echo '<p>The provided URL is not a valid image.</p>';
            echo '<p>Response from the URL:</p>';
            echo '<pre>';
            echo file_get_contents($avatarURL);
            echo '</pre>';
        }

        $conn->close();
        } else {
            echo '<p>User ID not found in the session. Please log in.</p>';
        }
        
} else {

    $conn = connectToDatabase();
    $username = $_SESSION['user']['username'];

    $sql = "SELECT username, avatar FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($username, $avatar);

    $stmt->fetch();
    $stmt->close();
}
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
            max-width: 200px; /* Set your desired max-width for the avatar */
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
