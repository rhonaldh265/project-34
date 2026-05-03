<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = $collection->findOne(['email' => $email]);

    if ($user && password_verify($password, $user['password'])) {
        echo "Login successful! Welcome " . htmlspecialchars($user['username']);
        // Start session and redirect here
    } else {
        echo "<div style='color:red;'>Incorrect email or password.</div>";
        echo "<p><a href='forgot_password.php'>Forgot your password? Click here to reset it.</a></p>";
    }
}
?>
