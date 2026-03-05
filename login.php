<?php
session_start();
require_once 'db.php';

if (isset($_POST['login'])) {
    $identifiant = $_POST['identifiant'];
    $mdp = $_POST['mdp'];

    if ($identifiant === 'david' && $mdp === '1234') {
        $_SESSION['user'] = $identifiant;
        $_SESSION['last_action'] = time();
        header("Location: index.php");
        exit();
    } else {
        $error = "Identifiants incorrects";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Connexion</title>
</head>
<body>
    <div class="container">
        <h2>Connexion</h2>
        <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="identifiant" placeholder="Identifiant" required><br><br>
            <input type="password" name="mdp" placeholder="Mot de passe" required><br><br>
            <button type="submit" name="login" class="btn btn-add">Se connecter</button>
        </form>
    </div>
</body>
</html>