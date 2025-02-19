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
    case 'GET':
        if($endpoint === "logs") {
            if ($id) {
                // fetchLog($conn, $id);
            } else {
                fetchLogs($conn);
            }
        }
        break;        

    case 'POST':
        if ($endpoint === "addLog") {
            if ($id){
                addLog($conn, $id);
            }
        } else {
            echo json_encode(["error" => "Invalid endpoint 😭"]);
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


function fetchLogs($conn) {
    $sql = "SELECT user_id, attendance_id, CONCAT(SUBSTRING(created_at, 1, 10),' ',
            SUBSTRING(created_at,12, 5))'Time Stamp', first_name, last_name, department 
            FROM Attendance LEFT JOIN Users using (user_id) 
            ORDER BY attendance_id DESC";

    $result = $conn->query($sql);

    if($result) {
        while ($row = $result->fetch_assoc()) {
            $logs[] = $row;
        }
        echo json_encode(["status" => 200, "result" => $logs]);
    } else {
        echo json_encode(["error" => "Failed to fetch Logs."]);
    }
}

?>