<?php
// Test de connexion à la base de données
require_once '../config/Database.php';

header('Content-Type: application/json');

try {
    $db = new \Config\Database();
    $pdo = $db->getConnection();
    
    if ($pdo) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Connexion à la base de données réussie !'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'La connexion a retourné null'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}