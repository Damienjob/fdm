<?php
session_start();

$host = 'localhost';        // ou l'adresse IP du serveur
$user = 'root';             // nom d'utilisateur MySQL
$password = '';             // mot de passe MySQL
$dbname = 'fdm'; // à remplacer par le nom de ta base de données

// Connexion à la base de données
$conn = new mysqli($host, $user, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Définir le charset pour éviter les problèmes d'accents
$conn->set_charset("utf8");

function generateSlug($titre) {
    $slug = strtolower(trim($titre));
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

function slugExists($conn, $slug, $id = null) {
    $sql = "SELECT COUNT(*) FROM activities WHERE slug = ?" . ($id ? " AND id != ?" : "");
    $stmt = $conn->prepare($sql);
    $id ? $stmt->bind_param("si", $slug, $id) : $stmt->bind_param("s", $slug);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    return $count > 0;
}

function makeSlugUnique($conn, $baseSlug, $id = null) {
    $slug = $baseSlug;
    $i = 1;
    while (slugExists($conn, $slug, $id)) {
        $slug = $baseSlug . '-' . $i;
        $i++;
    }
    return $slug;
}

// Récupération des activités
$sql = "SELECT id, titre FROM activities";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $titre = $row['titre'];
    $baseSlug = generateSlug($titre);
    $slugFinal = makeSlugUnique($conn, $baseSlug, $id);

    // Mettre à jour la base
    $stmt = $conn->prepare("UPDATE activities SET slug = ? WHERE id = ?");
    $stmt->bind_param("si", $slugFinal, $id);
    $stmt->execute();
    echo "Slug généré pour l'activité #$id : $slugFinal<br>";
}
?>
