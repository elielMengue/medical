<?php
// Test direct dans le dossier public
if(session_id() == '') {
    session_start();
}

// Inclure la base de données
require_once '../config/Database.php';
use Config\Database;

echo "<h1>Test de Session - Dossier Public</h1>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "SESSION: " . print_r($_SESSION, true) . "\n";
echo "</pre>";

// Si pas connecté, connectons le major
if(!isset($_SESSION['user_id'])) {
    echo "<h2>Connexion automatique du major...</h2>";
    
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
        
        echo "<p style='color: green;'>✓ Connecté en tant que: " . $major['prenom'] . " " . $major['nom'] . " (" . $major['role'] . ")</p>";
    } else {
        echo "<p style='color: red;'>✗ Aucun major trouvé</p>";
    }
} else {
    echo "<p style='color: green;'>✓ Déjà connecté en tant que: " . $_SESSION['user_prenom'] . " " . $_SESSION['user_nom'] . " (" . $_SESSION['user_role'] . ")</p>";
}

echo "<hr>";
echo "<p><a href='index.php?controller=soin&action=create'>🎯 TESTER LA CREATION DE SOIN</a></p>";
echo "<p><a href='index.php?controller=soin&action=index'>📅 Voir le planning des soins</a></p>";
echo "<hr>";
echo "<p><a href='?logout=1'>Se déconnecter</a></p>";

if(isset($_GET['logout'])) {
    session_destroy();
    header('Location: test_direct.php');
    exit();
}
?>