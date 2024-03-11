<?php
class Users{

    // Connection
    private $conn;

    //Table name
    private $db_table = "users";

    // field database
    public $id;
    public $name;
    public $email;
    public $age;
    public $designation;
    public $created;

    // Db connection
    public function __construct($db){
        $this->conn = $db;
    }

    // GET ALL
    public function getUsers(){
        $sqlQuery = "SELECT id, name, email, age, designation, created FROM " . $this->db_table . "";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->execute();
        return $stmt;
    }

    // CREATE
    public function createUser($passwordHash) {
        $sqlQuery = "INSERT INTO " . $this->db_table . "
                    SET
                        name = :name, 
                        email = :email, 
                        age = :age, 
                        designation = :designation, 
                        password = :password"; 
    
        $stmt = $this->conn->prepare($sqlQuery);
    
        
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":age", $this->age);
        $stmt->bindParam(":designation", $this->designation);
        $stmt->bindParam(":password", $passwordHash); 
    
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    

    // READ single
    public function getSingleUser(){
        $sqlQuery = "SELECT
                        id, 
                        name, 
                        email, 
                        age, 
                        designation, 
                        created
                      FROM
                        ". $this->db_table ."
                    WHERE 
                       id = ?
                    LIMIT 0,1";

        $stmt = $this->conn->prepare($sqlQuery);

        $stmt->bindParam(1, $this->id);

        $stmt->execute();

        $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->name = $dataRow['name'];
        $this->email = $dataRow['email'];
        $this->age = $dataRow['age'];
        $this->designation = $dataRow['designation'];
        $this->created = $dataRow['created'];
    }

    // UPDATE
    public function updateUser(){
        $sqlQuery = "UPDATE
                        ". $this->db_table ."
                    SET
                        name = :name, 
                        email = :email, 
                        age = :age, 
                        designation = :designation, 
                        created = :created
                    WHERE 
                        id = :id";

        $stmt = $this->conn->prepare($sqlQuery);
        // bind data
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":age", $this->age);
        $stmt->bindParam(":designation", $this->designation);
        $stmt->bindParam(":created", $this->created);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    // DELETE
    function deleteUser(){
        $sqlQuery = "DELETE FROM " . $this->db_table . " WHERE id = ?";
        $stmt = $this->conn->prepare($sqlQuery);

        $this->id=htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(1, $this->id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function login($email, $password) {
        $sqlQuery = "SELECT id, password FROM " . $this->db_table . " WHERE email = :email LIMIT 0,1";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        if($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($password, $user['password'])) {
                return $user; 
            } else {
                return false; 
            }
        } else {
            return false; 
        }
    }

    public function generateToken($userId, $hoursToExpire = 1) { 
        $token = bin2hex(random_bytes(16));
        $expiresAt = date('Y-m-d H:i:s', strtotime("+$hoursToExpire hour")); 
        
        $sqlQuery = "INSERT INTO session (UserToken, UserID, TokenExpire) VALUES (:token, :userId, :expiresAt)";
        $stmt = $this->conn->prepare($sqlQuery);
        
        $stmt->bindParam(":token", $token);
        $stmt->bindParam(":userId", $userId);
        $stmt->bindParam(":expiresAt", $expiresAt);
        
        if ($stmt->execute()) {
            return $token; 
        } else {
            return null; 
        }
    }
    
    
    public function validateToken($token) {
        $sqlQuery = "SELECT UserID FROM session WHERE UserToken = :token AND TokenExpire > NOW()";
        $stmt = $this->conn->prepare($sqlQuery);
    
        $stmt->bindParam(":token", $token);
        $stmt->execute();
    
        if ($stmt->rowCount() > 0) {
            return true; 
        } else {
            return false; 
        }
    }

    public function getAllProducts() {
        $sqlQuery = "SELECT * FROM products";
        $stmt = $this->conn->query($sqlQuery); 
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $products;
    }
    
}
