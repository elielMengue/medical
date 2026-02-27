<?php
namespace Models;

use Config\Database;
use PDO;
use PDOException;

class Soins {
    private $conn;
    private $table_name = "soins";
    
    public $id;
    public $patient_id;
    public $infirmier_id;
    public $type_soin;
    public $description;
    public $date_soin;
    public $heure_soin;
    public $numero_lit;
    public $statut;
    public $created_by;
    public $created_at;
    
    public function __construct() {
        $database = new \Config\Database();
        $this->conn = $database->getConnection();
    }
    
    // Récupérer tous les soins d'un patient
    public function lireParPatient($patient_id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                     WHERE patient_id = :patient_id 
                     ORDER BY date_soin DESC, heure_soin DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt;
        } catch(PDOException $e) {
            error_log("Erreur lors de la récupération des soins: " . $e->getMessage());
            return false;
        }
    }
    
    // Récupérer un soin par ID
    public function lireUn() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erreur lors de la récupération du soin: " . $e->getMessage());
            return false;
        }
    }
    
    // Créer un nouveau soin
    public function creer() {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                     (patient_id, infirmier_id, type_soin, description, date_soin, heure_soin, numero_lit, statut, created_by, created_at)
                     VALUES (:patient_id, :infirmier_id, :type_soin, :description, :date_soin, :heure_soin, :numero_lit, :statut, :created_by, NOW())";
            
            $stmt = $this->conn->prepare($query);
            
            // Nettoyer les données
            $this->patient_id = htmlspecialchars(strip_tags($this->patient_id));
            $this->infirmier_id = htmlspecialchars(strip_tags($this->infirmier_id));
            $this->type_soin = htmlspecialchars(strip_tags($this->type_soin));
            $this->description = htmlspecialchars(strip_tags($this->description));
            $this->date_soin = htmlspecialchars(strip_tags($this->date_soin));
            $this->heure_soin = htmlspecialchars(strip_tags($this->heure_soin));
            $this->numero_lit = htmlspecialchars(strip_tags($this->numero_lit));
            $this->statut = htmlspecialchars(strip_tags($this->statut));
            $this->created_by = htmlspecialchars(strip_tags($this->created_by));
            
            // Binder les paramètres
            $stmt->bindParam(':patient_id', $this->patient_id, PDO::PARAM_INT);
            $stmt->bindParam(':infirmier_id', $this->infirmier_id, PDO::PARAM_INT);
            $stmt->bindParam(':type_soin', $this->type_soin);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':date_soin', $this->date_soin);
            $stmt->bindParam(':heure_soin', $this->heure_soin);
            $stmt->bindParam(':numero_lit', $this->numero_lit);
            $stmt->bindParam(':statut', $this->statut);
            $stmt->bindParam(':created_by', $this->created_by, PDO::PARAM_INT);
            
            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Erreur lors de la création du soin: " . $e->getMessage());
            return false;
        }
    }
    
    // Mettre à jour un soin
    public function modifier() {
        try {
            $query = "UPDATE " . $this->table_name . " 
                     SET patient_id = :patient_id, infirmier_id = :infirmier_id, 
                         type_soin = :type_soin, description = :description,
                         date_soin = :date_soin, heure_soin = :heure_soin,
                         numero_lit = :numero_lit, statut = :statut
                     WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            // Nettoyer les données
            $this->patient_id = htmlspecialchars(strip_tags($this->patient_id));
            $this->infirmier_id = htmlspecialchars(strip_tags($this->infirmier_id));
            $this->type_soin = htmlspecialchars(strip_tags($this->type_soin));
            $this->description = htmlspecialchars(strip_tags($this->description));
            $this->date_soin = htmlspecialchars(strip_tags($this->date_soin));
            $this->heure_soin = htmlspecialchars(strip_tags($this->heure_soin));
            $this->numero_lit = htmlspecialchars(strip_tags($this->numero_lit));
            $this->statut = htmlspecialchars(strip_tags($this->statut));
            $this->id = htmlspecialchars(strip_tags($this->id));
            
            // Binder les paramètres
            $stmt->bindParam(':patient_id', $this->patient_id, PDO::PARAM_INT);
            $stmt->bindParam(':infirmier_id', $this->infirmier_id, PDO::PARAM_INT);
            $stmt->bindParam(':type_soin', $this->type_soin);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':date_soin', $this->date_soin);
            $stmt->bindParam(':heure_soin', $this->heure_soin);
            $stmt->bindParam(':numero_lit', $this->numero_lit);
            $stmt->bindParam(':statut', $this->statut);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            
            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Erreur lors de la modification du soin: " . $e->getMessage());
            return false;
        }
    }
    
    // Supprimer un soin
    public function supprimer() {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            
            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Erreur lors de la suppression du soin: " . $e->getMessage());
            return false;
        }
    }
    
    // Récupérer le nombre total de soins
    public function compter() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch(PDOException $e) {
            error_log("Erreur lors du comptage des soins: " . $e->getMessage());
            return 0;
        }
    }
    
    // Récupérer les soins par statut
    public function lireParStatut($statut) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                     WHERE statut = :statut 
                     ORDER BY date_soin DESC, heure_soin DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':statut', $statut);
            $stmt->execute();
            
            return $stmt;
        } catch(PDOException $e) {
            error_log("Erreur lors de la récupération des soins par statut: " . $e->getMessage());
            return false;
        }
    }
}