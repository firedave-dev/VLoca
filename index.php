<?php
session_start();

// 300 secondes = 5 minutes
if (isset($_SESSION['last_action'])) {
    $secondsInactive = time() - $_SESSION['last_action'];
    if ($secondsInactive >= 300) {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }
}
$_SESSION['last_action'] = time();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Supprimer un véhicule
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM vehicules WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php");
}

// Ajouter un véhicule
if (isset($_POST['add_car'])) {
    $stmt = $pdo->prepare("INSERT INTO vehicules (immatriculation, marque, modele, prix_journalier) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['immat'], $_POST['marque'], $_POST['modele'], $_POST['prix']]);
}

$vehicules = $pdo->query("SELECT * FROM vehicules")->fetchAll();
?>