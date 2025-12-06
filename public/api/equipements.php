<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

try {
    $pdo = getDBConnection();
    
    $stmt = $pdo->query("SELECT * FROM equipements WHERE statut = 'en_service'");
    $equipements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $result = array_map(function($equip) {
        return [
            'equip_numero' => 'LOCAL_' . $equip['id'],
            'equip_id' => 'LOCAL_' . $equip['id'],
            'equip_nom' => $equip['nom'],
            'inst_nom' => $equip['nom'],
            'equip_type_name' => $equip['type_equipement'],
            'new_name' => $equip['commune'],
            'arr_name' => $equip['commune'],
            'inst_cp' => $equip['code_postal'] ?? '',
            'inst_adresse' => $equip['adresse'] ?? '',
            'equip_surf' => $equip['surface'] ?? null,
            'equip_long' => $equip['longueur'] ?? null,
            'equip_larg' => $equip['largeur'] ?? null,
            'equip_pmr_acc' => $equip['accessible_pmr'] ? 'true' : 'false',
            'equip_coordonnees' => [
                'lat' => (float)$equip['latitude'],
                'lon' => (float)$equip['longitude']
            ],
            'dep_code' => substr($equip['code_postal'] ?? '00000', 0, 2),
            'telephone' => trim($equip['telephone'] ?? ''),
            'email' => trim($equip['email'] ?? ''),
            'source' => 'local'
        ];
    }, $equipements);
    
    echo json_encode(['results' => $result]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
