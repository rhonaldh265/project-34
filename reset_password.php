<?php
require 'db.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $user = $collection->findOne([
        'reset_token' => $token,
        'token_expiry' => ['$gt' => time()] // Check if token is not expired
    ]);

    if (!$user) {
        die("Invalid or expired token.");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Update password and clear token fields
        $collection->updateOne(
            ['_id' => $user['_id']],
            ['$set' => ['password' => $newPassword], '$unset' => ['reset_token' => '', 'token_expiry' => '']]
        );

        echo "Password updated successfully! <a href='login.php'>Login here</a>";
        exit;
    }
} else {
    die("No token provided.");
}
?>

<form method="POST">
    <h2>Enter New Password</h2>
    <input type="password" name="password" placeholder="New Password" required>
    <button type="submit">Update Password</button>
</form>
