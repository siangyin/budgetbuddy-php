<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/dbConnect.php';
$db= new DB_CONNECT();
$db->connect();

// Get req body raw data JSONObject and convert json to php object
$req_body= file_get_contents('php://input');
$post_data = json_decode($req_body, true);

$expenseId = isset($post_data['expenseId']) ? intval($post_data['expenseId']) : 0;

// Verify required input
if ($expenseId > 0) {
  // Delete expense
  $sql = "DELETE FROM expenses where expenseId = '$expenseId'";
  $result = mysqli_query($db->myconn, $sql);
  if ($result) {
    // Handling success response
    $response = array("status"=>1, "message"=>"Expense deleted successfully");
    echo json_encode($response);
  } else {
    // Handling failed response
    $response = array("status"=>0, "message"=>"Request failed");
    echo json_encode($response);
  }
} else {
  $response = array("status"=>0, "message"=>"ExpenseId is required");
  echo json_encode($response);
}
?>