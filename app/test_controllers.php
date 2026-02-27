<?php
// Test de chargement des contrôleurs
require_once 'public/index.php';

echo "Test d'autoloading des contrôleurs...\n\n";

// Test 1: BaseController
try {
    $baseController = new Controllers\BaseController();
    echo "✅ BaseController chargé avec succès\n";
} catch (Exception $e) {
    echo "❌ Erreur BaseController: " . $e->getMessage() . "\n";
}

// Test 2: CalendarController
try {
    $calendarController = new Controllers\CalendarController();
    echo "✅ CalendarController chargé avec succès\n";
} catch (Exception $e) {
    echo "❌ Erreur CalendarController: " . $e->getMessage() . "\n";
}

// Test 3: ExportController
try {
    $exportController = new Controllers\ExportController();
    echo "✅ ExportController chargé avec succès\n";
} catch (Exception $e) {
    echo "❌ Erreur ExportController: " . $e->getMessage() . "\n";
}

// Test 4: NotificationController
try {
    $notificationController = new Controllers\NotificationController();
    echo "✅ NotificationController chargé avec succès\n";
} catch (Exception $e) {
    echo "❌ Erreur NotificationController: " . $e->getMessage() . "\n";
}

// Test 5: PatientController
try {
    $patientController = new Controllers\PatientController();
    echo "✅ PatientController chargé avec succès\n";
} catch (Exception $e) {
    echo "❌ Erreur PatientController: " . $e->getMessage() . "\n";
}

echo "\nTest terminé !\n";