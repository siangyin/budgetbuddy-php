<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . "/dbConnect.php";
$db = new DB_CONNECT();
$db->connect();

// Get req body raw data JSONObject and convert json to php object
$req_body = file_get_contents("php://input");
$post_data = json_decode($req_body, true);

function formatDate($dateStr){
  $dateArr=explode("-",trim($dateStr));
  $reverse=array_reverse($dateArr);
  return implode("-", $reverse);
}

function formatCategories($cat){
$categories = explode(",", $cat);
$categories = array_map('trim', $categories);
return implode("','", $categories);
}

$expenseId = isset($post_data["expenseId"]) ? intval($post_data["expenseId"]) : 0;
$userId = isset($post_data["userId"]) ? intval($post_data["userId"]) : 0;
$start = isset($post_data["start"]) ? formatDate($post_data["start"]) : "";
$end = isset($post_data["end"]) ? formatDate($post_data["end"]) : "";
$categories = isset($post_data["categories"]) ? formatCategories($post_data["categories"]) : "";

class Expense {
    public static function generateExp($row) {
        return [
            "expenseId" => (int)$row["expenseId"],
            "category" => $row["category"],
            "date" => formatDate($row["date"]),
            "amount" => $row["amount"],
            "userId" => (int)$row["userId"],
            "description" => $row["description"],
            "createdOn" => $row["createdOn"],
        ];
    }
}

$sql = "SELECT * FROM expenses";
// Customised WHERE clause based on post_data
if ($expenseId > 0 && $userId > 0) {
    // Retrieve a expense based on expenseId and userId
    $sql .= " WHERE expenseId = '$expenseId' AND userId = '$userId'";
} elseif ($userId > 0 && !empty($start) && !empty($end) && !empty($categories)) {
    // Retrieve expenses list based on userId, categories and date start to end period range
    $sql .= " WHERE userId = '$userId' AND category IN ('$categories') AND date BETWEEN '$start' AND '$end'";
} elseif ($userId > 0 && !empty($start) && !empty($end)) {
     // Retrieve expenses list based on userId, and date start to end period range (no categories filters)
    $sql .= " WHERE userId = '$userId' AND date BETWEEN '$start' AND '$end'";
} elseif ($userId > 0 && !empty($categories)) {
    // Retrieve expenses list based on userId, and categories (no date range filter)
    $sql .= " WHERE userId = '$userId' AND category IN ('$categories')";
} elseif ($userId > 0) {
    // Retrieve expenses list based on userId (all expenses, without any filter)
    $sql .= " WHERE userId = '$userId'";
} // else will retrieve all expenses in database if none of any conditions is met

$sql .= " ORDER BY DATE ASC";
$result = mysqli_query($db->myconn, $sql);
if ($result) {
    $data = [];
    $count = mysqli_num_rows($result);

    if ($count > 0) {
        foreach ($result as $row) {
            $exp = Expense::generateExp($row);
            $data[] = $exp;
        }
        $response = ["status" => 1, "count" => $count, "data" => $data];
    } else {
        $response = ["status" => 0, "message" => "No data found"];
    }
} else {
    $response = ["status" => 0, "message" => "Request failed"];
}

echo json_encode($response);

?>
