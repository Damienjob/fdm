<?php
$conn = new mysqli("localhost", "root", "", "fdm");
if ($conn->connect_error) die("Erreur : " . $conn->connect_error);

// Paramètres de pagination
$activitesParPage = 3; // Nombre d'activités par page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$debut = ($page - 1) * $activitesParPage;

// Compter le nombre total d'activités publiées
$sqlCount = "SELECT COUNT(*) as total FROM activities WHERE statut = 'publié'";
$resultCount = $conn->query($sqlCount);
$totalActivites = $resultCount->fetch_assoc()['total'];
$totalPages = ceil($totalActivites / $activitesParPage);

// Récupérer les activités pour la page courante (triées par date de publication décroissante)
$sql = "SELECT * FROM activities WHERE statut = 'publié' ORDER BY created_at DESC LIMIT $debut, $activitesParPage";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />
    <meta name="description" content="" />
    <meta name="author" content="TemplateMo" />
    <link
      href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900&display=swap"
      rel="stylesheet"
    />
    <link rel="shortcut icon" href="" type="image/x-icon">
    <title>Accueil | Fondation Divine Miséricorde</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
      rel="stylesheet"
    />

    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css" />
    <link rel="stylesheet" href="assets/css/owl.css" />
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/jpg" href="assets/images/logo.jpg">
    <style>
      /* CSS pour uniformiser les hauteurs des colonnes */
      .equal-height {
        display: flex;
        flex-wrap: wrap;
      }

      .equal-height > .col-md-4 {
        display: flex;
        flex-direction: column;
      }

      .card,.service-item {
        flex: 1; /* Les cartes s'étirent également pour avoir la même hauteur */
      }

      .card img,.service-item img {
        object-fit: cover; /* Les images restent proportionnelles */
        height: 200px; /* Hauteur fixe pour toutes les images */
      }
      .service-item img{
        height: 260px;
      }
      #admin-button{
        background-color: #19CDFE;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: bold;
        color: #000;
        text-decoration: none;
        cursor: pointer;
        margin-left: 30px;
      }
      
      /* Style pour la pagination */
      .pagination {
        display: flex;
        justify-content: center;
        margin: 30px 0;
      }
      
      .pagination a, .pagination span {
        color: #FDB300;
        padding: 8px 16px;
        text-decoration: none;
        transition: background-color 0.3s;
        border: 1px solid #ddd;
        margin: 0 4px;
        border-radius: 4px;
        font-weight:bold;
      }
      
      .pagination a:hover {
        background-color: #FDB300;
        color: white;
      }
      
      .pagination .active {
        background-color: #FDB300;
        color: white;
        border: 1px solid #FDB300;
      }
      
      .pagination .disabled {
        color: #ddd;
        cursor: not-allowed;
      }
    </style>
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
    <div class="sub-header" style="	background-color: #fff;">
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
            <li class="nav-item active"><a class="nav-link" href="index.php" id="home">Accueil</a></li>
            <li class="nav-item "><a class="nav-link" href="about.php" id="about">A propos</a></li>
            <li class="nav-item"><a class="nav-link" href="services.php" id="services">Services</a></li>
            <li class="nav-item"><a class="nav-link" href="contact.php" id="contact">Contact</a></li>
            <a class="btn-getstarted" href="index.php#about" id="donate">Faire un don</a>
            <a id="admin-button" class="btn-getstarted btn d-flex align-items-center  ms-3" href="connexion.php">
  <i class="fa fa-user-shield text-white"></i>
  <span class="fw-medium small" style="margin-left: 10px;">Admin</span>
    </a>
            
          </ul>
          </div>
        </div>
      </nav>
    </header>
  <!-- Page Content -->
    <!-- Banner Starts Here -->
    <div class="main-banner header-text" id="top">
      <div class="Modern-Slider">
        <!-- Item -->
        <div class="item item-1">
          <div class="img-fill">
            
          </div>
        </div>
      </div>
    </div>
    <!-- Banner Ends Here -->
     <!-- Objective Section Start -->
     <div class="objective-section" >
      <div class="container">
        <div class="row align-items-center">
          <!-- Objectives Section -->
          <div class="">
            <div class="section-heading content">
              <h2 id="titre">Notre <em>Objectif</em></h2>
              <p style="margin-top: 20px; font-size: 1.2em; color: #4e4f50; line-height: 1.8;" id="text">La <strong style=\"color: #000;\">Fondation Divine Miséricorde (FDM)</strong> a pour objectif la promotion et la défense des initiatives citoyennes en faveur du bien-être, de la paix, la tolérance, le développement, la solidarité et la prospérité dans un espace durable.</p>              
            </div>
          </div>
        </div>
      </div>
    </div>
    
