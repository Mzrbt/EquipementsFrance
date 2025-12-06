<div class="container" style="max-width: 800px;">
    <div class="page-header">
        <h1 class="page-title">Ajouter un Équipement</h1>
        <p class="page-subtitle">Renseignez les informations de votre équipement sportif.</p>
    </div>
    
    <div class="card">
        <form method="POST" action="/equipements_sportifs/public/mes-equipements/ajouter">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            
            <div class="form-group">
                <label class="form-label">Nom de l'équipement *</label>
                <input type="text" name="nom" class="form-input" required placeholder="Ex: Stade Municipal Jean Dupont">
            </div>
            
            <div class="form-group">
                <label class="form-label">Type d'équipement *</label>
                <select name="type_equipement" class="form-select" required>
                    <option value="">Sélectionnez un type</option>
                    <?php foreach ($typesEquipements as $type): ?>
                        <option value="<?= e($type['nom']) ?>"><?= e($type['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Adresse</label>
                <input type="text" name="adresse" class="form-input" placeholder="Ex: 12 rue du Sport">
            </div>
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Commune</label>
                    <input type="text" name="commune" class="form-input" placeholder="Ex: Paris">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Code postal</label>
                    <input type="text" name="code_postal" class="form-input" placeholder="Ex: 75001">
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Latitude</label>
                    <input type="number" step="0.000001" name="latitude" class="form-input" placeholder="Ex: 48.856614">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Longitude</label>
                    <input type="number" step="0.000001" name="longitude" class="form-input" placeholder="Ex: 2.352222">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Surface (m²)</label>
                <input type="number" step="0.01" name="surface" class="form-input" placeholder="Ex: 1200">
            </div>
            
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" name="accessible_pmr">
                    <span>Accessible aux personnes à mobilité réduite (PMR)</span>
                </label>
            </div>
            
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" name="acces_libre">
                    <span>Accès libre au public</span>
                </label>
            </div>
            
            <div class="form-group">
                <label class="form-label">Observations</label>
                <textarea name="observations" class="form-textarea" placeholder="Informations complémentaires..."></textarea>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary btn-lg">Créer l'équipement</button>
                <a href="/equipements_sportifs/public/mes-equipements" class="btn btn-outline btn-lg">Annuler</a>
            </div>
        </form>
    </div>

    <script>
    let geocodeTimer = null;

    function updateGeolocation() {
        const adresse = document.querySelector('input[name="adresse"]').value.trim();
        const commune = document.querySelector('input[name="commune"]').value.trim();
        const codePostal = document.querySelector('input[name="code_postal"]').value.trim();
        
        const adresseComplete = [adresse, codePostal, commune].filter(v => v).join(' ');
        
        if (adresseComplete.length < 5) return;
        
        clearTimeout(geocodeTimer);
        
        geocodeTimer = setTimeout(async () => {
            try {
                const response = await fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(adresseComplete)}&limit=1`);
                const data = await response.json();
                
                if (data.features && data.features.length > 0) {
                    const coords = data.features[0].geometry.coordinates;
                    
                    document.querySelector('input[name="longitude"]').value = coords[0].toFixed(6);
                    document.querySelector('input[name="latitude"]').value = coords[1].toFixed(6);
                    
                    document.querySelector('input[name="latitude"]').style.borderColor = '#22c55e';
                    document.querySelector('input[name="longitude"]').style.borderColor = '#22c55e';
                    
                    setTimeout(() => {
                        document.querySelector('input[name="latitude"]').style.borderColor = '';
                        document.querySelector('input[name="longitude"]').style.borderColor = '';
                    }, 2000);
                }
            } catch (error) {
                console.error('Erreur géocodage:', error);
            }
        }, 500);
    }
    document.querySelector('input[name="adresse"]')?.addEventListener('input', updateGeolocation);
    document.querySelector('input[name="commune"]')?.addEventListener('input', updateGeolocation);
    document.querySelector('input[name="code_postal"]')?.addEventListener('input', updateGeolocation);
    </script>
</div>
