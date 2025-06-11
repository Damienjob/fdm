<?php

session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: connexion.php");
    exit();
}
// Connexion à la base de données
$host = 'localhost';
$db = 'fdm';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die('Erreur de connexion : ' . $conn->connect_error);
}

// Déterminer quelle section est active
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'activities';

// Configuration de la pagination pour les activités
$activitiesPerPage = 5; // Nombre d'activités par page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $activitiesPerPage;

// Compter le nombre total d'activités
$countSql = "SELECT COUNT(*) as total FROM activities";
$countResult = $conn->query($countSql);
$totalActivities = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalActivities / $activitiesPerPage);

// Récupération des activités avec pagination
$sql = "SELECT * FROM activities ORDER BY created_at DESC LIMIT $offset, $activitiesPerPage";
$result = $conn->query($sql);
$activities = [];
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $activities[] = $row;
  }
}

// Configuration de la pagination pour les emails
$emailsPerPage = 10; // Nombre d'emails par page
$emailPage = isset($_GET['email_page']) ? intval($_GET['email_page']) : 1;
$emailOffset = ($emailPage - 1) * $emailsPerPage;

// Compter le nombre total d'emails
$countEmailsSql = "SELECT COUNT(*) as total FROM subscribers";
$countEmailsResult = $conn->query($countEmailsSql);
$totalEmails = $countEmailsResult->fetch_assoc()['total'];
$totalEmailPages = ceil($totalEmails / $emailsPerPage);

// Récupération des emails avec pagination
$emailsSql = "SELECT * FROM subscribers ORDER BY date_subscribed DESC LIMIT $emailOffset, $emailsPerPage";
$emailsResult = $conn->query($emailsSql);
$emails = [];
if ($emailsResult && $emailsResult->num_rows > 0) {
  while ($row = $emailsResult->fetch_assoc()) {
    $emails[] = $row;
  }
}
// Configuration de la pagination pour les publications
$pubsPerPage = 5; // Nombre de publications par page
$pubPage = isset($_GET['pub_page']) ? intval($_GET['pub_page']) : 1;
$pubOffset = ($pubPage - 1) * $pubsPerPage;

// Compter le nombre total de publications
$countPubSql = "SELECT COUNT(*) as total FROM publications";
$countPubResult = $conn->query($countPubSql);
$totalPublications = $countPubResult->fetch_assoc()['total'];
$totalPubPages = ceil($totalPublications / $pubsPerPage);

