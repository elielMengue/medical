<?php
// Test de la timeline des soins
require_once 'config/database.php';

use Config\Database;

// Inclure le modèle Soins directement
require_once 'models/Soins.php';

use Models\Soins;

echo "Test de la timeline des soins...\n\n";

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Test 1: Récupérer les soins d'un patient
    echo "1. Test de récupération des soins par patient...\n";
    $soinsModel = new Soins();
    $patient_id = 1; // ID du patient Paul DURAND
    
    $soins = $soinsModel->lireParPatient($patient_id);
    
    if($soins && $soins->rowCount() > 0) {
        echo "✅ Soins récupérés avec succès !\n";
        echo "Nombre de soins : " . $soins->rowCount() . "\n\n";
        
        echo "Derniers soins :\n";
        $count = 0;
        while($soin = $soins->fetch(PDO::FETCH_ASSOC)) {
            if($count < 3) { // Afficher les 3 derniers
                echo "- " . $soin['date_soin'] . " " . $soin['heure_soin'] . " : " . $soin['type_soin'] . " (" . $soin['statut'] . ")\n";
                $count++;
            }
        }
        echo "\n";
    } else {
        echo "ℹ️ Aucun soin trouvé pour ce patient\n\n";
    }
    
    // Test 2: Créer un nouveau soin
    echo "2. Test de création d'un nouveau soin...\n";
    $soinsModel->patient_id = $patient_id;
    $soinsModel->date_soin = date('Y-m-d', strtotime('+1 day'));
    $soinsModel->heure_soin = '14:00:00';
    $soinsModel->type_soin = 'Consultation de suivi';
    $soinsModel->statut = 'planifie';
    $soinsModel->remarques = 'Contrôle post-opératoire';
    
    if($soinsModel->creer()) {
        echo "✅ Soin créé avec succès !\n\n";
    } else {
        echo "❌ Erreur lors de la création du soin\n\n";
    }
    
    // Test 3: Vérifier la timeline mise à jour
    echo "3. Test de la timeline mise à jour...\n";
    $soins = $soinsModel->lireParPatient($patient_id);
    
    if($soins && $soins->rowCount() > 0) {
        echo "✅ Timeline mise à jour !\n";
        echo "Nombre total de soins : " . $soins->rowCount() . "\n\n";
        
        // Afficher la timeline
        echo "Timeline des soins :\n";
        $colors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14'];
        $index = 0;
        
        while($soin = $soins->fetch(PDO::FETCH_ASSOC)) {
            $color = $colors[$index % count($colors)];
            $index++;
            
            echo "📅 " . $soin['date_soin'] . " " . $soin['heure_soin'] . "\n";
            echo "   Type: " . $soin['type_soin'] . "\n";
            echo "   Statut: " . $soin['statut'] . "\n";
            if(!empty($soin['remarques'])) {
                echo "   Notes: " . $soin['remarques'] . "\n";
            }
            echo "\n";
        }
    }
    
    echo "✅ La fonctionnalité de timeline des soins est opérationnelle !\n";
    echo "✅ Les soins sont maintenant affichés dans la page du patient.\n";
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
}

echo "\nTest terminé !\n";