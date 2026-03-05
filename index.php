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
?>