<!-- Objective Section End -->

   <!-- Services start -->
   <div class="services">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="section-heading heading_center">
              <h2 id="titre2">Nos <em>Domaines</em></h2>
              <span id="text2"
                >Découvrez nos principaux domaines d'intervention, qui reflètent notre engagement envers un impact positif et durable.</span
              >
            </div>
          </div>
          <div class="row equal-height">
            <div class="col-md-4">
              <div class="service-item">
                <img src="assets/images/sante.jpg" alt="" />
                <div class="down-content">
                  <h4 id="titre3">Santé</h4>
                  <p id="text3">
                  Nous intervenons dans la promotion des Droits à la Santé Sexuelle et Reproductive des jeunes, la lutte contre toutes les formes de violence basées sur le genre, la prévention des grossesses en milieu scolaire.
                  </p>
                  <a href="services.php#tabs-1" class="filled-button" id="button1"
                    >Lire la suite</a
                  >
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="service-item">
                <img src="assets/images/edu.jpg" alt="" />
                <div class="down-content">
                  <h4 class="mt-3" id="titre4">Education</h4>
                  <p id="text4">
                  Nous réalisons des campagnes de sensibilisation et de formation tant de notre personnel que de la population sur des thématiques données, afin de les outiller.
                  </p>
                  <a href="services.php#tabs-2" class="mt-3 filled-button"
                  id="button2">Lire la suite</a
                  >
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="service-item">
                <img src="assets/images/91121.jpg" alt="" />
                <div class="down-content">
                  <h4 class="mt-3" id="titre5">Environnement</h4>
                  <p id="text5">
                  Protéger notre planète est une responsabilité partagée. Nous travaillons à la préservation des écosystèmes et à la sensibilisation environnementale.
                  </p>
                  <a href="services.php#tabs-3" class="mt-5 filled-button"
                  id="button3">Lire la suite</a
                  >
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="service-item">
                <img src="assets/images/auto.jpg" alt="" />
                <div class="down-content">
                  <h4 id="titre6">Autonomisation des populations</h4>
                  <p id="text6">
                  Nous œuvrons à rendre indépendants les populations par l'accompagnement des collectivités locales à travers la création d'Activités Génératrices de Revenus.
                  </p>
                  <a href="services.php#tabs-4" class="filled-button"
                  id="button4">Lire la suite</a
                  >
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="service-item">
                <img src="assets/images/dev.jpg" alt="" />
                <div class="down-content">
                  <h4 id="titre7">Développement local</h4>
                  <p id="text7">Nous tenons à créer un environnement participatif qui renforce les capacités des institutions locales pour la mise en place d'interventions socialement inclusives et pourvoyeuses d'emplois.</p>
                  <a href="services.php#tabs-5" class="filled-button"
                  id="button5">Lire la suite</a
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Service end -->
    <!-- Valeur start -->
    <div class="fun-facts">
      <div class="container">
        <div class="row">
          <!-- Section gauche : Introduction des valeurs de l'entreprise -->
          <div class="col-md-6">
            <div class="left-content">
              <span id="titre8">Nos valeurs fondamentales</span>
              <h2 id="titre9">Ce qui guide notre <em>réussite   </em></h2>
              <p id="text8">
              À <strong><em>La Fondation Divine Miséricorde</em></strong>, nos valeurs sont les piliers de notre succès. Elles nous inspirent chaque jour à bâtir des relations solides et à offrir des services d'exception. Nous croyons fermement que la force d'une entreprise repose sur ses principes. Nos valeurs fondamentales représentent l'essence même de notre identité et sont au cœur de toutes nos actions. <br /><br /> Découvrez ce qui rend notre entreprise unique et pourquoi nous sommes votre partenaire idéal.
              </p>
            </div>
          </div>

          <!-- Section droite : Liste des valeurs -->
          <div class="col-md-6 align-self-center">
            <div class="row">
              <div class="col-md-6">
                <div class="count-area-content">
                  <div class="count"><i class="fa-solid fa-gavel"></i></div>
                  <div class="count-title" id="titre10">Discipline</div>
                  <p id="text9">
                  Impulser au sein de notre équipe la détermination, l'amour du travail et la fraternité pour l'aider à remplir notre mission.
                  </p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="count-area-content">
                  <div class="count"><i class="fa-solid fa-handshake"></i></div>
                  <div class="count-title" id="titre11">Respect</div>
                  <p id="text10">Respecter nos engagements, un impératif.</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="count-area-content">
                  <div class="count">
                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                  </div>
                  <div class="count-title" id="titre12">Proximité</div>
                  <p id="text11">Etablir et maintenir une proximité avec les communautés de base et les populations.</p>

                </div>
              </div>
              <div class="col-md-6">
                <div class="count-area-content">
                  <div class="count">
                    <i class="fa fa-heart" aria-hidden="true"></i>
                  </div>
                  <div class="count-title" id="titre13">Solidarité</div>
                  <p id="text12">Solidarité au sein de la fondation, solidarité avec les populations locales.</p>

                </div>
              </div>
              <div class="col-md-6">
                <div class="count-area-content">
                  <div class="count">
                    <i class="fa fa-share-alt" aria-hidden="true"></i>
                  </div>
                  <div class="count-title" id="titre14">Partage</div>
                  <p id="text13">Partager nos expériences, nos victoires, nos erreurs.</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="count-area-content">
                  <div class="count">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                  </div>
                  <div class="count-title" id="titre15">Transparence</div>
                  <p id="text14">Partager l'information avec les parties prenantes, de la manière la plus ouverte, honnête, précise et compréhensible  possible, qu'il s'agisse d'informations positives ou négatives.</p>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Valeur end -->
    <!-- activité start -->
    <div class="album py-5 bg-light">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="section-heading heading_center">
              <h2 id="titre16">Nos <em>Activités</em></h2>
              <span id="text15"
                >Ces activités sont le cœur de notre action, visant à améliorer la qualité de vie et à promouvoir un développement durable pour tous.</span
              >
            </div>
          </div>
        </div>

        <!-- Cartes des activités avec pagination -->
        <div class="row equal-height" id="liste-activites">
        <?php if($result->num_rows > 0): ?>
          <?php while($row = $result->fetch_assoc()): ?>
          <div class="col-md-4">
            <div class="card mb-4 box-shadow">
              <img
                class="card-img-top"
                src="<?= 'uploads/' . htmlspecialchars($row['image']) ?>"
                alt="<?= htmlspecialchars($row['titre']) ?>"
              />
              <div class="card-body">
                <p class="text-black">
                  <strong id="titre17">
                  <?= htmlspecialchars($row['titre']) ?></strong>
                </p>
                <div class="d-flex justify-content-center align-items-center">
                  <a href="activite.php?slug=<?= urlencode($row['slug']) ?>" class="filled-button">Lire la suite</a>
                </div>
              </div>
            </div>
          </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="col-12 text-center">
            <p>Aucune activité n'est disponible pour le moment.</p>
          </div>
        <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if($totalPages > 1): ?>
        <div class="pagination">
          <?php if($page > 1): ?>
            <a href="index.php?page=<?= $page-1 ?>">&laquo; Précédent</a>
          <?php else: ?>
            <span class="disabled">&laquo; Précédent</span>
          <?php endif; ?>
          
          <?php for($i = 1; $i <= $totalPages; $i++): ?>
            <?php if($i == $page): ?>
              <span class="active"><?= $i ?></span>
            <?php else: ?>
              <a href="index.php?page=<?= $i ?>"><?= $i ?></a>
            <?php endif; ?>
          <?php endfor; ?>
          
          <?php if($page < $totalPages): ?>
            <a href="index.php?page=<?= $page+1 ?>">Suivant &raquo;</a>
          <?php else: ?>
            <span class="disabled">Suivant &raquo;</span>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <!-- activité end -->
    

     <!-- Footer Starts Here -->
     <footer class="footer">
      <div class="container">
        <div class="row">
          <div class="col-md-3 footer-item ">
            <h4 id="nom">Fondation Divine Miséricorde</h4>
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
            <h4 id="lien">Liens</h4>
            <ul class="menu-list content">
              <li><a href="index.php" id="home1">Accueil</a></li>
              <li><a href="about.php" id="about1">A propos</a></li>
              <li><a href="services.php" id="services1">Services</a></li>
              <li><a href="contact.php" id="contact1">Contact</a></li>
            </ul>
          </div>
          <div class="col-md-3 footer-item footer-item2 content">
            <h4 id="domaine">Nos domaines</h4>
            <ul class="menu-list content">
              <li><a href="services.php#tabs-1" id="serv1">Santé</a></li>
              <li><a href="services.php#tabs-2" id="serv2">Education</a></li>
              <li><a href="services.php#tabs-3" id="serv3">Environnement</a></li>
              <li><a href="services.php#tabs-4" id="serv4">Autonomisation des populations</a></li>
              <li><a href="services.php#tabs-5" id="serv5">Développement local</a></li>
            </ul>
          </div>
          <div class="col-md-3 footer-item last-item footer-newsletter">
            <div class="content">
            <h4 id="lettre">Notre Newsletter</h4>
            <p id="lettretext">Abonnez-vous à notre newsletter et recevez les dernières nouvelles !</p>
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
            <strong id="texte">Fondation Divine Miséricorde</strong>
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
<script>
  // Récupération des éléments à manipuler
