<?php
/**
 * Configuration de la base de données
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'equipements_sportifs');
define('DB_USER', 'root');
define('DB_PASS', ''); // À modifier selon ta config XAMPP

// Configuration de l'application
define('APP_NAME', 'Équipements Sportifs');
define('APP_URL', 'http://localhost/equipements_sportifs');

// Clé secrète pour les sessions
define('SECRET_KEY', 'change_this_secret_key_in_production_' . bin2hex(random_bytes(16)));

// API Équipements Sportifs
define('API_BASE_URL', 'https://equipements.sports.gouv.fr/api/explore/v2.1/catalog/datasets/data-es/records');

/**
 * Connexion à la base de données avec PDO
 */
function getDBConnection(): PDO {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

/**
 * Fonction pour démarrer la session de manière sécurisée
 */
function startSecureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'cookie_httponly' => true,
            'cookie_secure' => isset($_SERVER['HTTPS']),
            'use_strict_mode' => true
        ]);
    }
}

/**
 * Fonction pour vérifier si l'utilisateur est connecté
 */
function isLoggedIn(): bool {
    startSecureSession();
    return isset($_SESSION['user_id']);
}

/**
 * Fonction pour obtenir l'utilisateur connecté
 */
function getCurrentUser(): ?array {
    if (!isLoggedIn()) {
        return null;
    }
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

/**
 * Fonction pour vérifier le rôle de l'utilisateur
 */
function hasRole(string $role): bool {
    $user = getCurrentUser();
    if (!$user) return false;
    
    // Admin a accès à tout
    if ($user['role'] === 'admin') return true;
    
    // Collectivité a accès aux fonctions collectivité et client
    if ($user['role'] === 'collectivite' && in_array($role, ['collectivite', 'client'])) return true;
    
    // Sinon vérification exacte
    return $user['role'] === $role;
}

/**
 * Fonction pour rediriger
 */
function redirect(string $path): void {
    header("Location: /equipements_sportifs/public" . $path);
    exit;
}

/**
 * Fonction pour échapper les sorties HTML
 */
function e(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Fonction pour générer un token CSRF
 */
function generateCSRFToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Fonction pour vérifier le token CSRF
 */
function verifyCSRFToken(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Fonction pour afficher un message flash
 */
function setFlashMessage(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
