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


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    // Modif ici : comparaison directe des mots de passe en clair
    if ($admin && $password === $admin['password']) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin['username'];

        echo "<script>
                setTimeout(() => {
                  Swal.fire({
                      icon: 'success',
                      title: 'Connexion réussie',
                      text: 'Redirection en cours...',
                      timer: 2000,
                      showConfirmButton: false
                  }).then(() => {
                      window.location.href = 'dashboard.php';
                  });
                }, 100);
              </script>";
    } else {
        echo "<script>
                setTimeout(() => {
                  Swal.fire({
                      icon: 'error',
                      title: 'Échec de la connexion',
                      text: 'Identifiants incorrects.'
                  });
                }, 100);
              </script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link
      href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap"
      rel="stylesheet"
    />
    <link rel="shortcut icon" href="" type="image/x-icon">
    <title>S'authentifier</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
      rel="stylesheet"
    />

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css" />
    <link rel="stylesheet" href="assets/css/owl.css" />
    <link rel="icon" type="image/jpg" href="assets/images/logo.jpg">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
      /* Base styles */
      body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f8f9fa;
        overflow: hidden;
      }
      .min-vh-100 {
        min-height: 100vh;
      }
      .navbar {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }
      .card {
        border-radius: 10px;
      }
      
      /* Responsive Styles */
      @media (max-width: 768px) {
        .card {
          margin-top: 20px;
          max-width: 90%;
        }

        .card-header h2 {
          font-size: 1.25rem;
        }

        .card-header p {
          font-size: 0.875rem;
        }

        .navbar-brand {
          font-size: 1rem;
        }

        .btn {
          font-size: 1rem;
        }

        .input-group-text {
          font-size: 1rem;
        }
      }

      @media (max-width: 576px) {
        .card {
          max-width: 100%;
        }

        .card-header h2 {
          font-size: 1rem;
        }

        .card-header p {
          font-size: 0.75rem;
        }

        .input-group-text {
          font-size: 0.875rem;
        }

        .navbar-brand {
          font-size: 0.875rem;
        }

        .btn {
          font-size: 0.875rem;
        }
      }
    </style>
</head>
<body>
<div class="min-vh-100 bg-light d-flex flex-column">
  <!-- Header/Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container py-2 d-flex justify-content-between align-items-center">
      <a href="index.php" data-readdy="true" class="navbar-brand fw-bold d-flex align-items-center">
        <span class="fw-bold" style="font-weight:bold;">Fondation Divine Miséricorde</span>
      </a>
      
      
    </div>
  </nav>

  <!-- Main Content -->
  <div class="flex-grow-1 d-flex align-items-center justify-content-center py-5 px-3" style="background-image: url('assets/images/image4.jpg'); background-size: cover; background-position: center;">
    <div class="card shadow-lg rounded-4 overflow-hidden w-100" style="max-width: 500px;">
      <div class="card-header text-black text-center" style="background-color: #19CDFE">
        <i class="fas fa-lock fa-2x mb-3"></i>
        <h2 class="h5 fw-bold mb-0">Connexion Administrateur</h2>
        <p class="text-black small mb-0">Accédez à votre espace sécurisé</p>
      </div>
      <div class="card-body p-4">

        <!-- Alert Message -->
        <div id="alert" class="alert alert-danger d-none" role="alert">
          <i class="fas fa-exclamation-circle me-2"></i>
          <span id="alertMessage">Message d'erreur</span>
        </div>
        <!-- Login Form -->
        <form method="POST" action="">
          <div class="mb-3">
            <label for="username" class="form-label">Identifiant</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa fa-user"></i></span>
              <input type="text" id="username" name="username" required class="form-control" placeholder="Votre identifiant">
            </div>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa fa-lock"></i></span>
              <input type="password" id="password" name="password" required class="form-control" placeholder="Votre mot de passe">
              <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                <i class="fa fa-eye" id="toggleIcon"></i>
              </button>
            </div>
          </div>
          <button type="submit" class="btn w-100" style="background-color: #19CDFE">
            <i class="fas fa-sign-in-alt me-2"></i> Se connecter
          </button>
        </form>

        <div class="text-center mt-4">
          <a href="index.php" class="text-decoration-none" style="color: #000000;text-decoration:none;">
            <i class="fas fa-arrow-left me-2"></i> Retour au site
          </a>
        </div>
      </div>

      <div class="card-footer bg-light border-top text-center text-muted small py-3">
        <i class="fas fa-shield-alt me-2"></i>Connexion sécurisée
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-white py-4 shadow-sm mt-auto">
    <div class="container d-flex justify-content-center text-center">
      <div>
        © <script>document.write(new Date().getFullYear());</script>
        <strong id="texte">Fondation Divine Miséricorde</strong> | Tous droits réservés.
      </div>
    </div>
  </footer>
</div>

<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
  function togglePassword() {
    var passwordInput = document.getElementById("password");
    var toggleIcon = document.getElementById("toggleIcon");

    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      toggleIcon.classList.remove("fa-eye");
      toggleIcon.classList.add("fa-eye-slash");
    } else {
      passwordInput.type = "password";
      toggleIcon.classList.remove("fa-eye-slash");
      toggleIcon.classList.add("fa-eye");
    }
  }
</script>

</body>
</html>
