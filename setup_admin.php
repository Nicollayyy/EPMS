<?php
// Simple script to create an admin user for testing
include 'database/db.php';

// Admin credentials
$username = "admin";
$password = "admin123"; // Change this to your desired password
$phone_number = "09999595754"; // Optional phone number

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if admin already exists
$check = $conn->query("SELECT * FROM admin WHERE username = '$username'");

if ($check->num_rows > 0) {
    echo "Admin user already exists!<br>";
    echo "Username: admin<br>";
    echo "Try logging in with your existing password.<br><br>";
    
    // Option to update password
    echo '<form method="POST">';
    echo '<input type="hidden" name="update_password" value="1">';
    echo '<button type="submit">Reset Password to: admin123</button>';
    echo '</form>';
    
    if (isset($_POST['update_password'])) {
        $update = $conn->query("UPDATE admin SET password = '$hashed_password' WHERE username = '$username'");
        if ($update) {
            echo "<br><strong>Password has been reset to: admin123</strong><br>";
            echo '<a href="html/login.html">Go to Login Page</a>';
        }
    }
} else {
    // Insert new admin user
    $query = "INSERT INTO admin (username, password, phone_number) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $username, $hashed_password, $phone_number);
    
    if ($stmt->execute()) {
        echo "<h2>✅ Admin user created successfully!</h2>";
        echo "<p><strong>Username:</strong> admin</p>";
        echo "<p><strong>Password:</strong> admin123</p>";
        echo "<br>";
        echo '<a href="html/login.html" style="padding: 10px 20px; background: #8b5e3c; color: white; text-decoration: none; border-radius: 5px;">Go to Login Page</a>';
    } else {
        echo "Error creating admin user: " . $conn->error;
    }
    
    $stmt->close();
}

$conn->close();
?>
