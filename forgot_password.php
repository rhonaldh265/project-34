<?php
require 'db.php';

$message = "";
$debugInfo = []; // Store debug information

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $debugInfo['email_entered'] = $email;
    
    // Check database connection
    try {
        $debugInfo['db_connected'] = true;
        $user = $collection->findOne(['email' => $email]);
        $debugInfo['user_found'] = $user ? true : false;
    } catch (Exception $e) {
        $debugInfo['db_error'] = $e->getMessage();
        $user = null;
    }

    if ($user) {
        try {
            // Generate token
            $token = bin2hex(random_bytes(32));
            $expiry = time() + 1800;
            $debugInfo['token_generated'] = true;
            $debugInfo['token_expiry'] = date('Y-m-d H:i:s', $expiry);

            // Update database
            $updateResult = $collection->updateOne(
                ['email' => $email],
                ['$set' => ['reset_token' => $token, 'token_expiry' => $expiry]]
            );
            $debugInfo['db_updated'] = $updateResult->getModifiedCount() > 0;

            // Get API Key
            $apiKey = getenv('SENDGRID_API_KEY');
            $debugInfo['api_key_exists'] = !empty($apiKey);
            $debugInfo['api_key_length'] = strlen($apiKey ?? '');
            
            if (!$apiKey) {
                throw new Exception('SENDGRID_API_KEY environment variable is not set on Render');
            }

            // Prepare reset link
            $resetLink = "https://loginpage34.onrender.com/reset_password.php?token=$token";
            $debugInfo['reset_link'] = $resetLink;

            // Prepare email content
            $emailContent = "
                <h3>Password Reset Request</h3>
                <p>You requested to reset your password for your Safari App account.</p>
                <p>Click the button below to reset your password. This link expires in 30 minutes.</p>
                <a href='$resetLink' style='background:#007bff; color:white; padding:12px 24px; text-decoration:none; border-radius:5px; display:inline-block; margin:20px 0;'>Reset My Password</a>
                <p>If the button doesn't work, copy and paste this link into your browser:</p>
                <p><small>$resetLink</small></p>
                <hr>
                <p><small>If you didn't request this password reset, please ignore this email.</small></p>
                <p><small>Safari App Support Team</small></p>
            ";

            $data = [
                "personalizations" => [[
                    "to" => [["email" => $email]],
                    "subject" => "Password Reset Request - Safari App"
                ]],
                "from" => ["email" => "ronaldkiprotich001@gmail.com", "name" => "Safari App Support"],
                "reply_to" => ["email" => "ronaldkiprotich001@gmail.com"],
                "content" => [[
                    "type" => "text/html",
                    "value" => $emailContent
                ]]
            ];

            // Send through SendGrid API
            $ch = curl_init("https://api.sendgrid.com/v3/mail/send");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            $debugInfo['sendgrid_http_code'] = $httpCode;
            $debugInfo['sendgrid_response'] = $response;
            $debugInfo['curl_error'] = $curlError ?: 'None';

            // Check SendGrid response
            if ($httpCode == 202) {
                $message = "<div style='color: #155724; background-color: #d4edda; padding: 15px; border-radius: 5px; margin-bottom: 15px; border-left: 4px solid #155724;'>
                    ✅ <strong>Success!</strong> Password reset link sent to $email<br>
                    <small>Please check your inbox and spam folder.</small>
                </div>";
                
                // Clear debug info on success (optional - remove if you want to keep showing it)
                $debugInfo = [];
            } else {
                // Parse SendGrid error
                $errorDetail = json_decode($response, true);
                $sendgridError = $errorDetail['errors'][0]['message'] ?? 'Unknown SendGrid error';
                $field = $errorDetail['errors'][0]['field'] ?? 'N/A';
                $help = $errorDetail['errors'][0]['help'] ?? 'No additional help available';
                
                throw new Exception("SendGrid Error: $sendgridError (Field: $field) - $help");
            }
            
        } catch (Exception $e) {
            $debugInfo['error_message'] = $e->getMessage();
            $debugInfo['error_trace'] = $e->getTraceAsString();
            
            $message = "<div style='color: #721c24; background-color: #f8d7da; padding: 15px; border-radius: 5px; margin-bottom: 15px; border-left: 4px solid #721c24;'>
                <strong>❌ Failed to send reset link</strong><br>
                Error: " . htmlspecialchars($e->getMessage()) . "<br><br>
                <details style='margin-top: 10px;'>
                    <summary style='cursor: pointer; font-weight: bold;'>Technical Details (for debugging)</summary>
                    <pre style='background: white; padding: 10px; margin-top: 10px; overflow-x: auto; font-size: 12px;'>" . htmlspecialchars(print_r($debugInfo, true)) . "</pre>
                </details>
            </div>";
        }
    } else {
        // Security: Always show success message even if email not found (prevents email enumeration)
        $message = "<div style='color: #155724; background-color: #d4edda; padding: 15px; border-radius: 5px; margin-bottom: 15px; border-left: 4px solid #155724;'>
            ✅ If registered, a password reset link has been sent to $email.<br>
            <small>Please check your inbox and spam folder.</small>
        </div>";
        
        // Still log for debugging (check Render logs)
        error_log("Password reset requested for non-registered email: $email");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Safari App</title>
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
            border-radius: 10px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.1); 
            width: 100%; 
            max-width: 450px; 
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
            line-height: 1.5;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        
        input[type="email"] { 
            width: 100%; 
            padding: 12px 15px; 
            border: 2px solid #e0e0e0; 
            border-radius: 8px; 
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        input[type="email"]:focus {
            outline: none;
            border-color: #667eea;
        }
        
        button { 
            width: 100%; 
            padding: 12px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-size: 16px; 
            font-weight: 600;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        button:hover { 
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .back-link { 
            margin-top: 20px; 
            display: block; 
            text-align: center;
            text-decoration: none; 
            color: #667eea; 
            font-size: 14px; 
            font-weight: 500;
        }
        
        .back-link:hover { 
            text-decoration: underline; 
        }
        
        .message {
            margin-bottom: 20px;
        }
        
        details {
            margin-top: 15px;
        }
        
        pre {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 11px;
            line-height: 1.4;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Reset Password</h2>
    <div class="subtitle">Enter your email address and we'll send you a link to reset your password.</div>
    
    <?php echo $message; ?>
    
    <form method="POST">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required autocomplete="email">
        </div>
        <button type="submit">Send Reset Link</button>
    </form>
    <a href="login.php" class="back-link">← Back to Login</a>
</div>
</body>
</html>
