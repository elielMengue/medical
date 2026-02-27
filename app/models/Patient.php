<?php
namespace Models;

use Config\Database;
use PDO;

class Patient {
    private $conn;
    private $table = "patients";

    // Propriétés
    public $id;
    public $nom;
    public $prenom;
    public $sexe;
    public $date_naissance;
    public $adresse;
    public $telephone;
    public $groupe_sanguin;  // AJOUTÉ ICI

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function getConnection() {
        return $this->conn;
    }

    public function patientExists($nom, $prenom, $date_naissance, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table . " 
                  WHERE LOWER(nom) = LOWER(:nom) 
                  AND LOWER(prenom) = LOWER(:prenom) 
                  AND date_naissance = :date_naissance";
        
        if($exclude_id !== null) {
            $query .= " AND id != :exclude_id";
        }
        
        $query .= " LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':date_naissance', $date_naissance);
        
        if($exclude_id !== null) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function lireTous() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY nom, prenom ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function lireUn() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    public function creer() {
        // Formater les données
        $this->nom = strtoupper(trim($this->nom));
        $this->prenom = ucfirst(strtolower(trim($this->prenom)));
        
        // Vérifier si le patient existe déjà
        if($this->patientExists($this->nom, $this->prenom, $this->date_naissance)) {
            return 'duplicate';
        }
        
        // Requête AVEC le champ groupe_sanguin
        $query = "INSERT INTO " . $this->table . "
                  (nom, prenom, sexe, date_naissance, adresse, telephone, groupe_sanguin)
                  VALUES (:nom, :prenom, :sexe, :date_naissance, :adresse, :telephone, :groupe_sanguin)";
        
        $stmt = $this->conn->prepare($query);

        // Nettoyage des champs optionnels
        $this->adresse = !empty($this->adresse) ? htmlspecialchars(strip_tags($this->adresse)) : null;
        $this->telephone = !empty($this->telephone) ? htmlspecialchars(strip_tags($this->telephone)) : null;
        $this->groupe_sanguin = !empty($this->groupe_sanguin) ? htmlspecialchars(strip_tags($this->groupe_sanguin)) : null;  // AJOUTÉ

        // Binding
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':prenom', $this->prenom);
        $stmt->bindParam(':sexe', $this->sexe);
        $stmt->bindParam(':date_naissance', $this->date_naissance);
        $stmt->bindParam(':adresse', $this->adresse);
        $stmt->bindParam(':telephone', $this->telephone);
        $stmt->bindParam(':groupe_sanguin', $this->groupe_sanguin);  // AJOUTÉ

        try {
            if($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch(PDOException $e) {
            error_log("Erreur SQL Patient::creer: " . $e->getMessage());
            return false;
        }
    }

    public function modifier() {
        // Formater les données
        $this->nom = strtoupper(trim($this->nom));
        $this->prenom = ucfirst(strtolower(trim($this->prenom)));
        
        // Vérifier si le patient existe déjà (sauf pour ce patient)
        if($this->patientExists($this->nom, $this->prenom, $this->date_naissance, $this->id)) {
            return 'duplicate';
        }
        
        // Requête AVEC le champ groupe_sanguin
        $query = "UPDATE " . $this->table . "
                  SET nom = :nom, 
                      prenom = :prenom,
                      sexe = :sexe,
                      date_naissance = :date_naissance,
                      adresse = :adresse,
                      telephone = :telephone,
                      groupe_sanguin = :groupe_sanguin
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        // Nettoyage des champs optionnels
        $this->adresse = !empty($this->adresse) ? htmlspecialchars(strip_tags($this->adresse)) : null;
        $this->telephone = !empty($this->telephone) ? htmlspecialchars(strip_tags($this->telephone)) : null;
        $this->groupe_sanguin = !empty($this->groupe_sanguin) ? htmlspecialchars(strip_tags($this->groupe_sanguin)) : null;  // AJOUTÉ

        // Binding
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':prenom', $this->prenom);
        $stmt->bindParam(':sexe', $this->sexe);
        $stmt->bindParam(':date_naissance', $this->date_naissance);
        $stmt->bindParam(':adresse', $this->adresse);
        $stmt->bindParam(':telephone', $this->telephone);
        $stmt->bindParam(':groupe_sanguin', $this->groupe_sanguin);  // AJOUTÉ

        try {
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Erreur SQL Patient::modifier: " . $e->getMessage());
            return false;
        }
    }

    public function supprimer() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        
        try {
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Erreur SQL Patient::supprimer: " . $e->getMessage());
            return false;
        }
    }

