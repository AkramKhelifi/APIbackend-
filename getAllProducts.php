<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'db/Database.php';
include_once 'orm/Users.php'; 

$database = new Database();
$db = $database->getConnection();

$users = new Users($db);

$headers = getallheaders();
if (isset($headers["x-auth-token"])) {
    $token = $headers["x-auth-token"];
    if($users->validateToken($token)) { 
        $products = $users->getAllProducts();
        echo json_encode($products);
    } else {
        http_response_code(401); 
        echo json_encode(["message" => "Invalid or expired token."]);
    }
} else {
    http_response_code(400); 
    echo json_encode(["message" => "Token not provided."]);
}
?>
