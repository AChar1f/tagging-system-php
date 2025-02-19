<?php
include('databaseConnection.php');

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Handle incoming request
$request_method = $_SERVER["REQUEST_METHOD"];
$endpoint = $_GET["endpoint"] ?? "";
$id = $_GET["id"] ?? "";
$payLoad = $_GET["payload"] ?? "";

// Ensure endpoint is provided
if (!$endpoint) {
    echo json_encode(["error" => "Endpoint not provided"]);
    exit;
}

// API Routing
switch ($request_method) {
    case 'POST':
        if ($endpoint === "addLog") {
            if ($id){
                addLog($conn, $id);
            }
        } else {
            echo json_encode(["error" => "Invalid endpoint"]);
        }
        break;

    default:
        echo json_encode(["error" => "Method not supported"]);
}

function addLog($conn, $id){
    $sql = "INSERT into Attendance(user_id) value($id);";

    $result = $conn->query($sql);

    if ($result) {
        echo json_encode(["status"=>"200", "message" => "Log added"]);
    } else {
        echo json_encode(["error" => "Failed to add log"]);
    }
}


?>