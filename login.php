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

if (!empty($data->email) && !empty($data->password)) {
    $email = $data->email;
    $password = $data->password;

    $user = $users->login($email, $password);

    if ($user) {
        $token = $users->generateToken($user['id'],2);
        if ($token) {
            http_response_code(200);
            echo json_encode(["message" => "Login successful", "token" => $token]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Unable to generate authentication token"]);
        }
    } else {
        http_response_code(401);
        echo json_encode(["message" => "Login failed. Invalid email or password."]);
    }
} else {
    http_response_code(400); 
    echo json_encode(["message" => "Unable to login. Missing email or password."]);
}
?>
