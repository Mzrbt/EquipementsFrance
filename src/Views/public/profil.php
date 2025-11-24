<div class="container" style="max-width: 800px;">
    <div class="page-header">
        <h1 class="page-title">Mon Profil</h1>
        <p class="page-subtitle">Gérez vos informations personnelles et préférences.</p>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informations personnelles</h3>
        </div>
        
        <form method="POST" action="/equipements_sportifs/public/profil/update">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="form-group">
                <label class="form-label">Nom complet</label>
                <input type="text" name="nom" class="form-input" value="<?= e($user['nom']) ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-input" value="<?= e($user['email']) ?>" disabled>
                <p class="form-hint">L'email ne peut pas être modifié</p>
            </div>
            
            <div class="form-group">
                <label class="form-label">Rôle</label>
                <input type="text" class="form-input" value="<?= ucfirst($user['role']) ?>" disabled>
            </div>
            
            <?php if ($user['role'] === 'collectivite'): ?>
                <div class="form-group">
                    <label class="form-label">Nom de la collectivité</label>
                    <input type="text" name="collectivite_nom" class="form-input" value="<?= e($user['collectivite_nom'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Commune</label>
                    <input type="text" name="commune" class="form-input" value="<?= e($user['commune'] ?? '') ?>">
                </div>
            <?php endif; ?>
            
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        </form>
    </div>
    
    <div class="card" style="margin-top: 2rem;">
        <div class="card-header">
            <h3 class="card-title">Sécurité du compte</h3>
        </div>
        
        <form method="POST" action="/equipements_sportifs/public/profil/password">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="form-group">
                <label class="form-label">Nouveau mot de passe</label>
                <input type="password" name="new_password" class="form-input" minlength="6" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Confirmer le mot de passe</label>
                <input type="password" name="confirm_password" class="form-input" minlength="6" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
        </form>
    </div>
</div>
