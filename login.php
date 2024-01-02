<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Replace the following with your database connection details
    $servername = "localhost";
    $usernameDB = "root";
    $passwordDB = "";
    $database = "ssrf";

    $conn = new mysqli($servername, $usernameDB, $passwordDB, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id,username, password, avatar, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($dbUserId,$dbUsername, $dbPassword, $dbAvatar, $dbRole);
        $stmt->fetch();

        if (password_verify($password, $dbPassword)) {
            $_SESSION['user'] = [
                'id' => $dbUserId,
                'username' => $dbUsername,
                'avatar' => $dbAvatar,
                'role' => $dbRole
            ];

            if ($dbRole == 'admin') {
                header('Location: admin/index.php');
                exit();
            } elseif ($dbRole == 'user') {
                header('Location: user/index.php');
                exit();
            }
        } else {
            $_SESSION['login_error'] = 'Invalid username or password.';
        }
    } else {
        $_SESSION['login_error'] = 'User not found.';
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        form {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            color: #555;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #2980b9;
        }

        p {
            color: #ff0000;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <form action="" method="post">
        <h2>Login</h2>
        <label for="username">Username:</label>
        <input type="text" name="username" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <input type="submit" value="Login">
        
        <?php
            if (isset($_SESSION['login_error'])) {
                echo '<p>' . $_SESSION['login_error'] . '</p>';
                unset($_SESSION['login_error']);
            }
        ?>
    </form>
</body>
</html>