// Récupération des publications avec pagination
$pubSql = "SELECT * FROM publications ORDER BY date_creation DESC LIMIT $pubOffset, $pubsPerPage";
$pubResult = $conn->query($pubSql);
$publications = [];
if ($pubResult && $pubResult->num_rows > 0) {
  while ($row = $pubResult->fetch_assoc()) {
    $publications[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Administration - Fondation Divine Miséricorde</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: { primary: "#19CDFE", secondary: "" },
            borderRadius: {
              none: "0px",
              sm: "4px",
              DEFAULT: "8px",
              md: "12px",
              lg: "16px",
              xl: "20px",
              "2xl": "24px",
              "3xl": "32px",
              full: "9999px",
              button: "8px",
            },
          },
        },
      };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css"
      rel="stylesheet"
    />
    
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/jpg" href="assets/images/logo.jpg">
    <style>
        
        /* Modal Styles */
        .modalpubli {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease;
        }
        
        .modalpubli.show {
            display: flex !important; /* Force l'affichage même si autre CSS interfère */
            align-items: center;
            justify-content: center;
        }
        
        .modal-content-publi {
            background: white;
            border-radius: 20px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            animation: slideUp 0.3s ease;
            position: relative; /* Assure que le modal reste au-dessus */
            z-index: 1001; /* Z-index plus élevé */
        }
        
        .modal-header-publi {
            background: linear-gradient(135deg, #19cdfe 0%, #0ea5e9 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .modal-header-publi h2 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .close-btn-publi {
            position: absolute;
            top: 15px;
            right: 20px;
            background: none;
            border: none;
            color: white;
            font-size: 30px;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s ease;
        }
        
        .close-btn-publi:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .modal-body-publi {
            padding: 40px;
        }
        
        .form-group-publi {
            margin-bottom: 25px;
        }
        
        .form-group-publi label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group-publi input[type="text"],
        .form-group-publi select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-group-publi input[type="text"]:focus,
        .form-group-publi select:focus {
            outline: none;
            border-color: #19cdfe;
        }
        
        .file-input-container-publi {
            position: relative;
            overflow: hidden;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .file-input-container-publi:hover {
            border-color: #19cdfe;
            background: #f0fbff;
        }
        
        .file-input-container-publi input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        
        .file-icon-publi {
            font-size: 3rem;
            color: #6c757d;
            margin-bottom: 15px;
        }
        
        .file-text-publi {
            color: #6c757d;
            font-size: 16px;
        }
        
        .file-selected-publi {
            color: #19cdfe;
            font-weight: 600;
            margin-top: 10px;
        }
        
        .submit-btn-publi {
            width: 100%;
            background: linear-gradient(135deg, #19cdfe 0%, #0ea5e9 100%);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        
        .submit-btn-publi:hover {
            transform: translateY(-2px);
        }
        
        .submit-btn-publi:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .progress-bar-publi {
            width: 100%;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            margin-top: 20px;
            overflow: hidden;
            display: none;
        }
        
        .progress-fill-publi {
            height: 100%;
            background: linear-gradient(135deg, #19cdfe 0%, #0ea5e9 100%);
            width: 0%;
            transition: width 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from { 
                opacity: 0;
                transform: translateY(50px) scale(0.9);
            }
            to { 
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .modal-content-publi {
                width: 95%;
                margin: 20px;
            }
            
            .modal-body-publi {
                padding: 20px;
            }
        }

        /* Corrections pour éviter les conflits */
        .modalpubli * {
            box-sizing: border-box;
        }
        
        /* Override pour forcer l'affichage */
        .modalpubli.show {
            visibility: visible !important;
            opacity: 1 !important;
        }
    </style>
  </head>
  <body>
    <!-- Header -->
    <header
      class="bg-primary text-white shadow-md fixed top-0 left-0 w-full z-10"
     
    >
      <div
        class="container mx-auto px-4 py-3 flex items-center justify-between"
      >
        <div class="flex items-center">
          <span class="hidden md:inline-block font-bold text-black"
            >Fondation Divine Miséricorde</span
          >
        </div>
        <h1 class="text-xl font-bold text-black">Administration</h1>
        <button
  onclick="confirmLogout()"
  class="flex items-center bg-white text-black px-3 py-2 rounded-button text-sm font-medium hover:bg-gray-100 transition whitespace-nowrap"
>
  <div class="w-5 h-5 flex items-center justify-center mr-1">
    <i class="ri-logout-box-line"></i>
  </div>
  Déconnexion
</button>

      </div>
    </header>
    <!-- Main Content -->
    <main class="container mx-auto px-4 pt-24 pb-10">
    <!-- Navigation Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="flex -mb-px">
            <a href="?tab=activities" class="<?php echo $activeTab == 'activities' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm mr-8 flex items-center">
                <div class="w-5 h-5 flex items-center justify-center mr-2">
                    <i class="ri-calendar-event-line"></i>
                </div>
                Activités
            </a>
            <a href="?tab=emails" class="<?php echo $activeTab == 'emails' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm flex items-center">
                <div class="w-5 h-5 flex items-center justify-center mr-2">
                    <i class="ri-mail-line"></i>
                </div>
                Emails
            </a>
            <a href="?tab=publications" class="<?php echo $activeTab == 'publications' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm flex items-center">
                <div class="w-5 h-5 flex items-center justify-center mr-2">
                    <i class="ri-movie-line"></i>
                </div>
                Publications
            </a>
        </nav>
    </div>
    
    <?php if ($activeTab == 'activities'): ?>
        <!-- Activities Tab Content -->
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Gestion des Activités</h2>
        <button
          id="addActivityBtn"
          class="bg-primary text-black px-4 py-2 rounded-button font-medium hover:bg-opacity-90 transition whitespace-nowrap !rounded-button flex items-center"
        >
          <div class="w-5 h-5 flex items-center justify-center mr-1">
            <i class="ri-add-line"></i>
          </div>
          Ajouter une activité
        </button>
        
      </div>
      
      <!-- Activities Table -->
      <div class="bg-white rounded shadow-sm overflow-hidden">
        <div class="table-responsive">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th
                  class="text-left py-3 px-4 font-semibold text-sm text-gray-600"
                >
                  Image
                </th>
                <th
                  class="text-left py-3 px-4 font-semibold text-sm text-gray-600"
                >
                  Titre
                </th>
                <th
                  class="text-left py-3 px-4 font-semibold text-sm text-gray-600"
                >
                  Date d'ajout
                </th>
                <th
                  class="text-left py-3 px-4 font-semibold text-sm text-gray-600"
                >
                  Statut
                </th>
                <th
                  class="text-right py-3 px-4 font-semibold text-sm text-gray-600"
                >
                  Actions
                </th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($activities as $row): ?>
              <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="py-3 px-4">
                  <div class="w-16 h-12 rounded overflow-hidden">
                    
                    <?php if (!empty($row['image'])): ?>
              <img src="<?= 'uploads/' . htmlspecialchars($row['image']) ?>" class="w-full h-full object-cover object-top" alt="Image principale">
            <?php endif; ?>
                  </div>
                </td>
                <td class="py-3 px-4 font-medium">
                <?= htmlspecialchars($row['titre']) ?>
                </td>
                <td class="py-3 px-4 text-gray-600"><?= $row['created_at'] ?></td>
                <td class="py-3 px-4">
                  <span
                    class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium"
                    ><?= $row['statut'] === 'publié' ? 'Publié' : 'Brouillon' ?></span
                  >
                </td>
                <td class="py-3 px-4 text-right">
                  <div class="flex justify-end gap-2">
                    <button
                      class="view-btn w-8 h-8 bg-blue-50 rounded-full flex items-center justify-center text-blue-600 hover:bg-blue-100"
                      data-id="<?= $row['id'] ?>"
                    >
                      <i class="ri-eye-line"></i>
                    </button>
                    
                    <button
                      class="delete-btn w-8 h-8 bg-red-50 rounded-full flex items-center justify-center text-red-600 hover:bg-red-100"
                      data-id="<?= $row['id'] ?>"
                    >
                      <i class="ri-delete-bin-line"></i>
                    </button>
                  </div>
                </td>
              </tr>
              
              <?php endforeach; ?>

            </tbody>
          </table>
        </div>
        <!-- Pagination for Activities -->
        <?php if ($totalPages > 1): ?>
        <div class="px-4 py-5 border-t border-gray-200 flex justify-between items-center">
          <div class="text-sm text-gray-600">
            Affichage de <?= min($activitiesPerPage * ($page - 1) + 1, $totalActivities) ?> à <?= min($activitiesPerPage * $page, $totalActivities) ?> sur <?= $totalActivities ?> activités
          </div>
          <div class="flex space-x-1">
            <?php if ($page > 1): ?>
              <a href="?tab=activities&page=<?= $page-1 ?>" class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 hover:bg-gray-100">
                <i class="ri-arrow-left-s-line"></i>
              </a>
            <?php endif; ?>
            
            <?php
            // Afficher les liens de pagination
            $startPage = max(1, min($page - 2, $totalPages - 4));
            $endPage = min($totalPages, max($page + 2, 5));
            
            // Toujours afficher au moins 5 pages si possible
            if ($endPage - $startPage < 4) {
              $startPage = max(1, $endPage - 4);
            }
            
            for ($i = $startPage; $i <= $endPage; $i++): 
            ?>
              <a href="?tab=activities&page=<?= $i ?>" class="w-8 h-8 flex items-center justify-center rounded-full <?= $i == $page ? 'bg-primary text-white' : 'border border-gray-300 hover:bg-gray-100' ?>">
                <?= $i ?>
              </a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
              <a href="?tab=activities&page=<?= $page+1 ?>" class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 hover:bg-gray-100">
                <i class="ri-arrow-right-s-line"></i>
              </a>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
    
    <?php elseif ($activeTab == 'emails'): ?>
        <!-- Emails Tab Content -->
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Liste des Abonnés</h2>
        <div class="flex gap-2">
          <button
            id="exportEmailsBtn"
            class="bg-green-600 text-white px-4 py-2 rounded-button font-medium hover:bg-green-700 transition whitespace-nowrap flex items-center"
          >
            <div class="w-5 h-5 flex items-center justify-center mr-1">
              <i class="ri-file-download-line"></i>
            </div>
            Exporter (CSV)
          </button>
        </div>
      </div>  
        
      </div>
      
      <!-- Emails Table -->
      <div class="bg-white rounded shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
          <h3 class="font-medium text-gray-700">Liste des emails</h3>
          
        </div>
        <div class="table-responsive">
          <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr>
                <th class="text-left py-3 px-4 font-semibold text-sm text-gray-600 w-12">
                  <input type="checkbox" id="selectAllEmails" class="rounded border-gray-300 text-primary focus:ring-primary">
                </th>
                <th class="text-left py-3 px-4 font-semibold text-sm text-gray-600">
                  Email
                </th>
                <th class="text-left py-3 px-4 font-semibold text-sm text-gray-600">
                  Date d'inscription
                </th>
                <th class="text-left py-3 px-4 font-semibold text-sm text-gray-600">
                  Statut
                </th>
                <th class="text-right py-3 px-4 font-semibold text-sm text-gray-600">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($emails as $email): ?>
              <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="py-3 px-4">
                  <input type="checkbox" name="email_ids[]" value="<?= $email['id'] ?>" class="email-checkbox rounded border-gray-300 text-primary focus:ring-primary">
                </td>
                <td class="py-3 px-4 font-medium">
                  <?= htmlspecialchars($email['email']) ?>
                </td>
                <td class="py-3 px-4 text-gray-600">
                  <?= date('d/m/Y H:i', strtotime($email['date_subscribed'])) ?>
                </td>
                <td class="py-3 px-4">
                  <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                    Actif
                  </span>
                </td>
                <td class="py-3 px-4 text-right">
                  <div class="flex justify-end gap-2">
                    <button class="email-delete-btn w-8 h-8 bg-red-50 rounded-full flex items-center justify-center text-red-600 hover:bg-red-100" data-id="<?= $email['id'] ?>">
                      <i class="ri-delete-bin-line"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($emails)): ?>
              <tr>
                <td colspan="5" class="py-8 text-center text-gray-500">
                  <div class="flex flex-col items-center">
                    <div class="bg-gray-100 p-3 rounded-full mb-2">
                      <i class="ri-mail-line text-gray-400 text-xl"></i>
                    </div>
                    <p>Aucun abonné trouvé</p>
                  </div>
                </td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        
        <!-- Pagination for Emails -->
        <?php if ($totalEmailPages > 1): ?>
        <div class="px-4 py-5 border-t border-gray-200 flex justify-between items-center">
          <div class="text-sm text-gray-600">
            Affichage de <?= min($emailsPerPage * ($emailPage - 1) + 1, $totalEmails) ?> à <?= min($emailsPerPage * $emailPage, $totalEmails) ?> sur <?= $totalEmails ?> abonnés
          </div>
          <div class="flex space-x-1">
            <?php if ($emailPage > 1): ?>
              <a href="?tab=emails&email_page=<?= $emailPage-1 ?>" class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 hover:bg-gray-100">
                <i class="ri-arrow-left-s-line"></i>
              </a>
            <?php endif; ?>
            
            <?php
            // Afficher les liens de pagination
            $startPage = max(1, min($emailPage - 2, $totalEmailPages - 4));
            $endPage = min($totalEmailPages, max($emailPage + 2, 5));
            
            // Toujours afficher au moins 5 pages si possible
            if ($endPage - $startPage < 4) {
              $startPage = max(1, $endPage - 4);
            }
            
            for ($i = $startPage; $i <= $endPage; $i++): 
            ?>
              <a href="?tab=emails&email_page=<?= $i ?>" class="w-8 h-8 flex items-center justify-center rounded-full <?= $i == $emailPage ? 'bg-primary text-white' : 'border border-gray-300 hover:bg-gray-100' ?>">
                <?= $i ?>
              </a>
            <?php endfor; ?>
            
            <?php if ($emailPage < $totalEmailPages): ?>
              <a href="?tab=emails&email_page=<?= $emailPage+1 ?>" class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 hover:bg-gray-100">
                <i class="ri-arrow-right-s-line"></i>
              </a>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
        
        <!-- Bulk Actions Footer -->
        <div class="p-4 bg-gray-50 flex items-center border-t border-gray-200">
          <label class="text-sm text-gray-600 mr-3">Pour la sélection:</label>
          <div class="flex space-x-2">
            <button id="bulkDeleteBtn" class="px-3 py-1 bg-red-600 text-white rounded-button text-sm font-medium hover:bg-red-700 transition flex items-center" disabled>
              <div class="w-4 h-4 flex items-center justify-center mr-1">
                <i class="ri-delete-bin-line"></i>
              </div>
              Supprimer
            </button>
          </div>
        </div>
      </div>
   
    <?php elseif ($activeTab == 'publications'): ?>
        <!-- Publications Tab Content -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Gestion des Publications</h2>
            <button
        onclick="openModalPublication()"
        class="bg-primary text-black px-4 py-2 rounded-button font-medium hover:bg-opacity-90 transition whitespace-nowrap !rounded-button flex items-center"
        style="padding: 12px 16px; color: black; border: none; cursor: pointer; display: inline-flex; align-items: center;"
    >
        <div class="w-5 h-5 flex items-center justify-center mr-1" style="width: 20px; height: 20px; margin-right: 8px;">
            <i class="ri-add-line"></i>
        </div>
        Ajouter une publication vidéo
    </button>
        </div>
        
        <!-- Publications Grid View -->
      <?php if (!empty($publications)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <?php foreach ($publications as $pub): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow duration-300">
              <!-- Media Container -->
              <div class="relative aspect-video bg-gray-100">
                
                  <!-- Video Player -->
                  <div class="w-full h-full flex items-center justify-center">
                    <video class="w-full h-full object-cover" controls>
                      <source src="<?= htmlspecialchars($pub['video_path']) ?>">
                      Votre navigateur ne supporte pas les vidéos.
                    </video>
                  </div>
                
              </div>
              
              <!-- Publication Info -->
              <div class="p-4">
                <h3 class="font-bold text-lg text-gray-800 mb-1 ">
                  <?= htmlspecialchars($pub['titre']) ?>
                </h3>
                
                <div class="flex justify-between items-center mt-3">
                  <!-- Date -->
                  <span class="text-sm text-gray-500">
                    <?= date('d/m/Y', strtotime($pub['date_creation'])) ?>
                  </span>
                  <div class="flex justify-end space-x-2 mt-4">
                  <!-- Status -->
                  <span class="px-2 py-1 <?= $pub['statut'] === 'actif' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?> rounded-full text-xs font-medium">
                    <?= htmlspecialchars($pub['statut']) ?>
                  </span>
                  <button
                    class="delete-pub-btn w-8 h-8 bg-red-50 rounded-full flex items-center justify-center text-red-600 hover:bg-red-100"
                    data-id="<?= $pub['id'] ?>"
                    title="Supprimer"
                  >
                    <i class="ri-delete-bin-line"></i>
                  </button>
                  </div>
                </div>
                
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
          <div class="mx-auto w-16 h-16 flex items-center justify-center bg-gray-100 rounded-full mb-4">
            <i class="ri-article-line text-gray-400 text-2xl"></i>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-1">Aucune publication !!!</h3>
          <p class="text-gray-500 mb-6">Commencez par ajouter votre première publication</p>
          
        </div>
      <?php endif; ?>
      
      <!-- Pagination for Publications -->
      <?php if ($totalPubPages > 1): ?>
        <div class="mt-8 px-4 py-5 border-t border-gray-200 flex justify-between items-center">
          <div class="text-sm text-gray-600">
            Affichage de <?= min($pubsPerPage * ($pubPage - 1) + 1, $totalPublications) ?> à <?= min($pubsPerPage * $pubPage, $totalPublications) ?> sur <?= $totalPublications ?> publications
          </div>
          <div class="flex space-x-1">
            <?php if ($pubPage > 1): ?>
              <a href="?tab=publications&pub_page=<?= $pubPage-1 ?>" class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 hover:bg-gray-100">
                <i class="ri-arrow-left-s-line"></i>
              </a>
            <?php endif; ?>
            
            <?php
            $startPage = max(1, min($pubPage - 2, $totalPubPages - 4));
            $endPage = min($totalPubPages, max($pubPage + 2, 5));
            
            if ($endPage - $startPage < 4) {
              $startPage = max(1, $endPage - 4);
            }
            
            for ($i = $startPage; $i <= $endPage; $i++): 
            ?>
              <a href="?tab=publications&pub_page=<?= $i ?>" class="w-8 h-8 flex items-center justify-center rounded-full <?= $i == $pubPage ? 'bg-primary text-white' : 'border border-gray-300 hover:bg-gray-100' ?>">
                <?= $i ?>
              </a>
            <?php endfor; ?>
            
            <?php if ($pubPage < $totalPubPages): ?>
              <a href="?tab=publications&pub_page=<?= $pubPage+1 ?>" class="w-8 h-8 flex items-center justify-center rounded-full border border-gray-300 hover:bg-gray-100">
                <i class="ri-arrow-right-s-line"></i>
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
      <script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la suppression des publications
    document.querySelectorAll('.delete-pub-btn').forEach(button => {
        button.addEventListener('click', function() {
            const pubId = this.getAttribute('data-id');
            
            Swal.fire({
                title: 'Confirmer la suppression',
                text: "Êtes-vous sûr de vouloir supprimer définitivement cette publication ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Envoi de la requête AJAX
                    fetch('delete_publication.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id=' + pubId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Supprimé !',
                                'La publication a été supprimée définitivement.',
                                'success'
                            ).then(() => {
                                // Actualiser la page après suppression
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Erreur',
                                data.message || 'Une erreur est survenue lors de la suppression',
                                'error'
                            );
                        }
                    })
                    .catch(error => {
                        Swal.fire(
                            'Erreur',
                            'Problème de connexion au serveur',
                            'error'
                        );
                    });
                }
            });
        });
    });
});
</script>
<?php endif; ?>
</main>
    
    <!-- Delete Email Confirmation Modal -->
    <div id="deleteEmailModal" class="modal fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
      <div class="bg-white rounded-lg w-full max-w-md">
        <div class="p-6 text-center">
          <div class="w-16 h-16 bg-red-100 rounded-full mx-auto mb-4 flex items-center justify-center">
            <i class="ri-error-warning-line ri-2x text-red-600"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-800 mb-2">
            Confirmer la suppression
          </h3>
          <p class="text-gray-600 mb-6">
            Êtes-vous sûr de vouloir supprimer cet email de la liste des abonnés ? Cette action est irréversible.
          </p>
          <div class="flex justify-center space-x-3">
            <button id="cancelDeleteEmailBtn" class="px-4 py-2 border border-gray-300 rounded-button text-gray-700 hover:bg-gray-50 whitespace-nowrap !rounded-button">
              Annuler
            </button>
            <button id="confirmDeleteEmailBtn" class="px-4 py-2 bg-red-600 text-white rounded-button hover:bg-red-700 whitespace-nowrap !rounded-button flex items-center justify-center">
              <div class="w-5 h-5 flex items-center justify-center mr-1">
                <i class="ri-delete-bin-line"></i>
              </div>
              Supprimer
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- Add/Edit Activity Modal -->
    <div
      id="activityModal"
      class="modal fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden"
    >
      <div
        class="bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-auto"
      >
        <div
          class="p-6 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white z-10"
        >
          <h3 class="text-xl font-bold text-gray-800" id="modalTitle">
            Ajouter une activité
          </h3>
          <button id="closeModal" class="text-gray-500 hover:text-gray-700">
            <div class="w-6 h-6 flex items-center justify-center">
              <i class="ri-close-line ri-lg"></i>
            </div>
          </button>
        </div>
        <form id="activityForm " class="p-6" action="activite/ajout.php" method="POST" enctype="multipart/form-data">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
              <label
                for="title"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Titre de l'activité</label
              >
              <input
                type="text"
                id="title"
                name="titre"
                class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="Entrez le titre de l'activité"
                required
              />
            </div>
            <div class="md:col-span-2">
              <label
                for="description"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Description</label
              >
              <div
                id="editor-container"
                class="border border-gray-300 rounded min-h-[200px] mb-2"
              >
                <div
                  id="toolbar"
                  class="border-b border-gray-300 p-2 bg-gray-50 flex flex-wrap gap-1"
                >
                  <button
                    type="button"
                    data-command="bold"
                    class="p-1 hover:bg-gray-200 rounded"
                  >
                    <div class="w-6 h-6 flex items-center justify-center">
                      <i class="ri-bold"></i>
                    </div>
                  </button>
                  <button
                    type="button"
                    data-command="italic"
                    class="p-1 hover:bg-gray-200 rounded"
                  >
                    <div class="w-6 h-6 flex items-center justify-center">
                      <i class="ri-italic"></i>
                    </div>
                  </button>
                  <button
                    type="button"
                    data-command="underline"
                    class="p-1 hover:bg-gray-200 rounded"
                  >
                    <div class="w-6 h-6 flex items-center justify-center">
                      <i class="ri-underline"></i>
                    </div>
                  </button>
                  <span class="w-px h-6 bg-gray-300 mx-1"></span>
                  
                  <button
                    type="button"
                    data-command="justifyLeft"
                    class="p-1 hover:bg-gray-200 rounded"
                  >
                    <div class="w-6 h-6 flex items-center justify-center">
                      <i class="ri-align-left"></i>
                    </div>
                  </button>
                  <button
                    type="button"
                    data-command="justifyCenter"
                    class="p-1 hover:bg-gray-200 rounded"
                  >
                    <div class="w-6 h-6 flex items-center justify-center">
                      <i class="ri-align-center"></i>
                    </div>
                  </button>
                  <button
                    type="button"
                    data-command="justifyRight"
                    class="p-1 hover:bg-gray-200 rounded"
                  >
                    <div class="w-6 h-6 flex items-center justify-center">
                      <i class="ri-align-right"></i>
                    </div>
                  </button>
                  <span class="w-px h-6 bg-gray-300 mx-1"></span>
                  
                  <!-- <button
                    type="button"
                    data-command="insertImage"
                    class="p-1 hover:bg-gray-200 rounded"
                  >
                    <div class="w-6 h-6 flex items-center justify-center">
                      <i class="ri-image-line"></i>
                    </div>
                  </button> -->
                  <button type="button" id="imageInsertBtn" class="p-1 hover:bg-gray-200 rounded">
                  <i class="ri-image-add-line"></i>
                </button>
                  

                </div>
                <div
                  id="editor-content"
                  class="p-4 min-h-[150px]"
                  contenteditable="true"
                ></div>
              </div>
              <textarea
                id="description"
                name="description"
                rows="6"
                class="hidden"
                required
              ></textarea>
            </div>
            
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Image principale</label
              >
              <div class="flex items-center space-x-4">
                <div
                  class="image-preview w-32 h-24 bg-gray-100 border border-gray-300 rounded flex items-center justify-center overflow-hidden"
                >
                  <img
                    id="mainImagePreview"
                    src=""
                    alt=""
                    class="hidden w-full h-full object-cover"
                  />
                  <div
                    id="mainImagePlaceholder"
                    class="text-gray-400 flex flex-col items-center"
                  >
                    <i class="ri-image-line ri-2x"></i>
                    <span class="text-xs mt-1">Aucune image</span>
                  </div>
                </div>
                <label
                  for="main_image"
                  class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-button cursor-pointer whitespace-nowrap !rounded-button flex items-center"
                >
                  <div class="w-5 h-5 flex items-center justify-center mr-1">
                    <i class="ri-upload-2-line"></i>
                  </div>
                  Choisir une image
                </label>
                <input
                  type="file"
                  id="main_image"
                  name="image"
                  accept="image/*"
                />
              </div>
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Images supplémentaires</label
              >
              <div class="mb-3">
                <label
                  for="additional_images"
                  class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-button cursor-pointer whitespace-nowrap !rounded-button inline-flex items-center"
                >
                  <div class="w-5 h-5 flex items-center justify-center mr-1">
                    <i class="ri-image-add-line"></i>
                  </div>
                  Ajouter des images
                </label>
                <input
                  type="file"
                  id="additional_images"
                  name="additional_images[]"
                  accept="image/*"
                  multiple
                />
              </div>
              <div
                id="additionalImagesPreview"
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3"
              >
                <!-- Additional images will be displayed here -->
              </div>
            </div>
            <div class="bg-white rounded shadow-sm p-6">
              <h2
                class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2"
              >
                Statut
              </h2>
              <div class="space-y-4">
                <div>
                  
                  <div class="flex items-center space-x-4">
                    <label class="inline-flex items-center cursor-pointer">
                      <div class="relative">
                        <input
                          type="radio"
                          name="statut"
                          value="brouillon"
                          class="sr-only peer"
                        />
                        <div
                          class="w-5 h-5 border border-gray-300 rounded-full peer-checked:border-4 peer-checked:border-primary"
                        ></div>
                      </div>
                      <span class="ml-2 text-gray-700">Brouillon</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                      <div class="relative">
                        <input
                          type="radio"
                          name="statut"
                          value="publié"
                          checked
                          class="sr-only peer"
                        />
                        <div
                          class="w-5 h-5 border border-gray-300 rounded-full peer-checked:border-4 peer-checked:border-primary"
                        ></div>
                      </div>
                      <span class="ml-2 text-gray-700">Publié</span>
                    </label>
                  </div>
                </div>
               
              </div>
            </div>
          </div>
          <div class="mt-8 flex justify-end space-x-3">
            <button
              type="button"
              id="cancelBtn"
              class="px-4 py-2 border border-gray-300 rounded-button text-gray-700 hover:bg-gray-50 whitespace-nowrap !rounded-button"
            >
              Annuler
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-primary text-white rounded-button hover:bg-opacity-90 whitespace-nowrap !rounded-button flex items-center"
            >
              <div class="w-5 h-5 flex items-center justify-center mr-1">
                <i class="ri-save-line"></i>
              </div>
              Enregistrer
            </button>
          </div>
        </form>
      </div>
    </div>
    <div
      id="modifModal"
      class="modal fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden"
    >
      <div
        class="bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-auto"
      >
        <div
          class="p-6 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white z-10"
        >
          <h3 class="text-xl font-bold text-gray-800" id="modalTitle">
            Modifier une activité
          </h3>
          <button id="closeModal2" class="text-gray-500 hover:text-gray-700">
            <div class="w-6 h-6 flex items-center justify-center">
              <i class="ri-close-line ri-lg"></i>
            </div>
          </button>
        </div>
        <form id="editForm" class="p-6" method="POST" enctype="multipart/form-data" action="">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <input type="hidden" name="id" id="activity_id" />
            <div class="md:col-span-2">
              <label
                for="title2"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Titre de l'activité</label
              >
              <input
                type="text"
                id="title2"
                name="titre"
                class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                placeholder="Entrez le titre de l'activité"
                required
              />
            </div>
            <div class="md:col-span-2">
              <label
                for="description2"
                class="block text-sm font-medium text-gray-700 mb-1"
                >Description</label
              >
              <div
                id="editor-container2"
                class="border border-gray-300 rounded min-h-[200px] mb-2"
              >
                <div
                  id="toolbar"
                  class="border-b border-gray-300 p-2 bg-gray-50 flex flex-wrap gap-1"
                >
                  <button
                    type="button"
                    data-command="bold"
                    class="p-1 hover:bg-gray-200 rounded"
                  >
                    <div class="w-6 h-6 flex items-center justify-center">
                      <i class="ri-bold"></i>
                    </div>
                  </button>
                  <button
                    type="button"
                    data-command="italic"
                    class="p-1 hover:bg-gray-200 rounded"
                  >
                    <div class="w-6 h-6 flex items-center justify-center">
                      <i class="ri-italic"></i>
                    </div>
                  </button>
                  <button
                    type="button"
                    data-command="underline"
                    class="p-1 hover:bg-gray-200 rounded"
                  >
                    <div class="w-6 h-6 flex items-center justify-center">
                      <i class="ri-underline"></i>
                    </div>
                  </button>
                  <span class="w-px h-6 bg-gray-300 mx-1"></span>
                  
                  <button
                    type="button"
                    data-command="justifyLeft"
                    class="p-1 hover:bg-gray-200 rounded"
                  >
                    <div class="w-6 h-6 flex items-center justify-center">
                      <i class="ri-align-left"></i>
                    </div>
                  </button>
                  <button
                    type="button"
                    data-command="justifyCenter"
                    class="p-1 hover:bg-gray-200 rounded"
                  >
                    <div class="w-6 h-6 flex items-center justify-center">
                      <i class="ri-align-center"></i>
                    </div>
                  </button>
                  <button
                    type="button"
                    data-command="justifyRight"
                    class="p-1 hover:bg-gray-200 rounded"
                  >
                    <div class="w-6 h-6 flex items-center justify-center">
                      <i class="ri-align-right"></i>
                    </div>
                  </button>
                  <span class="w-px h-6 bg-gray-300 mx-1"></span>
                                   
                  <button type="button" id="imageInsertBtn" class="p-1 hover:bg-gray-200 rounded">
                  <i class="ri-image-add-line"></i>
                </button>
                  

                </div>
                <div
                  id="editor-content2"
                  class="p-4 min-h-[150px]"
                  contenteditable="true"
                ></div>
              </div>
              <textarea
                id="description2"
                name="description"
                rows="6"
                class="hidden"
                required
              ></textarea>
            </div>
            
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Image principale</label
              >
              <div class="flex items-center space-x-4">
                <div
                  class="image-preview w-32 h-24 bg-gray-100 border border-gray-300 rounded flex items-center justify-center overflow-hidden"
                >
                  <img
                    id="mainImagePreview2"
                    src=""
                    alt=""
                    class="hidden w-full h-full object-cover"
                  />
                  <div
                    id="mainImagePlaceholder2"
                    class="text-gray-400 flex flex-col items-center"
                  >
                    <i class="ri-image-line ri-2x"></i>
                    <span class="text-xs mt-1">Aucune image</span>
                  </div>
                </div>
                <label
                  for="main_image2"
                  class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-button cursor-pointer whitespace-nowrap !rounded-button flex items-center"
                >
                  <div class="w-5 h-5 flex items-center justify-center mr-1">
                    <i class="ri-upload-2-line"></i>
                  </div>
                  Choisir une image
                </label>
                <input
                  type="file"
                  id="main_image2"
                  name="image"
                  accept="image/*"
                />
              </div>
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Images supplémentaires</label
              >
              <div class="mb-3">
                <label
                  for="additional_images2"
                  class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-button cursor-pointer whitespace-nowrap !rounded-button inline-flex items-center"
                >
                  <div class="w-5 h-5 flex items-center justify-center mr-1">
                    <i class="ri-image-add-line"></i>
                  </div>
                  Ajouter des images
                </label>
                <input
                  type="file"
                  id="additional_images2"
                  name="additional_images[]"
                  accept="image/*"
                  multiple
                />
              </div>
              <div
                id="additionalImagesPreview2"
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3"
              >
                <!-- Additional images will be displayed here -->
              </div>
            </div>
            <div class="bg-white rounded shadow-sm p-6">
              <h2
                class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2"
              >
                Statut
              </h2>
              <div class="space-y-4">
                <div>
                  
                  <div class="flex items-center space-x-4">
                    <label class="inline-flex items-center cursor-pointer">
                      <div class="relative">
                        <input
                          type="radio"
                          name="statut"
                          value="brouillon"
                          class="sr-only peer"
                        />
                        <div
                          class="w-5 h-5 border border-gray-300 rounded-full peer-checked:border-4 peer-checked:border-primary"
                        ></div>
                      </div>
                      <span class="ml-2 text-gray-700">Brouillon</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                      <div class="relative">
                        <input
                          type="radio"
                          name="statut"
                          value="publié"
                          checked
                          class="sr-only peer"
                        />
                        <div
                          class="w-5 h-5 border border-gray-300 rounded-full peer-checked:border-4 peer-checked:border-primary"
                        ></div>
                      </div>
                      <span class="ml-2 text-gray-700">Publié</span>
                    </label>
                  </div>
                </div>
               
              </div>
            </div>
          </div>
          <div class="mt-8 flex justify-end space-x-3">
            <button
              type="button"
              id="cancelBtn2"
              class="px-4 py-2 border border-gray-300 rounded-button text-gray-700 hover:bg-gray-50 whitespace-nowrap !rounded-button"
            >
              Annuler
            </button>
            <button
              id="modification"
              type="submit"
              class="px-4 py-2 bg-primary text-white rounded-button hover:bg-opacity-90 whitespace-nowrap !rounded-button flex items-center"
            >
              <div class="w-5 h-5 flex items-center justify-center mr-1">
                <i class="ri-save-line"></i>
              </div>
              Modifier
            </button>
          </div>
        </form>
      </div>
    </div>
    <!-- View Activity Modal -->
    <div
      id="viewModal"
      class="modal fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden"
    >
      <div
        class="bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-auto"
      >
        <div
          class="p-6 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white z-10"
        >
          <h3 class="text-xl font-bold text-gray-800" id="viewModalTitle">
            Détails de l'activité
          </h3>
          <button id="closeViewModal" class="text-gray-500 hover:text-gray-700">
            <div class="w-6 h-6 flex items-center justify-center">
              <i class="ri-close-line ri-lg"></i>
            </div>
          </button>
        </div>
        <div class="p-6">
          <div class="mb-8">
            <img
              id="viewMainImage"
              src=""
              alt="Image principale"
              class="w-full h-84 object-cover object-top rounded"
            />
          </div>
          <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 mb-1">Titre</h4>
            <p id="viewTitle" class="text-xl font-bold text-gray-800">
            </p>
          </div>
          
          <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-500 mb-1">Description</h4>
            <p id="viewDescription" class="text-gray-800">
              
            </p>
          </div>
          <div>
            <h4 class="text-sm font-medium text-gray-500 mb-3">
              Galerie d'images
            </h4>
            <div id="gallery" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
              
              
            </div>
          </div>
          <div class="mt-8 flex justify-end space-x-3">
            <button
              type="button"
              id="editFromViewBtn"
              class="px-4 py-2 border border-gray-300 rounded-button text-gray-700 hover:bg-gray-50 whitespace-nowrap !rounded-button flex items-center"
            >
              <div class="w-5 h-5 flex items-center justify-center mr-1">
                <i class="ri-edit-line"></i>
              </div>
              Modifier
            </button>
            <button
              type="button"
              id="closeViewBtn"
              class="px-4 py-2 bg-primary text-white rounded-button hover:bg-opacity-90 whitespace-nowrap !rounded-button"
            >
              Fermer
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- Delete Confirmation Modal -->
    <div
      id="deleteModal"
      class="modal fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden"
    >
      <div class="bg-white rounded-lg w-full max-w-md">
        <div class="p-6 text-center">
          <div
            class="w-16 h-16 bg-red-100 rounded-full mx-auto mb-4 flex items-center justify-center"
          >
            <i class="ri-error-warning-line ri-2x text-red-600"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-800 mb-2">
            Confirmer la suppression
          </h3>
          <p class="text-gray-600 mb-6">
            Êtes-vous sûr de vouloir supprimer cette activité ? Cette action est
            irréversible.
          </p>
          <div class="flex justify-center space-x-3">
            <button
              id="cancelDeleteBtn"
              class="px-4 py-2 border border-gray-300 rounded-button text-gray-700 hover:bg-gray-50 whitespace-nowrap !rounded-button"
            >
              Annuler
            </button>
            <button
              id="confirmDeleteBtn"
              class="px-4 py-2 bg-red-600 text-white rounded-button hover:bg-red-700 whitespace-nowrap !rounded-button flex items-center justify-center"
            >
              <div class="w-5 h-5 flex items-center justify-center mr-1">
                <i class="ri-delete-bin-line"></i>
              </div>
              Supprimer
            </button>
          </div>
        </div>
      </div>
    </div>
   
    <!-- Modal de Création de Publication -->
    <div id="publicationModal" class="modalpubli">
        <div class="modal-content-publi">
            <div class="modal-header-publi">
                <button class="close-btn-publi" onclick="closeModalPublication()">&times;</button>
                <h2>📹 Créer une Publication</h2>
                <p>Partagez votre contenu vidéo avec le monde</p>
            </div>
            
            <div class="modal-body-publi">
                <form action="ajout_publication.php" method="POST" enctype="multipart/form-data" id="publicationForm">
                    
                    <div class="form-group-publi">
                        <label for="titre">📝 Titre de la publication *</label>
                        <input type="text" id="titre" name="titre" required 
                               placeholder="Entrez le titre de votre publication">
                    </div>
                    
                    <div class="form-group-publi">
                        <label for="video">🎬 Vidéo à publier *</label>
                        <div class="file-input-container-publi" onclick="document.getElementById('video').click()">
                            <input type="file" id="video" name="video" accept="video/*" required>
                            <div class="file-icon-publi">📁</div>
                            <div class="file-text-publi">
                                Cliquez pour sélectionner une vidéo<br>
                                <small>Formats acceptés: MP4, AVI, MOV, WMV, WEBM (Max: 100MB)</small>
                            </div>
                            <div class="file-selected-publi" id="selectedFile"></div>
                        </div>
                    </div>
                    
                    <div class="form-group-publi">
                        <label for="statut">📊 Statut de la publication</label>
                        <select id="statut" name="statut">
                            <option value="actif">🟢 Actif (Publié)</option>
                            <option value="inactif">🔴 Inactif (Brouillon)</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="submit-btn-publi" id="submitBtn">
                        🚀 Créer la Publication
                    </button>
                    
                    <div class="progress-bar-publi" id="progressBar">
                        <div class="progress-fill-publi" id="progressFill"></div>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
  function confirmLogout() {
    Swal.fire({
      title: 'Se déconnecter ?',
      text: "Vous serez redirigé vers la page de connexion.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Oui, me déconnecter',
      cancelButtonText: 'Annuler'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = 'logout.php';
      }
    });
  }
