<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - ' : '' ?><?= APP_NAME ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="/equipements_sportifs/assets/css/style.css">
    
    <!-- Leaflet CSS pour les cartes -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Leaflet MarkerCluster CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.Default.css" />
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <a href="/equipements_sportifs/public/" class="logo">
                <svg class="logo-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 6v6l4 2"/>
                </svg>
                <span>Équipements Sportifs</span>
            </a>
            
            <nav class="nav">
                <a href="/equipements_sportifs/public/carte" class="nav-link <?= ($currentPage ?? '') === 'carte' ? 'active' : '' ?>">Carte</a>
                <a href="/equipements_sportifs/public/equipements" class="nav-link <?= ($currentPage ?? '') === 'equipements' ? 'active' : '' ?>">Équipements</a>
                
                <?php if (isLoggedIn()): ?>
                    <?php $user = getCurrentUser(); ?>
                    
                    <?php if (hasRole('collectivite')): ?>
                        <a href="/equipements_sportifs/public/mes-equipements" class="nav-link <?= ($currentPage ?? '') === 'mes-equipements' ? 'active' : '' ?>">Mes Équipements</a>
                    <?php endif; ?>
                    
                    <?php if (hasRole('admin')): ?>
                        <a href="/equipements_sportifs/public/admin" class="nav-link <?= ($currentPage ?? '') === 'admin' ? 'active' : '' ?>">Tableau de bord</a>
                    <?php endif; ?>
                    
                    <a href="/equipements_sportifs/public/profil" class="nav-link <?= ($currentPage ?? '') === 'profil' ? 'active' : '' ?>">Profil</a>
                <?php endif; ?>
            </nav>
            
            <div class="header-actions">
                <?php if (isLoggedIn()): ?>
                    <span class="user-badge"><?= e($user['nom']) ?></span>
                    <a href="/equipements_sportifs/public/deconnexion" class="btn btn-outline">Déconnexion</a>
                <?php else: ?>
                    <a href="/equipements_sportifs/public/connexion" class="btn btn-primary">Connexion</a>
                    <a href="/equipements_sportifs/public/inscription" class="btn btn-outline">Inscription</a>
                <?php endif; ?>
            </div>
            
            <!-- Menu mobile -->
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>
    
    <!-- Messages flash -->
    <?php $flash = getFlashMessage(); ?>
    <?php if ($flash): ?>
        <div class="flash-message flash-<?= $flash['type'] ?>">
            <?= e($flash['message']) ?>
            <button onclick="this.parentElement.remove()">×</button>
        </div>
    <?php endif; ?>
    
    <!-- Contenu principal -->
    <main class="main-content">
        <?= $content ?? '' ?>
    </main>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <p>&copy; <?= date('Y') ?> <?= APP_NAME ?> - Tous droits réservés</p>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="/equipements_sportifs/assets/js/app.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/leaflet.markercluster.js"></script>
    <?php if (isset($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
