<?php
$servername = "localhost";
$username = "root";  // Utilisateur que tu as créé
$password = "";  // Mot de passe de l'utilisateur
$dbname = "fdm";  // Nom de ta base de données

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Si la connexion réussit, tu peux continuer à utiliser la variable $conn dans le reste de ton code.
?>
