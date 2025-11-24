<div class="container">
    <div class="page-header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="page-title">Gestion des Équipements Sportifs</h1>
                <p class="page-subtitle">Ajoutez et gérez les équipements sportifs de votre collectivité.</p>
            </div>
            <a href="/equipements_sportifs/public/mes-equipements/ajouter" class="btn btn-primary">
                + Ajouter un Équipement
            </a>
        </div>
    </div>
    
    <?php if (empty($equipements)): ?>
        <div class="card text-center" style="padding: 3rem;">
            <p style="font-size: 1.125rem; color: var(--text-muted); margin-bottom: 1.5rem;">
                Vous n'avez pas encore ajouté d'équipement.
            </p>
            <a href="/equipements_sportifs/public/mes-equipements/ajouter" class="btn btn-primary">
                Ajouter mon premier équipement
            </a>
        </div>
    <?php else: ?>
        <div class="equipements-grid">
            <?php foreach ($equipements as $equip): ?>
                <div class="equipement-card">
                    <div class="equipement-card-header">
                        <div>
                            <h3 class="equipement-card-title"><?= e($equip['nom']) ?></h3>
                        </div>
                        <?php
                        $badgeClass = match($equip['statut']) {
                            'en_service' => 'badge-success',
                            'en_attente' => 'badge-warning',
                            'en_travaux' => 'badge-warning',
                            'ferme' => 'badge-danger',
                            default => 'badge-secondary'
                        };
                        $statutLabel = match($equip['statut']) {
                            'en_service' => 'En service',
                            'en_attente' => 'En attente',
                            'en_travaux' => 'En travaux',
                            'ferme' => 'Fermé',
                            default => $equip['statut']
                        };
                        ?>
                        <span class="badge <?= $badgeClass ?>"><?= $statutLabel ?></span>
                    </div>
                    
                    <p class="equipement-card-info"><strong>Type :</strong> <?= e($equip['type_equipement']) ?></p>
                    <p class="equipement-card-info"><strong>Commune :</strong> <?= e($equip['commune'] ?? '-') ?></p>
                    <p class="equipement-card-info"><strong>Adresse :</strong> <?= e($equip['adresse'] ?? '-') ?></p>
                    <?php if ($equip['surface']): ?>
                        <p class="equipement-card-info"><strong>Surface :</strong> <?= $equip['surface'] ?> m²</p>
                    <?php endif; ?>
                    
                    <div class="equipement-card-actions">
                        <a href="/equipements_sportifs/public/mes-equipements/modifier/<?= $equip['id'] ?>" class="btn btn-outline btn-sm">
                            Modifier
                        </a>
                        <form method="POST" action="/equipements_sportifs/public/mes-equipements/supprimer/<?= $equip['id'] ?>" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet équipement ?');">
                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