document.addEventListener('DOMContentLoaded', function() {
  // Initialiser la pagination AJAX
  initAjaxPagination();
});

function initAjaxPagination() {
  // Attacher des écouteurs d'événements à tous les liens de pagination
  document.querySelectorAll('.pagination a').forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Récupérer le numéro de page depuis l'URL du lien
      const url = new URL(this.href);
      const page = url.searchParams.get('page');
      
      // Charger les activités de cette page via AJAX
      loadActivitiesAjax(page);
      
      // Mettre à jour l'URL sans recharger la page 
      window.history.pushState({page: page}, '', url.toString());
    });
  });
  
  // Gestion du bouton retour du navigateur
  window.addEventListener('popstate', function(e) {
    if (e.state && e.state.page) {
      loadActivitiesAjax(e.state.page);
    } else {
      loadActivitiesAjax(1); // Page par défaut
    }
  });
}

function loadActivitiesAjax(page) {
  const xhr = new XMLHttpRequest();
  xhr.open('GET', 'ajax-activities.php?page=' + page, true);
  
  xhr.onload = function() {
    if (this.status === 200) {
      try {
        const response = JSON.parse(this.responseText);
        
        // Mettre à jour la liste des activités
        document.getElementById('liste-activites').innerHTML = response.activitiesHtml;
        
        // Mettre à jour la pagination
        document.querySelector('.pagination').innerHTML = response.paginationHtml;
        
        // Réinitialiser les écouteurs d'événements sur les nouveaux liens
        initAjaxPagination();
        
        // Faire défiler jusqu'au début de la section des activités
        document.getElementById('liste-activites').scrollIntoView({behavior: 'smooth'});
      } catch (e) {
        console.error('Erreur de parsing JSON:', e);
      }
    }
  };
  
  xhr.onerror = function() {
    console.error('Erreur de connexion AJAX');
  };
  
  xhr.send();
}
</script>

  </body>
</html>