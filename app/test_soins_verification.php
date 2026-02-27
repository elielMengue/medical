<?php
// Test de vérification des soins dans la base de données
require_once 'config/database.php';
require_once 'models/Soins.php';

use Config\Database;

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "=== Vérification des soins dans la base de données ===\n\n";
    
    // Récupérer tous les soins du patient 1
    $query = "SELECT * FROM soins WHERE patient_id = 1 ORDER BY date_soin DESC, heure_soin DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $soins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Nombre total de soins pour le patient 1 : " . count($soins) . "\n\n";
    
    if(!empty($soins)) {
        echo "Détails de tous les soins :\n";
        foreach($soins as $soin) {
            echo "- ID: " . $soin['id'] . "\n";
            echo "  Date: " . $soin['date_soin'] . "\n";
            echo "  Heure: " . $soin['heure_soin'] . "\n";
            echo "  Type: " . $soin['type_soin'] . "\n";
            echo "  Description: " . ($soin['description'] ?? 'N/A') . "\n";
            echo "  Infirmier ID: " . $soin['infirmier_id'] . "\n";
            echo "  Numéro lit: " . ($soin['numero_lit'] ?? 'N/A') . "\n";
            echo "  Statut: " . $soin['statut'] . "\n";
            echo "  Créé par: " . $soin['created_by'] . "\n";
            echo "  Créé le: " . $soin['created_at'] . "\n\n";
        }
    } else {
        echo "Aucun soin trouvé pour ce patient.\n";
    }
    
    // Vérifier la structure de la table
    echo "=== Vérification de la structure de la table soins ===\n";
    $query = "SHOW COLUMNS FROM soins";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Colonnes de la table soins :\n";
    foreach($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ") " . ($column['Null'] === 'NO' ? 'NOT NULL' : 'NULL') . "\n";
    }
    
} catch(Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}