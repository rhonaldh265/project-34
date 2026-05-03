<?php
require 'db.php'; // This connects to your MongoDB cluster

try {
    // 1. Get all documents from the 'users' collection
    $cursor = $collection->find();

    echo "<html><head><title>Admin Panel</title>";
    echo "<style>
            body { font-family: Arial; padding: 20px; background: #f0f2f5; }
            table { width: 100%; border-collapse: collapse; background: white; }
            th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
            th { background-color: #007bff; color: white; }
          </style></head><body>";
    
    echo "<h2>Registered Database Users</h2>";
    echo "<table>
            <tr>
                <th>Username</th>
                <th>Password Hash (Secure)</th>
                <th>Account Created</th>
            </tr>";

    // 2. Loop through each user and put them in a table row
    foreach ($cursor as $user) {
        // Convert MongoDB Date to a readable string if it exists
        $date = isset($user['created_at']) ? $user['created_at']->toDateTime()->format('Y-m-d H:i:s') : 'N/A';
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($user['username']) . "</td>";
        echo "<td><code>" . htmlspecialchars($user['password']) . "</code></td>";
        echo "<td>" . $date . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p><a href='signup.php'>Go back to Sign Up</a></p>";
    echo "</body></html>";

} catch (Exception $e) {
    echo "Error fetching users: " . $e->getMessage();
}
?>
