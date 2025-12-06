<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

$dep_code = $_GET['dep_code'] ?? null;
$commune = $_GET['commune'] ?? null;

if (!$dep_code) {
    echo json_encode(['error' => 'Département requis']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Chercher d'abord par commune exacte
    if ($commune) {
        $stmt = $pdo->prepare("SELECT * FROM collectivites_contacts WHERE dep_code = ? AND commune = ?");
        $stmt->execute([$dep_code, $commune]);
        $contact = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($contact) {
            echo json_encode($contact);
            exit;
        }
    }
    
    // Si pas trouvé, chercher par département seulement
    $stmt = $pdo->prepare("SELECT * FROM collectivites_contacts WHERE dep_code = ? AND commune IS NULL LIMIT 1");
    $stmt->execute([$dep_code]);
    $contact = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($contact) {
        echo json_encode($contact);
    } else {
        echo json_encode(['error' => 'Contact non trouvé']);
    }
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}