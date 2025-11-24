<div class="container">
    <div class="page-header">
        <h1 class="page-title">Gestion des utilisateurs</h1>
        <p class="page-subtitle">Modifiez les rôles et gérez les comptes utilisateurs.</p>
    </div>
    
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Collectivité</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= e($user['nom']) ?></td>
                            <td><?= e($user['email']) ?></td>
                            <td>
                                <span class="badge badge-primary"><?= ucfirst($user['role']) ?></span>
                            </td>
                            <td><?= e($user['collectivite_nom'] ?? '-') ?></td>
                            <td>
                                <form method="POST" action="/equipements_sportifs/public/admin/utilisateur/role" style="display: inline-flex; gap: 0.5rem; align-items: center;">
                                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <select name="role" class="form-select" style="padding: 0.375rem 0.5rem; font-size: 0.875rem;">
                                        <option value="client" <?= $user['role'] === 'client' ? 'selected' : '' ?>>Client</option>
                                        <option value="collectivite" <?= $user['role'] === 'collectivite' ? 'selected' : '' ?>>Collectivité</option>
                                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <button type="submit" class="btn btn-outline btn-sm">Modifier</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
