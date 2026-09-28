<?php
session_start();

session_destroy(); // Vernietig de huidige sessie en alle bijbehorende gegevens

header("Location: login.php"); // Stuur de gebruiker door naar de loginpagina

exit; // Stop verdere uitvoering van het script
?>

