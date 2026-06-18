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
$zoek      = trim($_GET['zoek'] ?? '');
$gekozen   = $_GET['standplaats'] ?? '';
$sortering = $_GET['sortering'] ?? '';
$heeftZoekopdracht = $zoek !== '';
?>
<form method="GET" action="Assortiment.php">
  <input type="text" name="zoek" placeholder="Zoek op naam..." value="<?= htmlspecialchars($zoek) ?>">
  <select name="standplaats" onchange="this.form.submit()">
    <option value="">Alle planten</option>
    <?php while ($rij = $standplaatsen->fetch_assoc()): ?>
      <option value="<?= htmlspecialchars($rij['Standplaats']) ?>" <?= ($gekozen === $rij['Standplaats']) ? 'selected' : '' ?>>
        <?= htmlspecialchars($rij['Standplaats']) ?>
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

<?php
if ($heeftZoekopdracht):
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

  if ($result->num_rows === 0):
?>
    <p>Geen planten gevonden.</p>
<?php else: ?>
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
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars((string) $row['Naam']) ?></td>
          <td><?= htmlspecialchars((string) $row['Latijnse naam']) ?></td>
          <td><?= htmlspecialchars((string) $row['Waterbehoefte']) ?></td>
          <td><?= htmlspecialchars((string) $row['Lichtbehoefte']) ?></td>
          <td><?= htmlspecialchars((string) $row['Groeihoogte_cm']) ?></td>
          <td><?= htmlspecialchars((string) $row['Verkoopprijs_eur']) ?></td>
          <td><?= htmlspecialchars((string) $row['Standplaats']) ?></td>
          <td><?= htmlspecialchars((string) $row['Voorraad']) ?></td>
          <td><?= htmlspecialchars((string) $row['Bloeitijd']) ?></td>
          <td><?= htmlspecialchars((string) $row['Kleur']) ?></td>
          <td><?= htmlspecialchars((string) $row['Huisdier_vriendelijk']) ?></td>
          <td><img src="images/<?= htmlspecialchars((string) $row['Overview_image']) ?>" width="100" alt="<?= htmlspecialchars((string) $row['Naam']) ?>"></td>
          <td><?= htmlspecialchars((string) $row['Additional_image1']) ?></td>
          <td><?= htmlspecialchars((string) $row['Additional_image2']) ?></td>
          <td><?= htmlspecialchars((string) $row['standplaats_id']) ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
<?php
  endif;

  $stmt->close();
endif;
?>
</body>

</html>
