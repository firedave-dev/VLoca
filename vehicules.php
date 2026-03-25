<?php
declare(strict_types=1);

require_once __DIR__ . '/layout.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_vehicle') {
        $immatriculation = strtoupper(trim($_POST['immat'] ?? ''));
        $marque = trim($_POST['marque'] ?? '');
        $modele = trim($_POST['modele'] ?? '');
        $prix = (float) ($_POST['prix'] ?? 0);

        if ($immatriculation === '' || $marque === '' || $modele === '' || $prix <= 0) {
            set_flash('error', 'Merci de remplir correctement tous les champs du vehicule.');
            redirect('vehicules.php');
        }

        $exists = $pdo->prepare("SELECT COUNT(*) FROM vehicules WHERE immatriculation = ?");
        $exists->execute([$immatriculation]);

        if ((int) $exists->fetchColumn() > 0) {
            set_flash('error', 'Cette immatriculation existe deja dans le parc.');
            redirect('vehicules.php');
        }

        $stmt = $pdo->prepare(
            "INSERT INTO vehicules (immatriculation, marque, modele, prix_journalier, statut)
             VALUES (?, ?, ?, ?, 'disponible')"
        );
        $stmt->execute([$immatriculation, $marque, $modele, $prix]);

        set_flash('success', 'Le vehicule a ete ajoute avec succes.');
        redirect('vehicules.php');
    }

    if ($action === 'delete_vehicle') {
        $vehicleId = (int) ($_POST['vehicle_id'] ?? 0);

        $vehicleQuery = $pdo->prepare("SELECT statut FROM vehicules WHERE id = ?");
        $vehicleQuery->execute([$vehicleId]);
        $vehicle = $vehicleQuery->fetch(PDO::FETCH_ASSOC);

        if (!$vehicle) {
            set_flash('error', 'Le vehicule demande est introuvable.');
            redirect('vehicules.php');
        }

        if (($vehicle['statut'] ?? '') === 'loue') {
            set_flash('error', 'Impossible de supprimer un vehicule actuellement en location.');
            redirect('vehicules.php');
        }

        $deleteQuery = $pdo->prepare("DELETE FROM vehicules WHERE id = ?");
        $deleteQuery->execute([$vehicleId]);

        set_flash('success', 'Le vehicule a ete supprime du parc.');
        redirect('vehicules.php');
    }
}

$vehicules = $pdo->query(
    "SELECT id, immatriculation, marque, modele, prix_journalier, statut
     FROM vehicules
     ORDER BY marque ASC, modele ASC, immatriculation ASC"
)->fetchAll(PDO::FETCH_ASSOC);

$vehicleStats = $pdo->query(
    "SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN statut = 'disponible' THEN 1 ELSE 0 END) AS disponibles,
        SUM(CASE WHEN statut = 'loue' THEN 1 ELSE 0 END) AS loues
     FROM vehicules"
)->fetch(PDO::FETCH_ASSOC);

render_page_start(
    'Vehicules',
    'vehicules',
    'Gestion des vehicules',
    'Ajoutez, consultez et entretenez votre parc automobile avec un suivi clair des tarifs et de la disponibilite.'
);
?>

<section class="stats-grid">
    <article class="stat-card">
        <p class="eyebrow">Total du parc</p>
        <strong><?= (int) ($vehicleStats['total'] ?? 0) ?></strong>
        <span>Vehicules actuellement enregistres</span>
    </article>
    <article class="stat-card">
        <p class="eyebrow">Disponibles</p>
        <strong><?= (int) ($vehicleStats['disponibles'] ?? 0) ?></strong>
        <span>Prets a etre loues</span>
    </article>
    <article class="stat-card">
        <p class="eyebrow">Loues</p>
        <strong><?= (int) ($vehicleStats['loues'] ?? 0) ?></strong>
        <span>Actuellement chez les clients</span>
    </article>
</section>

<section class="two-columns">
    <article class="card">
        <div class="card-header">
            <div>
                <p class="eyebrow">Nouveau vehicule</p>
                <h3>Ajouter au parc</h3>
            </div>
            <p class="muted">Tous les champs sont obligatoires.</p>
        </div>

        <form method="POST" class="form-grid">
            <input type="hidden" name="action" value="add_vehicle">

            <div class="field">
                <label for="immat">Immatriculation</label>
                <input id="immat" type="text" name="immat" placeholder="AB-123-CD" required>
            </div>

            <div class="field">
                <label for="marque">Marque</label>
                <input id="marque" type="text" name="marque" placeholder="Toyota" required>
            </div>

            <div class="field">
                <label for="modele">Modele</label>
                <input id="modele" type="text" name="modele" placeholder="Corolla" required>
            </div>

            <div class="field">
                <label for="prix">Prix journalier</label>
                <input id="prix" type="number" step="0.01" min="1" name="prix" placeholder="25000" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Enregistrer le vehicule</button>
            </div>
        </form>
    </article>

    <article class="card">
        <div class="card-header">
            <div>
                <p class="eyebrow">Vue d'ensemble</p>
                <h3>Bonnes pratiques</h3>
            </div>
        </div>

        <div class="mini-card">
            <strong>Immatriculation unique</strong>
            <span>Chaque vehicule est controle avant ajout afin d'eviter les doublons dans le parc.</span>
        </div>

        <div class="mini-card">
            <strong>Suppression securisee</strong>
            <span>Un vehicule en cours de location ne peut pas etre retire pour proteger la coherence des donnees.</span>
        </div>
    </article>
</section>

<section class="card">
    <div class="card-header">
        <div>
            <p class="eyebrow">Inventaire</p>
            <h3>Liste des vehicules</h3>
        </div>
        <p class="muted"><?= count($vehicules) ?> vehicule(s) affiche(s)</p>
    </div>

    <?php if ($vehicules === []): ?>
        <div class="empty-state">Aucun vehicule enregistre pour le moment.</div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Vehicule</th>
                        <th>Tarif</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vehicules as $vehicle): ?>
                        <?php $isAvailable = $vehicle['statut'] === 'disponible'; ?>
                        <tr>
                            <td>
                                <div class="table-title">
                                    <strong><?= e($vehicle['marque'] . ' ' . $vehicle['modele']) ?></strong>
                                    <span><?= e($vehicle['immatriculation']) ?></span>
                                </div>
                            </td>
                            <td><span class="price-badge"><?= e(format_price((float) $vehicle['prix_journalier'])) ?></span></td>
                            <td>
                                <span class="status-badge <?= $isAvailable ? 'available' : 'rented' ?>">
                                    <?= $isAvailable ? 'Disponible' : 'Loue' ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" onsubmit="return confirm('Supprimer ce vehicule du parc ?');">
                                    <input type="hidden" name="action" value="delete_vehicle">
                                    <input type="hidden" name="vehicle_id" value="<?= (int) $vehicle['id'] ?>">
                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php render_page_end(); ?>