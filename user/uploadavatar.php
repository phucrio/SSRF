<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $avatarURL = $_POST['avatar'];

    if (isset($_SESSION['user']['id'])) {
        $userId = $_SESSION['user']['id'];

        if (isImage($avatarURL)) {
            updateAvatar($userId, $avatarURL);

            // echo '<img src="' . $avatarURL . '" alt="User Avatar">';
            echo '<p>Avatar uploaded successfully!</p>';
        } else {
            echo '<p>The provided URL is not a valid image.</p>';
            echo '<p>Response from the URL:</p>';
            echo '<pre>';
            echo file_get_contents($avatarURL); 
            echo '</pre>';
        }
    } else {
        echo '<p>User ID not found in the session. Please log in.</p>';
    }
} else {
    header('Location: user.php');
    exit;
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

function updateAvatar($userId, $avatarURL)
{

    $servername = "localhost";
    $usernameDB = "root";
    $passwordDB = "";
    $database = "ssrf";

    $conn = new mysqli($servername, $usernameDB, $passwordDB, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sqlUpdate = "UPDATE users SET avatar = ? WHERE id = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("si", $avatarURL, $userId);

    if ($stmtUpdate->execute()) {
    } else {
        echo "Error updating avatar: " . $stmtUpdate->error;
    }

    $stmtUpdate->close();
    $conn->close();
}
?>
