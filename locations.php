<?php
declare(strict_types=1);

require_once __DIR__ . '/layout.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_rental') {
        $vehicleId = (int) ($_POST['v_id'] ?? 0);
        $client = trim($_POST['client'] ?? '');
        $startDate = $_POST['debut'] ?? '';
        $endDate = $_POST['fin'] ?? '';

        if ($vehicleId <= 0 || $client === '' || $startDate === '' || $endDate === '') {
            set_flash('error', 'Merci de remplir tous les champs de la location.');
            redirect('locations.php');
        }

        if ($endDate < $startDate) {
            set_flash('error', 'La date de fin doit etre posterieure a la date de debut.');
            redirect('locations.php');
        }

        $vehicleQuery = $pdo->prepare("SELECT statut FROM vehicules WHERE id = ?");
        $vehicleQuery->execute([$vehicleId]);
        $vehicle = $vehicleQuery->fetch(PDO::FETCH_ASSOC);

        if (!$vehicle || ($vehicle['statut'] ?? '') !== 'disponible') {
            set_flash('error', 'Le vehicule selectionne n est plus disponible.');
            redirect('locations.php');
        }

        $stmt = $pdo->prepare(
            "INSERT INTO locations (vehicule_id, nom_client, date_debut, date_fin)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$vehicleId, $client, $startDate, $endDate]);

        $update = $pdo->prepare("UPDATE vehicules SET statut = 'loue' WHERE id = ?");
        $update->execute([$vehicleId]);

        set_flash('success', 'La location a ete enregistree avec succes.');
        redirect('locations.php');
    }

    if ($action === 'close_rental') {
        $vehicleId = (int) ($_POST['vehicle_id'] ?? 0);

        if ($vehicleId <= 0) {
            set_flash('error', 'Le vehicule selectionne est invalide.');
            redirect('locations.php');
        }

        $update = $pdo->prepare("UPDATE vehicules SET statut = 'disponible' WHERE id = ? AND statut = 'loue'");
        $update->execute([$vehicleId]);

        set_flash('success', 'Le vehicule est de nouveau disponible.');
        redirect('locations.php');
    }
}

$disponibles = $pdo->query(
    "SELECT id, marque, modele, immatriculation, prix_journalier
     FROM vehicules
     WHERE statut = 'disponible'
     ORDER BY marque ASC, modele ASC"
)->fetchAll(PDO::FETCH_ASSOC);

$historique = $pdo->query(
    "SELECT l.id, l.nom_client, l.date_debut, l.date_fin, v.id AS vehicule_id,
            v.marque, v.modele, v.immatriculation, v.statut, v.prix_journalier,
            CASE
                WHEN v.statut = 'loue'
                     AND l.id = (
                         SELECT MAX(l2.id)
                         FROM locations l2
                         WHERE l2.vehicule_id = l.vehicule_id
                     )
                THEN 1
                ELSE 0
            END AS is_active
     FROM locations l
     INNER JOIN vehicules v ON l.vehicule_id = v.id
     ORDER BY l.date_debut DESC, l.id DESC"
)->fetchAll(PDO::FETCH_ASSOC);

$locationStats = $pdo->query(
    "SELECT
        COUNT(*) AS total_locations,
        SUM(CASE WHEN statut = 'disponible' THEN 1 ELSE 0 END) AS vehicules_disponibles,
        SUM(CASE WHEN statut = 'loue' THEN 1 ELSE 0 END) AS vehicules_loues
     FROM vehicules"
)->fetch(PDO::FETCH_ASSOC);

render_page_start(
    'Locations',
    'locations',
    'Gestion des locations',
    'Creez rapidement une location, visualisez l historique des contrats et remettez un vehicule en disponibilite quand il revient.'
);
?>

<section class="stats-grid">
    <article class="stat-card">
        <p class="eyebrow">Locations enregistrees</p>
        <strong><?= count($historique) ?></strong>
        <span>Operations de location en base</span>
    </article>
    <article class="stat-card">
        <p class="eyebrow">Vehicules disponibles</p>
        <strong><?= (int) ($locationStats['vehicules_disponibles'] ?? 0) ?></strong>
        <span>Prets pour une nouvelle reservation</span>
    </article>
    <article class="stat-card">
        <p class="eyebrow">Vehicules loues</p>
        <strong><?= (int) ($locationStats['vehicules_loues'] ?? 0) ?></strong>
        <span>Actuellement affectes a des clients</span>
    </article>
