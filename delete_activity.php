<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
  $id = intval($_POST['id']);

  $conn = new mysqli('localhost', 'root', '', 'fdm');
  if ($conn->connect_error) {
    die('Erreur de connexion : ' . $conn->connect_error);
  }

  $stmt = $conn->prepare("DELETE FROM activities WHERE id = ?");
  $stmt->bind_param('i', $id);

  if ($stmt->execute()) {
    echo 'success';
  } else {
    echo 'error';
  }

  $stmt->close();
  $conn->close();
}