</script>
   <script>
        // Gestion du modal - NOMS DE FONCTIONS UNIQUES
        const modalPubli = document.getElementById('publicationModal');
        
        function openModalPublication() {
            console.log('Ouverture du modal publication'); // Debug
            modalPubli.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        
        function closeModalPublication() {
            console.log('Fermeture du modal publication'); // Debug
            modalPubli.classList.remove('show');
            document.body.style.overflow = 'auto';
            resetFormPublication();
        }
        
        function resetFormPublication() {
            document.getElementById('publicationForm').reset();
            document.getElementById('selectedFile').innerHTML = '';
            document.getElementById('submitBtn').disabled = false;
            document.getElementById('submitBtn').innerHTML = '🚀 Créer la Publication';
            document.getElementById('progressBar').style.display = 'none';
            document.getElementById('progressFill').style.width = '0%';
        }
        
        // Fermer le modal en cliquant à l'extérieur - ÉVÉNEMENT SPÉCIFIQUE
        modalPubli.addEventListener('click', function(event) {
            if (event.target === modalPubli) {
                closeModalPublication();
            }
        });
        
        // Fermer avec la touche Échap - ÉVÉNEMENT CONDITIONNEL
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && modalPubli.classList.contains('show')) {
                closeModalPublication();
            }
        });

        // Gestion du formulaire
        const videoInput = document.getElementById('video');
        const selectedFile = document.getElementById('selectedFile');
        const form = document.getElementById('publicationForm');
        const submitBtn = document.getElementById('submitBtn');
        const progressBar = document.getElementById('progressBar');
        const progressFill = document.getElementById('progressFill');

        // Gestion de la sélection de fichier
        videoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const fileSize = (file.size / (1024 * 1024)).toFixed(2);
                selectedFile.innerHTML = `
                    ✅ Fichier sélectionné: <strong>${file.name}</strong><br>
                    <small>Taille: ${fileSize} MB</small>
                `;
                
                // Vérification de la taille
                if (file.size > 100 * 1024 * 1024) {
                    selectedFile.innerHTML = `
                        ❌ <strong>Erreur:</strong> Fichier trop volumineux<br>
                        <small>Taille maximale: 100MB</small>
                    `;
                    videoInput.value = '';
                }
            } else {
                selectedFile.innerHTML = '';
            }
        });
        // Initialisation
        (function() {
            'use strict';
            console.log('Modal publication initialisé');
        })();
    </script>       
  </body>
</html>
