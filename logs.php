<?php
include('databaseConnection.php');

// header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");

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
                fetchLog($conn, $id);
            } else {
                fetchLogs($conn);
            }
        }

        if($endpoint === "status") {
            if($conn) {
                userStatus($conn);
                // echo json_encode(["status"=>"200", "message" => "Connected to the database"]);
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
};

function fetchLog($conn, $id) {
    $sql = "SELECT user_id, attendance_id, CONCAT(SUBSTRING(created_at,1, 10), ' ' , SUBSTRING(created_at,12, 5))'Time Stamp', first_name, last_name, department 
    FROM Attendance LEFT JOIN Users USING (user_id) 
    WHERE user_id = $id 
    ORDER BY attendance_id DESC";

    $result = $conn->query($sql);

    if(!$id) {
        echo json_encode(["error" => "No ID provided."]);
    }

    if($result -> num_rows > 0) {
        $log = $row = $result->fetch_assoc();
        echo json_encode(["status" => 200, "result" =>$log]);
    } else {
        echo json_encode(["error" => "No Log found."]);
    }

};

function userStatus($conn) {
    $sql = "SELECT u.user_id, CONCAT(u.first_name, ' ', u.last_name) AS 'Full Name', u.department, 
                DATE_FORMAT(MAX(a.created_at), '%Y-%m-%d %H:%i') AS 'Latest Log',
                CASE 
                    WHEN COUNT(a.attendance_id) % 2 = 0 THEN 'Off-Site'
                    ELSE 'On-site'
                END AS status
            FROM Attendance a
            LEFT JOIN Users u ON a.user_id = u.user_id
            GROUP BY u.user_id, u.first_name, u.last_name, u.department
            ORDER BY MAX(a.created_at) DESC";

    $result = $conn->query($sql);

    if ($result) {
        $status = [];
        while ($row = $result->fetch_assoc()) {
            $status[] = $row;
        }
        echo json_encode(["status" => 200, "result" => $status]);
    } else {
        echo json_encode(["error" => "Failed to Fetch Users' Status."]);
    }
}

?>