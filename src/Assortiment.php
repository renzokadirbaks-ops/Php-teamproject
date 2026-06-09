<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Games; met SQL prepared statement en partial</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>

<?php
$conn = require_once "partials/dbconnection.php";

$stmt2 = $conn->prepare("SELECT DISTINCT Standplaats FROM planten WHERE Standplaats IS NOT NULL AND Standplaats != '' AND Standplaats != 'standplaats'");
$stmt2->execute();
$standplaatsen = $stmt2->get_result();
?>

<form method="GET" action="">
  <select name="standplaats" onchange="this.form.submit()">
    <option value="">-- Alle planten --</option>
    <?php while ($rij = $standplaatsen->fetch_assoc()): ?>
      <option value="<?= $rij['Standplaats'] ?>">
        <?= $rij['Standplaats'] ?>
      </option>
    <?php endwhile; ?>
  </select>
</form>

  <table>
    <tr>
      <th>Naam</th>
      <th>Latijnse naam</th>
      <th>Waterbehoefte</th>
      <th>Lichtbehoefte</th>
      <th>Groeihoogte-cm</th>
      <th>Verkoopprijs_eur</th>
      <th>Standplaats</th>
      <th>Voorraad</th>
      <th>Bloeitijd</th>
      <th>Kleur</th>
      <th>Huisdier_vriendelijk</th>
      <th>Overview_image</th>
      <th>Additional_image1</th>
      <th>Additional_image2</th>
    </tr>

    <?php
    $gekozen = $_GET['standplaats'] ?? '';

    if ($gekozen !== '') {
      $stmt = $conn->prepare("SELECT * FROM planten WHERE Voorraad > 0 AND Standplaats = ? LIMIT 20");
      $stmt->bind_param("s", $gekozen);
    } else {
      $stmt = $conn->prepare("SELECT * FROM planten WHERE Voorraad > 0 LIMIT 20");
    }

    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0)
      exit('No rows');

    while ($row = $result->fetch_assoc()) {
      echo "<tr>";
      echo "<td>" . $row['Naam'] . "</td>";
      echo "<td>" . $row['Latijnse naam'] . "</td>";
      echo "<td>" . $row['Waterbehoefte'] . "</td>";
      echo "<td>" . $row['Lichtbehoefte'] . "</td>";
      echo "<td>" . $row['Groeihoogte-cm'] . "</td>";
      echo "<td>" . $row['Verkoopprijs_eur'] . "</td>";
      echo "<td>" . $row['Standplaats'] . "</td>";
      echo "<td>" . $row['Voorraad'] . "</td>";
      echo "<td>" . $row['Bloeitijd'] . "</td>";
      echo "<td>" . $row['Kleur'] . "</td>";
      echo "<td>" . $row['Huisdier_vriendelijk'] . "</td>";
      echo "<td><img src='images/" . $row['Overview_image'] . "' width='100' /></td>";
      echo "<td>" . $row['Additional_image1'] . "</td>";
      echo "<td>" . $row['Additional_image2'] . "</td>";
      echo "</tr>";
    }
    echo "</table>";

    $stmt->close();
    ?>
</body>

</html>