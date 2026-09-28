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

// SQL-query om alle leerpartijen op te halen, de nieuwste (hoogste ID) eerst
$sql = "SELECT * FROM voorraadbeheer ORDER BY id DESC";
$result = $conn->query($sql);

$partijen = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $partijen[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <link rel="stylesheet" href="css/style1.css">
    <meta charset="UTF-8">
    <title>Voorraadbeheer Overzicht</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        
        .top-bar { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px; 
        }

        .btn-toevoegen { 
            padding: 10px 16px; 
            background-color: #28a745; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            font-weight: bold; 
            display: inline-block;
        }

        .btn-bestellingen {
            padding: 10px 16px; 
            background-color: #007bff; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            font-weight: bold; 
            display: inline-block;
            margin-left: 10px;
        }

        .btn-uitloggen { 
            padding: 10px 16px; 
            background-color: #dc3545; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            font-weight: bold; 
            display: inline-block;
        }

        .btn-bewerken {
            padding: 5px 10px;
            background-color: #ffc107;
            color: #000;
            text-decoration: none;
            border-radius: 3px;
            font-weight: bold;
            font-size: 13px;
        }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>

    <h2>Voorraadbeheer - Overzicht Leerpartijen</h2>

    <div class="top-bar">
        <div>
            <a href="partijtoevoegen.php" class="btn-toevoegen">+ Nieuwe Leerpartij Toevoegen</a>
            <a href="bestellingen.php" class="btn-bestellingen">📦 Bekijk Bestellingen</a>
        </div>
        <a href="logout.php" class="btn-uitloggen">Uitloggen 🚪</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Medewerker</th>
                <th>Gewicht (KG)</th>
                <th>Kleur</th>
                <th>Formaat</th>
                <th>Dikte (mm)</th>
                <th>Status</th>
                <th>Datum Registratie</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($partijen) > 0): ?>
                <?php foreach ($partijen as $partij): ?>
                    <tr>
                        <td>#<?= htmlspecialchars($partij['id']) ?></td>
                        <td><?= htmlspecialchars($partij['medewerker']) ?></td>
                        <td><?= htmlspecialchars($partij['gewicht_kg']) ?> kg</td>
                        <td><?= htmlspecialchars($partij['kleur']) ?></td>
                        <td><?= htmlspecialchars($partij['formaat']) ?></td>
                        <td><?= htmlspecialchars($partij['dikte']) ?> mm</td>
                        <td><strong><?= htmlspecialchars($partij['status']) ?></strong></td>
                        <td><?= htmlspecialchars($partij['datum_registratie']) ?></td>
                        <td>
                            <a href="partijbewerken.php?id=<?= $partij['id'] ?>" class="btn-bewerken">✏️ Bewerken</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9">Er zijn nog geen leerpartijen aanwezig in de voorraad.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>