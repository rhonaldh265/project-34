<?php
require 'db.php';

$error = "";
$success = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $user = $collection->findOne([
        'reset_token' => $token,
        'token_expiry' => ['$gt' => time()]
    ]);

    if (!$user) {
        $error = "Invalid or expired reset link. Please request a new password reset.";
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$error) {
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        
        // Validate password strength
        if (strlen($password) < 6) {
            $error = "Password must be at least 6 characters long.";
        } elseif ($password !== $confirmPassword) {
            $error = "Passwords do not match.";
        } else {
            $newPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $collection->updateOne(
                ['_id' => $user['_id']],
                ['$set' => ['password' => $newPassword], '$unset' => ['reset_token' => '', 'token_expiry' => '']]
            );
            
            $success = true;
        }
    }
} else {
    $error = "No reset token provided. Please use the link from your email.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Safari App</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0; 
            padding: 20px;
        }
        
        .container { 
            background: white; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 20px 60px rgba(0,0,0,0.3); 
            width: 100%; 
            max-width: 450px; 
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7);
            }
            70% {
                box-shadow: 0 0 0 15px rgba(102, 126, 234, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(102, 126, 234, 0);
            }
        }
        
        .icon svg {
            width: 35px;
            height: 35px;
            fill: white;
        }
        
        h2 { 
            color: #333; 
            margin-bottom: 10px; 
            font-size: 28px;
        }
        
        .subtitle {
            color: #666; 
            font-size: 14px; 
            margin-bottom: 25px; 
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            font-size: 14px;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        input[type="password"] { 
            width: 100%; 
            padding: 14px 15px; 
            border: 2px solid #e0e0e0; 
            border-radius: 10px; 
            box-sizing: border-box;
            font-size: 16px;
            transition: all 0.3s ease;
            font-family: monospace;
            letter-spacing: 1px;
        }
        
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
            font-size: 14px;
            user-select: none;
        }
        
        .toggle-password:hover {
            color: #667eea;
        }
        
        .password-strength {
            margin-top: 8px;
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            overflow: hidden;
        }
        
        .strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease, background-color 0.3s ease;
        }
        
        .strength-text {
            font-size: 11px;
            margin-top: 5px;
            color: #999;
        }
        
        button { 
            width: 100%; 
            padding: 14px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; 
            border: none; 
            border-radius: 10px; 
            cursor: pointer; 
            font-size: 16px; 
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        button:hover { 
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #f5c6cb;
            font-size: 14px;
        }
        
        .success-message {
            text-align: center;
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .success-icon svg {
            width: 40px;
            height: 40px;
            fill: white;
        }
        
        .success-message h3 {
            color: #28a745;
            margin-bottom: 10px;
        }
        
        .login-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        hr {
            margin: 20px 0;
            border: none;
            border-top: 1px solid #e0e0e0;
        }
        
        .info-text {
            background: #e7f3ff;
            padding: 10px;
            border-radius: 8px;
            font-size: 12px;
            color: #004085;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <?php if ($success): ?>
        <!-- Success State -->
        <div class="success-message">
            <div class="success-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                </svg>
            </div>
            <h3>Password Updated Successfully!</h3>
            <p style="color: #666; margin-top: 10px;">Your password has been reset. You can now log in with your new password.</p>
            <a href="login.php" class="login-btn">Login Now</a>
        </div>
    <?php elseif ($error): ?>
        <!-- Error State -->
        <div class="error-message">
            <strong>⚠️ Error:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
        <div style="text-align: center;">
            <a href="forgot_password.php" style="color: #667eea; text-decoration: none;">Request New Reset Link →</a>
        </div>
        <hr>
        <a href="login.php" class="back-link">← Back to Login</a>
    <?php else: ?>
        <!-- Reset Password Form -->
        <div class="header">
            <div class="icon">
                <svg viewBox="0 0 24 24">
                    <path d="M12 2C8.13 2 5 5.13 5 9v4c0 .83.67 1.5 1.5 1.5h1.5c.28 0 .5-.22.5-.5v-4c0-2.76 2.24-5 5-5s5 2.24 5 5v4h1.5c.83 0 1.5-.67 1.5-1.5v-4c0-3.87-3.13-7-7-7z"/>
                    <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2z"/>
                    <path d="M18 16H6c-.55 0-1-.45-1-1s.45-1 1-1h12c.55 0 1 .45 1 1s-.45 1-1 1z"/>
                </svg>
            </div>
            <h2>Create New Password</h2>
            <div class="subtitle">Choose a strong password for your account</div>
        </div>
        
        <form method="POST" id="resetForm">
            <div class="form-group">
                <label>New Password</label>
                <div class="input-wrapper">
                    <input type="password" name="password" id="password" placeholder="Enter new password" required>
                    <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
                </div>
                <div class="password-strength">
                    <div class="strength-bar" id="strengthBar"></div>
                </div>
                <div class="strength-text" id="strengthText">Password strength</div>
            </div>
            
            <div class="form-group">
                <label>Confirm Password</label>
                <div class="input-wrapper">
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm your password" required>
                    <span class="toggle-password" onclick="togglePassword('confirm_password')">👁️</span>
                </div>
            </div>
            
            <button type="submit">Reset Password</button>
        </form>
        
        <div class="info-text">
            🔒 Password must be at least 6 characters long
        </div>
        
        <a href="login.php" class="back-link">← Back to Login</a>
    <?php endif; ?>
</div>

<script>
    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
        field.setAttribute('type', type);
    }
    
    // Password strength checker
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm_password');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let message = '';
            let color = '#e0e0e0';
            
            if (password.length > 0) {
                // Length check
                if (password.length >= 8) strength += 25;
                else if (password.length >= 6) strength += 15;
                
                // Lowercase & Uppercase
                if (password.match(/[a-z]+/)) strength += 25;
                if (password.match(/[A-Z]+/)) strength += 25;
                
                // Numbers
                if (password.match(/[0-9]+/)) strength += 15;
                
                // Special characters
                if (password.match(/[$@#&!]+/)) strength += 10;
                
                // Cap at 100
                strength = Math.min(strength, 100);
                
                if (strength < 30) {
                    message = 'Weak password';
                    color = '#dc3545';
                } else if (strength < 60) {
                    message = 'Fair password';
                    color = '#ffc107';
                } else if (strength < 80) {
                    message = 'Good password';
                    color = '#28a745';
                } else {
                    message = 'Strong password!';
                    color = '#00a854';
                }
            } else {
                message = 'Password strength';
                color = '#e0e0e0';
            }
            
            strengthBar.style.width = strength + '%';
            strengthBar.style.backgroundColor = color;
            strengthText.textContent = message;
            strengthText.style.color = color;
        });
        
        // Real-time password match validation
        if (confirmInput) {
            function validateMatch() {
                if (confirmInput.value.length > 0) {
                    if (passwordInput.value !== confirmInput.value) {
                        confirmInput.style.borderColor = '#dc3545';
                    } else {
                        confirmInput.style.borderColor = '#28a745';
                    }
                } else {
                    confirmInput.style.borderColor = '#e0e0e0';
                }
            }
            
            passwordInput.addEventListener('input', validateMatch);
            confirmInput.addEventListener('input', validateMatch);
        }
    }
    
    // Form validation before submit
    const form = document.getElementById('resetForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            
            if (password !== confirm) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
        });
    }
</script>
</body>
</html>
