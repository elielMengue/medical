<?php
// Test pour vérifier si l'utilisateur ID 1 existe
require_once __DIR__ . '/config/Database.php';

try {
    $database = new \Config\Database();
    $conn = $database->getConnection();
    
    // Vérifier si l'utilisateur ID 1 existe
    $query = "SELECT id, nom, prenom, role FROM utilisateurs WHERE id = 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "✅ Utilisateur ID 1 trouvé : " . $user['nom'] . " " . $user['prenom'] . " (Rôle: " . $user['role'] . ")";
    } else {
        echo "❌ Utilisateur ID 1 non trouvé";
        
        // Vérifier s'il y a des utilisateurs dans la base
        $query = "SELECT COUNT(*) as total FROM utilisateurs";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "\n📊 Nombre total d'utilisateurs : " . $result['total'];
        
        if($result['total'] > 0) {
            // Afficher les premiers utilisateurs
            $query = "SELECT id, nom, prenom, role FROM utilisateurs ORDER BY id ASC LIMIT 5";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            echo "\n\nUtilisateurs disponibles :";
            while($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "\n- ID " . $user['id'] . ": " . $user['nom'] . " " . $user['prenom'] . " (" . $user['role'] . ")";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>