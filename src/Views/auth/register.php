<div class="container" style="max-width: 580px; margin-top: 4rem;">
    <div class="card">
        <div class="card-header text-center">
            <h1 class="card-title">Créer un compte</h1>
            <p class="card-subtitle">Rejoignez la plateforme de gestion des équipements sportifs</p>
        </div>
        
        <form method="POST" action="/equipements_sportifs/public/inscription">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="form-group">
                <label class="form-label">Nom complet</label>
                <input 
                    type="text" 
                    name="nom" 
                    class="form-input" 
                    placeholder="Jean Dupont"
                    required
                    autofocus
                >
            </div>
            
            <div class="form-group">
                <label class="form-label">Adresse e-mail</label>
                <input 
                    type="email" 
                    name="email" 
                    class="form-input" 
                    placeholder="votre@email.com"
                    required
                >
            </div>
            
            <div class="form-group">
                <label class="form-label">Mot de passe</label>
                <input 
                    type="password" 
                    name="password" 
                    class="form-input" 
                    placeholder="Minimum 6 caractères"
                    required
                    minlength="6"
                >
                <p class="form-hint">Le mot de passe doit contenir au moins 6 caractères</p>
            </div>
            
            <div class="form-group">
                <label class="form-label">Confirmer le mot de passe</label>
                <input 
                    type="password" 
                    name="password_confirm" 
                    class="form-input" 
                    placeholder="Confirmez votre mot de passe"
                    required
                >
            </div>
            
            <div class="form-group">
                <label class="form-label">Type de compte</label>
                <select name="role" class="form-select" id="role-select" onchange="toggleCollectiviteFields()">
                    <option value="client">Client (Consultation uniquement)</option>
                    <option value="collectivite">Collectivité (Gestion d'équipements)</option>
                </select>
            </div>
            
            <!-- Champs spécifiques aux collectivités -->
            <div id="collectivite-fields" style="display: none;">
                <div class="form-group">
                    <label class="form-label">Nom de la collectivité <span style="color: var(--danger);">*</span></label>
                    <input 
                        type="text" 
                        name="collectivite_nom" 
                        class="form-input" 
                        placeholder="Ex: Mairie de Paris, CC de la Dombes..."
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label">Commune (optionnel)</label>
                    <input 
                        type="text" 
                        name="commune" 
                        class="form-input" 
                        placeholder="Ex: Paris, Lyon..."
                    >
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block btn-lg">
                Créer mon compte
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
            <p class="text-muted">Vous avez déjà un compte ?</p>
            <a href="/equipements_sportifs/public/connexion" class="btn btn-outline">Se connecter</a>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 2rem;">
        <a href="/equipements_sportifs/public/" class="text-muted">← Retour à l'accueil</a>
    </div>
</div>

<script>
function toggleCollectiviteFields() {
    const role = document.getElementById('role-select').value;
    const collectiviteFields = document.getElementById('collectivite-fields');
    
    if (role === 'collectivite') {
        collectiviteFields.style.display = 'block';
        document.querySelector('input[name="collectivite_nom"]').required = true;
    } else {
        collectiviteFields.style.display = 'none';
        document.querySelector('input[name="collectivite_nom"]').required = false;
    }
}
</script>
