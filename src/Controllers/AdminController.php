<?php
/**
 * Contrôleur pour l'espace Admin
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Equipement.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';

class AdminController {
    private User $userModel;
    private Equipement $equipementModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->equipementModel = new Equipement();
    }
    
    /**
     * Dashboard administrateur
     */
    public function dashboard(): void {
        AuthMiddleware::requireAdmin();
        
        $stats = [
            'total_equipements' => count($this->equipementModel->getAll()),
            'equipements_en_attente' => count($this->equipementModel->getAll('en_attente')),
            'total_users' => count($this->userModel->getAll()),
            'users_by_role' => $this->userModel->countByRole()
        ];
        
        $equipements_counts = $this->equipementModel->countByStatut();
        
        $pageTitle = 'Tableau de bord administrateur';
        $currentPage = 'admin';
        
        ob_start();
        include __DIR__ . '/../Views/admin/dashboard.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../Views/layouts/base.php';
    }
    
    /**
     * Gestion des utilisateurs
     */
    public function utilisateurs(): void {
        AuthMiddleware::requireAdmin();
        
        $users = $this->userModel->getAll();
        
        $pageTitle = 'Gestion des utilisateurs';
        $currentPage = 'admin';
        
        ob_start();
        include __DIR__ . '/../Views/admin/utilisateurs.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../Views/layouts/base.php';
    }
    
    /**
     * Approbations des équipements
     */
    public function approbations(): void {
        AuthMiddleware::requireAdmin();
        
        $equipements = $this->equipementModel->getAll('en_attente');
        
        $pageTitle = 'Équipements en attente d\'approbation';
        $currentPage = 'admin';
        
        ob_start();
        include __DIR__ . '/../Views/admin/approbations.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../Views/layouts/base.php';
    }
    
    /**
     * Modifier le rôle d'un utilisateur
     */
    public function updateRole(): void {
        AuthMiddleware::requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/utilisateurs');
            return;
        }
        
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token invalide.');
            redirect('/admin/utilisateurs');
            return;
        }
        
        $userId = (int)($_POST['user_id'] ?? 0);
        $role = $_POST['role'] ?? '';
        
        if (!in_array($role, ['client', 'collectivite', 'admin'])) {
            setFlashMessage('error', 'Rôle invalide.');
            redirect('/admin/utilisateurs');
            return;
        }
        
        if ($this->userModel->updateRole($userId, $role)) {
            setFlashMessage('success', 'Rôle modifié avec succès.');
        } else {
            setFlashMessage('error', 'Erreur lors de la modification.');
        }
        
        redirect('/admin/utilisateurs');
    }
    
    /**
     * Approuver un équipement
     */
    public function approuver(string $id): void {
        AuthMiddleware::requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/approbations');
            return;
        }
        
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token invalide.');
            redirect('/admin/approbations');
            return;
        }
        
        if ($this->equipementModel->approve((int)$id)) {
            setFlashMessage('success', 'Équipement approuvé avec succès.');
        } else {
            setFlashMessage('error', 'Erreur lors de l\'approbation.');
        }
        
        redirect('/admin/approbations');
    }
    
    /**
     * Rejeter un équipement
     */
    public function rejeter(string $id): void {
        AuthMiddleware::requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/approbations');
            return;
        }
        
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token invalide.');
            redirect('/admin/approbations');
            return;
        }
        
        if ($this->equipementModel->reject((int)$id)) {
            setFlashMessage('success', 'Équipement rejeté et supprimé.');
        } else {
            setFlashMessage('error', 'Erreur lors du rejet.');
        }
        
        redirect('/admin/approbations');
    }
}
