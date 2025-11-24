<div class="container">
    <div class="page-header">
        <h1 class="page-title">Tableau de bord administrateur</h1>
        <p class="page-subtitle">Gérez les utilisateurs, approuvez les équipements et consultez les statistiques.</p>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-label">Total équipements</div>
            <div class="stat-card-value"><?= $stats['total_equipements'] ?></div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-label">En attente</div>
            <div class="stat-card-value" style="color: var(--warning);"><?= $stats['equipements_en_attente'] ?></div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-label">Total utilisateurs</div>
            <div class="stat-card-value"><?= $stats['total_users'] ?></div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-label">Dernière mise à jour</div>
            <div style="font-size: 0.875rem; color: var(--text-muted);"><?= date('d/m/Y H:i') ?></div>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Équipements par statut</h3>
            </div>
            <div style="padding: 1rem 0;">
                <?php foreach ($equipements_counts as $statut => $count): ?>
                    <div style="display: flex; justify-between; padding: 0.75rem 0; border-bottom: 1px solid var(--border-color);">
                        <span><?= ucfirst(str_replace('_', ' ', $statut)) ?></span>
                        <strong><?= $count ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Utilisateurs par rôle</h3>
            </div>
            <div style="padding: 1rem 0;">
                <?php foreach ($stats['users_by_role'] as $role => $count): ?>
                    <div style="display: flex; justify-between; padding: 0.75rem 0; border-bottom: 1px solid var(--border-color);">
                        <span><?= ucfirst($role) ?></span>
                        <strong><?= $count ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <div style="margin-top: 2rem; display: flex; gap: 1rem;">
        <a href="/equipements_sportifs/public/admin/utilisateurs" class="btn btn-primary">Gérer les utilisateurs</a>
        <a href="/equipements_sportifs/public/admin/approbations" class="btn btn-warning">Voir les approbations (<?= $stats['equipements_en_attente'] ?>)</a>
    </div>
</div>
