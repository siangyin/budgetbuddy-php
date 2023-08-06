<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/dbConnect.php';
$db = new DB_CONNECT();
$db->connect();

$req_body = file_get_contents('php://input');
$post_data = json_decode($req_body, true);

// Validate and sanitize input fields
$name = isset($post_data['name']) ? $post_data['name'] : '';
$email = isset($post_data['email']) ? $post_data['email'] : '';
$password = isset($post_data['password']) ? $post_data['password'] : '';

// Validate required fields
if (empty($name) || empty($email) || empty($password)) {
    $response = array("status" => 0, "message" => "Please provide name, email, and password");
    echo json_encode($response);
} else {
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response = array("status" => 0, "message" => "Invalid email format");
        echo json_encode($response);
    } else {
        // Check if email is already registered
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($db->myconn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $response = array("status" => 0, "message" => "Email has been registered, please login or try another email");
            echo json_encode($response);
        } else {
            // Create new user account
            $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
            $result = mysqli_query($db->myconn, $sql);

            if ($result) {
                $id = mysqli_insert_id($db->myconn);
                
                $response = array(
                    "status" => 1, 
                    "message" => "User registration success",
                    "userId" => $id
                );
                echo json_encode($response);
            } else {
                $response = array("status" => 0, "message" => "User registration failed");
                echo json_encode($response);
            }
        }
    }
}
?>
