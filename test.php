<?php
session_start();

function generateCSRFToken() {
    $token = bin2hex(random_bytes(32)); 
    $_SESSION['csrf_token'] = [
        'token' => $token,
        'expires' => time() + 600,
    ];
    return $token;
}

function isValidCSRFToken($token) {
    if (isset($_SESSION['csrf_token']) && isset($_SESSION['csrf_token']['token']) && isset($_SESSION['csrf_token']['expires'])) {
        $expires = $_SESSION['csrf_token']['expires'];
        if (time() <= $expires && hash_equals($_SESSION['csrf_token']['token'], $token)) {
            return true;
        } else {
            // Token hết hạn hoặc không khớp, xử lý lỗi hoặc xóa token
            unset($_SESSION['csrf_token']);
            return false;
        }
    }
    return false;
}

// Kiểm tra token khi xử lý form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

    if (isValidCSRFToken($submittedToken)) {
        // Token hợp lệ, xử lý form
        // ...
    } else {
        // Token không hợp lệ, xử lý lỗi
        // ...
    }
}
?>


<form action="process_form.php" method="post">
    <!-- Các trường dữ liệu của form -->

    <!-- Thêm token vào form -->
    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
    
    <button type="submit">Submit</button>
</form>