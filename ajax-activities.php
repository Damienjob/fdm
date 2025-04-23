<?php
// ajax-activities.php
$conn = new mysqli("localhost", "root", "", "fdm");
if ($conn->connect_error) {
    die(json_encode(['error' => "Erreur de connexion: " . $conn->connect_error]));
}

// Paramètres de pagination
$activitesParPage = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$debut = ($page - 1) * $activitesParPage;

// Compter le nombre total d'activités publiées
$sqlCount = "SELECT COUNT(*) as total FROM activities WHERE statut = 'publié'";
$resultCount = $conn->query($sqlCount);
$totalActivites = $resultCount->fetch_assoc()['total'];
$totalPages = ceil($totalActivites / $activitesParPage);

// Récupérer les activités pour la page courante
$sql = "SELECT * FROM activities WHERE statut = 'publié' ORDER BY created_at DESC LIMIT $debut, $activitesParPage";
$result = $conn->query($sql);

// Préparer le HTML des activités
ob_start();
if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        ?>
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
                        <a href="activite.php?id=<?= $row['id'] ?>" class="filled-button">Lire la suite</a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    echo '<div class="col-12 text-center"><p>Aucune activité n\'est disponible pour le moment.</p></div>';
}
$activitiesHtml = ob_get_clean();

// Préparer le HTML de la pagination
ob_start();
if($totalPages > 1) {
    ?>
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
    <?php
}
$paginationHtml = ob_get_clean();

// Envoyer la réponse JSON
header('Content-Type: application/json');
echo json_encode([
    'activitiesHtml' => $activitiesHtml,
    'paginationHtml' => $paginationHtml,
    'currentPage' => $page,
    'totalPages' => $totalPages
]);

$conn->close();
?>