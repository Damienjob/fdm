<?php
$conn = new mysqli("localhost", "root", "", "fdm");
if ($conn->connect_error) die("Erreur : " . $conn->connect_error);

// Récupérer l'ID de l'activité depuis l'URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

// Vérifier si l'ID est valide
if (empty($slug)) {
    header("Location: index.php");
    exit;
}

// Récupérer les détails de l'activité
$sql = "SELECT * FROM activities WHERE slug = ? AND statut = 'publié'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

// Vérifier si l'activité existe
if ($result->num_rows === 0) {
    header("Location: index.php");
    exit;
}

$activite = $result->fetch_assoc();


$activite_id = $activite['id']; // Récupérer l'ID explicitement

// Récupérer les images supplémentaires liées à cette activité en utilisant l'ID
$sql_images = "SELECT * FROM images_activites WHERE activite_id = ?";
$stmt_images = $conn->prepare($sql_images);
$stmt_images->bind_param("i", $activite_id);
$stmt_images->execute();
$result_images = $stmt_images->get_result();

// Stocker les chemins des images dans un tableau
$images = [];
while ($row = $result_images->fetch_assoc()) {
    $images[] = $row['chemin'];
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="TemplateMo" />
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <title><?= htmlspecialchars($activite['titre']) ?> | Fondation Divine Miséricorde</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css" />
    <link rel="stylesheet" href="assets/css/owl.css" />
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/jpg" href="assets/images/logo.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

</head>

<body>
    <!-- ***** Preloader Start ***** -->
    <div id="preloader">
        <div class="jumper">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <!-- ***** Preloader End ***** -->

    <!-- Header -->
    <div class="sub-header" style="background-color: #fff;">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <ul class="right-icons" id="language-buttons">
                        <li class="gtranslate_wrapper"></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script>window.gtranslateSettings = {"default_language":"fr","detect_browser_language":true,"languages":["fr","en"],"wrapper_selector":".gtranslate_wrapper","flag_size":24,"horizontal_position":"right","vertical_position":"top","flag_style":"3d"}</script>
    <script src="https://cdn.gtranslate.net/widgets/latest/fn.js" defer></script>
    <header class="">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="index.php">            
                    <img src="assets/images/logo.jpg" alt="Logo Fondation Divine Miséricorde" />
                </a>
                <button
                    class="navbar-toggler"
                    type="button"
                    data-toggle="collapse"
                    data-target="#navbarResponsive"
                    aria-controls="navbarResponsive"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto ">
                        <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                        <li class="nav-item"><a class="nav-link" href="about.php">A propos</a></li>
                        <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                        <a class="btn-getstarted" href="index.php#about">Faire un don</a>
                        
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Page Content -->
    <div class="page-heading3 header-text">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1><?= htmlspecialchars($activite['titre']) ?></h1>
                </div>
            </div>
        </div>
    </div>

    <div class="sect">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section content" >
                        <p><?= $activite['description'] ?></p>
                        <center class="content">
                            <h1 style="color: #FDB300;" id="responsables-title">Quelques images de l'activité</h1>
                        </center><br><br>
                        <div class="container">
                            <div class="row text-center">
                            <?php foreach ($images as $image): ?>
        <div class="col-md-4 mb-4">
            <img class="img-fluid fixed-size" src="fdm-main/<?= htmlspecialchars($image) ?>" alt="Image de l'activité">
        </div>
        <?php endforeach; ?>
        <?php if (empty($activite['image']) && count($images) === 0): ?>
        <div class="col-12">
            <p>Aucune image disponible pour cette activité.</p>
        </div>
        <?php endif; ?>
        
                                
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Starts Here -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-3 footer-item ">
                    <h4>Fondation Divine Miséricorde</h4>
                    <ul class="social-icons">
                        <li>
                            <a rel="nofollow" href="https://www.facebook.com/share/p/14dDtSat98/?mibextid=wwXIfr" target="_blank"><i class="fa fa-facebook"></i></a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-twitter"></i></a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-linkedin"></i></a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-instagram"></i></a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-3 footer-item content">
                    <h4>Liens</h4>
                    <ul class="menu-list content">
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="about.php">A propos</a></li>
                        <li><a href="services.php">Services</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-3 footer-item footer-item2 content">
                    <h4>Nos domaines</h4>
                    <ul class="menu-list content">
                        <li><a href="services.php#tabs-1">Santé</a></li>
                        <li><a href="services.php#tabs-2">Education</a></li>
                        <li><a href="services.php#tabs-3">Environnement</a></li>
                        <li><a href="services.php#tabs-4">Autonomisation des populations</a></li>
                        <li><a href="services.php#tabs-5">Développement local</a></li>
                    </ul>
                </div>
                <div class="col-md-3 footer-item last-item footer-newsletter">
                    <div class="content">
                        <h4>Notre Newsletter</h4>
                        <p>Abonnez-vous à notre newsletter et recevez les dernières nouvelles !</p>
                    </div>
                    <form id="newsletter-form" class="php-email-form content">
                        <div class="newsletter-form"><input type="email" name="email" required><input type="submit" id="subscribe-button" value="S'abonner"></div>
                    </form>
                </div>
            </div>
        </div>
    </footer>

    <div class="sub-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <p>
                        Copyright &copy;
                        <script>
                            document.write(new Date().getFullYear());
                        </script>
                        <strong>Fondation Divine Miséricorde</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Additional Scripts -->
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/owl.js"></script>
    <script src="assets/js/slick.js"></script>
    <script src="assets/js/accordions.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script language="text/Javascript">
        cleared[0] = cleared[1] = cleared[2] = 0; //set a cleared flag for each field
        function clearField(t) {                   //declaring the array outside of the
            if (!cleared[t.id]) {                      // function makes it static and global
                cleared[t.id] = 1;  // you could use true and false, but that's more typing
                t.value = '';         // with more chance of typos
                t.style.color = '#fff';
            }
        }
    </script>
    <script>
    document.getElementById('newsletter-form').addEventListener('submit', function (event) {
        event.preventDefault(); // Empêche le formulaire de se soumettre normalement

        var email = document.querySelector('input[name="email"]').value;

        // Vérifier si l'email est valide
        if (validateEmail(email)) {
            // Envoi AJAX du formulaire
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'newsletter.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    
                    // Afficher SweetAlert2 en fonction de la réponse
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Merci pour votre abonnement!',
                            text: 'Nous vous enverrons nos dernières nouvelles à ' + email + '.'
                        }).then(() => {
                            // Réinitialiser le formulaire après succès
                            document.getElementById('newsletter-form').reset();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: response.message
                        });
                    }
                }
            };

            xhr.send('email=' + encodeURIComponent(email));
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Email invalide',
                text: 'Veuillez entrer un email valide.'
            });
        }
    });

    // Fonction de validation de l'email
    function validateEmail(email) {
        var regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        return regex.test(email);
    }
    </script>
</body>
</html>