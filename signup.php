<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'db/Database.php';
include_once 'orm/Users.php';

$database = new Database();
$db = $database->getConnection();

$users = new Users($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email) && !empty($data->password) && !empty($data->name) && !empty($data->age) && !empty($data->designation)) {
    $users->name = $data->name;
    $users->email = $data->email;
    $users->age = $data->age;
    $users->designation = $data->designation;
    
    $passwordHash = password_hash($data->password, PASSWORD_DEFAULT);


    if ($users->createUser($passwordHash)) {
        http_response_code(201); 
        echo json_encode(["message" => "User was created successfully."]);
    } else {
        http_response_code(400); 
        echo json_encode(["message" => "Unable to create user. User with this email may already exist or other error."]);
    }
} else {
    http_response_code(400); 
    echo json_encode(["message" => "Unable to sign up. Data is incomplete."]);
}
?>
