<?php
/**
 * Modèle User - Gestion des utilisateurs
 */

require_once __DIR__ . '/../../config/database.php';

class User {
    private PDO $pdo;
    
    public function __construct() {
        $this->pdo = getDBConnection();
    }
    
    /**
     * Créer un nouvel utilisateur
     */
    public function create(array $data): bool {
        $sql = "INSERT INTO users (nom, email, password, role, collectivite_nom, commune) 
                VALUES (:nom, :email, :password, :role, :collectivite_nom, :commune)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'nom' => $data['nom'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'] ?? 'client',
            'collectivite_nom' => $data['collectivite_nom'] ?? null,
            'commune' => $data['commune'] ?? null
        ]);
    }
    
    /**
     * Trouver un utilisateur par email
     */
    public function findByEmail(string $email): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
    
    /**
     * Trouver un utilisateur par ID
     */
    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
    
    /**
     * Vérifier les identifiants
     */
    public function verifyCredentials(string $email, string $password): ?array {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return null;
    }
    
    /**
     * Mettre à jour le profil utilisateur
     */
    public function updateProfile(int $userId, array $data): bool {
        $sql = "UPDATE users SET 
                nom = :nom,
                collectivite_nom = :collectivite_nom,
                commune = :commune,
                notifications_email = :notifications_email,
                notifications_app = :notifications_app,
                frequence_notifications = :frequence_notifications
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $userId,
            'nom' => $data['nom'],
            'collectivite_nom' => $data['collectivite_nom'] ?? null,
            'commune' => $data['commune'] ?? null,
            'notifications_email' => $data['notifications_email'] ?? 1,
            'notifications_app' => $data['notifications_app'] ?? 1,
            'frequence_notifications' => $data['frequence_notifications'] ?? 'quotidien'
        ]);
    }
    
    /**
     * Changer le mot de passe
     */
    public function updatePassword(int $userId, string $newPassword): bool {
        $sql = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $userId,
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);
    }
    
    /**
     * Mettre à jour le rôle d'un utilisateur (admin uniquement)
     */
    public function updateRole(int $userId, string $role): bool {
        $sql = "UPDATE users SET role = :role WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $userId,
            'role' => $role
        ]);
    }
    
    /**
     * Récupérer tous les utilisateurs (admin)
     */
    public function getAll(?string $roleFilter = null): array {
        if ($roleFilter) {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE role = ? ORDER BY created_at DESC");
            $stmt->execute([$roleFilter]);
        } else {
            $stmt = $this->pdo->query("SELECT * FROM users ORDER BY created_at DESC");
        }
        
        return $stmt->fetchAll();
    }
    
    /**
     * Compter les utilisateurs par rôle
     */
    public function countByRole(): array {
        $stmt = $this->pdo->query("
            SELECT role, COUNT(*) as count 
            FROM users 
            GROUP BY role
        ");
        
        $counts = [
            'client' => 0,
            'collectivite' => 0,
            'admin' => 0
        ];
        
        foreach ($stmt->fetchAll() as $row) {
            $counts[$row['role']] = (int)$row['count'];
        }
        
        return $counts;
    }
    
    /**
     * Vérifier si un email existe déjà
     */
    public function emailExists(string $email): bool {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }
}
