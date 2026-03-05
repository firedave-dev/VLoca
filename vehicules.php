<?php
session_start(); require 'db.php';
if (!isset($_SESSION['user'])) header("Location: login.php");

// Ajouter
if (isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO vehicules (immatriculation, marque, modele, prix_journalier) VALUES (?,?,?,?)");
    $stmt->execute([$_POST['immat'], $_POST['marque'], $_POST['modele'], $_POST['prix']]);
}

// Supprimer
if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM vehicules WHERE id = ?")->execute([$_GET['del']]);
}

$vehicules = $pdo->query("SELECT * FROM vehicules")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head><link rel="stylesheet" href="style.css"><title>Véhicules</title></head>
<body>
    <div class="container">
        <nav><a href="index.php">Retour</a></nav>
        <div class="card">
            <h3>Ajouter un véhicule</h3>
            <form method="POST">
                <input type="text" name="immat" placeholder="Immatriculation" required>
                <input type="text" name="marque" placeholder="Marque" required>
                <input type="text" name="modele" placeholder="Modèle" required>
                <input type="number" name="prix" placeholder="Prix/Jour" required>
                <button name="add" class="btn btn-blue">Enregistrer</button>
            </form>
        </div>
        <div class="card">
            <table>
                <tr><th>Véhicule</th><th>Statut</th><th>Actions</th></tr>
                <?php foreach($vehicules as $v): ?>
                <tr>
                    <td><?= "$v[marque] $v[modele] ($v[immatriculation])" ?></td>
                    <td><?= $v['statut'] ?></td>
                    <td><a href="?del=<?= $v['id'] ?>" class="btn btn-red">Supprimer</a></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>