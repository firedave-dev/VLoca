<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function render_nav_link(string $href, string $label, string $activePage): void
{
    $isActive = basename($href, '.php') === $activePage;
    $className = $isActive ? 'nav-link is-active' : 'nav-link';
    ?>
    <a class="<?= $className ?>" href="<?= e($href) ?>"><?= e($label) ?></a>
    <?php
}

function render_page_start(string $title, string $activePage, string $heading, string $subtitle): void
{
    $flash = get_flash();
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= e($title) ?> | VLoca</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body class="app-body">
        <div class="app-shell">
            <aside class="sidebar">
                <div class="brand-block">
                    <span class="brand-mark">VL</span>
                    <div>
                        <p class="eyebrow">Plateforme</p>
                        <h1>VLoca</h1>
                    </div>
                </div>

                <nav class="sidebar-nav">
                    <?php render_nav_link('index.php', 'Tableau de bord', $activePage); ?>
                    <?php render_nav_link('vehicules.php', 'Vehicules', $activePage); ?>
                    <?php render_nav_link('locations.php', 'Locations', $activePage); ?>
                    <?php render_nav_link('logout.php', 'Deconnexion', $activePage); ?>
                </nav>

                <div class="sidebar-card">
                    <p class="eyebrow">Session</p>
                    <strong><?= e(current_user()) ?></strong>
                    <span>Gestionnaire connecte</span>
                </div>
            </aside>

            <div class="app-main">
                <header class="topbar">
                    <div>
                        <p class="eyebrow">Administration</p>
                        <h2><?= e($heading) ?></h2>
                    </div>
                    <div class="topbar-badge">Location de vehicules</div>
                </header>

                <?php if ($flash !== null): ?>
                    <div class="alert alert-<?= e($flash['type']) ?>">
                        <?= e($flash['message']) ?>
                    </div>
                <?php endif; ?>

                <section class="hero-card">
                    <div>
                        <p class="eyebrow">Espace de gestion</p>
                        <h3><?= e($heading) ?></h3>
                        <p><?= e($subtitle) ?></p>
                    </div>
                </section>

                <main class="page-content">
    <?php
}

function render_page_end(): void
{
    ?>
                </main>
            </div>
        </div>
    </body>
    </html>
    <?php
}

function render_auth_start(string $title, string $heading, string $subtitle): void
{
    $flash = get_flash();
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= e($title) ?> | VLoca</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body class="auth-body">
        <div class="auth-shell">
            <section class="auth-showcase">
                <p class="eyebrow">Gestion professionnelle</p>
                <h1>VLoca</h1>
                <p><?= e($subtitle) ?></p>
                <div class="auth-highlights">
                    <div class="mini-card">
                        <strong>Parc centralise</strong>
                        <span>Suivi des vehicules et disponibilites</span>
                    </div>
                    <div class="mini-card">
                        <strong>Locations fluides</strong>
                        <span>Creation et suivi des demandes clients</span>
                    </div>
                </div>
            </section>

            <section class="auth-card">
                <p class="eyebrow">Connexion</p>
                <h2><?= e($heading) ?></h2>

                <?php if ($flash !== null): ?>
                    <div class="alert alert-<?= e($flash['type']) ?>">
                        <?= e($flash['message']) ?>
                    </div>
                <?php endif; ?>
    <?php
}

function render_auth_end(): void
{
    ?>
            </section>
        </div>
    </body>
    </html>
    <?php
}
