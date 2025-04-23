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

// Récupérer l'ID de l'activité depuis la requête GET
$activityId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$response = ['success' => false, 'message' => ''];

if ($activityId > 0) {
  // Requête pour récupérer les détails de l'activité et les images supplémentaires
  $stmt = $conn->prepare("SELECT a.*, i.chemin AS image_chemin
                          FROM activities a
                          LEFT JOIN images_activites i ON a.id = i.activite_id
                          WHERE a.id = ?");
  $stmt->bind_param("i", $activityId);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $activity = $result->fetch_assoc();
    $extraImages = [];

    // Récupérer toutes les images supplémentaires associées à l'activité
    $stmtImages = $conn->prepare("SELECT id,chemin FROM images_activites WHERE activite_id = ?");
    $stmtImages->bind_param("i", $activityId);
    $stmtImages->execute();
    $resultImages = $stmtImages->get_result();

    while ($image = $resultImages->fetch_assoc()) {
      $extraImages[] = $image;
    }

    // Retourner les données de l'activité sous forme de JSON
    $response['success'] = true;
    $response['activity'] = [
      'image' => $activity['image'], // Image principale
      'titre' => $activity['titre'],
      
      'description' => $activity['description'],
      'images' => $extraImages // Assure-toi que la clé ici est 'images'
    ];
  }
}

echo json_encode($response);
?>
