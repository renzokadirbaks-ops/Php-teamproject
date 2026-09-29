<?php
// Start de sessie om te controleren of de gebruiker is ingelogd
session_start();

// Beveiliging: als de gebruiker niet is ingelogd, stuur direct door naar de login-pagina
if (!isset($_SESSION['ingelogd']) || $_SESSION['ingelogd'] !== true) {
    header("Location: login.php");
    exit;
}

// Laad de databaseverbinding in
require_once "partials/database.php";

$voorraad_id = $_GET['voorraad_id'] ?? null;

if (!$voorraad_id) {
    header("Location: voorraadbeheer.php");
    exit;
}

// 1. Verwerk het formulier wanneer er op "Bestelling Aanmaken & Koppelen" wordt geklikt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $klant_id = $_POST['klant_id'] ?? null;
    
    // Als de gebruiker kiest voor 'nieuwe_klant', voeg dan eerst de nieuwe klant toe
    if ($klant_id === 'nieuw') {
        $naam = trim($_POST['nieuwe_klant_naam']);
        $email = trim($_POST['nieuwe_klant_email']);
        $adres = trim($_POST['nieuwe_klant_adres']);

        $stmt_klant = $conn->prepare("INSERT INTO klanten (naam, email, adres) VALUES (?, ?, ?)");
        $stmt_klant->bind_param("sss", $naam, $email, $adres);
        $stmt_klant->execute();
        $klant_id = $conn->insert_id; // Haal het zojuist aangemaakte klant_id op
    }

    if ($klant_id) {
        $nieuw_bestelnummer = "BST-" . date("Y") . "-" . rand(100, 999);
        
        // Stap A: Maak de bestelling aan voor de gekozen/nieuwe klant
        $stmt1 = $conn->prepare("INSERT INTO bestellingen (klant_id, bestelnummer, status) VALUES (?, ?, 'In behandeling')");
        $stmt1->bind_param("is", $klant_id, $nieuw_bestelnummer);
        $stmt1->execute();
        $nieuwe_bestelling_id = $conn->insert_id;

        // Stap B: Koppel de leerpartij aan de bestelling, zet status op 'Verkocht' EN maak medewerker leeg (NULL)
        // NIEUWE CODE (Verwijder de partij uit voorraadbeheer):
    $stmt2 = $conn->prepare("DELETE FROM voorraadbeheer WHERE id = ?");
    $stmt2->bind_param("i", $voorraad_id);
    $stmt2->execute();

        header("Location: bestellingen.php");
        exit;
    }
}

// 2. Haal alle bestaande klanten op voor de dropdown
$klanten_result = $conn->query("SELECT * FROM klanten");
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Koppel aan Bestelling - Circuleather</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        // JS functie om het 'Nieuwe Klant'-formulier te tonen/verbergen
        function checkKlantSelectie(select) {
            var nieuwKlantForm = document.getElementById('nieuw_klant_velden');
            if (select.value === 'nieuw') {
                nieuwKlantForm.style.display = 'block';
                document.getElementById('nieuwe_klant_naam').required = true;
            } else {
                nieuwKlantForm.style.display = 'none';
                document.getElementById('nieuwe_klant_naam').required = false;
            }
        }
    </script>
</head>
<body>
    <div class="container" style="max-width: 600px; margin: 30px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px;">
        <h2>Leerpartij #<?= htmlspecialchars($voorraad_id); ?> Koppelen aan Bestelling</h2>
        
        <form method="POST">
            <div style="margin-bottom: 15px;">
                <label for="klant_id"><strong>Selecteer Klant:</strong></label><br>
                <select name="klant_id" id="klant_id" onchange="checkKlantSelectie(this)" required style="width: 100%; padding: 8px; margin-top: 5px;">
                    <option value="">-- Kies een bestaande klant of voeg een nieuwe toe --</option>
                    <option value="nieuw" style="font-weight: bold; color: #27ae60;">+ Nieuwe Klant Toevoegen</option>
                    <?php if ($klanten_result && $klanten_result->num_rows > 0): ?>
                        <?php while ($klant = $klanten_result->fetch_assoc()): ?>
                            <option value="<?= $klant['id']; ?>"><?= htmlspecialchars($klant['naam']); ?></option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Velden voor nieuwe klant (verschijnen automatisch als '+ Nieuwe Klant Toevoegen' wordt gekozen) -->
            <div id="nieuw_klant_velden" style="display: none; background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
                <h3>Nieuwe Klant Gegevens</h3>
                <div style="margin-bottom: 10px;">
                    <label for="nieuwe_klant_naam">Naam Klant / Bedrijf *:</label><br>
                    <input type="text" name="nieuwe_klant_naam" id="nieuwe_klant_naam" style="width: 100%; padding: 8px;">
                </div>
                <div style="margin-bottom: 10px;">
                    <label for="nieuwe_klant_email">E-mailadres:</label><br>
                    <input type="email" name="nieuwe_klant_email" id="nieuwe_klant_email" style="width: 100%; padding: 8px;">
                </div>
                <div style="margin-bottom: 10px;">
                    <label for="nieuwe_klant_adres">Adres:</label><br>
                    <input type="text" name="nieuwe_klant_adres" id="nieuwe_klant_adres" style="width: 100%; padding: 8px;">
                </div>
            </div>

            <button type="submit" class="btn-submit" style="background: #27ae60; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;">Bestelling Aanmaken & Koppelen</button>
            <a href="voorraadbeheer.php" style="margin-left: 10px; color: #555; text-decoration: none;">Annuleren</a>
        </form>
    </div>
</body>
</html>