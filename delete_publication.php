<?php
session_start();
header('Content-Type: application/json'); // Assure une réponse JSON cohérente

// Configuration de la base de données (à remplacer par vos valeurs)
define('DB_HOST', 'localhost');
define('DB_NAME', 'fdm');
define('DB_USER', 'root');
define('DB_PASS', '');

// Connexion PDO avec gestion d'erreurs
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de connexion à la base de données'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false];

    // Validation de l'ID
    if (!isset($_POST['id']) || !filter_var($_POST['id'], FILTER_VALIDATE_INT)) {
        $response['message'] = 'ID de publication invalide';
        echo json_encode($response);
        exit;
    }

    $pubId = (int)$_POST['id'];
    
    try {
        // Récupération du chemin de la vidéo
        $stmt = $pdo->prepare("SELECT video_path FROM publications WHERE id = ?");
        $stmt->execute([$pubId]);
        $publication = $stmt->fetch();
        
        // Suppression physique du fichier si existant
        if ($publication && !empty($publication['video_path'])) {
            // Chemin absolu du dossier vidéo
            $videoDir = realpath(__DIR__ . '/uploads/videos');
            
            // Construction du chemin complet
            $filePath = realpath(__DIR__ . basename($publication['video_path']));
            
            // Vérification sécurité : doit être dans le dossier vidéo
            if ($filePath && strpos($filePath, $videoDir) === 0 && is_file($filePath)) {
                unlink($filePath);
            }            
        };

        // Suppression de la base de données
        $stmt = $pdo->prepare("DELETE FROM publications WHERE id = ?");
        $stmt->execute([$pubId]);
        
        if ($stmt->rowCount() > 0) {
            $response['success'] = true;
            $response['message'] = 'Publication supprimée avec succès';
        } else {
            $response['message'] = 'Publication introuvable';
        }
        
    } catch (PDOException $e) {
        $response['message'] = 'Erreur base de données: ' . $e->getMessage();
    }
    
    echo json_encode($response);
    exit;
}