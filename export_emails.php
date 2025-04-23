<?php
session_start();

// Vérification de la connexion admin
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

// Récupération de tous les emails
$sql = "SELECT email, date_subscribed FROM subscribers ORDER BY date_subscribed DESC";
$result = $conn->query($sql);

// Chargement de PhpSpreadsheet
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Création d'un nouveau fichier Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// En-tête personnalisé (ligne 1)
$sheet->mergeCells('A1:B1');
$sheet->setCellValue('A1', 'Liste des abonnés à la newsletter');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// En-têtes de colonnes (ligne 3)
$sheet->setCellValue('A3', 'Email');
$sheet->setCellValue('B3', 'Date d\'inscription');
$sheet->getStyle('A3:B3')->getFont()->setBold(true);

// Définir la largeur des colonnes
$sheet->getColumnDimension('A')->setWidth(40); // Email
$sheet->getColumnDimension('B')->setWidth(30); // Date d'inscription

// Remplissage des données (à partir de la ligne 4)
$rowNum = 4;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $date = new DateTime($row['date_subscribed']);
        $formatted_date = $date->format('d/m/Y H:i');

        $sheet->setCellValue('A' . $rowNum, $row['email']);
        $sheet->setCellValue('B' . $rowNum, $formatted_date);
        $rowNum++;
    }
}

// Nom du fichier Excel
$filename = 'abonnes_newsletter_' . date('Y-m-d') . '.xlsx';

// Préparation du téléchargement
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

// Création et envoi du fichier
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

// Fermeture
$conn->close();
exit;
?>
