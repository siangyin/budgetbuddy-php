<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Budget Buddy</title>
    <link rel="icon" href="./budget.png" type="image/png" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css"
      integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA=="
      crossorigin="anonymous" referrerpolicy="no-referrer" />
  </head>
   
  <body>
    <section class="hero mt-6">
      <div class="container pt-6">
        <?php
          session_start();
          $start='';
          $end='';
          $userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : '';
          if(!empty($userId) && isset( $_SESSION['name'])) {
            echo "<p class='subtitle'> <span><strong>". strtoupper($_SESSION['name'])."'s Expense report</strong></span><br>
          </p>";
          }
        ?>

        <form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
          <div class="columns">
            <label class="column is-one-third label">Period range</label>
            <input class="column is-one-third input" type="date" name="start" placeholder="Start date">
            <input class="column is-one-third input" type="date" name="end" placeholder="End date">
            </div>

          <div style="text-align:right">
            <input type="submit" name="export" class='button is-danger ' value="Export" />
          </div>
        </form>

        <?php
          session_start();
          class Expense {
            public static function generateExp($row) {
              return [
                "date" => $row["date"],
                "category" => $row["category"],
                "description" => $row["description"],
                "amount" => $row["amount"]
              ];
            }
          }
    
          $start = isset($_POST['start']) ? ($_POST['start']) : "";
          $end = isset($_POST['end']) ? ($_POST['end']) : "";
          $title = $start." - ".$end;
    
          if(isset($_POST['export']) && !empty($userId)){
            require_once __DIR__ . "/api/v1/dbConnect.php";
            $db = new DB_CONNECT();
            $db->connect();
            $sql = "SELECT * FROM expenses WHERE userId = '$userId'";
      
            if(!empty($start) && !empty($end)){
              $sql .= " AND date BETWEEN '$start' AND '$end' ORDER BY date";
            }
      
            $result = mysqli_query($db->myconn, "$sql");
            if ($result) {
              $data = [];
              $count = mysqli_num_rows($result);
              foreach ($result as $row) {
                  $exp = Expense::generateExp($row);
                  $data[] = $exp;
                }
                // Define the filename for the Excel file
                $filename = "report".date('Ymd') . ".xls";
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Cache-Control: max-age=0');
                // Output buffer to capture the Excel data
                ob_start();
                // Fetch and output each row of data
                while ($row = $result->fetch_assoc()) {
                  echo $row['date'] . "\t" . $row['category'] . "\t" . $row['description'] . "\t" . $row['amount'] . "\n";
                }
                // Get the captured Excel data from the output buffer and send it to the browser
                echo ob_get_clean();
            } else {
              echo "Request failed";
            }
          }
        ?>
      </div>
    </section>
    <div class="container is-invisible">
       <p style="text-align:left"><?php echo $start." - ".$end; ?></p>
      <table class="table is-bordered is-striped is-hoverable" >
        <tr>
          <th>DATE</th>
          <th>CATEGORY</th>
          <th>DESCRIPTION</th>
          <th>AMOUNT</th>			
        </tr>
        <tbody>
          <?php foreach($data as $item) { ?>
            <tr>
            <td><?php echo $item['date']; ?></td>
            <td><?php echo $item['category']; ?></td>
            <td><?php echo $item['description']; ?></td>  
            <td><?php echo $item['amount']; ?></td>
            </tr>
          <?php } ?>
        </tbody>
        </table>
      </div>
  </body>
</html>
<!--  -->