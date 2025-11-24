<div class="container">
    <div class="page-header">
        <h1 class="page-title">Équipements en attente d'approbation</h1>
        <p class="page-subtitle">Approuvez ou rejetez les équipements ajoutés par les collectivités.</p>
    </div>
    
    <?php if (empty($equipements)): ?>
        <div class="card text-center" style="padding: 3rem;">
            <p style="font-size: 1.125rem; color: var(--text-muted);">
                Aucun équipement en attente d'approbation.
            </p>
        </div>
    <?php else: ?>
        <div class="equipements-grid">
            <?php foreach ($equipements as $equip): ?>
                <div class="equipement-card">
                    <div class="equipement-card-header">
                        <div>
                            <h3 class="equipement-card-title"><?= e($equip['nom']) ?></h3>
                            <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.25rem;">
                                Par <?= e($equip['collectivite_nom'] ?? $equip['user_nom']) ?>
                            </p>
                        </div>
                        <span class="badge badge-warning">En attente</span>
                    </div>
                    
                    <p class="equipement-card-info"><strong>Type :</strong> <?= e($equip['type_equipement']) ?></p>
                    <p class="equipement-card-info"><strong>Commune :</strong> <?= e($equip['commune'] ?? '-') ?></p>
                    <p class="equipement-card-info"><strong>Adresse :</strong> <?= e($equip['adresse'] ?? '-') ?></p>
                    <?php if ($equip['surface']): ?>
                        <p class="equipement-card-info"><strong>Surface :</strong> <?= $equip['surface'] ?> m²</p>
                    <?php endif; ?>
                    <?php if ($equip['observations']): ?>
                        <p class="equipement-card-info"><strong>Observations :</strong> <?= e($equip['observations']) ?></p>
                    <?php endif; ?>
                    
                    <div class="equipement-card-actions">
                        <form method="POST" action="/equipements_sportifs/public/admin/equipement/approuver/<?= $equip['id'] ?>" style="display: inline;">
                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                            <button type="submit" class="btn btn-success btn-sm">Approuver</button>
                        </form>
                        <form method="POST" action="/equipements_sportifs/public/admin/equipement/rejeter/<?= $equip['id'] ?>" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir rejeter cet équipement ?');">
                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Rejeter</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
