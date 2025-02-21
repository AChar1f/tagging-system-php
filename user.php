<?php
include('databaseConnection.php');

// Handle incoming request
$request_method = $_SERVER["REQUEST_METHOD"];
$endpoint = $_GET["endpoint"] ?? "";
$id = $_GET["id"] ?? "";
$payLoad = $_GET["payLoad"] ?? "";

// Ensure endpoint is provided
if (!$endpoint) {
    echo json_encode(["error" => "Endpoint not provided"]);
    exit;
}

// API Routing
switch ($request_method) {
    case 'GET':
        if ($endpoint === "users") {
            if ($id) {
                getUser($conn, $id);
            }else{
                getUsers($conn);
            }

        } elseif ($endpoint === "monitors") {
            if ($conn) {
                fetchAdmin($conn);
            }
        } else {
            echo json_encode(["error" => "Invalid endpoint"]);
        }
        break;

    case 'PATCH':
        if($endpoint === "users") {
            if ($payLoad) {
                updateUser($conn, $id, $payLoad);
            }
        }
        break;

    default:
        echo json_encode(["error" => "Method not supported"]);
}

// Function to fetch users from database
function getUsers($conn) {
    $sql = "SELECT * FROM Users";
    $result = $conn->query($sql);

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    echo json_encode(["status" => 200, "result" => $users]);
}

function getUser($conn, $id){
    if (!$id) {
        echo json_encode(["error" => "User ID not provided"]);
        return;
    }

    $sql = "SELECT * FROM Users WHERE user_id=$id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode(["status"=> 200, "result" => $user]);
    } else {
        echo json_encode(["error" => "User not found"]);
    }
}

// Function to add a user to the database
function addUser($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["name"])) {
        echo json_encode(["error" => "Invalid data"]);
        return;
    }

    $name = $conn->real_escape_string($data["name"]);
    $sql = "INSERT INTO users (name) VALUES ('$name')";

    if ($conn->query($sql)) {
        echo json_encode(["message" => "User added", "id" => $conn->insert_id]);
    } else {
        echo json_encode(["error" => "Failed to add user"]);
    }
}

function updateUser($conn, $id, $payLoad){
    if ($payLoad) {
       $data = json_decode($payLoad);
    }

    $name = $data->first_name;

    $sql = "UPDATE Users SET first_name='$name' WHERE user_id=$id";

    if ($conn->query($sql)) {
        echo json_encode(["message" => "User updated"]);
    } else {
        echo json_encode(["error" => "Failed to update user"]);
    }
}

function fetchAdmin($conn){
    $sql = "SELECT monitor_id, user_id, email_add, user_pass, concat(first_name, ' ', last_name) 'Full Name', department
        FROM Monitoring LEFT JOIN Users using(user_id)
        order by user_id desc;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        echo json_encode(["status"=> 200, "result" => $admin]);
    } else {
        echo json_encode(["error" => "Admin not found"]);
    }
}



