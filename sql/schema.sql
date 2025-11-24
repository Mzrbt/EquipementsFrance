-- Base de données : equipements_sportifs
-- Création de la base
CREATE DATABASE IF NOT EXISTS equipements_sportifs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE equipements_sportifs;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('client', 'collectivite', 'admin') DEFAULT 'client',
    collectivite_nom VARCHAR(255) DEFAULT NULL,
    commune VARCHAR(255) DEFAULT NULL,
    notifications_email TINYINT(1) DEFAULT 1,
    notifications_app TINYINT(1) DEFAULT 1,
    frequence_notifications ENUM('immediat', 'quotidien', 'hebdomadaire') DEFAULT 'quotidien',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des équipements (stockage local des équipements ajoutés par les collectivités)
CREATE TABLE IF NOT EXISTS equipements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nom VARCHAR(255) NOT NULL,
    type_equipement VARCHAR(100) NOT NULL,
    statut ENUM('en_attente', 'en_service', 'en_travaux', 'ferme') DEFAULT 'en_attente',
    
    -- Localisation
    adresse VARCHAR(255),
    commune VARCHAR(100),
    code_postal VARCHAR(10),
    departement VARCHAR(100),
    region VARCHAR(100),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    
    -- Caractéristiques structurelles
    longueur DECIMAL(10, 2),
    largeur DECIMAL(10, 2),
    surface DECIMAL(10, 2),
    hauteur DECIMAL(10, 2),
    type_sol VARCHAR(100),
    nature_equipement VARCHAR(100), -- Intérieur, Extérieur, Site naturel
    
    -- Capacité et aménagements
    capacite_tribunes INT DEFAULT 0,
    nb_vestiaires INT DEFAULT 0,
    eclairage TINYINT(1) DEFAULT 0,
    douches TINYINT(1) DEFAULT 0,
    sanitaires TINYINT(1) DEFAULT 0,
    
    -- Accessibilité
    accessible_pmr TINYINT(1) DEFAULT 0,
    acces_libre TINYINT(1) DEFAULT 0,
    
    -- Propriétaire / Gestionnaire
    type_proprietaire VARCHAR(100),
    type_gestionnaire VARCHAR(100),
    
    -- Autres
    url VARCHAR(255),
    observations TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table des notifications
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('approbation', 'rejet', 'modification', 'systeme') NOT NULL,
    titre VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    lu TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table des types d'équipements (pour les filtres)
CREATE TABLE IF NOT EXISTS types_equipements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,
    famille VARCHAR(100)
) ENGINE=InnoDB;

-- Insertion des types d'équipements courants
INSERT INTO types_equipements (nom, famille) VALUES
('Gymnase', 'Salle'),
('Stade', 'Terrain de grands jeux'),
('Terrain extérieur', 'Terrain de grands jeux'),
('Piscine', 'Bassin de natation'),
('Court de tennis', 'Court de tennis'),
('Salle polyvalente', 'Salle non spécialisée'),
('Terrain de football', 'Terrain de grands jeux'),
('Terrain de basketball', 'Plateau EPS'),
('Boulodrome', 'Terrain de petits jeux'),
('Skatepark', 'Equipement de sports urbains'),
('Parcours de santé', 'Divers équipements Sports de nature'),
('Salle de musculation', 'Salle de remise en forme'),
('Dojo', 'Salle spécialisée'),
('Mur d''escalade', 'Salle spécialisée');

-- Création d'un utilisateur admin par défaut (mot de passe: admin123)
INSERT INTO users (nom, email, password, role) VALUES
('Administrateur', 'admin@equipements.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Index pour optimiser les recherches
CREATE INDEX idx_equipements_commune ON equipements(commune);
CREATE INDEX idx_equipements_type ON equipements(type_equipement);
CREATE INDEX idx_equipements_statut ON equipements(statut);
CREATE INDEX idx_equipements_coords ON equipements(latitude, longitude);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_notifications_user ON notifications(user_id, lu);
