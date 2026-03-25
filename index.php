<?php
declare(strict_types=1);

require_once __DIR__ . '/layout.php';

require_login();

$stats = $pdo->query(
    "SELECT
        COUNT(*) AS total_vehicules,
        SUM(CASE WHEN statut = 'disponible' THEN 1 ELSE 0 END) AS disponibles,
        SUM(CASE WHEN statut = 'loue' THEN 1 ELSE 0 END) AS loues
     FROM vehicules"
)->fetch(PDO::FETCH_ASSOC);

$totalLocations = (int) $pdo->query("SELECT COUNT(*) FROM locations")->fetchColumn();
$averagePrice = (float) $pdo->query("SELECT COALESCE(AVG(prix_journalier), 0) FROM vehicules")->fetchColumn();

$recentLocations = $pdo->query(
    "SELECT l.nom_client, l.date_debut, l.date_fin, v.marque, v.modele, v.immatriculation
     FROM locations l
     INNER JOIN vehicules v ON v.id = l.vehicule_id
     ORDER BY l.date_debut DESC, l.id DESC
     LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);

$fleetOverview = $pdo->query(
    "SELECT marque, modele, immatriculation, statut, prix_journalier
     FROM vehicules
     ORDER BY marque ASC, modele ASC
     LIMIT 6"
)->fetchAll(PDO::FETCH_ASSOC);

render_page_start(
    'Tableau de bord',
    'index',
    'Tableau de bord',
    'Pilotez rapidement votre activite de location avec une vue claire sur le parc, les disponibilites et les derniers mouvements.'
);
?>

<section class="stats-grid">
    <article class="stat-card">
        <p class="eyebrow">Parc total</p>
        <strong><?= (int) ($stats['total_vehicules'] ?? 0) ?></strong>
        <span>Vehicules enregistres dans l'application</span>
    </article>
    <article class="stat-card">
        <p class="eyebrow">Disponibles</p>
        <strong><?= (int) ($stats['disponibles'] ?? 0) ?></strong>
        <span>Vehicules immediatement louables</span>
    </article>
    <article class="stat-card">
        <p class="eyebrow">En location</p>
        <strong><?= (int) ($stats['loues'] ?? 0) ?></strong>
        <span>Vehicules actuellement affectes</span>
    </article>
    <article class="stat-card">
        <p class="eyebrow">Tarif moyen</p>
        <strong><?= e(format_price($averagePrice)) ?></strong>
        <span>Prix journalier moyen du parc</span>
    </article>
</section>

<section class="dashboard-grid">
    <article class="card">
        <div class="card-header">
            <div>
                <p class="eyebrow">Activite recente</p>
                <h3>Dernieres locations</h3>
            </div>
            <div class="topbar-badge"><?= $totalLocations ?> operation(s)</div>
        </div>

        <?php if ($recentLocations === []): ?>
            <div class="empty-state">Aucune location n'a encore ete enregistree.</div>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Vehicule</th>
                            <th>Periode</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentLocations as $location): ?>
                            <tr>
                                <td><?= e($location['nom_client']) ?></td>
                                <td>
                                    <div class="table-title">
                                        <strong><?= e($location['marque'] . ' ' . $location['modele']) ?></strong>
                                        <span><?= e($location['immatriculation']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?= e(format_date_fr($location['date_debut'])) ?>
                                    au
                                    <?= e(format_date_fr($location['date_fin'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </article>

    <article class="card">
        <div class="card-header">
            <div>
                <p class="eyebrow">Apercu du parc</p>
                <h3>Vehicules suivis</h3>
            </div>
            <a class="btn btn-secondary" href="vehicules.php">Gerer le parc</a>
        </div>

        <?php if ($fleetOverview === []): ?>
            <div class="empty-state">Ajoutez votre premier vehicule pour demarrer la gestion.</div>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Vehicule</th>
                            <th>Statut</th>
                            <th>Tarif</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fleetOverview as $vehicle): ?>
                            <?php $isAvailable = $vehicle['statut'] === 'disponible'; ?>
                            <tr>
                                <td>
                                    <div class="table-title">
                                        <strong><?= e($vehicle['marque'] . ' ' . $vehicle['modele']) ?></strong>
                                        <span><?= e($vehicle['immatriculation']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge <?= $isAvailable ? 'available' : 'rented' ?>">
                                        <?= $isAvailable ? 'Disponible' : 'Loue' ?>
                                    </span>
                                </td>
                                <td><span class="price-badge"><?= e(format_price((float) $vehicle['prix_journalier'])) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </article>
</section>

<?php render_page_end(); ?>