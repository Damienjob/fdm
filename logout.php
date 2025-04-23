<?php
session_start();
session_destroy(); // Détruire toutes les sessions
header('Location: connexion.php'); // Rediriger vers la page de connexion
exit();
?>
