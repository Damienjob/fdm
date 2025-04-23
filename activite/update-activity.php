<?php
header('Content-Type: application/json');

$host = 'localhost';
$db = 'fdm';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

// Vérifie la connexion
if ($conn->connect_error) {
  echo json_encode(['success' => false, 'error' => 'Erreur de connexion à la base de données']);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $titre = $_POST['titre'] ?? '';
 
  $description = $_POST['description'] ?? '';

  // Vérification des champs requis
  if (empty($titre) || empty($description)) {
    echo json_encode(['success' => false, 'error' => 'Tous les champs sont requis']);
    exit;
  }

  $imagePrincipaleModifiee = false;
  $imagesAjoutees = 0;
  $nouvellesImages = [];

  // Image principale
  if (!empty($_FILES['image']['name'])) {
    $imageName = uniqid() . "_" . basename($_FILES['image']['name']);
    $uploadPath = "../uploads/" . $imageName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
      $conn->query("UPDATE activities SET image='$imageName' WHERE id=$id");
      $imagePrincipaleModifiee = true;
    } else {
      echo json_encode(['success' => false, 'error' => "Échec du téléchargement de l'image principale"]);
      exit;
    }
  }

  // Mise à jour de l'activité
  $stmt = $conn->prepare("UPDATE activities SET titre=?, description=? WHERE id=?");
  if ($stmt) {
    $stmt->bind_param("ssi", $titre, $description, $id);
    $stmt->execute();
  } else {
    echo json_encode(['success' => false, 'error' => 'Erreur lors de la mise à jour des données']);
    exit;
  }

  // Images supplémentaires
  if (!empty($_FILES['additional_images']['name'][0])) {
    foreach ($_FILES['additional_images']['tmp_name'] as $key => $tmp_name) {
      if (!empty($_FILES['additional_images']['name'][$key])) {
        $fileName = uniqid() . "_" . basename($_FILES['additional_images']['name'][$key]);
        $uploadPath = "../uploads/" . $fileName;

        if (move_uploaded_file($tmp_name, $uploadPath)) {
          $stmtImg = $conn->prepare("INSERT INTO images_activites (activite_id, chemin) VALUES (?, ?)");
          if ($stmtImg) {
            $stmtImg->bind_param("is", $id, $fileName);
            $stmtImg->execute();
            $imagesAjoutees++;
            $nouvellesImages[] = $fileName; // ✅ Ajouter le nom de l'image à la liste
          }
        }
      }
    }
  }

  $response = [
    'success' => true,
    'titre' => $titre,
    'id' => $id,
    'message' => 'Activité mise à jour avec succès',
    'image_principale_modifiee' => $imagePrincipaleModifiee,
    'images_supplementaires_ajoutees' => $imagesAjoutees
  ];
  
  if ($imagePrincipaleModifiee) {
    $response['nouvelle_image'] = $imageName;
  }
  if (!empty($nouvellesImages)) {
    $response['images_supplementaires'] = $nouvellesImages;
  }
  
  echo json_encode($response);
  
}
?>
