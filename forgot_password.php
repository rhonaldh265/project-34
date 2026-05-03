<?php
require 'vendor/autoload.php';
require 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $user = $collection->findOne(['email' => $email]);

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expiry = time() + 1800;

        $collection->updateOne(
            ['email' => $email],
            ['$set' => ['reset_token' => $token, 'token_expiry' => $expiry]]
        );

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ronaldkiprotich001@gmail.com'; 
            $mail->Password   = getenv('SMTP_PASSWORD');       
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('ronaldkiprotich001@gmail.com', 'Safari App Support');
            $mail->addAddress($email);

            $resetLink = "https://loginpage34.onrender.com/reset_password.php?token=$token";

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "<h3>Password Reset</h3>
                              <p>You requested a password reset. Click the button below to continue. This link expires in 30 minutes.</p>
                              <a href='$resetLink' style='background:#007bff; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; display:inline-block;'>Reset My Password</a>";

            $mail->send();
            $message = "<div style='color: #155724; background-color: #d4edda; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>Link sent! Check your inbox.</div>";
        } catch (Exception $e) {
            $message = "<div style='color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>Error: Could not send email.</div>";
        }
    } else {
        $message = "<div style='color: #155724; background-color: #d4edda; padding: 10px; border-radius: 5px; margin-bottom: 15px;'>If registered, a link has been sent.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <style>
        /* Modern UI Styling to match Signup/Login */
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            background-color: #f4f4f4; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }
        .container { 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
            width: 100%; 
            max-width: 350px; 
            text-align: center; 
        }
        h2 { color: #333; margin-bottom: 10px; }
        p { color: #666; font-size: 14px; margin-bottom: 20px; }
        input[type="email"] { 
            width: 100%; 
            padding: 12px; 
            margin: 10px 0; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
            box-sizing: border-box; 
        }
        button { 
            width: 100%; 
            padding: 12px; 
            background-color: #007bff; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 16px; 
            transition: background 0.3s ease;
        }
        button:hover { background-color: #0056b3; }
        .back-link { margin-top: 15px; display: block; text-decoration: none; color: #007bff; font-size: 14px; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <h2>Reset Password</h2>
    <p>Enter your email to receive a secure link.</p>
    
    <?php echo $message; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit">Send Reset Link</button>
    </form>

    <a href="login.php" class="back-link">Return to Login</a>
</div>

</body>
</html>
