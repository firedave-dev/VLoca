<?php
session_start();
if (!isset($_SESSION['user']) || (time() - $_SESSION['last_action'] > 300)) {
    session_destroy(); header("Location: login.php"); exit();
}
$_SESSION['last_action'] = time();
require 'db.php';

$total_cars = $pdo->query("SELECT COUNT(*) FROM vehicules")->fetchColumn();
$total_rented = $pdo->query("SELECT COUNT(*) FROM vehicules WHERE statut='loue'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head><link rel="stylesheet" href="style.css"><title>Dashboard</title></head>
<body>
    <div class="container">
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="vehicules.php">Véhicules</a>
            <a href="locations.php">Locations</a>
            <a href="logout.php" style="color:var(--danger)">Quitter</a>
        </nav>
        <div class="card">
            <h1>Bienvenue, <?= $_SESSION['user'] ?></h1>
            <h1>VLoca - Système de Gestion de Location</h1>
            <p>Statistiques actuelles :</p>
            <ul>
                <li>Total véhicules : <?= $total_cars ?></li>
                <li>En location : <?= $total_rented ?></li>
            </ul>
        </div>
    </div>
</body>
</html>