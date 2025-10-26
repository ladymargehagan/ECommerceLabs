<?php
// Simple database test
require_once 'settings/db_class.php';

$db = new db_connection();
if ($db->db_connect()) {
    echo "Database connection successful!<br>";
    
    // Test a simple query
    $result = $db->db_query("SELECT COUNT(*) as count FROM products");
    if ($result) {
        $row = mysqli_fetch_assoc($db->results);
        echo "Number of products in database: " . $row['count'] . "<br>";
    } else {
        echo "Query failed<br>";
    }
} else {
    echo "Database connection failed!<br>";
}
?>
