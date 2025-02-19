<?php


include('databaseConnection.php');

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
        if ($endpoint === "") {
            if ($id) {
                getUser($conn, $id);
            }else{
                getUsers($conn);
            }
        } else {
            echo json_encode(["error" => "Invalid endpoint"]);
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
    
    echo json_encode(["status" => 200, "result
    " => $users]);
}

function getUser($conn, $id){
    // $id = $_GET["id"]?? "";

    if (!$id) {
        echo json_encode(["error" => "User ID not provided"]);
        return;
    }

    $sql = "SELECT * FROM Users WHERE user_id=$id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode($user);
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
       
        echo json_encode(["message" => $payLoad]);
    }
    // $data = json_decode($payLoad, true);

    // if (!isset($data["name"])) {
    //     echo json_encode(["error" => "Invalid data"]);
    //     return;
    // }

    // $name = $conn->real_escape_string($data["name"]);
    // $sql = "UPDATE users SET name='$name' WHERE user_id=$id";

    // if ($conn->query($sql)) {
    //     echo json_encode(["message" => "User updated"]);
    // } else {
    //     echo json_encode(["error" => "Failed to update user"]);
    // }
}

