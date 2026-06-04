<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="max-age=86400">
    <title>Assortiment</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header class="main-header">
    <div class="header-logo">
      <img src="images/Nora'sFloraLogo.png" data-src="images/Nora'sFloraLogo.png" alt="Nora's Flora logo">
    </div>

    <nav class="header-menu">
      <a href="Hoofdpagina.html">Home</a>
      <span>|</span>
      <a href="Assortiment2.php">Assortiment</a>
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
    <section class="assortiment-grid">
      <article class="product">
        <img src="images/zonnebloemen.jpg" data-src="images/zonnebloemen.jpg" alt="Zonnebloemen">
        <h3>Zonnebloemen</h3>
        <p>EUR 5,00</p>
      </article>

      <article class="product">
        <img src="images/rodetulpen.jpg" data-src="images/rodetulpen.jpg" alt="Rode tulpen">
        <h3>Rode tulpen</h3>
        <p>EUR 7,68</p>
      </article>

      <article class="product">
        <img src="images/cactus.jpg" data-src="images/cactus.jpg" alt="Cactus">
        <h3>Cactus</h3>
        <p>EUR 2,00</p>
      </article>

      <article class="product">
        <img src="images/Rozen_bouquet_.jpg" data-src="images/rozen bouquet.jpg" alt="Rozen bouquet">
        <h3>Rozen bouquet</h3>
        <p>EUR 19,60</p>
      </article>

      <article class="product featured">
        <img src="images/speciaal-samengestelde-trouwdag-bouquet-bundel.jpg" data-src="images/speciaal samengestelde trouwdag bouquet bundel.jpg" alt="Trouwdag bouquet bundel">
        <h3>Speciaal samengestelde trouwdag bouquet bundel</h3>
        <p>Vanaf EUR 25,00</p>
      </article>

      <article class="product">
        <img src="images/Lente-bouquet.jpg" data-src="images/Lente bouquet.jpg" alt="Lentebouquet">
        <h3>Lentebouquet</h3>
        <p>Vanaf EUR 20,00</p>
      </article>

      <article class="product">
        <img src="images/vetplanten.jpg" data-src="images/vetplanten.jpg" alt="Vetplanten">
        <h3>Vetplanten</h3>
        <p>Vanaf EUR 5,00</p>
      </article>

      <article class="product">
        <img src="images/diverse-lengte-bloemen.jpg" data-src="images/diverse lengte bloemen.jpg" alt="Diverse lengte bloemen">
        <h3>Diverse lengte bloemen</h3>
        <p>Al vanaf EUR 7,50 per bos</p>
      </article>

      <article class="product">
        <img src="images/allium.jpg" data-src="images/allium.jpg" alt="Allium bloemen">
        <h3>Paarse allium bloemen</h3>
        <p>Vanaf EUR 5,00</p>
      </article>
    </section>
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

    $stmt = $conn->prepare("SELECT * FROM planten WHERE vorraad > 0 LIMIT 20;");
    
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
      echo "<td>" . $row['vorraad'] . "</td>";
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

  <script src="javascript.js"></script>
</body>
</html>
