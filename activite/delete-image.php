<?php
$host = 'localhost';
$db = 'fdm';
$user = 'root';
$pass = '';

// Connexion à la base de données
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Échec de la connexion à la base de données.']);
    exit;
}

// Récupération des données JSON
$data = json_decode(file_get_contents("php://input"), true);
$imageId = $data['imageId'] ?? null;

if (!$imageId) {
    echo json_encode(['success' => false, 'error' => 'Aucun ID d\'image fourni.']);
    exit;
}

// Récupération du chemin de l'image
$stmt = $conn->prepare("SELECT chemin FROM images_activites WHERE id = ?");
$stmt->bind_param("i", $imageId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Image non trouvée.']);
    exit;
}

$row = $result->fetch_assoc();
$chemin = $row['chemin'];

// Suppression de l'image de la base de données
$stmt = $conn->prepare("DELETE FROM images_activites WHERE id = ?");
$stmt->bind_param("i", $imageId);
$stmt->execute();

// Suppression du fichier physique
$path = "../uploads/" . $chemin;
if (file_exists($path)) {
    if (unlink($path)) {
        echo json_encode(['success' => true, 'message' => 'Image supprimée avec succès']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Image supprimée de la base mais échec lors de la suppression du fichier.']);
    }
} else {
    echo json_encode(['success' => true, 'message' => 'Image supprimée de la base. Le fichier n\'existait plus.']);
}
?>
