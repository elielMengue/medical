<?php
// Test de connexion à la base de données - Version simplifiée
try {
    $host = 'localhost';
    $dbname = 'gestion_medicale';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>✅ Connexion réussie !</h1>";
    echo "<p>La connexion à la base de données fonctionne correctement.</p>";
    
    // Test simple
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM patients");
    $result = $stmt->fetch();
    echo "<p>Nombre de patients: " . $result['total'] . "</p>";
    
} catch (PDOException $e) {
    echo "<h1>❌ Erreur de connexion</h1>";
    echo "<p>Message d'erreur: " . $e->getMessage() . "</p>";
    echo "<p>Code d'erreur: " . $e->getCode() . "</p>";
    
    // Essayer de se connecter sans spécifier de base de données
    try {
        $pdo = new PDO("mysql:host=$host", $username, $password);
        echo "<p>✅ Connexion au serveur MySQL réussie, mais la base '$dbname' n'existe pas ou est inaccessible.</p>";
    } catch (PDOException $e2) {
        echo "<p>❌ Impossible de se connecter au serveur MySQL.</p>";
        echo "<p>Vérifiez que XAMPP/WAMP est démarré et que MySQL fonctionne.</p>";
    }
}

// Afficher les informations PHP pour le debug
phpinfo();
?>