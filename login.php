<?php
declare(strict_types=1);

require_once __DIR__ . '/layout.php';

if (!empty($_SESSION['user'])) {
    redirect('index.php');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = trim($_POST['identifiant'] ?? '');
    $password = $_POST['mdp'] ?? '';

    if ($identifiant === '' || $password === '') {
        $error = 'Veuillez renseigner votre identifiant et votre mot de passe.';
    } else {
        $stmt = $pdo->prepare("SELECT identifiant, mot_de_passe FROM utilisateurs WHERE identifiant = ?");
        $stmt->execute([$identifiant]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['user'] = $user['identifiant'];
            $_SESSION['last_action'] = time();
            set_flash('success', 'Connexion reussie. Bienvenue sur votre espace de gestion.');
            redirect('index.php');
        }

        $error = 'Identifiant ou mot de passe incorrect.';
    }
}

render_auth_start(
    'Connexion',
    'Accedez a votre espace',
    'Une interface sobre et professionnelle pour piloter votre parc automobile et suivre les locations de vos clients.'
);
?>

<?php if ($error !== null): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
<?php endif; ?>

<form method="POST">
    <div class="field">
        <label for="identifiant">Identifiant</label>
        <input id="identifiant" type="text" name="identifiant" placeholder="Votre identifiant" required>
    </div>

    <div class="field">
        <label for="mdp">Mot de passe</label>
        <input id="mdp" type="password" name="mdp" placeholder="Votre mot de passe" required>
    </div>

    <button type="submit" class="btn btn-primary">Se connecter</button>
</form>

<?php render_auth_end(); ?>