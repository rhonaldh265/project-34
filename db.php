<?php
// This line is mandatory for Render to find the MongoDB library
require 'vendor/autoload.php';

// 1. YOUR CONNECTION STRING
// REPLACE 'YourRealPasswordHere' with the password you set in MongoDB Atlas Database Access
$pass = "YourRealPasswordHere"; 
$connectionString = "mongodb+srv://ronaldkiprotich001_db_user:" . $pass . "@cluster0.euqh3vy.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0";

try {
    // 2. CREATE THE CLIENT
    $client = new MongoDB\Client($connectionString);

    // 3. SELECT DATABASE AND COLLECTION
    // This will automatically create 'test_db' and 'users' if they don't exist
    $db = $client->selectDatabase('test_db');
    $collection = $db->selectCollection('users');

} catch (Exception $e) {
    // If the password or network access is wrong, this will tell you why
    die("Database Connection Error: " . $e->getMessage());
}
?>
