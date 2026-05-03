<?php
// 1. Enable error reporting so you can see the mistake on screen
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $user = $collection->findOne(['email' => $email]);

        if ($user && password_verify($password, $user['password'])) {
            $message = "<div style='color:green;'>Login successful! Welcome " . htmlspecialchars($user['username']) . "</div>";
            // In a real app, you would start a session here: session_start(); $_SESSION['user'] = $user['email'];
        } else {
            $message = "<div style='color:red;'>Incorrect email or password.</div>
                        <p><a href='forgot_password.php'>Forgot your password? Click here to reset it.</a></p>";
        }
    } catch (Exception $e) {
        $message = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: Arial; display: flex; justify-content: center; padding-top: 50px; background: #f4f4f4; }
        form { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 300px; }
        input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>

<form method="POST">
    <h2>Login</h2>
    <?php echo $message; ?>
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
    <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
</form>

</body>
</html>
