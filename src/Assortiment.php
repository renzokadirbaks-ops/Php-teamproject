<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="max-age=86400">
    <title>Assortiment</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <header class="main-header">
    <div class="header-logo">
      <img src="images/Nora'sFloraLogo.png" data-src="images/aanbevolen-planten-images/Nora'sFloraLogo.png" alt="Nora's Flora logo">
    </div>

    <nav class="header-menu">
      <a href="Hoofdpagina.html">Home</a>
      <span>|</span>
      <a href="Assortiment.php">Assortiment</a>
      <span>|</span>
      <a href="Contact.html">Contact</a>
      <span>|</span>
      <a href="winkelwagen.html" class="cart-link">
        <span class="cart-link-label">Winkelwagen</span>
        <span class="cart-count-badge" aria-live="polite">0</span>
      </a>
    </nav>

    <div class="header-divider"></div>
  </header>

  <main class="assortiment">
    <h1>Ons Assortiment</h1>

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

    <form class="assortiment-search" method="GET" action="Assortiment.php">
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
    <p class="no-products-message">Geen planten gevonden.</p>
<?php else: ?>
    <div class="assortiment-results-wrap">
      <table class="assortiment-results-table">
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
    </div>
<?php
  endif;

  $stmt->close();
endif;
?>
<?php if (!$heeftZoekopdracht): ?>
    <h2 class="aanbevolen-planten-title">Aanbevolen planten</h2>

    <section class="assortiment-grid">
      <article class="product" data-name="Zonnebloemen" data-price="5.00" data-standplaats="Buiten">
        <img src="images/aanbevolen-planten-images/zonnebloemen.jpg" data-src="images/aanbevolen-planten-images/zonnebloemen.jpg" alt="Zonnebloemen">
        <h3>Zonnebloemen</h3>
        <p>EUR 5,00</p>
      </article>

      <article class="product" data-name="Rode tulpen" data-price="7.68" data-standplaats="Buiten">
        <img src="images/aanbevolen-planten-images/rodetulpen.jpg" data-src="images/aanbevolen-planten-images/rodetulpen.jpg" alt="Rode tulpen">
        <h3>Rode tulpen</h3>
        <p>EUR 7,68</p>
      </article>

      <article class="product" data-name="Cactus" data-price="2.00" data-standplaats="Binnen">
        <img src="images/aanbevolen-planten-images/Cactus.jpg" data-src="images/aanbevolen-planten-images/Cactus.jpg" alt="Cactus">
        <h3>Cactus</h3>
        <p>EUR 2,00</p>
      </article>

      <article class="product" data-name="Rozen bouquet" data-price="19.60" data-standplaats="Boeket">
        <img src="images/aanbevolen-planten-images/Rozen_bouquet_.jpg" data-src="images/aanbevolen-planten-images/Rozen_bouquet_.jpg" alt="Rozen bouquet">
        <h3>Rozen bouquet</h3>
        <p>EUR 19,60</p>
      </article>

      <article class="product featured" data-name="Speciaal samengestelde trouwdag bouquet bundel" data-price="25.00" data-standplaats="Boeket">
        <img src="images/aanbevolen-planten-images/speciaal-samengestelde-trouwdag-bouquet-bundel.jpg" data-src="images/aanbevolen-planten-images/speciaal-samengestelde-trouwdag-bouquet-bundel.jpg" alt="Trouwdag bouquet bundel">
        <h3>Speciaal samengestelde trouwdag bouquet bundel</h3>
        <p>Vanaf EUR 25,00</p>
      </article>

      <article class="product" data-name="Lentebouquet" data-price="20.00" data-standplaats="Boeket">
        <img src="images/aanbevolen-planten-images/Lente-bouquet.jpg" data-src="images/aanbevolen-planten-images/Lente-bouquet.jpg" alt="Lentebouquet">
        <h3>Lentebouquet</h3>
        <p>Vanaf EUR 20,00</p>
      </article>

      <article class="product" data-name="Vetplanten" data-price="5.00" data-standplaats="Binnen">
        <img src="images/aanbevolen-planten-images/vetplanten.jpg" data-src="images/aanbevolen-planten-images/vetplanten.jpg" alt="Vetplanten">
        <h3>Vetplanten</h3>
        <p>Vanaf EUR 5,00</p>
      </article>

      <article class="product" data-name="Diverse lengte bloemen" data-price="7.50" data-standplaats="Buiten">
        <img src="images/aanbevolen-planten-images/diverse-lengte-bloemen.jpg" data-src="images/aanbevolen-planten-images/diverse-lengte-bloemen.jpg" alt="Diverse lengte bloemen">
        <h3>Diverse lengte bloemen</h3>
        <p>Al vanaf EUR 7,50 per bos</p>
      </article>

      <article class="product" data-name="Paarse allium bloemen" data-price="5.00" data-standplaats="Buiten">
        <img src="images/aanbevolen-planten-images/allium.jpg" data-src="images/aanbevolen-planten-images/allium.jpg" alt="Allium bloemen">
        <h3>Paarse allium bloemen</h3>
        <p>Vanaf EUR 5,00</p>
      </article>
    </section>
<?php endif; ?>
  </main>

  <footer class="footer-wrapper">
    <div class="footer-content">
      <div class="contact-info">
        <div>
          <span class="contact-label">Email</span>
          <span class="contact-separator">:</span>
          <span class="contact-value">contact@noraflora.com</span>
        </div>
        <div>
          <span class="contact-label">Telefoon</span>
          <span class="contact-separator">:</span>
          <span class="contact-value">06 12345678</span>
        </div>
        <div>
          <span class="contact-label">Adres</span>
          <span class="contact-separator">:</span>
          <span class="contact-value">Zwolle, pannenkoekendijk 420 B</span>
        </div>
      </div>

      <div class="opening-hours">
        <h2>Openingstijden</h2>
        <p>Ma - Vr : 12:00 - 17:00</p>
        <p>Zaterdag : 10:00 - 17:00</p>
      </div>
    </div>
  </footer>
  <script src="javascript.js"></script>
</body>
</html>
