<?php
require 'vendor/autoload.php';
require 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $user = $collection->findOne(['email' => $email]);

    if ($user) {
        $token = bin2hex(random_bytes(32)); // Create a secure random token
        $expiry = time() + 1800; // Token expires in 30 minutes

        // Save token to MongoDB
        $collection->updateOne(
            ['email' => $email],
            ['$set' => ['reset_token' => $token, 'token_expiry' => $expiry]]
        );

        // Send Email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'YOUR_GMAIL_ADDRESS@gmail.com'; // Your Gmail
            $mail->Password   = getenv('SMTP_PASSWORD');       // From Render Env
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('ronaldkiprotich001@@gmail.com', 'Your App Support');
            $mail->addAddress($email);

            $resetLink = "https://loginpage34.onrender.com/reset_password.php?token=$token";

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "Click the link below to reset your password. It expires in 30 minutes:<br><br>
                              <a href='$resetLink'>$resetLink</a>";

            $mail->send();
            echo "A reset link has been sent to your email.";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "If that email is registered, a link has been sent.";
    }
}
?>

<form method="POST">
    <h2>Reset Password</h2>
    <input type="email" name="email" placeholder="Enter your registered email" required>
    <button type="submit">Send Reset Link</button>
</form>
