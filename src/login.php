<?php
// Start de PHP-sessie. Dit is nodig om de gebruiker ingelogd te houden op andere pagina's.
session_start();

// Laad de databaseverbinding in vanuit de partials map ($conn via mysqli)
require_once 'partials/database.php';

// Initialiseer variabelen voor meldingen
$foutMelding = '';

// Controleer of de gebruiker al is ingelogd. Zo ja, stuur direct door naar het voorraadoverzicht.
if (isset($_SESSION['ingelogd']) && $_SESSION['ingelogd'] === true) {
    header("Location: voorraadbeheer.php");
    exit;
}

// Controleer of het inlogformulier is verzonden (POST-verzoek)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Ophalen en opschonen van de ingevulde formuliervelden
    $gebruikersnaam = trim($_POST['gebruikersnaam'] ?? '');
    $wachtwoord     = trim($_POST['wachtwoord'] ?? '');

    // Controleer of beide velden zijn ingevuld
    if (!empty($gebruikersnaam) && !empty($wachtwoord)) {
        
        // SQL-query voorbereiden om de gebruiker op te zoeken op basis van gebruikersnaam
        $sql = "SELECT id, gebruikersnaam, wachtwoord, rol FROM gebruikers WHERE gebruikersnaam = ?";
        
        // Prepared statement aanmaken tegen SQL-injection
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            // Koppel de gebruikersnaam als string ('s') aan de query
            $stmt->bind_param("s", $gebruikersnaam);
            $stmt->execute();
            
            // Haal het resultaat op
            $result = $stmt->get_result();

            if ($result && $result->num_rows === 1) {
                $gebruiker = $result->fetch_assoc();

                // Controleer of het ingevoerde wachtwoord overeenkomt met de gehashte hash in de database
                if (password_verify($wachtwoord, $gebruiker['wachtwoord'])) {
                    
                    // Wachtwoord klopt! Sla de gebruikersgegevens op in de sessie
                    $_SESSION['ingelogd']       = true;
                    $_SESSION['gebruiker_id']   = $gebruiker['id'];
                    $_SESSION['gebruikersnaam'] = $gebruiker['gebruikersnaam'];
                    $_SESSION['rol']            = $gebruiker['rol'];

                    // Stuur de gebruiker door naar het voorraadbeheer overzicht
                    header("Location: voorraadbeheer.php");
                    exit;
                } else {
                    // Wachtwoord is onjuist (geef een generieke melding om hackers niet slim te maken)
                    $foutMelding = "Ongeldige gebruikersnaam of wachtwoord.";
                }
            } else {
                // Gebruikersnaam niet gevonden
                $foutMelding = "Ongeldige gebruikersnaam of wachtwoord.";
            }

            // Sluit het statement af
            $stmt->close();
        } else {
            $foutMelding = "Fout bij verwerken van het verzoek.";
        }
    } else {
        $foutMelding = "Vul alstublieft zowel gebruikersnaam als wachtwoord in.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <link rel="stylesheet" href="css/style1.css">
    <meta charset="UTF-8">
    <title>Inloggen - Circuleather</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .login-card { max-width: 400px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"] { width: 100%; padding: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .foutmelding { color: red; font-weight: bold; margin-bottom: 15px; }
    </style>
</head>
<body>

    <div class="login-card">
        <h2>Inloggen Circuleather</h2>

        <!-- Foutmeldingen tonen als de inlogpoging mislukt -->
        <?php if ($foutMelding): ?>
            <p class="foutmelding"><?= htmlspecialchars($foutMelding) ?></p>
        <?php endif; ?>

        <!-- Inlogformulier -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="gebruikersnaam">Gebruikersnaam:</label>
                <input type="text" id="gebruikersnaam" name="gebruikersnaam" required>
            </div>

            <div class="form-group">
                <label for="wachtwoord">Wachtwoord:</label>
                <input type="password" id="wachtwoord" name="wachtwoord" required>
            </div>

            <button type="submit">Inloggen</button>
        </form>
    </div>

</body>
</html>