<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();
include 'database/db.php';

function redirectWithError($error) {
    ob_end_clean();
    header("Location: html/login.html?error=" . urlencode($error));
    exit();
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        redirectWithError('empty_fields');
    }

    if (strlen($username) < 3) {
        redirectWithError('invalid_credentials');
    }

    if (strlen($password) < 6) {
        redirectWithError('invalid_credentials');
    }

    try {
        $query = "SELECT admin_id, username, password FROM admin WHERE username = ? LIMIT 1";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            error_log("Database prepare error: " . $conn->error);
            redirectWithError('system_error');
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                session_regenerate_id(true);
                
                $_SESSION['admin_id'] = $row['admin_id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['login_time'] = time();
                
                if (isset($_POST['remember']) && $_POST['remember'] == 'on') {
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
                    ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60);
                }

                error_log("Successful login for user: " . $username . " at " . date('Y-m-d H:i:s'));
                
                ob_end_clean();
                header("Location: html/dashboard.html");
                exit();
            } else {
                error_log("Failed login attempt for user: " . $username . " at " . date('Y-m-d H:i:s'));
                redirectWithError('invalid_credentials');
            }
        } else {
            error_log("Login attempt for non-existent user: " . $username . " at " . date('Y-m-d H:i:s'));
            redirectWithError('invalid_credentials');
        }

        $stmt->close();
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        redirectWithError('system_error');
    }
} else {
    header("Location: html/login.html");
    exit();
}
?>
