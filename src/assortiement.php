<?php
echo __FILE__;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Assortiement</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <table>
    <tr>
      <th>COL 1</th>
      <th>COL 2</th>
      <th>COL 3</th>
      <th>COL 4</th>
      <th>COL 5</th>
      <th>COL 6</th>
      <th>COL 7</th>
      <th>voorraad</th>
      <th>COL 9</th>
      <th>COL 10</th>
      <th>COL 11</th>
      <th>COL 12</th>
      <th>COL 13</th>
      <th>COL 14</th>
    </tr>;

    <?php
    $conn = require_once "partials/dbconnection.php";

    $stmt = $conn->prepare("SELECT * FROM planten WHERE voorraad > 0 LIMIT 200;");
    
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0)
      exit('No rows');

    while ($row = $result->fetch_assoc()) {
      echo "<tr>";
      echo "<td>" . $row['COL 1'] . "</td>";
      echo "<td>" . $row['COL 2'] . "</td>";
      echo "<td>" . $row['COL 3'] . "</td>";
      echo "<td>" . $row['COL 4'] . "</td>";
      echo "<td>" . $row['COL 5'] . "</td>";
      echo "<td>" . $row['COL 6'] . "</td>";
      echo "<td>" . $row['COL 7'] . "</td>";
      echo "<td>" . $row['voorraad'] . "</td>";
      echo "<td>" . $row['COL 9'] . "</td>";
      echo "<td>" . $row['COL 10'] . "</td>";
      echo "<td>" . $row['COL 11'] . "</td>";
      echo "<td> <img src ='images/" . $row['COL 12'] . "'/></td>";
      echo "<td>" . $row['COL 13'] . "</td>";
      echo "<td>" . $row['COL 14'] . "</td>";
      echo "</tr>";
    }
    echo "</table>";

    $stmt->close();
    ?>
</body>

</html>