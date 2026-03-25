<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

session_unset();
session_destroy();
session_start();
set_flash('success', 'Vous avez ete deconnecte avec succes.');
redirect('login.php');