# Équipements Sportifs - Application de Gestion

## 📦 Installation

### 1. Extraire le projet
```bash
cd /opt/lampp/htdocs/
# Extraire le ZIP ici
```

### 2. Créer la base de données
```bash
/opt/lampp/bin/mysql -u root < /opt/lampp/htdocs/equipements_sportifs/sql/schema.sql
```

### 3. Démarrer XAMPP
```bash
sudo /opt/lampp/lampp start
```

### 4. Accéder à l'application
Ouvrir dans le navigateur : **http://localhost/equipements_sportifs/public/**

---

## 🔐 Comptes par défaut

### Administrateur
- **Email** : admin@equipements.fr
- **Mot de passe** : admin123

---

## 📂 Structure

```
equipements_sportifs/
├── assets/           # CSS et JavaScript
├── config/           # Configuration BDD
├── public/           # Point d'entrée (index.php)
├── sql/              # Schéma base de données
└── src/
    ├── Controllers/  # Logique métier
    ├── Models/       # Accès base de données
    ├── Middleware/   # Protection des routes
    └── Views/        # Templates HTML
```

---

## 🎯 Fonctionnalités

### Pour les Clients (visiteurs)
- ✅ Consulter les équipements sur une carte interactive
- ✅ Filtrer par type, commune, statut
- ✅ Voir les détails des équipements

### Pour les Collectivités
- ✅ Ajouter des équipements sportifs
- ✅ Modifier ses équipements
- ✅ Supprimer ses équipements
- ✅ Suivre le statut (en attente, approuvé, etc.)

### Pour les Administrateurs
- ✅ Tableau de bord avec statistiques
- ✅ Approuver/rejeter les équipements en attente
- ✅ Gérer les utilisateurs
- ✅ Modifier les rôles des utilisateurs

---

## 🚀 Développé avec
- PHP 8.2
- MySQL/MariaDB
- Leaflet.js (cartes interactives)
- API Équipements Sportifs (data.gouv.fr)

---

## 📝 Notes
- Les équipements ajoutés par les collectivités sont en attente d'approbation
- Seuls les admins peuvent approuver les équipements
- La carte affiche à la fois les équipements locaux et ceux de l'API