    public function compter() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function rechercher($nom = '', $prenom = '', $date_naissance = '') {
        $query = "SELECT * FROM " . $this->table . " WHERE 1=1";
        $params = array();
        
        if(!empty($nom)) {
            $query .= " AND nom LIKE :nom";
            $params[':nom'] = '%' . strtoupper($nom) . '%';
        }
        
        if(!empty($prenom)) {
            $query .= " AND prenom LIKE :prenom";
            $params[':prenom'] = '%' . ucfirst(strtolower($prenom)) . '%';
        }
        
        if(!empty($date_naissance)) {
            $query .= " AND date_naissance = :date_naissance";
            $params[':date_naissance'] = $date_naissance;
        }
        
        $query .= " ORDER BY nom, prenom ASC";
        
        $stmt = $this->conn->prepare($query);
        
        foreach($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt;
    }

    public function getStats() {
        $stats = array();
        
        // Total patients
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total'] = $result['total'];
        
        // Patients par sexe
        $query = "SELECT 
                    SUM(CASE WHEN sexe = 'M' THEN 1 ELSE 0 END) as hommes,
                    SUM(CASE WHEN sexe = 'F' THEN 1 ELSE 0 END) as femmes
                  FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $sexes = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stats['hommes'] = $sexes['hommes'];
        $stats['femmes'] = $sexes['femmes'];
        
        // Patients par tranche d'âge
        $query = "SELECT 
                    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) < 18 THEN 1 ELSE 0 END) as enfants,
                    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) BETWEEN 18 AND 65 THEN 1 ELSE 0 END) as adultes,
                    SUM(CASE WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) > 65 THEN 1 ELSE 0 END) as seniors
                  FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $ages = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stats['enfants'] = $ages['enfants'];
        $stats['adultes'] = $ages['adultes'];
        $stats['seniors'] = $ages['seniors'];
        
        return $stats;
    }

    // ========== MÉTHODES POUR LE DASHBOARD ==========
    
    /**
     * Trouver les patients sans antécédents
     */
    public function findWithoutAntecedents() {
        $query = "SELECT p.* 
                  FROM patients p
                  LEFT JOIN antecedents a ON p.id = a.patient_id
                  WHERE a.id IS NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // ========== NOUVELLES MÉTHODES POUR LES RAPPORTS ==========

    /**
     * Compter les patients sans antécédents
     */
    public function countWithoutAntecedents() {
        try {
            $query = "SELECT COUNT(*) as total 
                      FROM patients p
                      LEFT JOIN antecedents a ON p.id = a.patient_id
                      WHERE a.id IS NULL";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch(PDOException $e) {
            error_log("Erreur SQL Patient::countWithoutAntecedents: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Récupérer les patients avec leurs antécédents
     */
    public function findWithAntecedents() {
        try {
            $query = "SELECT p.*, 
                             COUNT(a.id) as nb_antecedents,
                             MAX(a.date_consultation) as dernier_antecedent
                      FROM patients p
                      LEFT JOIN antecedents a ON p.id = a.patient_id
                      GROUP BY p.id
                      ORDER BY nb_antecedents DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erreur SQL Patient::findWithAntecedents: " . $e->getMessage());
            return array();
        }
    }

    /**
     * Récupérer les patients par tranche d'âge
     */
    public function getPatientsByAgeRange() {
        try {
            $query = "SELECT 
                        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) < 18 THEN 1 ELSE 0 END) as enfants,
                        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) BETWEEN 18 AND 30 THEN 1 ELSE 0 END) as jeunes_adultes,
                        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) BETWEEN 31 AND 50 THEN 1 ELSE 0 END) as adultes,
                        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) BETWEEN 51 AND 70 THEN 1 ELSE 0 END) as seniors,
                        SUM(CASE WHEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) > 70 THEN 1 ELSE 0 END) as grands_seniors
                      FROM " . $this->table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erreur SQL Patient::getPatientsByAgeRange: " . $e->getMessage());
            return array();
        }
    }

    /**
     * Récupérer les patients par mois d'inscription
     */
    public function getPatientsByMonth() {
        try {
            $query = "SELECT 
                        DATE_FORMAT(created_at, '%Y-%m') as mois,
                        DATE_FORMAT(created_at, '%M %Y') as mois_nom,
                        COUNT(*) as total
                      FROM " . $this->table . "
                      WHERE created_at IS NOT NULL
                      GROUP BY mois
                      ORDER BY mois DESC
                      LIMIT 12";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erreur SQL Patient::getPatientsByMonth: " . $e->getMessage());
            return array();
        }
    }
}
?>