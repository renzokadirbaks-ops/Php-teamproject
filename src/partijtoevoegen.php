<?php
// Laad de databaseverbinding in vanuit de partials map ($conn via mysqli)
require_once 'partials/database.php';

// Initialiseer lege variabelen voor meldingen op de pagina
$succesMelding = '';
$foutMelding = '';

// Controleer of de gebruiker op de knop "Partij Opslaan" heeft geklikt (POST-verzoek)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Ophalen en opschonen van de ingevulde formuliergegevens
    $medewerker = trim($_POST['medewerker'] ?? '');
    $gewicht_kg = !empty($_POST['gewicht_kg']) ? floatval($_POST['gewicht_kg']) : 0.00;
    $kleur      = trim($_POST['kleur'] ?? '');
    $formaat    = trim($_POST['formaat'] ?? '');
    $dikte      = !empty($_POST['dikte']) ? floatval($_POST['dikte']) : 0.00;
    $status     = trim($_POST['status'] ?? 'Beschikbaar');

    // SQL-query voorbereiden met vraagtekens (?) tegen SQL-injection (veiligheid)
    $sql = "INSERT INTO voorraadbeheer (medewerker, gewicht_kg, kleur, formaat, dikte, status) VALUES (?, ?, ?, ?, ?, ?)";
    
    // Bereid de query voor via mysqli (Prepared Statement)
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        // 'sdsdds' geeft de datatypen aan: s = string (tekst), d = double/decimal (getal)
        $stmt->bind_param("sdsdds", $medewerker, $gewicht_kg, $kleur, $formaat, $dikte, $status);

        // Voer de query uit naar de MySQL database
        if ($stmt->execute()) {
            $succesMelding = "Nieuwe leerpartij is succesvol toegevoegd!";
        } else {
            $foutMelding = "Fout bij opslaan: " . $stmt->error;
        }
        
        // Sluit het statement af om geheugen vrij te maken
        $stmt->close();
    } else {
        $foutMelding = "Fout in SQL query: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Nieuwe Leerpartij Toevoegen</title>
</head>
<link rel="stylesheet" href="css/style1.css">
<body>

    <h2>Nieuwe Leerpartij Toevoegen</h2>

    <!-- Feedbackmeldingen tonen aan de gebruiker -->
    <?php if ($succesMelding): ?>
        <p style="color: green; font-weight: bold;"><?= $succesMelding ?></p>
    <?php endif; ?>

    <?php if ($foutMelding): ?>
        <p style="color: red; font-weight: bold;"><?= $foutMelding ?></p>
    <?php endif; ?>

    <!-- Invoerformulier voor de voorraadmedewerker -->
    <form method="POST" action="">
        <fieldset>
            <legend>Algemene Gegevens</legend>
            <label for="medewerker">Medewerker:</label><br>
            <input type="text" id="medewerker" name="medewerker" placeholder="Naam medewerker" required><br><br>
        </fieldset>

        <fieldset>
            <legend>Kenmerken Leer</legend>
            
            <label for="gewicht_kg">Gewicht (KG):</label><br>
            <input type="number" step="0.01" id="gewicht_kg" name="gewicht_kg" placeholder="0.00"><br><br>

            <label for="kleur">Kleur:</label><br>
            <input type="text" id="kleur" name="kleur" placeholder="bijv. Zwart, Bruin"><br><br>

            <label for="formaat">Formaat:</label><br>
            <input type="text" id="formaat" name="formaat" placeholder="bijv. Groot, Klein, 60x40cm"><br><br>

            <label for="dikte">Dikte (mm):</label><br>
            <input type="number" step="0.01" id="dikte" name="dikte" placeholder="bijv. 1.20"><br><br>

            <label for="status">Status:</label><br>
            <select id="status" name="status">
                <option value="Beschikbaar">Beschikbaar</option>
                <option value="Gereserveerd">Gereserveerd</option>
                <option value="Verkocht">Verkocht</option>
            </select>
        </fieldset>

        <br>
        <button type="submit">Partij Opslaan</button>
    </form>

    <p><a href="voorraadbeheer.php">← Terug naar overzicht</a></p>

</body>
</html>