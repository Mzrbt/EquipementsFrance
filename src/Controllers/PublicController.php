<?php
/**
 * Contrôleur pour les pages publiques
 */

require_once __DIR__ . '/../../config/database.php';

class PublicController {
    
    /**
     * Page d'accueil avec la carte
     */
    public function index(): void {
        $this->carte();
    }
    
    /**
     * Page carte
     */
    public function carte(): void {
        $pageTitle = 'Carte des équipements';
        $currentPage = 'carte';
        
        // Récupérer les types d'équipements pour les filtres
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT * FROM types_equipements ORDER BY nom");
        $typesEquipements = $stmt->fetchAll();
        
        // Charger la vue
        ob_start();
        include __DIR__ . '/../Views/public/carte.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../Views/layouts/base.php';
    }
    
    /**
     * Liste des équipements
     */
    public function equipements(): void {
        $pageTitle = 'Liste des équipements';
        $currentPage = 'equipements';
        
        // Récupérer les types d'équipements pour les filtres
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT * FROM types_equipements ORDER BY nom");
        $typesEquipements = $stmt->fetchAll();
        
        ob_start();
        include __DIR__ . '/../Views/public/equipements.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../Views/layouts/base.php';
    }
    
    /**
     * Détail d'un équipement
     */
    public function equipementDetail(string $id): void {
        $pageTitle = 'Détail équipement';
        $currentPage = 'equipements';
        
        ob_start();
        include __DIR__ . '/../Views/public/equipement-detail.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../Views/layouts/base.php';
    }
    
    /**
     * Documentation API
     */
    public function apiDoc(): void {
        $pageTitle = 'Documentation API';
        $currentPage = 'api';
        
        ob_start();
        include __DIR__ . '/../Views/public/api.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../Views/layouts/base.php';
    }
}
