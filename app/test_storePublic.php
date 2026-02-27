<?php
// Test qui reproduit exactement le traitement du contrôleur storePublic
session_start();
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/Soin.php';

echo "🔄 Test du traitement storePublic\n";
echo "=================================\n\n";

// Simuler les données POST
$_POST = [
    'patient_id' => '1',
    'infirmier_id' => '4',
    'type_soin' => 'Pansement',
    'date_heure' => '2026-02-19T19:31',
    'description' => 'Test de description',
    'numero_lit' => 'A1',
    'statut' => 'planifié'
];

echo "1. Données simulées :\n";
foreach($_POST as $key => $value) {
    echo "   - $key : '$value'\n";
}

echo "\n2. Simulation de la validation :\n";
$errors = array();

if(empty($_POST['patient_id'])) {
    $errors[] = "Le patient est obligatoire";
}
if(empty($_POST['infirmier_id'])) {
    $errors[] = "L'infirmier est obligatoire";
}
if(empty($_POST['type_soin'])) {
    $errors[] = "Le type de soin est obligatoire";
}
if(empty($_POST['date_heure'])) {
    $errors[] = "La date et l'heure sont obligatoires";
}

if(!empty($errors)) {
    echo "❌ Erreurs de validation :\n";
    foreach($errors as $error) {
        echo "   - $error\n";
    }
} else {
    echo "✅ Validation réussie\n";
    
    echo "\n3. Conversion de la date/heure :\n";
    try {
        $date_heure = new \DateTime($_POST['date_heure']);
        $date_soin = $date_heure->format('Y-m-d');
        $heure_soin = $date_heure->format('H:i:s');
        
        echo "   - Date extraite : $date_soin\n";
        echo "   - Heure extraite : $heure_soin\n";
        echo "✅ Conversion réussie\n";
        
        echo "\n4. Test de création du soin :\n";
        
        // Créer une instance du modèle
        $soinModel = new \Models\Soin();
        
        // Définir les propriétés comme dans le contrôleur
        $soinModel->patient_id = $_POST['patient_id'];
        $soinModel->infirmier_id = $_POST['infirmier_id'];
        $soinModel->type_soin = $_POST['type_soin'];
        $soinModel->description = isset($_POST['description']) ? $_POST['description'] : '';
        $soinModel->date_soin = $date_soin;
        $soinModel->heure_soin = $heure_soin;
        $soinModel->numero_lit = isset($_POST['numero_lit']) ? $_POST['numero_lit'] : '';
        $soinModel->statut = isset($_POST['statut']) ? $_POST['statut'] : 'planifié';
        $soinModel->created_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
        
        echo "   - Patient ID : {$soinModel->patient_id}\n";
        echo "   - Infirmier ID : {$soinModel->infirmier_id}\n";
        echo "   - Type soin : {$soinModel->type_soin}\n";
        echo "   - Date soin : {$soinModel->date_soin}\n";
        echo "   - Heure soin : {$soinModel->heure_soin}\n";
        echo "   - Statut : {$soinModel->statut}\n";
        echo "   - Créé par : {$soinModel->created_by}\n";
        
        echo "\n5. Tentative d'insertion...\n";
        
        try {
            $result = $soinModel->create();
            
            if($result) {
                echo "✅ Soin créé avec succès ! ID : $result\n";
                
                // Nettoyer le test
                $database = new \Config\Database();
                $conn = $database->getConnection();
                $conn->exec("DELETE FROM soins WHERE id = $result");
                echo "🧹 Test nettoyé\n";
                
            } else {
                echo "❌ Échec de la création (retour false)\n";
            }
            
        } catch (Exception $e) {
            echo "❌ Exception capturée : " . $e->getMessage() . "\n";
            
            // Essayer d'obtenir plus d'informations
            $database = new \Config\Database();
            $conn = $database->getConnection();
            $error = $conn->errorInfo();
            if(isset($error[2])) {
                echo "Détails SQL : " . $error[2] . "\n";
            }
        }
        
    } catch (Exception $e) {
        echo "❌ Erreur de conversion : " . $e->getMessage() . "\n";
    }
}

echo "\n✅ Test terminé\n";
?>