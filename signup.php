<?php
require 'db.php'; // This connects to your MongoDB

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email']; // New email field
      $password = $_POST['password']; 
  //  $password = password_hash ($_POST['password'], PASSWORD_DEFAULT);

    try {
        // 1. Check if the email is already in the database
        $existingUser = $collection->findOne(['email' => $email]);

        if ($existingUser) {
            echo "<div style='color:red;'>Error: This email is already registered! <a href='login.php'>Login here</a></div>";
        } else {
            // 2. Insert the new user with their email
            $insertResult = $collection->insertOne([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'created_at' => new MongoDB\BSON\UTCDateTime()
            ]);

            if ($insertResult->getInsertedCount() === 1) {
                echo "<div style='color:green;'>Registration successful! <a href='login.php'>Login here</a></div>";
            }
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <style>
        body { font-family: Arial; display: flex; justify-content: center; padding-top: 50px; background: #f4f4f4; }
        form { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 300px; }
        input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>

<form method="POST">
    <h2>Create Account</h2>
    <input type="text" name="username" placeholder="Username" required>
    
    <!-- IMPORTANT: The new Email input -->
    <input type="email" name="email" placeholder="Email Address" required>
    
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Sign Up</button>
    <p>Already have an account? <a href="login.php">Login</a></p>
</form>

</body>
</html>
