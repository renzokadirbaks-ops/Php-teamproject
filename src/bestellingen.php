<?php
// Start de sessie om te controleren of de gebruiker is ingelogd
session_start();

// Beveiliging: als de gebruiker niet is ingelogd, stuur direct door naar de login-pagina
if (!isset($_SESSION['ingelogd']) || $_SESSION['ingelogd'] !== true) {
    header("Location: login.php");
    exit;
}

// Laad de databaseverbinding in vanuit de partials map ($conn via mysqli)
require_once "partials/database.php";

// SQL-query met JOINs: Haal bestellingen op, koppel de klantnaam uit 'klanten' en voorraad uit 'voorraadbeheer'
$sql = "SELECT 
            b.id AS bestelling_id,
            b.bestelnummer,
            b.status AS bestelling_status,
            k.naam AS klantnaam,
            v.id AS voorraad_id,
            v.kleur,
            v.gewicht_kg,
            v.dikte
        FROM bestellingen b
        LEFT JOIN klanten k ON b.klant_id = k.id
        LEFT JOIN voorraadbeheer v ON v.bestellingen_id = b.id
        ORDER BY b.id DESC";

$result = $conn->query($sql);

// Maak een lege array aan voor de bestellingen
$bestellingen = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bestellingen[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <link rel="stylesheet" href="css/style1.css">
    <meta charset="UTF-8">
    <title>Bestellingen Overzicht - Circuleather</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        
        .top-bar { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px; 
        }

        .btn-terug {
            padding: 8px 14px; 
            background-color: #6c757d; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            font-weight: bold;
            display: inline-block;
        }

        .btn-uitloggen { 
            padding: 8px 14px; 
            background-color: #dc3545; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            font-weight: bold; 
            display: inline-block;
        }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .geen-leer { color: #888; font-style: italic; }
    </style>
</head>
<body>

    <h2>Bestellingen Overzicht</h2>

    <!-- Navigatiebalk met duidelijke knoppen -->
    <div class="top-bar">
        <div>
            <a href="voorraadbeheer.php" class="btn-terug">← Terug naar Voorraadbeheer</a>
        </div>
        <a href="logout.php" class="btn-uitloggen">Uitloggen 🚪</a>
    </div>

    <!-- Tabel met bestellingen en gekoppelde voorraad -->
    <table>
        <thead>
            <tr>
                <th>Bestelling ID</th>
                <th>Bestelnummer</th>
                <th>Klantnaam</th>
                <th>Gekoppelde Leerpartij</th>
                <th>Status Bestelling</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($bestellingen) > 0): ?>
                <?php foreach ($bestellingen as $b): ?>
                    <tr>
                        <td>#<?= htmlspecialchars($b['bestelling_id']) ?></td>
                        <td><?= htmlspecialchars($b['bestelnummer'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($b['klantnaam'] ?? 'Onbekend') ?></td>
                        
                        <!-- Controleren of er een leerpartij gekoppeld is aan deze bestelling -->
                        <td>
                            <?php if (!empty($b['voorraad_id'])): ?>
                                Partij #<?= htmlspecialchars($b['voorraad_id']) ?> 
                                (<?= htmlspecialchars($b['kleur']) ?>, <?= htmlspecialchars($b['gewicht_kg']) ?> kg, <?= htmlspecialchars($b['dikte']) ?> mm)
                            <?php else: ?>
                                <span class="geen-leer">Nog geen leer gekoppeld</span>
                            <?php endif; ?>
                        </td>

                        <td><strong><?= htmlspecialchars($b['bestelling_status'] ?? 'In behandeling') ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Er zijn nog geen bestellingen gevonden in de database.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>