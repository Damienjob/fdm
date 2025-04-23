<?php
session_start();

// Vérification de la connexion admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

// Vérification des paramètres
if (!isset($_POST['ids']) || empty($_POST['ids'])) {
    echo json_encode(['success' => false, 'message' => 'IDs manquants']);
    exit();
}

// Connexion à la base de données
$host = 'localhost';
$db = 'fdm';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
    exit();
}

$ids = $_POST['ids'];
$idArray = json_decode($ids, true);

if (!is_array($idArray) || empty($idArray)) {
    echo json_encode(['success' => false, 'message' => 'Format de données invalide']);
    exit();
}

// Préparation de la requête avec des placeholders
$placeholders = implode(',', array_fill(0, count($idArray), '?'));
$sql = "DELETE FROM subscribers WHERE id IN ($placeholders)";
$stmt = $conn->prepare($sql);

// Création du type pour bind_param
$types = str_repeat('i', count($idArray));
// Utilisation de call_user_func_array pour passer un tableau d'arguments à bind_param
$params = array_merge([$types], $idArray);
$tmp = [];
foreach($params as $key => $value) $tmp[$key] = &$params[$key];
call_user_func_array([$stmt, 'bind_param'], $tmp);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'count' => $stmt->affected_rows]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression en masse']);
}

$stmt->close();
$conn->close();
?>