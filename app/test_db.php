<?php
require_once 'config/Database.php';

try {
    $db = new Config\Database();
    $pdo = $db->getConnection();
    echo "Connexion à la base de données réussie !\n";
    
    // Test simple
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM patients");
    $result = $stmt->fetch();
    echo "Nombre de patients: " . $result['total'] . "\n";
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}