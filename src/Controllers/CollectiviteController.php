<?php
/**
 * Contrôleur pour l'espace Collectivité
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Equipement.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';

class CollectiviteController {
    private Equipement $equipementModel;
    
    public function __construct() {
        $this->equipementModel = new Equipement();
    }
    
    /**
     * Page d'accueil collectivité - Liste des équipements
     */
    public function index(): void {
        AuthMiddleware::requireCollectivite();
        
        $user = getCurrentUser();
        $equipements = $this->equipementModel->getByUser($user['id']);
        
        $pageTitle = 'Mes Équipements';
        $currentPage = 'mes-equipements';
        
        ob_start();
        include __DIR__ . '/../Views/collectivite/index.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../Views/layouts/base.php';
    }
    
    /**
     * Formulaire d'ajout d'équipement
     */
    public function createForm(): void {
        AuthMiddleware::requireCollectivite();
        
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT * FROM types_equipements ORDER BY nom");
        $typesEquipements = $stmt->fetchAll();
        
        $pageTitle = 'Ajouter un Équipement';
        $currentPage = 'mes-equipements';
        
        ob_start();
        include __DIR__ . '/../Views/collectivite/create.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../Views/layouts/base.php';
    }
    
    /**
     * Traiter l'ajout d'équipement
     */
    public function create(): void {
        AuthMiddleware::requireCollectivite();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/mes-equipements');
            return;
        }
        
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de sécurité invalide.');
            redirect('/mes-equipements/ajouter');
            return;
        }
        
        $user = getCurrentUser();
        
        $data = [
            'user_id' => $user['id'],
            'nom' => trim($_POST['nom'] ?? ''),
            'type_equipement' => $_POST['type_equipement'] ?? '',
            'statut' => 'en_attente',
            'adresse' => trim($_POST['adresse'] ?? ''),
            'commune' => trim($_POST['commune'] ?? ''),
            'code_postal' => trim($_POST['code_postal'] ?? ''),
            'latitude' => !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null,
            'longitude' => !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null,
            'surface' => !empty($_POST['surface']) ? (float)$_POST['surface'] : null,
            'accessible_pmr' => isset($_POST['accessible_pmr']) ? 1 : 0,
            'acces_libre' => isset($_POST['acces_libre']) ? 1 : 0,
            'observations' => trim($_POST['observations'] ?? ''),
            'telephone' => trim($_POST['telephone'] ?? ''),
            'email' => trim($_POST['email'] ?? '')
        ];
        
        if (empty($data['nom']) || empty($data['type_equipement'])) {
            setFlashMessage('error', 'Le nom et le type d\'équipement sont obligatoires.');
            redirect('/mes-equipements/ajouter');
            return;
        }
        
        if ($this->equipementModel->create($data)) {
            setFlashMessage('success', 'Équipement créé avec succès ! Il sera examiné par un administrateur.');
            redirect('/mes-equipements');
        } else {
            setFlashMessage('error', 'Erreur lors de la création de l\'équipement.');
            redirect('/mes-equipements/ajouter');
        }
    }
    
    /**
     * Formulaire de modification
     */
    public function editForm(string $id): void {
        AuthMiddleware::requireCollectivite();
        
        $user = getCurrentUser();
        $equipement = $this->equipementModel->getById((int)$id);
        
        if (!$equipement || $equipement['user_id'] != $user['id']) {
            setFlashMessage('error', 'Équipement non trouvé.');
            redirect('/mes-equipements');
            return;
        }
        
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT * FROM types_equipements ORDER BY nom");
        $typesEquipements = $stmt->fetchAll();
        
        $pageTitle = 'Modifier l\'Équipement';
        $currentPage = 'mes-equipements';
        
        ob_start();
        include __DIR__ . '/../Views/collectivite/edit.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../Views/layouts/base.php';
    }
    
    /**
     * Traiter la modification
     */
    public function update(string $id): void {
        AuthMiddleware::requireCollectivite();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/mes-equipements');
            return;
        }
        
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de sécurité invalide.');
            redirect('/mes-equipements/modifier/' . $id);
            return;
        }
        
        $user = getCurrentUser();
        $equipement = $this->equipementModel->getById((int)$id);
        
        if (!$equipement || $equipement['user_id'] != $user['id']) {
            setFlashMessage('error', 'Équipement non trouvé.');
            redirect('/mes-equipements');
            return;
        }
        
        $data = [
            'nom' => trim($_POST['nom'] ?? ''),
            'type_equipement' => $_POST['type_equipement'] ?? '',
            'statut' => $equipement['statut'],
            'adresse' => trim($_POST['adresse'] ?? ''),
            'commune' => trim($_POST['commune'] ?? ''),
            'code_postal' => trim($_POST['code_postal'] ?? ''),
            'latitude' => !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null,
            'longitude' => !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null,
            'surface' => !empty($_POST['surface']) ? (float)$_POST['surface'] : null,
            'accessible_pmr' => isset($_POST['accessible_pmr']) ? 1 : 0,
            'acces_libre' => isset($_POST['acces_libre']) ? 1 : 0,
            'observations' => trim($_POST['observations'] ?? ''),
            'telephone' => trim($_POST['telephone'] ?? ''),
            'email' => trim($_POST['email'] ?? '')
        ];
        
        if ($this->equipementModel->update((int)$id, $data)) {
            setFlashMessage('success', 'Équipement modifié avec succès !');
            redirect('/mes-equipements');
        } else {
            setFlashMessage('error', 'Erreur lors de la modification.');
            redirect('/mes-equipements/modifier/' . $id);
        }
    }
    
    /**
     * Supprimer un équipement
     */
    public function delete(string $id): void {
        AuthMiddleware::requireCollectivite();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/mes-equipements');
            return;
        }
        
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            setFlashMessage('error', 'Token de sécurité invalide.');
            redirect('/mes-equipements');
            return;
        }
        
        $user = getCurrentUser();
        $equipement = $this->equipementModel->getById((int)$id);
        
        if (!$equipement || $equipement['user_id'] != $user['id']) {
            setFlashMessage('error', 'Équipement non trouvé.');
            redirect('/mes-equipements');
            return;
        }
        
        if ($this->equipementModel->delete((int)$id)) {
            setFlashMessage('success', 'Équipement supprimé avec succès.');
        } else {
            setFlashMessage('error', 'Erreur lors de la suppression.');
        }
        
        redirect('/mes-equipements');
    }
}
