<?php
// Connexion à la base de données
$host = 'localhost';
$db = 'fdm';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die('Erreur de connexion : ' . $conn->connect_error);
}
// Définir le charset pour éviter les problèmes d'accents
$conn->set_charset("utf8");
// Fonctions slug
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
// Récupération des champs
$titre = $conn->real_escape_string($_POST['titre']);
$description = $conn->real_escape_string($_POST['description']);

$statut = $_POST['statut'];

// Téléversement de l'image principale
$image_path = '';
if (!empty($_FILES['image']['name'])) {
    $image = $_FILES['image'];
    $image_name = time() . '_' . basename($image['name']);
    $target_dir = '../uploads/';
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Crée le dossier avec droits d'écriture
    }
    $target_file = $target_dir . $image_name;

    if (move_uploaded_file($image['tmp_name'], $target_file)) {
        $image_path = $target_file;
    }
}
// Génération du slug
    $baseSlug = generateSlug($titre);
    $slug = makeSlugUnique($conn, $baseSlug);

// Insertion de l’activité
$sql = "INSERT INTO activities (titre, description,  image, statut, slug)
        VALUES ('$titre', '$description', '$image_path', '$statut', '$slug')";

if ($conn->query($sql) === TRUE) {
    $activite_id = $conn->insert_id;

    // Téléversement des images supplémentaires
    if (!empty($_FILES['additional_images']['name'][0])) {
        foreach ($_FILES['additional_images']['tmp_name'] as $key => $tmp_name) {
            $file_name = time() . '_' . basename($_FILES['additional_images']['name'][$key]);
            $file_tmp = $_FILES['additional_images']['tmp_name'][$key];
            $file_path = $target_dir . $file_name;

            if (move_uploaded_file($file_tmp, $file_path)) {
                $conn->query("INSERT INTO images_activites (activite_id, chemin) VALUES ($activite_id, '$file_path')");
            }
        }
    }

    // Redirection vers la page dashboard.php après l'insertion
    header("Location: ../dashboard.php");
    exit;

} else {
    echo "Erreur : " . $conn->error;
}

$conn->close();
?>
