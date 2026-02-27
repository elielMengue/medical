<?php
// Test pour simuler la création d'un soin avec des données contrôlées
require_once __DIR__ . '/config/Database.php';

try {
    $database = new \Config\Database();
    $conn = $database->getConnection();
    
    echo "🧪 Test de création de soin\n";
    echo "==========================\n\n";
    
    // Vérifier les patients disponibles
    echo "1. Patients disponibles :\n";
    $query = "SELECT id, nom, prenom FROM patients ORDER BY id ASC LIMIT 3";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($patients as $patient) {
        echo "   - ID " . $patient['id'] . ": " . $patient['prenom'] . " " . $patient['nom'] . "\n";
    }
    
    // Vérifier les infirmiers disponibles
    echo "\n2. Infirmiers disponibles :\n";
    $query = "SELECT id, nom, prenom FROM utilisateurs WHERE role = 'infirmier' ORDER BY id ASC LIMIT 3";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $infirmiers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($infirmiers as $infirmier) {
        echo "   - ID " . $infirmier['id'] . ": " . $infirmier['prenom'] . " " . $infirmier['nom'] . "\n";
    }
    
    if(empty($patients) || empty($infirmiers)) {
        echo "\n❌ Impossible de continuer le test : pas assez de données\n";
        exit;
    }
    
    // Prendre le premier patient et infirmier
    $patient_id = $patients[0]['id'];
    $infirmier_id = $infirmiers[0]['id'];
    
    echo "\n3. Test de création avec :\n";
    echo "   - Patient ID : " . $patient_id . "\n";
    echo "   - Infirmier ID : " . $infirmier_id . "\n";
    echo "   - Type de soin : Pansement\n";
    echo "   - Date : " . date('Y-m-d') . "\n";
    echo "   - Heure : " . date('H:i:s') . "\n";
    echo "   - Créé par : 1 (Admin)\n\n";
    
    // Tester la création
    $query = "INSERT INTO soins (patient_id, infirmier_id, type_soin, description, date_soin, heure_soin, numero_lit, statut, created_by) 
              VALUES (:patient_id, :infirmier_id, :type_soin, :description, :date_soin, :heure_soin, :numero_lit, :statut, :created_by)";
    
    $stmt = $conn->prepare($query);
    
    $type_soin = "Pansement";
    $description = "Test de pansement";
    $date_soin = date('Y-m-d');
    $heure_soin = date('H:i:s');
    $numero_lit = "A1";
    $statut = "planifié";
    $created_by = 1;
    
    $stmt->bindParam(':patient_id', $patient_id);
    $stmt->bindParam(':infirmier_id', $infirmier_id);
    $stmt->bindParam(':type_soin', $type_soin);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':date_soin', $date_soin);
    $stmt->bindParam(':heure_soin', $heure_soin);
    $stmt->bindParam(':numero_lit', $numero_lit);
    $stmt->bindParam(':statut', $statut);
    $stmt->bindParam(':created_by', $created_by);
    
    if($stmt->execute()) {
        $new_id = $conn->lastInsertId();
        echo "✅ Soin créé avec succès ! ID : " . $new_id . "\n";
        
        // Nettoyer le test
        $conn->exec("DELETE FROM soins WHERE id = $new_id");
        echo "🧹 Test nettoyé\n";
        
    } else {
        echo "❌ Échec de la création du soin\n";
        $error = $stmt->errorInfo();
        echo "Erreur SQL : " . $error[2] . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur générale : " . $e->getMessage() . "\n";
    
    // Essayer d'obtenir plus d'informations
    if(isset($conn)) {
        $error = $conn->errorInfo();
        if(isset($error[2])) {
            echo "Détails SQL : " . $error[2] . "\n";
        }
    }
}
?>