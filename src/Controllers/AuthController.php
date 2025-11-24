<?php
/**
 * Contrôleur d'authentification
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/User.php';

class AuthController {
    private User $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Afficher le formulaire de connexion
     */
    public function loginForm(): void {
        // Si déjà connecté, rediriger
        if (isLoggedIn()) {
            $user = getCurrentUser();
            if ($user['role'] === 'admin') {
                redirect('/admin');
            } elseif ($user['role'] === 'collectivite') {
                redirect('/mes-equipements');
            } else {
                redirect('/');
            }
            return;
        }
        
        $pageTitle = 'Connexion';
        $currentPage = 'connexion';
        
        ob_start();
        include __DIR__ . '/../Views/auth/login.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../Views/layouts/base.php';
    }
    
    /**
     * Traiter la connexion
     */
    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/connexion');
            return;
        }
        
        // Vérifier le token CSRF
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('error', 'Token de sécurité invalide.');
            redirect('/connexion');
            return;
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validation
        if (empty($email) || empty($password)) {
            setFlashMessage('error', 'Veuillez remplir tous les champs.');
            redirect('/connexion');
            return;
        }
        
        // Vérifier les identifiants
        $user = $this->userModel->verifyCredentials($email, $password);
        
        if (!$user) {
            setFlashMessage('error', 'Email ou mot de passe incorrect.');
            redirect('/connexion');
            return;
        }
        
        // Connexion réussie
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        
        setFlashMessage('success', 'Connexion réussie ! Bienvenue ' . $user['nom']);
        
        // Redirection selon le rôle
        if ($user['role'] === 'admin') {
            redirect('/admin');
        } elseif ($user['role'] === 'collectivite') {
            redirect('/mes-equipements');
        } else {
            redirect('/');
        }
    }
    
    /**
     * Afficher le formulaire d'inscription
     */
    public function registerForm(): void {
        // Si déjà connecté, rediriger
        if (isLoggedIn()) {
            redirect('/');
            return;
        }
        
        $pageTitle = 'Inscription';
        $currentPage = 'inscription';
        
        ob_start();
        include __DIR__ . '/../Views/auth/register.php';
        $content = ob_get_clean();
        
        include __DIR__ . '/../Views/layouts/base.php';
    }
    
    /**
     * Traiter l'inscription
     */
    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/inscription');
            return;
        }
        
        // Vérifier le token CSRF
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            setFlashMessage('error', 'Token de sécurité invalide.');
            redirect('/inscription');
            return;
        }
        
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $role = $_POST['role'] ?? 'client';
        $collectiviteNom = trim($_POST['collectivite_nom'] ?? '');
        $commune = trim($_POST['commune'] ?? '');
        
        // Validation
        $errors = [];
        
        if (empty($nom)) {
            $errors[] = 'Le nom est obligatoire.';
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide.';
        }
        
        if (empty($password) || strlen($password) < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
        }
        
        if ($password !== $passwordConfirm) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }
        
        if (!in_array($role, ['client', 'collectivite'])) {
            $errors[] = 'Rôle invalide.';
        }
        
        if ($role === 'collectivite' && empty($collectiviteNom)) {
            $errors[] = 'Le nom de la collectivité est obligatoire.';
        }
        
        // Vérifier si l'email existe déjà
        if ($this->userModel->emailExists($email)) {
            $errors[] = 'Cet email est déjà utilisé.';
        }
        
        if (!empty($errors)) {
            setFlashMessage('error', implode(' ', $errors));
            redirect('/inscription');
            return;
        }
        
        // Créer l'utilisateur
        $created = $this->userModel->create([
            'nom' => $nom,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'collectivite_nom' => $role === 'collectivite' ? $collectiviteNom : null,
            'commune' => $commune
        ]);
        
        if ($created) {
            setFlashMessage('success', 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.');
            redirect('/connexion');
        } else {
            setFlashMessage('error', 'Erreur lors de la création du compte.');
            redirect('/inscription');
        }
    }
    
    /**
     * Déconnexion
     */
    public function logout(): void {
        session_destroy();
        setFlashMessage('success', 'Vous êtes déconnecté.');
        redirect('/');
    }
}
