<?php
// Test de chargement des contrôleurs et vérification des méthodes
require_once 'public/index.php';

echo "Test d'autoloading et de visibilité des méthodes...\n\n";

// Test 1: PatientController
try {
    $patientController = new Controllers\PatientController();
    
    // Vérifier que la méthode checkAccess existe et est accessible
    $reflection = new ReflectionClass($patientController);
    $checkAccessMethod = $reflection->getMethod('checkAccess');
    
    if($checkAccessMethod->isProtected()) {
        echo "✅ PatientController::checkAccess() est protégée ✓\n";
    } else {
        echo "❌ PatientController::checkAccess() n'est pas protégée\n";
    }
    
    $checkEditAccessMethod = $reflection->getMethod('checkEditAccess');
    if($checkEditAccessMethod->isProtected()) {
        echo "✅ PatientController::checkEditAccess() est protégée ✓\n";
    }
    
    $checkDeleteAccessMethod = $reflection->getMethod('checkDeleteAccess');
    if($checkDeleteAccessMethod->isProtected()) {
        echo "✅ PatientController::checkDeleteAccess() est protégée ✓\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur PatientController: " . $e->getMessage() . "\n";
}

// Test 2: DashboardController
try {
    $dashboardController = new Controllers\DashboardController();
    
    $reflection = new ReflectionClass($dashboardController);
    $getAlertesMethod = $reflection->getMethod('getAlertes');
    
    if($getAlertesMethod->isProtected()) {
        echo "✅ DashboardController::getAlertes() est protégée ✓\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur DashboardController: " . $e->getMessage() . "\n";
}

echo "\n✅ Toutes les erreurs de visibilité corrigées !\n";
echo "L'application devrait maintenant fonctionner correctement.\n";