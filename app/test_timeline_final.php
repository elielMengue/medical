<?php
// Test de la timeline avec le nouveau modèle Soins
require_once 'config/database.php';
require_once 'models/Soins.php';

use Config\Database;

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Test avec le patient ID 1
    $patient_id = 1;
    
    echo "=== Test de la timeline pour le patient $patient_id ===\n\n";
    
    // Créer une instance du modèle Soins
    $soinsModel = new \App\Models\Soins();
    
    // Récupérer les soins du patient
    $stmt = $soinsModel->lireParPatient($patient_id);
    $soins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Nombre de soins trouvés : " . count($soins) . "\n\n";
    
    if(!empty($soins)) {
        echo "Détails des soins :\n";
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
    
} catch(Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}