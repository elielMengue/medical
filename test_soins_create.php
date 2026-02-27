<?php
// Test de débogage pour la création de soins
session_start();

// Inclure la configuration
require_once 'app/config/Database.php';
use Config\Database;

echo "=== Test de débogage - Création de soins ===<br>";
echo "Session ID: " . session_id() . "<br>";
echo "User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Non connecté') . "<br>";
echo "User Role: " . (isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'Non défini') . "<br>";
echo "User Nom: " . (isset($_SESSION['user_nom']) ? $_SESSION['user_nom'] : 'Non défini') . "<br>";
echo "User Prenom: " . (isset($_SESSION['user_prenom']) ? $_SESSION['user_prenom'] : 'Non défini') . "<br>";

// Test de connexion avec le major
if(!isset($_SESSION['user_id'])) {
    echo "<br>=== Connexion automatique avec le major ===<br>";
    
    // Simuler la connexion du major
    $database = new Database();
    $conn = $database->getConnection();
    
    $query = "SELECT * FROM utilisateurs WHERE role = 'major' LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $major = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($major) {
        $_SESSION['user_id'] = $major['id'];
        $_SESSION['user_role'] = $major['role'];
        $_SESSION['user_nom'] = $major['nom'];
        $_SESSION['user_prenom'] = $major['prenom'];
        $_SESSION['user_email'] = $major['email'];
        
        echo "Connecté en tant que : " . $major['prenom'] . " " . $major['nom'] . " (" . $major['role'] . ")<br>";
    } else {
        echo "Aucun major trouvé dans la base de données<br>";
    }
}

echo "<br>=== Test de redirection ===<br>";
echo "<a href='app/public/index.php?controller=soin&action=create'>Cliquez ici pour tester la création de soin</a><br>";
echo "<a href='app/public/index.php?controller=soin&action=index'>Cliquez ici pour tester l'index des soins</a><br>";

echo "<br>=== Informations PHP ===<br>";
echo "Error reporting: " . ini_get('error_reporting') . "<br>";
echo "Display errors: " . ini_get('display_errors') . "<br>";

// Forcer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<br>=== Test d'inclusion ===<br>";
try {
    require_once 'app/controllers/SoinController.php';
    echo "SoinController chargé avec succès<br>";
    
    if(class_exists('Controllers\SoinController')) {
        echo "Classe SoinController trouvée<br>";
    } else {
        echo "Classe SoinController NON trouvée<br>";
    }
} catch(Exception $e) {
    echo "Erreur lors du chargement de SoinController : " . $e->getMessage() . "<br>";
}

echo "<br><a href='#' onclick='location.reload();'>Recharger la page</a> | ";
echo "<a href='?logout=1'>Déconnecter</a>";

// Déconnexion
if(isset($_GET['logout'])) {
    session_destroy();
    header('Location: test_soins_create.php');
    exit();
}
?>