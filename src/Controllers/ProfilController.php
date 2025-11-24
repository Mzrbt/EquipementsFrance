<?php
/**
 * Contrôleur Profil
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';

class ProfilController {
    private User $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Page profil
     */
    public function index(): void {
        AuthMiddleware::requireAuth();
        
        $user = getCurrentUser();
        
        $pageTitle = 'Mon Profil';
        $currentPage = 'profil';
        
        ob_start();
        include __DIR__ . '/../Views/public/profil.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../Views/layouts/base.php';
    }
    
    /**
     * Mettre à jour le profil
     */
    public function update(): void {
        AuthMiddleware::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/profil');
            return;
        }
        
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token invalide.');
            redirect('/profil');
            return;
        }
        
        $user = getCurrentUser();
        
        $data = [
            'nom' => trim($_POST['nom'] ?? ''),
            'collectivite_nom' => trim($_POST['collectivite_nom'] ?? ''),
            'commune' => trim($_POST['commune'] ?? ''),
            'notifications_email' => 1,
            'notifications_app' => 1,
            'frequence_notifications' => 'quotidien'
        ];
        
        if ($this->userModel->updateProfile($user['id'], $data)) {
            setFlashMessage('success', 'Profil mis à jour avec succès.');
        } else {
            setFlashMessage('error', 'Erreur lors de la mise à jour.');
        }
        
        redirect('/profil');
    }
    
    /**
     * Changer le mot de passe
     */
    public function updatePassword(): void {
        AuthMiddleware::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/profil');
            return;
        }
        
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token invalide.');
            redirect('/profil');
            return;
        }
        
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (strlen($newPassword) < 6) {
            setFlashMessage('error', 'Le mot de passe doit contenir au moins 6 caractères.');
            redirect('/profil');
            return;
        }
        
        if ($newPassword !== $confirmPassword) {
            setFlashMessage('error', 'Les mots de passe ne correspondent pas.');
            redirect('/profil');
            return;
        }
        
        $user = getCurrentUser();
        
        if ($this->userModel->updatePassword($user['id'], $newPassword)) {
            setFlashMessage('success', 'Mot de passe modifié avec succès.');
        } else {
            setFlashMessage('error', 'Erreur lors de la modification.');
        }
        
        redirect('/profil');
    }
}
