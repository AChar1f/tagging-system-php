<?php
// Database connection parameters
$servername = "b6gef7owonfccgwjikcp-mysql.services.clever-cloud.com";
$username = "uuowrksdta2ceyez";
$password = "LgpAY5G7FN5uUVnXMrST";
$dbname = "b6gef7owonfccgwjikcp";

// Enable MySQLi exceptions for better error handling
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4"); 
} catch (Exception $e) {
    echo json_encode(["error" => "Database connection failed", "details" => $e->getMessage()]);
    exit;
}