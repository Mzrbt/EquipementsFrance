<?php
/**
 * Middleware d'authentification
 */

require_once __DIR__ . '/../../config/database.php';

class AuthMiddleware {
    
    /**
     * Vérifier que l'utilisateur est connecté
     */
    public static function requireAuth(): void {
        if (!isLoggedIn()) {
            setFlashMessage('error', 'Vous devez être connecté pour accéder à cette page.');
            redirect('/connexion');
        }
    }
    
    /**
     * Vérifier que l'utilisateur a le rôle requis
     */
    public static function requireRole(string $role): void {
        self::requireAuth();
        
        if (!hasRole($role)) {
            setFlashMessage('error', 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
            redirect('/');
        }
    }
    
    /**
     * Vérifier que l'utilisateur est admin
     */
    public static function requireAdmin(): void {
        self::requireRole('admin');
    }
    
    /**
     * Vérifier que l'utilisateur est collectivité
     */
    public static function requireCollectivite(): void {
        self::requireRole('collectivite');
    }
}
