<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/dbConnect.php';
$db= new DB_CONNECT();
$db->connect();

// Get req body raw data JSONObject and convert json to php object
$req_body= file_get_contents('php://input');
$post_data = json_decode($req_body, true);

function formatDate($dateStr){
  $dateArr=explode("-",trim($dateStr));
  $reverse=array_reverse($dateArr);
  return implode("-", $reverse);
}

$category = isset($post_data['category']) ? trim($post_data['category']) : '';
$userId = isset($post_data['userId']) ? intval($post_data['userId']) : 0;
$date = isset($post_data['date']) ? formatDate($post_data['date']) : '';
$amount = isset($post_data['amount']) ? floatval($post_data['amount']) : 0.0;
$description = isset($post_data['description']) ? trim($post_data['description']) : '';

// Verify required input
if (!empty($category) && $userId > 0 && !empty($date) && $amount > 0) {
  // Create new expense
  $sql = "INSERT INTO expenses (category, date, amount, userId, description) VALUES ('$category', '$date', '$amount', '$userId', '$description')";
  $result = mysqli_query($db->myconn, $sql);
  if ($result) {
    // Handling success response
     $id = mysqli_insert_id($db->myconn);
    $response = array("status"=>1, "message"=>"Expense added successfully","expenseId"=>$id);
    echo json_encode($response);
  } else {
    // Handling failed response
    $response = array("status"=>0, "message"=>"Request failed");
    echo json_encode($response);
  }
} else {
  $response = array("status"=>0, "message"=>"Incomplete details (category, date and amount are required)");
  echo json_encode($response);
}
?>