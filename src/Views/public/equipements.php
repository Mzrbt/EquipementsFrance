<div class="container">
    <div class="page-header">
        <h1 class="page-title">Liste des Équipements</h1>
        <p class="page-subtitle">Consultez les équipements sportifs approuvés de votre région.</p>
    </div>
    
    <?php
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM equipements WHERE statut = 'en_service' ORDER BY created_at DESC");
    $equipements = $stmt->fetchAll();
    ?>
    
    <?php if (empty($equipements)): ?>
        <div class="card text-center" style="padding: 3rem;">
            <p style="font-size: 1.125rem; color: var(--text-muted);">
                Aucun équipement approuvé pour le moment.
            </p>
            <p class="text-muted">Les équipements ajoutés par les collectivités apparaîtront ici une fois approuvés.</p>
        </div>
    <?php else: ?>
        <div class="equipements-grid">
            <?php foreach ($equipements as $equip): ?>
                <div class="equipement-card">
                    <div class="equipement-card-header">
                        <div>
                            <h3 class="equipement-card-title"><?= e($equip['nom']) ?></h3>
                        </div>
                        <span class="badge badge-success">En service</span>
                    </div>
                    
                    <p class="equipement-card-info"><strong>Type :</strong> <?= e($equip['type_equipement']) ?></p>
                    <p class="equipement-card-info"><strong>Commune :</strong> <?= e($equip['commune'] ?? '-') ?></p>
                    <p class="equipement-card-info"><strong>Adresse :</strong> <?= e($equip['adresse'] ?? '-') ?></p>
                    <?php if ($equip['surface']): ?>
                        <p class="equipement-card-info"><strong>Surface :</strong> <?= $equip['surface'] ?> m²</p>
                    <?php endif; ?>
                    
                    <?php if ($equip['accessible_pmr']): ?>
                        <span class="badge badge-success" style="margin-top: 0.5rem;">♿ Accessible PMR</span>
                    <?php endif; ?>
                    <?php if ($equip['acces_libre']): ?>
                        <span class="badge badge-primary" style="margin-top: 0.5rem;">🔓 Accès libre</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
