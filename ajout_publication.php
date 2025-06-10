<?php
// Désactiver la limite de temps
set_time_limit(0);

// Afficher toutes les erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Erreur : Cette page ne peut être accédée que via un formulaire POST");
}

// Vérifier si les champs existent
if (empty($_POST['titre']) || empty($_FILES['video']['name'])) {
    die("Erreur : Tous les champs sont requis");
}

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fdm";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Traitement du titre
$titre = htmlspecialchars(trim($_POST['titre']));

// Traitement du fichier vidéo
$video = $_FILES['video'];
$dossierUpload = "uploads/videos/";

// Créer le dossier s'il n'existe pas
if (!file_exists($dossierUpload)) {
    if (!mkdir($dossierUpload, 0777, true)) {
        die("Erreur : Impossible de créer le dossier de destination");
    }
}

// Générer un nom de fichier unique
$nomFichier = uniqid() . '_' . basename($video["name"]);
$cheminFichier = $dossierUpload . $nomFichier;
$extensionFichier = strtolower(pathinfo($cheminFichier, PATHINFO_EXTENSION));

// Vérifications du fichier
$erreurs = [];
$typesAutorises = ["mp4", "mov", "avi"];

// Vérification de l'extension
if (!in_array($extensionFichier, $typesAutorises)) {
    $erreurs[] = "Seuls les formats MP4, MOV, AVI sont autorisés. Votre fichier: $extensionFichier";
}

// Vérification de la taille (max 100MB)
$maxSize = 100 * 1024 * 1024; // 100MB en octets
if ($video["size"] > $maxSize) {
    $erreurs[] = "Fichier trop volumineux (> 100MB)";
}

// Vérification des erreurs d'upload
if ($video["error"] !== UPLOAD_ERR_OK) {
    $erreurs[] = "Erreur lors du téléchargement : " . $video["error"];
}

// Traitement final
if (empty($erreurs)) {
    if (move_uploaded_file($video["tmp_name"], $cheminFichier)) {
        $stmt = $conn->prepare("INSERT INTO publications (titre, video_path) VALUES (?, ?)");
        $stmt->bind_param("ss", $titre, $cheminFichier);
        
        if ($stmt->execute()) {
            // SUCCÈS - Générer une page avec SweetAlert et redirection
            echo '<!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Publication réussie</title>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            </head>
            <body>
                <script>
                    Swal.fire({
                        title: "Succès!",
                        text: "Publication créée avec succès!",
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        window.location.href = "dashboard.php";
                    });
                </script>
            </body>
            </html>';
        } else {
            echo "Erreur base de données : " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Erreur : Impossible d'enregistrer le fichier sur le serveur";
    }
} else {
    // Afficher les erreurs avec une alerte SweetAlert
    $erreurMessages = implode("\\n", $erreurs);
    echo '<!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Erreur</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <script>
            Swal.fire({
                title: "Erreur!",
                html: "' . addslashes($erreurMessages) . '",
                icon: "error",
                confirmButtonText: "OK"
            }).then(() => {
                window.history.back();
            });
        </script>
    </body>
    </html>';
}

$conn->close();
?>