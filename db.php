<?php
require 'vendor/autoload.php';

// Replace with your real Atlas connection string
$connectionString = "mongodb+srv://<username>:<password>@cluster.mongodb.net/";

try {
    $client = new MongoDB\Client($connectionString);
    // This will create a database named 'auth_db' and a collection 'users'
    $collection = $client->auth_db->users;
} catch (Exception $e) {
    die("Error connecting to database: " . $e->getMessage());
}
?>
