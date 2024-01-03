<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $avatarURL = $_POST['avatar'];

    // Check if the user is logged in and has an ID in the session
    if (isset($_SESSION['user']['id'])) {
        $userId = $_SESSION['user']['id'];

        // Check if the URL is an image
        if (isImage($avatarURL)) {
            // Update the user's avatar URL in the database
            updateAvatar($userId, $avatarURL); // Implement this function to update the avatar

            // Display the image
            // echo '<img src="' . $avatarURL . '" alt="User Avatar">';
            echo '<p>Avatar uploaded successfully!</p>';
        } else {
            echo '<p>The provided URL is not a valid image.</p>';
            echo '<p>Response from the URL:</p>';
            echo '<pre>';
            echo file_get_contents($avatarURL); // Echo the content from the URL
            echo '</pre>';
        }
    } else {
        echo '<p>User ID not found in the session. Please log in.</p>';
    }
} else {
    header('Location: user.php');
    exit;
}

// Function to check if the URL points to an image
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

// Function to update the user's avatar in the database
function updateAvatar($userId, $avatarURL)
{

    $servername = "localhost";
    $usernameDB = "root";
    $passwordDB = "";
    $database = "ssrf";

    // Create connection
    $conn = new mysqli($servername, $usernameDB, $passwordDB, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute the update query
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
