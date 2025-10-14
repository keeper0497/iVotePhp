<?php
require_once 'config/database.php';

try {
    $conn = getConnection();
    echo "✅ Database connection successful!<br>";
    echo "Server: " . $conn->host_info . "<br>";
    $conn->close();
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage();
}
?>