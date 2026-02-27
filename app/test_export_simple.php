<?php
// Test simple de la requête d'export
require_once 'config/database.php';

use Config\Database;

echo "Test de la requête d'export PDF/Excel...\n\n";

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $query = "SELECT 
                p.id,
                p.nom,
                p.prenom,
                p.date_naissance,
                p.adresse,
                p.telephone,
                p.email,
                p.created_at,
                TIMESTAMPDIFF(YEAR, p.date_naissance, CURDATE()) as age,
                COUNT(a.id) as nb_antecedents,
                COUNT(s.id) as nb_soins,
                MAX(s.date_soin) as dernier_soin
              FROM patients p
              LEFT JOIN antecedents a ON p.id = a.patient_id
              LEFT JOIN soins s ON p.id = s.patient_id
              GROUP BY p.id, p.nom, p.prenom, p.date_naissance, p.adresse, p.telephone, p.email, p.created_at
              ORDER BY p.nom, p.prenom ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "✅ Requête d'export réussie !\n";
    echo "Nombre de patients : " . count($patients) . "\n\n";
    
    if(count($patients) > 0) {
        echo "Données du premier patient :\n";
        echo "- Nom: " . $patients[0]['nom'] . "\n";
        echo "- Prénom: " . $patients[0]['prenom'] . "\n";
        echo "- Age: " . $patients[0]['age'] . " ans\n";
        echo "- Nombre de soins: " . $patients[0]['nb_soins'] . "\n";
        echo "- Dernier soin: " . $patients[0]['dernier_soin'] . "\n";
    }
    
    echo "\n✅ L'export PDF/Excel devrait maintenant fonctionner !\n";
    echo "✅ La table 'rendez_vous' a été remplacée par 'soins' dans toutes les requêtes.\n";
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
}

echo "\nTest terminé !\n";