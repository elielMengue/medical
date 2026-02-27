<?php
namespace Models;

use Config\Database;
use PDO;

class User {
    private $conn;
    private $table = "utilisateurs";

    public $id;
    public $nom;
    public $prenom;
    public $email;
    public $password;
    public $role;
    public $matricule;
    public $telephone;
    public $service;  // AJOUTÉ
    public $centre;   // AJOUTÉ

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Trouver un utilisateur par email
    public function findByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Trouver par matricule
    public function findByMatricule($matricule) {
        $query = "SELECT * FROM " . $this->table . " WHERE matricule = :matricule LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':matricule', $matricule);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Trouver par ID
    public function findById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Créer un nouvel utilisateur
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                  (nom, prenom, email, password, role, matricule, telephone, service, centre)
                  VALUES (:nom, :prenom, :email, :password, :role, :matricule, :telephone, :service, :centre)";
        
        $stmt = $this->conn->prepare($query);

        // Nettoyage
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->prenom = htmlspecialchars(strip_tags($this->prenom));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->matricule = htmlspecialchars(strip_tags($this->matricule));
        $this->telephone = htmlspecialchars(strip_tags($this->telephone));
        $this->service = htmlspecialchars(strip_tags($this->service));   // AJOUTÉ
        $this->centre = htmlspecialchars(strip_tags($this->centre));     // AJOUTÉ

        // Hash du mot de passe avec sha1 pour PHP 5.3
        $hashed_password = sha1($this->password);

        // Binding
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':prenom', $this->prenom);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':matricule', $this->matricule);
        $stmt->bindParam(':telephone', $this->telephone);
        $stmt->bindParam(':service', $this->service);   // AJOUTÉ
        $stmt->bindParam(':centre', $this->centre);     // AJOUTÉ

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        // Afficher l'erreur SQL si elle existe
        $error = $stmt->errorInfo();
        if($error[0] != '00000') {
            error_log("Erreur SQL User::create: " . $error[2]);
        }
        return false;
    }

    // Vérifier le mot de passe
    public function verifyPassword($email, $password) {
        $user = $this->findByEmail($email);
        
        // Pour PHP 5.3, on utilise sha1
        if($user && isset($user['password']) && $user['password'] === sha1($password)) {
            return $user;
        }
        return false;
    }

    // Mettre à jour un utilisateur
    // Mettre à jour un utilisateur
public function update($id) {
    $query = "UPDATE " . $this->table . "
              SET nom = :nom, prenom = :prenom, email = :email, 
                  role = :role, matricule = :matricule, telephone = :telephone,
                  service = :service, centre = :centre
              WHERE id = :id";
    
    $stmt = $this->conn->prepare($query);

    // Nettoyage
    $this->nom = htmlspecialchars(strip_tags($this->nom));
    $this->prenom = htmlspecialchars(strip_tags($this->prenom));
    $this->email = htmlspecialchars(strip_tags($this->email));
    $this->role = htmlspecialchars(strip_tags($this->role));
    $this->matricule = htmlspecialchars(strip_tags($this->matricule));
    $this->telephone = htmlspecialchars(strip_tags($this->telephone));
    $this->service = htmlspecialchars(strip_tags($this->service));
    $this->centre = htmlspecialchars(strip_tags($this->centre));

    // Binding
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':nom', $this->nom);
    $stmt->bindParam(':prenom', $this->prenom);
    $stmt->bindParam(':email', $this->email);
    $stmt->bindParam(':role', $this->role);
    $stmt->bindParam(':matricule', $this->matricule);
    $stmt->bindParam(':telephone', $this->telephone);
    $stmt->bindParam(':service', $this->service);
    $stmt->bindParam(':centre', $this->centre);

    if($stmt->execute()) {
        return true;
    }
    
    $error = $stmt->errorInfo();
    if($error[0] != '00000') {
        error_log("Erreur SQL User::update: " . $error[2]);
    }
    return false;
}
    // Changer le mot de passe
  // Changer le mot de passe
public function changePassword($id, $new_password) {
    $query = "UPDATE " . $this->table . " SET password = :password WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    
    // Hash avec sha1 pour PHP 5.3
    $hashed_password = sha1($new_password);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':id', $id);
    
    return $stmt->execute();
}

    // Lister tous les utilisateurs
    public function findAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY role, nom, prenom ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Supprimer un utilisateur
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Compter par rôle
    public function countByRole() {
        $query = "SELECT role, COUNT(*) as total FROM " . $this->table . " GROUP BY role";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Compter tous les utilisateurs
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Vérifier si un email existe déjà
    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Vérifier si un matricule existe déjà
    public function matriculeExists($matricule) {
        $query = "SELECT id FROM " . $this->table . " WHERE matricule = :matricule LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':matricule', $matricule);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>