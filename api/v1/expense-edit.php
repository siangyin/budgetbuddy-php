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

$expenseId = isset($post_data['expenseId']) ? intval($post_data['expenseId']) : 0;
$category = isset($post_data['category']) ? trim($post_data['category']) : '';
$date = isset($post_data['date']) ? formatDate($post_data['date']) : '';
$amount = isset($post_data['amount']) ? floatval($post_data['amount']) : 0.0;
$description = isset($post_data['description']) ? trim($post_data['description']) : '';

// Verify required input
if (!empty($category) && $expenseId > 0 && !empty($date) && $amount > 0) {
  // Edit expense
  $sql = "UPDATE expenses set category = '$category', date = '$date', amount = '$amount', description = '$description' where expenseId = '$expenseId'";
  $result = mysqli_query($db->myconn, $sql);
  if ($result) {
    // Handling success response
    $response = array("status"=>1, "message"=>"Expense updated successfully");
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