</section>

<section class="two-columns">
    <article class="card">
        <div class="card-header">
            <div>
                <p class="eyebrow">Nouvelle operation</p>
                <h3>Enregistrer une location</h3>
            </div>
            <p class="muted"><?= count($disponibles) ?> vehicule(s) disponible(s)</p>
        </div>

        <?php if ($disponibles === []): ?>
            <div class="empty-state">Aucun vehicule n est disponible actuellement pour une nouvelle location.</div>
        <?php else: ?>
            <form method="POST" class="form-grid">
                <input type="hidden" name="action" value="create_rental">

                <div class="field">
                    <label for="v_id">Vehicule</label>
                    <select id="v_id" name="v_id" required>
                        <option value="">Choisir un vehicule</option>
                        <?php foreach ($disponibles as $vehicle): ?>
                            <option value="<?= (int) $vehicle['id'] ?>">
                                <?= e($vehicle['marque'] . ' ' . $vehicle['modele'] . ' - ' . $vehicle['immatriculation']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="client">Nom du client</label>
                    <input id="client" type="text" name="client" placeholder="Nom complet" required>
                </div>

                <div class="field">
                    <label for="debut">Date de debut</label>
                    <input id="debut" type="date" name="debut" required>
                </div>

                <div class="field">
                    <label for="fin">Date de fin</label>
                    <input id="fin" type="date" name="fin" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Valider la location</button>
                </div>
            </form>
        <?php endif; ?>
    </article>

    <article class="card">
        <div class="card-header">
            <div>
                <p class="eyebrow">Informations utiles</p>
                <h3>Suivi des contrats</h3>
            </div>
        </div>

        <div class="mini-card">
            <strong>Controle des dates</strong>
            <span>La date de fin doit etre superieure ou egale a la date de debut pour garantir un contrat coherent.</span>
        </div>

        <div class="mini-card">
            <strong>Retour du vehicule</strong>
            <span>Quand la location se termine, un clic suffit pour remettre le vehicule dans la liste des disponibilites.</span>
        </div>
    </article>
</section>

<section class="card">
    <div class="card-header">
        <div>
            <p class="eyebrow">Historique</p>
            <h3>Liste des locations</h3>
        </div>
        <div class="topbar-badge"><?= count($historique) ?> enregistrement(s)</div>
    </div>

    <?php if ($historique === []): ?>
        <div class="empty-state">Aucune location n'a encore ete enregistree.</div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Vehicule</th>
                        <th>Periode</th>
                        <th>Tarif</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historique as $entry): ?>
                        <?php $isActive = (int) $entry['is_active'] === 1; ?>
                        <tr>
                            <td><?= e($entry['nom_client']) ?></td>
                            <td>
                                <div class="table-title">
                                    <strong><?= e($entry['marque'] . ' ' . $entry['modele']) ?></strong>
                                    <span><?= e($entry['immatriculation']) ?></span>
                                </div>
                            </td>
                            <td><?= e(format_date_fr($entry['date_debut'])) ?> au <?= e(format_date_fr($entry['date_fin'])) ?></td>
                            <td><span class="price-badge"><?= e(format_price((float) $entry['prix_journalier'])) ?></span></td>
                            <td>
                                <span class="status-badge <?= $isActive ? 'rented' : 'available' ?>">
                                    <?= $isActive ? 'En cours' : 'Terminee' ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!$isActive): ?>
                                    <span class="muted">Archivee</span>
                                <?php else: ?>
                                    <form method="POST" onsubmit="return confirm('Marquer ce vehicule comme disponible ?');">
                                        <input type="hidden" name="action" value="close_rental">
                                        <input type="hidden" name="vehicle_id" value="<?= (int) $entry['vehicule_id'] ?>">
                                        <button type="submit" class="btn btn-secondary">Cloturer</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php render_page_end(); ?>