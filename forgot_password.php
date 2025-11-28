<?php
session_start();
include 'database/db.php';

if (isset($_POST['send_otp'])) {
    $phone = $_POST['phone'];

   
    $check = $conn->query("SELECT * FROM admin WHERE phone_number='$phone'");
    if ($check->num_rows > 0) {
    
        $otp = rand(100000, 999999);

   
        $_SESSION['otp'] = $otp;
        $_SESSION['phone'] = $phone;

  
        $api_token = "05e3bff5a13d1f3b77c7aa787d57e20b70de8f9b"; // 🔹 Replace with your actual API token

      
        $data = [
            'api_token'    => $api_token,
            'phone_number' => $phone,
            'message'      => "Your OTP code is: $otp. It will expire in 5 minutes."
        ];

  
        $ch = curl_init("https://sms.iprogtech.com/api/v1/otp/send_otp");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            echo "<script>alert('Failed to send OTP. Error: " . addslashes($error) . "'); window.location='./html/forgot_password.html';</script>";
        } else {
            $result = json_decode($response, true);
            if (isset($result['status']) && $result['status'] === 'success') {
                echo "<script>alert('OTP sent successfully!'); window.location='./html/verify_otp_simple.html';</script>";
            } else {
                echo "<script>alert('Failed to send OTP. Please try again.'); window.location='./html/forgot_password.html';</script>";
            }
        }
    } else {
        echo "<script>alert('Phone number not found in system.'); window.location='./html/forgot_password.html';</script>";
    }
} else {
    // If accessed directly without POST, redirect to the form
    header("Location: html/forgot_password.html");
    exit();
}
?>
