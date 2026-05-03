<?php
require 'db.php';
session_start();

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = $collection->findOne(['username' => $username]);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $username;
        $message = "Welcome, " . $username . "! You are logged in.";
    } else {
        $message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; }
        form { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input { display: block; width: 250px; margin-bottom: 10px; padding: 10px; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Login</h2>
        <p><?php echo $message; ?></p>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <p>No account? <a href="signup.php">Sign Up</a></p>
    </form>
</body>
</html>
