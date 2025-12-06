<?php
/**
 * Modèle Equipement
 */

require_once __DIR__ . '/../../config/database.php';

class Equipement {
    private PDO $pdo;
    
    public function __construct() {
        $this->pdo = getDBConnection();
    }
    
    /**
     * Créer un équipement
     */
    public function create(array $data): bool {
        $sql = "INSERT INTO equipements (
            user_id, nom, type_equipement, statut, adresse, commune, code_postal,
            latitude, longitude, surface, accessible_pmr, acces_libre, observations,
            telephone, email
        ) VALUES (
            :user_id, :nom, :type_equipement, :statut, :adresse, :commune, :code_postal,
            :latitude, :longitude, :surface, :accessible_pmr, :acces_libre, :observations,
            :telephone, :email
        )";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'user_id' => $data['user_id'],
            'nom' => $data['nom'],
            'type_equipement' => $data['type_equipement'],
            'statut' => $data['statut'] ?? 'en_attente',
            'adresse' => $data['adresse'] ?? null,
            'commune' => $data['commune'] ?? null,
            'code_postal' => $data['code_postal'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'surface' => $data['surface'] ?? null,
            'accessible_pmr' => $data['accessible_pmr'] ?? 0,
            'acces_libre' => $data['acces_libre'] ?? 0,
            'observations' => $data['observations'] ?? null,
            'telephone' => $data['telephone'] ?? null,
            'email' => $data['email'] ?? null
        ]);
    }
    
    /**
     * Obtenir tous les équipements d'un utilisateur
     */
    public function getByUser(int $userId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM equipements WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtenir un équipement par ID
     */
    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM equipements WHERE id = ?");
        $stmt->execute([$id]);
        $equip = $stmt->fetch();
        return $equip ?: null;
    }
    
    /**
     * Mettre à jour un équipement
     */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE equipements SET
            nom = :nom,
            type_equipement = :type_equipement,
            statut = :statut,
            adresse = :adresse,
            commune = :commune,
            code_postal = :code_postal,
            latitude = :latitude,
            longitude = :longitude,
            surface = :surface,
            accessible_pmr = :accessible_pmr,
            acces_libre = :acces_libre,
            observations = :observations,
            telephone = :telephone,
            email = :email
            WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'nom' => $data['nom'],
            'type_equipement' => $data['type_equipement'],
            'statut' => $data['statut'],
            'adresse' => $data['adresse'] ?? null,
            'commune' => $data['commune'] ?? null,
            'code_postal' => $data['code_postal'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'surface' => $data['surface'] ?? null,
            'accessible_pmr' => $data['accessible_pmr'] ?? 0,
            'acces_libre' => $data['acces_libre'] ?? 0,
            'observations' => $data['observations'] ?? null,
            'telephone' => $data['telephone'] ?? null,
            'email' => $data['email'] ?? null
        ]);
    }
    
    /**
     * Supprimer un équipement
     */
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM equipements WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Obtenir tous les équipements (admin)
     */
    public function getAll(?string $statutFilter = null): array {
        if ($statutFilter) {
            $stmt = $this->pdo->prepare("
                SELECT e.*, u.nom as user_nom, u.collectivite_nom 
                FROM equipements e 
                JOIN users u ON e.user_id = u.id 
                WHERE e.statut = ? 
                ORDER BY e.created_at DESC
            ");
            $stmt->execute([$statutFilter]);
        } else {
            $stmt = $this->pdo->query("
                SELECT e.*, u.nom as user_nom, u.collectivite_nom 
                FROM equipements e 
                JOIN users u ON e.user_id = u.id 
                ORDER BY e.created_at DESC
            ");
        }
        return $stmt->fetchAll();
    }
    
    /**
     * Compter les équipements par statut
     */
    public function countByStatut(): array {
        $stmt = $this->pdo->query("
            SELECT statut, COUNT(*) as count 
            FROM equipements 
            GROUP BY statut
        ");
        
        $counts = [
            'en_attente' => 0,
            'en_service' => 0,
            'en_travaux' => 0,
            'ferme' => 0
        ];
        
        foreach ($stmt->fetchAll() as $row) {
            $counts[$row['statut']] = (int)$row['count'];
        }
        
        return $counts;
    }
    
    /**
     * Approuver un équipement
     */
    public function approve(int $id): bool {
        $stmt = $this->pdo->prepare("UPDATE equipements SET statut = 'en_service' WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Rejeter un équipement
     */
    public function reject(int $id): bool {
        return $this->delete($id);
    }
}
