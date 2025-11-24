<?php
/**
 * Point d'entrée principal de l'application
 * Toutes les requêtes passent par ce fichier
 */

// Chargement de la configuration
require_once __DIR__ . '/../config/database.php';

// Démarrage de la session
startSecureSession();

// Récupération de l'URL demandée
$requestUri = $_SERVER['REQUEST_URI'];
$basePath = '/equipements_sportifs/public';
$path = str_replace($basePath, '', parse_url($requestUri, PHP_URL_PATH));
$path = $path ?: '/';

// Méthode HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Routage simple
$routes = [
    // Routes publiques
    'GET' => [
        '/' => 'PublicController@index',
        '/carte' => 'PublicController@carte',
        '/equipements' => 'PublicController@equipements',
        '/equipement/{id}' => 'PublicController@equipementDetail',
        '/api' => 'PublicController@apiDoc',
        
        // Auth
        '/connexion' => 'AuthController@loginForm',
        '/inscription' => 'AuthController@registerForm',
        '/deconnexion' => 'AuthController@logout',
        
        // Collectivité
        '/mes-equipements' => 'CollectiviteController@index',
        '/mes-equipements/ajouter' => 'CollectiviteController@createForm',
        '/mes-equipements/modifier/{id}' => 'CollectiviteController@editForm',
        
        // Admin
        '/admin' => 'AdminController@dashboard',
        '/admin/utilisateurs' => 'AdminController@utilisateurs',
        '/admin/approbations' => 'AdminController@approbations',
        
        // Profil
        '/profil' => 'ProfilController@index',
    ],
    'POST' => [
        '/connexion' => 'AuthController@login',
        '/inscription' => 'AuthController@register',
        
        // Collectivité
        '/mes-equipements/ajouter' => 'CollectiviteController@create',
        '/mes-equipements/modifier/{id}' => 'CollectiviteController@update',
        '/mes-equipements/supprimer/{id}' => 'CollectiviteController@delete',
        
        // Admin
        '/admin/utilisateur/role' => 'AdminController@updateRole',
        '/admin/equipement/approuver/{id}' => 'AdminController@approuver',
        '/admin/equipement/rejeter/{id}' => 'AdminController@rejeter',
        
        // Profil
        '/profil/update' => 'ProfilController@update',
        '/profil/password' => 'ProfilController@updatePassword',
        '/profil/notifications' => 'ProfilController@updateNotifications',
    ]
];

/**
 * Fonction pour faire correspondre une route avec des paramètres
 */
function matchRoute(string $path, array $routes): ?array {
    foreach ($routes as $route => $handler) {
        // Convertir la route en regex
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $route);
        $pattern = '#^' . $pattern . '$#';
        
        if (preg_match($pattern, $path, $matches)) {
            // Filtrer uniquement les paramètres nommés
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return [
                'handler' => $handler,
                'params' => $params
            ];
        }
    }
    return null;
}

// Recherche de la route correspondante
$match = null;
if (isset($routes[$method])) {
    $match = matchRoute($path, $routes[$method]);
}

if ($match) {
    // Extraction du contrôleur et de la méthode
    list($controllerName, $methodName) = explode('@', $match['handler']);
    $controllerFile = __DIR__ . '/../src/Controllers/' . $controllerName . '.php';
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $controller = new $controllerName();
        
        // Appel de la méthode avec les paramètres
        call_user_func_array([$controller, $methodName], $match['params']);
    } else {
        http_response_code(500);
        echo "Erreur: Contrôleur non trouvé";
    }
} else {
    // Page 404
    http_response_code(404);
    require_once __DIR__ . '/../src/Views/errors/404.php';
}
