<?php
namespace Models;

use Config\Database;
use PDO;

class Soin {
    private $conn;
    private $table = "soins";

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
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Créer un nouveau soin
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                  (patient_id, infirmier_id, type_soin, description, date_soin, heure_soin, numero_lit, statut, created_by)
                  VALUES (:patient_id, :infirmier_id, :type_soin, :description, :date_soin, :heure_soin, :numero_lit, :statut, :created_by)";
        
        $stmt = $this->conn->prepare($query);

        $this->type_soin = htmlspecialchars(strip_tags($this->type_soin));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->numero_lit = htmlspecialchars(strip_tags($this->numero_lit));

        $stmt->bindParam(':patient_id', $this->patient_id);
        $stmt->bindParam(':infirmier_id', $this->infirmier_id);
        $stmt->bindParam(':type_soin', $this->type_soin);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':date_soin', $this->date_soin);
        $stmt->bindParam(':heure_soin', $this->heure_soin);
        $stmt->bindParam(':numero_lit', $this->numero_lit);
        $stmt->bindParam(':statut', $this->statut);
        $stmt->bindParam(':created_by', $this->created_by);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Récupérer tous les soins
     */
    public function findAll() {
        $query = "SELECT s.*, 
                         p.nom as patient_nom, p.prenom as patient_prenom, 
                         u.nom as infirmier_nom, u.prenom as infirmier_prenom,
                         m.nom as major_nom, m.prenom as major_prenom
                  FROM " . $this->table . " s
                  JOIN patients p ON s.patient_id = p.id
                  JOIN utilisateurs u ON s.infirmier_id = u.id
                  JOIN utilisateurs m ON s.created_by = m.id
                  ORDER BY s.date_soin DESC, s.heure_soin ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Récupérer les soins par date
     */
    public function findByDate($date) {
        $query = "SELECT s.*, 
                         p.nom as patient_nom, p.prenom as patient_prenom, 
                         u.nom as infirmier_nom, u.prenom as infirmier_prenom
                  FROM " . $this->table . " s
                  JOIN patients p ON s.patient_id = p.id
                  JOIN utilisateurs u ON s.infirmier_id = u.id
                  WHERE s.date_soin = :date
                  ORDER BY s.heure_soin ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Récupérer les soins par infirmier
     */
    public function findByInfirmier($infirmier_id) {
        $query = "SELECT s.*, 
                         p.nom as patient_nom, p.prenom as patient_prenom
                  FROM " . $this->table . " s
                  JOIN patients p ON s.patient_id = p.id
                  WHERE s.infirmier_id = :infirmier_id
                  ORDER BY s.date_soin DESC, s.heure_soin ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':infirmier_id', $infirmier_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Récupérer les soins par infirmier et par date
     */
    public function findByInfirmierAndDate($infirmier_id, $date) {
        $query = "SELECT s.*, 
                         p.nom as patient_nom, p.prenom as patient_prenom,
                         p.telephone as patient_tel
                  FROM " . $this->table . " s
                  JOIN patients p ON s.patient_id = p.id
                  WHERE s.infirmier_id = :infirmier_id 
                    AND s.date_soin = :date
                  ORDER BY s.heure_soin ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':infirmier_id', $infirmier_id);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Récupérer les soins par patient
     */
    public function findByPatient($patient_id) {
        $query = "SELECT s.*, 
                         u.nom as infirmier_nom, u.prenom as infirmier_prenom
                  FROM " . $this->table . " s
                  JOIN utilisateurs u ON s.infirmier_id = u.id
                  WHERE s.patient_id = :patient_id
                  ORDER BY s.date_soin DESC, s.heure_soin ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Récupérer un soin par son ID
     */
    public function findById($id) {
        $query = "SELECT s.*, 
                         p.nom as patient_nom, p.prenom as patient_prenom,
                         p.adresse, p.telephone as patient_tel,
                         u.nom as infirmier_nom, u.prenom as infirmier_prenom,
                         u.telephone as infirmier_tel
                  FROM " . $this->table . " s
                  JOIN patients p ON s.patient_id = p.id
                  JOIN utilisateurs u ON s.infirmier_id = u.id
                  WHERE s.id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Mettre à jour le statut d'un soin
     */
    public function updateStatut($id, $statut) {
        $query = "UPDATE " . $this->table . " SET statut = :statut WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':statut', $statut);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Mettre à jour un soin
     */
    public function update($id) {
        $query = "UPDATE " . $this->table . "
                  SET patient_id = :patient_id,
                      infirmier_id = :infirmier_id,
                      type_soin = :type_soin,
                      description = :description,
                      date_soin = :date_soin,
                      heure_soin = :heure_soin,
                      numero_lit = :numero_lit,
                      statut = :statut
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        $this->type_soin = htmlspecialchars(strip_tags($this->type_soin));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->numero_lit = htmlspecialchars(strip_tags($this->numero_lit));

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':patient_id', $this->patient_id);
        $stmt->bindParam(':infirmier_id', $this->infirmier_id);
        $stmt->bindParam(':type_soin', $this->type_soin);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':date_soin', $this->date_soin);
        $stmt->bindParam(':heure_soin', $this->heure_soin);
        $stmt->bindParam(':numero_lit', $this->numero_lit);
        $stmt->bindParam(':statut', $this->statut);

        return $stmt->execute();
    }

    /**
     * Supprimer un soin
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Obtenir les statistiques des soins
     */
    public function getStats() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN statut = 'planifie' THEN 1 ELSE 0 END) as planifies,
                    SUM(CASE WHEN statut = 'en_cours' THEN 1 ELSE 0 END) as en_cours,
                    SUM(CASE WHEN statut = 'effectue' THEN 1 ELSE 0 END) as effectues,
                    SUM(CASE WHEN statut = 'annule' THEN 1 ELSE 0 END) as annules
                  FROM " . $this->table;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir les statistiques des soins par date
     */
    public function getStatsByDate($date) {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN statut = 'planifie' THEN 1 ELSE 0 END) as planifies,
                    SUM(CASE WHEN statut = 'en_cours' THEN 1 ELSE 0 END) as en_cours,
                    SUM(CASE WHEN statut = 'effectue' THEN 1 ELSE 0 END) as effectues,
                    SUM(CASE WHEN statut = 'annule' THEN 1 ELSE 0 END) as annules
                  FROM " . $this->table . "
                  WHERE date_soin = :date";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer la liste des infirmiers
     */
    public function getInfirmiers() {
        $query = "SELECT id, nom, prenom, matricule FROM utilisateurs WHERE role = 'infirmier' ORDER BY nom, prenom";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer la liste des patients
     */
    public function getPatients() {
        $query = "SELECT id, nom, prenom FROM patients ORDER BY nom, prenom";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer les soins à venir
     */
    public function getProchainsSoins($limit = 10) {
        $query = "SELECT s.*, 
                         p.nom as patient_nom, p.prenom as patient_prenom,
                         u.nom as infirmier_nom, u.prenom as infirmier_prenom
                  FROM " . $this->table . " s
                  JOIN patients p ON s.patient_id = p.id
                  JOIN utilisateurs u ON s.infirmier_id = u.id
                  WHERE s.date_soin >= CURDATE() 
                    AND s.statut IN ('planifie', 'en_cours')
                  ORDER BY s.date_soin ASC, s.heure_soin ASC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Compter les soins par statut
     */
    public function countByStatut() {
        $query = "SELECT statut, COUNT(*) as total 
                  FROM " . $this->table . " 
                  GROUP BY statut";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $result = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[$row['statut']] = $row['total'];
        }
        
        return $result;
    }

    // ========== MÉTHODES POUR LE DASHBOARD ==========

    /**
     * Compter tous les soins
     */
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    /**
     * Compter les soins par date
     */
    public function countByDate($date) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE date_soin = :date";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    /**
     * Compter les soins par statut (pour un statut spécifique)
     */
    public function countByStatutValue($statut) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE statut = :statut";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':statut', $statut);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    /**
     * Récupérer tous les statuts avec leur nombre
     */
    public function countByStatutAll() {
        $query = "SELECT statut, COUNT(*) as nombre FROM " . $this->table . " GROUP BY statut";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer les soins par mois pour le graphique
     */
    public function getSoinsParMois() {
        $query = "SELECT DATE_FORMAT(date_soin, '%Y-%m') as mois, 
                         COUNT(*) as nombre,
                         DATE_FORMAT(date_soin, '%M %Y') as mois_nom
                  FROM " . $this->table . " 
                  WHERE date_soin >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                  GROUP BY mois 
                  ORDER BY mois ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Trouver les soins en retard
     */
    public function findSoinsEnRetard() {
        $query = "SELECT s.*, 
                         p.nom as patient_nom, 
                         p.prenom as patient_prenom,
                         u.nom as infirmier_nom, 
                         u.prenom as infirmier_prenom
                  FROM " . $this->table . " s
                  JOIN patients p ON s.patient_id = p.id
                  JOIN utilisateurs u ON s.infirmier_id = u.id
                  WHERE s.date_soin < CURDATE() 
                  AND s.statut IN ('planifie', 'en_cours')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // ========== NOUVELLES MÉTHODES POUR LES RAPPORTS ==========

    /**
     * Récupérer les soins par période
     */
    public function getSoinsByPeriode($date_debut, $date_fin) {
        try {
            $query = "SELECT s.*, 
                             p.nom as patient_nom, p.prenom as patient_prenom,
                             u.nom as infirmier_nom, u.prenom as infirmier_prenom
                      FROM " . $this->table . " s
                      JOIN patients p ON s.patient_id = p.id
                      JOIN utilisateurs u ON s.infirmier_id = u.id
                      WHERE s.date_soin BETWEEN :date_debut AND :date_fin
                      ORDER BY s.date_soin DESC, s.heure_soin ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':date_debut', $date_debut);
            $stmt->bindParam(':date_fin', $date_fin);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erreur SQL Soin::getSoinsByPeriode: " . $e->getMessage());
            return array();
        }
    }

    /**
     * Récupérer les statistiques par période
     */
    public function getStatsByPeriode($date_debut, $date_fin) {
        try {
            $query = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN statut = 'planifie' THEN 1 ELSE 0 END) as planifies,
                        SUM(CASE WHEN statut = 'en_cours' THEN 1 ELSE 0 END) as en_cours,
                        SUM(CASE WHEN statut = 'effectue' THEN 1 ELSE 0 END) as effectues,
                        SUM(CASE WHEN statut = 'annule' THEN 1 ELSE 0 END) as annules,
                        COUNT(DISTINCT patient_id) as patients_distincts,
                        COUNT(DISTINCT infirmier_id) as infirmiers_distincts
                      FROM " . $this->table . "
                      WHERE date_soin BETWEEN :date_debut AND :date_fin";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':date_debut', $date_debut);
            $stmt->bindParam(':date_fin', $date_fin);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Erreur SQL Soin::getStatsByPeriode: " . $e->getMessage());
            return array(
                'total' => 0,
                'planifies' => 0,
                'en_cours' => 0,
                'effectues' => 0,
                'annules' => 0,
                'patients_distincts' => 0,
                'infirmiers_distincts' => 0
            );
        }
    }

    /**
     * Récupérer les soins par type
     */
    public function getSoinsByType($type, $date_debut = null, $date_fin = null) {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE type_soin = :type";
            $params = array(':type' => $type);
            
            if($date_debut && $date_fin) {
                $query .= " AND date_soin BETWEEN :date_debut AND :date_fin";
                $params[':date_debut'] = $date_debut;
                $params[':date_fin'] = $date_fin;
            }
            
            $stmt = $this->conn->prepare($query);
            foreach($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch(PDOException $e) {
            error_log("Erreur SQL Soin::getSoinsByType: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Récupérer les types de soins distincts
     */
    public function getTypesSoins() {
        try {
            $query = "SELECT DISTINCT type_soin FROM " . $this->table . " ORDER BY type_soin";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch(PDOException $e) {
            error_log("Erreur SQL Soin::getTypesSoins: " . $e->getMessage());
            return array();
        }
    }
}
?>