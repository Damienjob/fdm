<?php
session_start();

// Connexion à la base de données
$host = 'localhost'; // Nom de l'hôte
$dbname = 'fdm'; // Nom de la base de données
$username = 'root'; // Utilisateur de la base de données
$password = ''; // Mot de passe

// Connexion avec PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer les données du formulaire
    $email = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validation des champs
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Tous les champs doivent être remplis.";
        header('Location: index.php');
        exit();

    }

    // Préparer la requête pour chercher l'utilisateur dans la base de données
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE username = :username");
    $stmt->bindParam(':username', $email, PDO::PARAM_STR);
    $stmt->execute();

    // Vérifier si l'utilisateur existe
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        // Vérifier si le mot de passe est correct (avec hashage)
        if (password_verify($password, $user['mot_de_passe'])) {
            // Si les identifiants sont valides, on démarre une session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_username'] = $user['username'];

            // Rediriger vers la page sécurisée
            header('Location: dashboard.php');
            exit();
        } else {
            $_SESSION['error'] = "Le mot de passe est incorrect.";
            alert($_SESSION['error']);
            exit();
        }
    } else {
        $_SESSION['error'] = "Aucun utilisateur trouvé avec cet email.";
        alert($_SESSION['error']);
        exit();
    }
}
?>
