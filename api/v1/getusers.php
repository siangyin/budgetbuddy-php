<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/dbConnect.php';
$db = new DB_CONNECT();
$db->connect();

class User {
    public static function generateUser($row) {
        return [
            "userId" => (int)$row["userId"],
            "name" => $row["name"],
            "email" => $row["email"],
            "password" => $row["password"],
            "createdOn" => $row["createdOn"]
        ];
    }
}

$sql = "SELECT * FROM users";
$result = mysqli_query($db->myconn, $sql);

if (mysqli_num_rows($result) > 0) {
    // handle success, has result response
    $data = [];
    foreach ($result as $row) {
        $user = User::generateUser($row);
        $data[] = $user;
    }
    $response = array("status" => 1, "data" => $data);
    echo json_encode($response);
} else {
    // handle no result response
    $response = array("status" => 0, "message" => "No users found");
    echo json_encode($response);
}
?>