<?php
// Start sessiebeveiliging
session_start();
if (!isset($_SESSION['ingelogd']) || $_SESSION['ingelogd'] !== true) {
    header("Location: login.php");
    exit;
}

require_once "partials/database.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: voorraadbeheer.php");
    exit;
}

$bericht = "";

// Verwerk het formulier als er op opslaan is geklikt
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $medewerker = $_POST['medewerker'];
    $gewicht    = $_POST['gewicht_kg'];
    $kleur      = $_POST['kleur'];
    $dikte      = $_POST['dikte'];
    $status     = $_POST['status'];

    // Update statement met prepared statements voor beveiliging
    $stmt = $conn->prepare("UPDATE voorraadbeheer SET medewerker = ?, gewicht_kg = ?, kleur = ?, dikte = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sdsdsi", $medewerker, $gewicht, $kleur, $dikte, $status, $id);

    if ($stmt->execute()) {
        $bericht = "<p style='color: green;'>Leerpartij succesvol bijgewerkt!</p>";
    } else {
        $bericht = "<p style='color: red;'>Fout bij bijwerken: " . $conn->error . "</p>";
    }
}

// Haal de huidige gegevens van deze specifieke leerpartij op
$stmt = $conn->prepare("SELECT * FROM voorraadbeheer WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$partij = $stmt->get_result()->fetch_assoc();

if (!$partij) {
    echo "Partij niet gevonden.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Leerpartij Bewerken</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select { width: 300px; padding: 8px; box-sizing: border-box; }
        .btn-opslaan { padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn-terug { text-decoration: none; color: #6c757d; margin-left: 10px; }
    </style>
</head>
<link rel="stylesheet" href="css/style1.css">
<body>

    <h2>Leerpartij #<?= htmlspecialchars($partij['id']) ?> Bewerken</h2>
    <?= $bericht ?>

    <form method="POST">
        <div class="form-group">
            <label>Medewerker:</label>
            <input type="text" name="medewerker" value="<?= htmlspecialchars($partij['medewerker']) ?>" required>
        </div>

        <div class="form-group">
            <label>Gewicht (KG):</label>
            <input type="number" step="0.01" name="gewicht_kg" value="<?= htmlspecialchars($partij['gewicht_kg']) ?>" required>
        </div>

        <div class="form-group">
            <label>Kleur:</label>
            <input type="text" name="kleur" value="<?= htmlspecialchars($partij['kleur']) ?>" required>
        </div>

        <div class="form-group">
            <label>Dikte (mm):</label>
            <input type="number" step="0.01" name="dikte" value="<?= htmlspecialchars($partij['dikte']) ?>" required>
        </div>

        <div class="form-group">
            <label>Status:</label>
            <select name="status">
                <option value="Beschikbaar" <?= $partij['status'] == 'Beschikbaar' ? 'selected' : '' ?>>Beschikbaar</option>
                <option value="Gereserveerd" <?= $partij['status'] == 'Gereserveerd' ? 'selected' : '' ?>>Gereserveerd</option>
                <option value="Verkocht" <?= $partij['status'] == 'Verkocht' ? 'selected' : '' ?>>Verkocht</option>
            </select>
        </div>

        <button type="submit" class="btn-opslaan">Wijzigingen Opslaan</button>
        <a href="voorraadbeheer.php" class="btn-terug">Annuleren</a>
    </form>

</body>
</html>