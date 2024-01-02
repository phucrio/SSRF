<?php
session_start();


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header('Location: ../index.php');
    exit();
}

$servername = "localhost";
$usernameDB = "root";
$passwordDB = "";
$database = "ssrf";


$conn = new mysqli($servername, $usernameDB, $passwordDB, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addUser'])) {
    $newUser = $_POST['newUser'];

    $hashedPassword = password_hash("user", PASSWORD_DEFAULT);
    $defaultAvatar = "default_avatar.jpg";
    $defaultRole = "user";

    $sqlInsert = "INSERT INTO users (username, password, avatar, role) VALUES (?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("ssss", $newUser, $hashedPassword, $defaultAvatar, $defaultRole);

    if ($stmtInsert->execute()) {

    } else {
        echo "Error adding user: " . $stmtInsert->error;
    }

    $stmtInsert->close();
}

if (isset($_GET['deleteUser'])) {
    $userToDelete = $_GET['deleteUser'];

    $sqlDelete = "DELETE FROM users WHERE username = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("s", $userToDelete);

    if ($stmtDelete->execute()) {

    } else {
        echo "Error deleting user: " . $stmtDelete->error;
    }

    $stmtDelete->close();
}


$sqlFetchUsers = "SELECT username FROM users WHERE role = 'user'";
$resultFetchUsers = $conn->query($sqlFetchUsers);

$users = [];

if ($resultFetchUsers->num_rows > 0) {
    while ($row = $resultFetchUsers->fetch_assoc()) {
        $users[] = $row['username'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h2, h3 {
            color: #333;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
        }

        a {
            text-decoration: none;
            color: #3498db;
        }

        a:hover {
            color: #2980b9;
        }

        form {
            margin-top: 10px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="submit"] {
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['user']['username']; ?> (Admin)</h2>

    <h3>Users:</h3>
    <ul>
        <?php foreach ($users as $user): ?>
            <li>
                <?php echo $user; ?>
                <a href="?deleteUser=<?php echo $user; ?>">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <h3>Add User:</h3>
    <form action="" method="post">
        <label for="newUser">Username:</label>
        <input type="text" name="newUser" required>
        <input type="submit" name="addUser" value="Add User">
    </form>

    <br>

    <a href="../logout.php">Logout</a>
</body>
</html>
