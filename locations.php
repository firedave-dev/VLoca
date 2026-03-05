<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }

// Logique pour enregistrer une location
if (isset($_POST['rent'])) {
    $v_id = $_POST['v_id'];
    $client = $_POST['client'];
    $debut = $_POST['debut'];
    $fin = $_POST['fin'];

// Enregistrer la location
    $stmt = $pdo->prepare("INSERT INTO locations (vehicule_id, nom_client, date_debut, date_fin) VALUES (?,?,?,?)");
    $stmt->execute([$v_id, $client, $debut, $fin]);

    $update = $pdo->prepare("UPDATE vehicules SET statut='loue' WHERE id=?");
    $update->execute([$v_id]);
    
    header("Location: locations.php");
}

$disponibles = $pdo->query("SELECT * FROM vehicules WHERE statut='disponible'")->fetchAll();

$historique = $pdo->query("SELECT l.*, v.marque, v.modele, v.immatriculation 
                           FROM locations l 
                           JOIN vehicules v ON l.vehicule_id = v.id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Locations</title>
</head>
<body>
    <div class="container">
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="vehicules.php">Véhicules</a>
            <a href="locations.php">Locations</a>
        </nav>

        <div class="card">
            <h3>Nouvelle Location</h3>
            <form method="POST">
                <select name="v_id" required>
                    <option value="">-- Choisir un véhicule --</option>
                    <?php foreach($disponibles as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= "$d[marque] $d[modele] ($d[immatriculation])" ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="client" placeholder="Nom du client" required>
                <label>Début :</label> <input type="date" name="debut" required>
                <label>Fin :</label> <input type="date" name="fin" required>
                <button name="rent" class="btn btn-blue">Valider la location</button>
            </form>
        </div>

        <div class="card">
            <h3>Historique des Locations</h3>
            <table>
                <tr><th>Client</th><th>Véhicule</th><th>Période</th></tr>
                <?php foreach($historique as $h): ?>
                <tr>
                    <td><?= $h['nom_client'] ?></td>
                    <td><?= "$h[marque] $h[modele]" ?></td>
                    <td>Du <?= $h['date_debut'] ?> au <?= $h['date_fin'] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>