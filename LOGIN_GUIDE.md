# 🔐 Login System Guide - 1028 Café Management

## Quick Start

### 1. Test Your System
Visit: `http://localhost/yourproject/test_complete_login.php`

This will check:
- ✅ Database connection
- ✅ Admin users
- ✅ All required files
- ✅ Provide a test login form

### 2. Login Credentials
- **Username:** `1028_cafe`
- **Password:** `admin123` (if you used the reset script)

### 3. Login URL
`http://localhost/yourproject/html/login.html`

---

## Complete Flow

### Login Process
1. User enters username and password
2. Form validates input (min 3 chars for username, min 6 for password)
3. PHP verifies credentials against database
4. On success → Redirects to `html/dashboard.html`
5. On failure → Shows error message

### Forgot Password Flow
1. User clicks "Forgot password?" on login page
2. Enters registered phone number
3. Receives OTP via SMS (valid for 5 minutes)
4. Enters OTP code
5. Sets new password
6. Redirected back to login with success message

---

## Files Overview

### Frontend (HTML)
- `html/login.html` - Login page
- `html/forgot_password.html` - Request OTP
- `html/verify_otp.html` - Enter OTP
- `html/reset_password.html` - Set new password
- `html/dashboard.html` - Main dashboard

### Backend (PHP)
- `login.php` - Handles login authentication
- `forgot_password.php` - Sends OTP via SMS
- `verify_otp.php` - Validates OTP
- `reset_password.php` - Updates password
- `logout.php` - Ends session

### Database
- `database/db.php` - Database connection
- Table: `admin` (admin_id, username, password, phone_number)

---

## Features

✅ **Secure Authentication**
- Password hashing with `password_hash()`
- Session management
- SQL injection protection (prepared statements)

✅ **Form Validation**
- Client-side validation (JavaScript)
- Server-side validation (PHP)
- Real-time error messages

✅ **Password Recovery**
- OTP via SMS
- 5-minute expiry
- Session-based verification

✅ **User Experience**
- Loading spinners
- Error/success messages
- Password visibility toggle
- Remember me option

---

## Troubleshooting

### Login not working?
1. Check username is correct: `1028_cafe`
2. Reset password: Visit `reset_password.php`
3. Check database connection in `database/db.php`

### Can't access dashboard?
- Make sure you're using correct path: `html/dashboard.html`
- Check browser console for errors (F12)

### Forgot password not working?
- Verify SMS API credentials in `forgot_password.php`
- Check phone number is registered in database

---

## Security Notes

🔒 **Implemented:**
- Password hashing
- Prepared statements
- Session regeneration
- Output buffering
- Error logging

⚠️ **Recommendations:**
- Use HTTPS in production
- Implement rate limiting
- Add CSRF protection
- Set secure cookie flags
- Regular security audits

---

## Need Help?

Run the test script: `test_complete_login.php`
It will diagnose any issues automatically.
