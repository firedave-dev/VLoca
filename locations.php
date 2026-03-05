<?php
session_start(); require 'db.php';
if (isset($_POST['rent'])) {
    $pdo->prepare("INSERT INTO locations (vehicule_id, nom_client, date_debut, date_fin) VALUES (?,?,?,?)")->execute([$_POST['v_id'], $_POST['client'], $_POST['debut'], $_POST['fin']]);
    $pdo->prepare("UPDATE vehicules SET statut='loue' WHERE id=?")->execute([$_POST['v_id']]);
}
$dispo = $pdo->query("SELECT * FROM vehicules WHERE statut='disponible'")->fetchAll();
$locations = $pdo->query("SELECT l.*, v.marque, v.modele FROM locations l JOIN vehicules v ON l.vehicule_id = v.id")->fetchAll();
?>