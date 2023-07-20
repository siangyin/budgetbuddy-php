<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/dbConnect.php';
$db = new DB_CONNECT();
$db->connect();

$req_body = file_get_contents('php://input');
$post_data = json_decode($req_body, true);

$email = isset($post_data['email']) ? $post_data['email'] : '';
$password = isset($post_data['password']) ? $post_data['password'] : '';

// Verify required input
if (!empty($email) && !empty($password)) {
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($db->myconn, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Handling success response
        $row = mysqli_fetch_assoc($result);
        $response = array(
            "status" => 1,
            "message" => "User logged in successfully",
            "userId" => (int)$row["userId"],
            "name" => $row["name"],
            "email" => $row["email"],
            "createdOn" => $row["createdOn"]
        );
        echo json_encode($response);
    } else {
        // Handling failed response
        $response = array("status" => 0, "message" => "Invalid user credentials");
        echo json_encode($response);
    }
} else {
    $response = array("status" => 0, "message" => "Please provide email and password");
    echo json_encode($response);
}
?>
