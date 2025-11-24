<div class="container" style="max-width: 480px; margin-top: 4rem;">
    <div class="card">
        <div class="card-header text-center">
            <h1 class="card-title">Connexion</h1>
            <p class="card-subtitle">Connectez-vous pour accéder à votre espace</p>
        </div>
        
        <form method="POST" action="/equipements_sportifs/public/connexion">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="form-group">
                <label class="form-label">Adresse e-mail</label>
                <input 
                    type="email" 
                    name="email" 
                    class="form-input" 
                    placeholder="votre@email.com"
                    required
                    autofocus
                >
            </div>
            
            <div class="form-group">
                <label class="form-label">Mot de passe</label>
                <input 
                    type="password" 
                    name="password" 
                    class="form-input" 
                    placeholder="Votre mot de passe"
                    required
                >
            </div>
            
            <button type="submit" class="btn btn-primary btn-block btn-lg">
                Se connecter
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
            <p class="text-muted">Pas encore de compte ?</p>
            <a href="/equipements_sportifs/public/inscription" class="btn btn-outline">Créer un compte</a>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 2rem;">
        <a href="/equipements_sportifs/public/" class="text-muted">← Retour à l'accueil</a>
    </div>
</div>
