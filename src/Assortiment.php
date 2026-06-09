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
  <input type="text" name="zoek" placeholder="Zoek op naam..." value="<?= htmlspecialchars($_GET['zoek'] ?? '') ?>">
  <select name="standplaats" onchange="this.form.submit()">
    <option value="">Alle planten</option>
    <?php while ($rij = $standplaatsen->fetch_assoc()): ?>
      <option value="<?= $rij['Standplaats'] ?>" <?= (($_GET['standplaats'] ?? '') === $rij['Standplaats']) ? 'selected' : '' ?>>
        <?= $rij['Standplaats'] ?>
      </option>
    <?php endwhile; ?>
  </select>
  <select name="sortering">
    <option value="">Geen sortering</option>
    <option value="ASC" <?= (($_GET['sortering'] ?? '') === 'ASC') ? 'selected' : '' ?>>Prijs: laag → hoog</option>
    <option value="DESC" <?= (($_GET['sortering'] ?? '') === 'DESC') ? 'selected' : '' ?>>Prijs: hoog → laag</option>
  </select>
  <button type="submit">Zoeken</button>
</form>
  <table>
    <tr>
      <th>Naam</th>
      <th>Latijnse naam</th>
      <th>Waterbehoefte</th>
      <th>Lichtbehoefte</th>
      <th>Groeihoogte_cm</th>
      <th>Verkoopprijs_eur</th>
      <th>Standplaats</th>
      <th>Voorraad</th>
      <th>Bloeitijd</th>
      <th>Kleur</th>
      <th>Huisdier_vriendelijk</th>
      <th>Overview_image</th>
      <th>Additional_image1</th>
      <th>Additional_image2</th>
      <th>standplaats_id</th>
    </tr>
    <?php
    $gekozen   = $_GET['standplaats'] ?? '';
    $zoek      = $_GET['zoek'] ?? '';
    $sortering = $_GET['sortering'] ?? '';

    $zoekParam        = '%' . $zoek . '%';
    $veiligeSortering = in_array($sortering, ['ASC', 'DESC']) ? $sortering : '';
    $volgorde         = $veiligeSortering !== '' ? "ORDER BY Verkoopprijs_eur $veiligeSortering" : '';

    if ($gekozen !== '') {
      $stmt = $conn->prepare("SELECT * FROM planten WHERE Voorraad > 0 AND Standplaats = ? AND Naam LIKE ? $volgorde LIMIT 20");
      $stmt->bind_param("ss", $gekozen, $zoekParam);
    } else {
      $stmt = $conn->prepare("SELECT * FROM planten WHERE Voorraad > 0 AND Naam LIKE ? $volgorde LIMIT 20");
      $stmt->bind_param("s", $zoekParam);
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
      echo "<td>" . $row['Groeihoogte_cm'] . "</td>";
      echo "<td>" . $row['Verkoopprijs_eur'] . "</td>";
      echo "<td>" . $row['Standplaats'] . "</td>";
      echo "<td>" . $row['Voorraad'] . "</td>";
      echo "<td>" . $row['Bloeitijd'] . "</td>";
      echo "<td>" . $row['Kleur'] . "</td>";
      echo "<td>" . $row['Huisdier_vriendelijk'] . "</td>";
      echo "<td><img src='images/" . $row['Overview_image'] . "' width='100' /></td>";
      echo "<td>" . $row['Additional_image1'] . "</td>";
      echo "<td>" . $row['Additional_image2'] . "</td>";
      echo "<td>" . $row['standplaats_id'] . "</td>";
      echo "</tr>";
    }
    echo "</table>";
    $stmt->close();
    ?>
</body>

</html>