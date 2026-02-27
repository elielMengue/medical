<?php
namespace Models;

use Config\Database;
use PDO;

class Antecedent {
    private $conn;
    private $table = "antecedents";

    // Propriétés
    public $id;
    public $patient_id;
    public $date_consultation;
    public $motif_consultation;
    public $historique_maladie;
    public $antecedents_medicaux;
    public $antecedents_chirurgicaux;
    public $antecedents_familiaux;
    public $allergies;
    public $ta;
    public $fc;
    public $temperature;
    public $fr;
    public $saturation;
    public $poids;
    public $appareil_pleuro_pulmonaire;
    public $appareil_cardio_vasculaire;
    public $appareil_digestif;
    public $appareil_locomoteur;
    public $appareil_uro_genital;
    public $autre_organe;
    public $resume_syndromique;
    public $diagnostic_presomption;
    public $examen_complementaire;
    public $traitement_symptomatique;
    public $resultat;
    public $traitement_specifique;
    public $created_at;
    public $created_by;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Créer un nouvel antécédent
     */
    public function creer() {
        $query = "INSERT INTO " . $this->table . " (
            patient_id, date_consultation, motif_consultation, 
            historique_maladie, antecedents_medicaux, antecedents_chirurgicaux, 
            antecedents_familiaux, allergies, ta, fc, temperature, fr, 
            saturation, poids, appareil_pleuro_pulmonaire, appareil_cardio_vasculaire, 
            appareil_digestif, appareil_locomoteur, appareil_uro_genital, 
            autre_organe, resume_syndromique, diagnostic_presomption, 
            examen_complementaire, traitement_symptomatique, resultat, traitement_specifique,
            created_by
        ) VALUES (
            :patient_id, :date_consultation, :motif_consultation,
            :historique_maladie, :antecedents_medicaux, :antecedents_chirurgicaux,
            :antecedents_familiaux, :allergies, :ta, :fc, :temperature, :fr,
            :saturation, :poids, :appareil_pleuro_pulmonaire, :appareil_cardio_vasculaire,
            :appareil_digestif, :appareil_locomoteur, :appareil_uro_genital,
            :autre_organe, :resume_syndromique, :diagnostic_presomption,
            :examen_complementaire, :traitement_symptomatique, :resultat, :traitement_specifique,
            :created_by
        )";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $this->patient_id = htmlspecialchars(strip_tags($this->patient_id));
        $this->date_consultation = htmlspecialchars(strip_tags($this->date_consultation));
        $this->motif_consultation = htmlspecialchars(strip_tags($this->motif_consultation));
        $this->historique_maladie = !empty($this->historique_maladie) ? htmlspecialchars(strip_tags($this->historique_maladie)) : null;
        $this->antecedents_medicaux = !empty($this->antecedents_medicaux) ? htmlspecialchars(strip_tags($this->antecedents_medicaux)) : null;
        $this->antecedents_chirurgicaux = !empty($this->antecedents_chirurgicaux) ? htmlspecialchars(strip_tags($this->antecedents_chirurgicaux)) : null;
        $this->antecedents_familiaux = !empty($this->antecedents_familiaux) ? htmlspecialchars(strip_tags($this->antecedents_familiaux)) : null;
        $this->allergies = !empty($this->allergies) ? htmlspecialchars(strip_tags($this->allergies)) : null;
        $this->ta = !empty($this->ta) ? htmlspecialchars(strip_tags($this->ta)) : null;
        $this->fc = !empty($this->fc) ? htmlspecialchars(strip_tags($this->fc)) : null;
        $this->temperature = !empty($this->temperature) ? htmlspecialchars(strip_tags($this->temperature)) : null;
        $this->fr = !empty($this->fr) ? htmlspecialchars(strip_tags($this->fr)) : null;
        $this->saturation = !empty($this->saturation) ? htmlspecialchars(strip_tags($this->saturation)) : null;
        $this->poids = !empty($this->poids) ? htmlspecialchars(strip_tags($this->poids)) : null;
        $this->appareil_pleuro_pulmonaire = !empty($this->appareil_pleuro_pulmonaire) ? htmlspecialchars(strip_tags($this->appareil_pleuro_pulmonaire)) : null;
        $this->appareil_cardio_vasculaire = !empty($this->appareil_cardio_vasculaire) ? htmlspecialchars(strip_tags($this->appareil_cardio_vasculaire)) : null;
        $this->appareil_digestif = !empty($this->appareil_digestif) ? htmlspecialchars(strip_tags($this->appareil_digestif)) : null;
        $this->appareil_locomoteur = !empty($this->appareil_locomoteur) ? htmlspecialchars(strip_tags($this->appareil_locomoteur)) : null;
        $this->appareil_uro_genital = !empty($this->appareil_uro_genital) ? htmlspecialchars(strip_tags($this->appareil_uro_genital)) : null;
        $this->autre_organe = !empty($this->autre_organe) ? htmlspecialchars(strip_tags($this->autre_organe)) : null;
        $this->resume_syndromique = !empty($this->resume_syndromique) ? htmlspecialchars(strip_tags($this->resume_syndromique)) : null;
        $this->diagnostic_presomption = !empty($this->diagnostic_presomption) ? htmlspecialchars(strip_tags($this->diagnostic_presomption)) : null;
        $this->examen_complementaire = !empty($this->examen_complementaire) ? htmlspecialchars(strip_tags($this->examen_complementaire)) : null;
        $this->traitement_symptomatique = !empty($this->traitement_symptomatique) ? htmlspecialchars(strip_tags($this->traitement_symptomatique)) : null;
        $this->resultat = !empty($this->resultat) ? htmlspecialchars(strip_tags($this->resultat)) : null;
        $this->traitement_specifique = !empty($this->traitement_specifique) ? htmlspecialchars(strip_tags($this->traitement_specifique)) : null;
        $this->created_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        // Binding
        $stmt->bindParam(':patient_id', $this->patient_id);
        $stmt->bindParam(':date_consultation', $this->date_consultation);
        $stmt->bindParam(':motif_consultation', $this->motif_consultation);
        $stmt->bindParam(':historique_maladie', $this->historique_maladie);
        $stmt->bindParam(':antecedents_medicaux', $this->antecedents_medicaux);
        $stmt->bindParam(':antecedents_chirurgicaux', $this->antecedents_chirurgicaux);
        $stmt->bindParam(':antecedents_familiaux', $this->antecedents_familiaux);
        $stmt->bindParam(':allergies', $this->allergies);
        $stmt->bindParam(':ta', $this->ta);
        $stmt->bindParam(':fc', $this->fc);
        $stmt->bindParam(':temperature', $this->temperature);
        $stmt->bindParam(':fr', $this->fr);
        $stmt->bindParam(':saturation', $this->saturation);
        $stmt->bindParam(':poids', $this->poids);
        $stmt->bindParam(':appareil_pleuro_pulmonaire', $this->appareil_pleuro_pulmonaire);
        $stmt->bindParam(':appareil_cardio_vasculaire', $this->appareil_cardio_vasculaire);
        $stmt->bindParam(':appareil_digestif', $this->appareil_digestif);
        $stmt->bindParam(':appareil_locomoteur', $this->appareil_locomoteur);
        $stmt->bindParam(':appareil_uro_genital', $this->appareil_uro_genital);
        $stmt->bindParam(':autre_organe', $this->autre_organe);
        $stmt->bindParam(':resume_syndromique', $this->resume_syndromique);
        $stmt->bindParam(':diagnostic_presomption', $this->diagnostic_presomption);
        $stmt->bindParam(':examen_complementaire', $this->examen_complementaire);
        $stmt->bindParam(':traitement_symptomatique', $this->traitement_symptomatique);
        $stmt->bindParam(':resultat', $this->resultat);
        $stmt->bindParam(':traitement_specifique', $this->traitement_specifique);
        $stmt->bindParam(':created_by', $this->created_by);

        try {
            if($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch(PDOException $e) {
            error_log("Erreur SQL Antecedent::creer: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lire tous les antécédents d'un patient
     */
    public function lireParPatient($patient_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE patient_id = :patient_id 
                  ORDER BY date_consultation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Lire un antécédent par son ID
     */
    public function lireUn($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    /**
     * Mettre à jour un antécédent
     */
    public function modifier() {
        $query = "UPDATE " . $this->table . " SET
            date_consultation = :date_consultation,
            motif_consultation = :motif_consultation,
            historique_maladie = :historique_maladie,
            antecedents_medicaux = :antecedents_medicaux,
            antecedents_chirurgicaux = :antecedents_chirurgicaux,
            antecedents_familiaux = :antecedents_familiaux,
            allergies = :allergies,
            ta = :ta,
            fc = :fc,
            temperature = :temperature,
            fr = :fr,
            saturation = :saturation,
            poids = :poids,
            appareil_pleuro_pulmonaire = :appareil_pleuro_pulmonaire,
            appareil_cardio_vasculaire = :appareil_cardio_vasculaire,
            appareil_digestif = :appareil_digestif,
            appareil_locomoteur = :appareil_locomoteur,
            appareil_uro_genital = :appareil_uro_genital,
            autre_organe = :autre_organe,
            resume_syndromique = :resume_syndromique,
            diagnostic_presomption = :diagnostic_presomption,
            examen_complementaire = :examen_complementaire,
            traitement_symptomatique = :traitement_symptomatique,
            resultat = :resultat,
            traitement_specifique = :traitement_specifique
            WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $this->date_consultation = htmlspecialchars(strip_tags($this->date_consultation));
        $this->motif_consultation = htmlspecialchars(strip_tags($this->motif_consultation));
        $this->historique_maladie = !empty($this->historique_maladie) ? htmlspecialchars(strip_tags($this->historique_maladie)) : null;
        $this->antecedents_medicaux = !empty($this->antecedents_medicaux) ? htmlspecialchars(strip_tags($this->antecedents_medicaux)) : null;
        $this->antecedents_chirurgicaux = !empty($this->antecedents_chirurgicaux) ? htmlspecialchars(strip_tags($this->antecedents_chirurgicaux)) : null;
        $this->antecedents_familiaux = !empty($this->antecedents_familiaux) ? htmlspecialchars(strip_tags($this->antecedents_familiaux)) : null;
        $this->allergies = !empty($this->allergies) ? htmlspecialchars(strip_tags($this->allergies)) : null;
        $this->ta = !empty($this->ta) ? htmlspecialchars(strip_tags($this->ta)) : null;
        $this->fc = !empty($this->fc) ? htmlspecialchars(strip_tags($this->fc)) : null;
        $this->temperature = !empty($this->temperature) ? htmlspecialchars(strip_tags($this->temperature)) : null;
        $this->fr = !empty($this->fr) ? htmlspecialchars(strip_tags($this->fr)) : null;
        $this->saturation = !empty($this->saturation) ? htmlspecialchars(strip_tags($this->saturation)) : null;
        $this->poids = !empty($this->poids) ? htmlspecialchars(strip_tags($this->poids)) : null;
        $this->appareil_pleuro_pulmonaire = !empty($this->appareil_pleuro_pulmonaire) ? htmlspecialchars(strip_tags($this->appareil_pleuro_pulmonaire)) : null;
        $this->appareil_cardio_vasculaire = !empty($this->appareil_cardio_vasculaire) ? htmlspecialchars(strip_tags($this->appareil_cardio_vasculaire)) : null;
        $this->appareil_digestif = !empty($this->appareil_digestif) ? htmlspecialchars(strip_tags($this->appareil_digestif)) : null;
        $this->appareil_locomoteur = !empty($this->appareil_locomoteur) ? htmlspecialchars(strip_tags($this->appareil_locomoteur)) : null;
        $this->appareil_uro_genital = !empty($this->appareil_uro_genital) ? htmlspecialchars(strip_tags($this->appareil_uro_genital)) : null;
        $this->autre_organe = !empty($this->autre_organe) ? htmlspecialchars(strip_tags($this->autre_organe)) : null;
        $this->resume_syndromique = !empty($this->resume_syndromique) ? htmlspecialchars(strip_tags($this->resume_syndromique)) : null;
        $this->diagnostic_presomption = !empty($this->diagnostic_presomption) ? htmlspecialchars(strip_tags($this->diagnostic_presomption)) : null;
        $this->examen_complementaire = !empty($this->examen_complementaire) ? htmlspecialchars(strip_tags($this->examen_complementaire)) : null;
        $this->traitement_symptomatique = !empty($this->traitement_symptomatique) ? htmlspecialchars(strip_tags($this->traitement_symptomatique)) : null;
        $this->resultat = !empty($this->resultat) ? htmlspecialchars(strip_tags($this->resultat)) : null;
        $this->traitement_specifique = !empty($this->traitement_specifique) ? htmlspecialchars(strip_tags($this->traitement_specifique)) : null;

        // Binding
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':date_consultation', $this->date_consultation);
        $stmt->bindParam(':motif_consultation', $this->motif_consultation);
        $stmt->bindParam(':historique_maladie', $this->historique_maladie);
        $stmt->bindParam(':antecedents_medicaux', $this->antecedents_medicaux);
        $stmt->bindParam(':antecedents_chirurgicaux', $this->antecedents_chirurgicaux);
        $stmt->bindParam(':antecedents_familiaux', $this->antecedents_familiaux);
        $stmt->bindParam(':allergies', $this->allergies);
        $stmt->bindParam(':ta', $this->ta);
        $stmt->bindParam(':fc', $this->fc);
        $stmt->bindParam(':temperature', $this->temperature);
        $stmt->bindParam(':fr', $this->fr);
        $stmt->bindParam(':saturation', $this->saturation);
        $stmt->bindParam(':poids', $this->poids);
        $stmt->bindParam(':appareil_pleuro_pulmonaire', $this->appareil_pleuro_pulmonaire);
        $stmt->bindParam(':appareil_cardio_vasculaire', $this->appareil_cardio_vasculaire);
        $stmt->bindParam(':appareil_digestif', $this->appareil_digestif);
        $stmt->bindParam(':appareil_locomoteur', $this->appareil_locomoteur);
        $stmt->bindParam(':appareil_uro_genital', $this->appareil_uro_genital);
        $stmt->bindParam(':autre_organe', $this->autre_organe);
        $stmt->bindParam(':resume_syndromique', $this->resume_syndromique);
        $stmt->bindParam(':diagnostic_presomption', $this->diagnostic_presomption);
        $stmt->bindParam(':examen_complementaire', $this->examen_complementaire);
        $stmt->bindParam(':traitement_symptomatique', $this->traitement_symptomatique);
        $stmt->bindParam(':resultat', $this->resultat);
        $stmt->bindParam(':traitement_specifique', $this->traitement_specifique);

        try {
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Erreur SQL Antecedent::modifier: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Sauvegarder uniquement les résultats d'un antécédent
     */
    public function saveResultat() {
        $query = "UPDATE " . $this->table . " 
                  SET resultat = :resultat, 
                      traitement_specifique = :traitement_specifique 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyage
        $this->resultat = !empty($this->resultat) ? htmlspecialchars(strip_tags($this->resultat)) : null;
        $this->traitement_specifique = !empty($this->traitement_specifique) ? htmlspecialchars(strip_tags($this->traitement_specifique)) : null;
        
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':resultat', $this->resultat);
        $stmt->bindParam(':traitement_specifique', $this->traitement_specifique);
        
        try {
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Erreur SQL Antecedent::saveResultat: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer un antécédent
     */
    public function supprimer($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        try {
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Erreur SQL Antecedent::supprimer: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Compter le nombre d'antécédents pour un patient
     */
    public function compterParPatient($patient_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE patient_id = :patient_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    /**
     * Récupérer les statistiques des antécédents
     */
    public function getStats() {
        $stats = array();
        
        // Total des antécédents
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total'] = $result['total'];
        
        // Antécédents par mois
        $query = "SELECT 
                    DATE_FORMAT(date_consultation, '%Y-%m') as mois,
                    COUNT(*) as total
                  FROM " . $this->table . "
                  GROUP BY mois
                  ORDER BY mois DESC
                  LIMIT 12";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['par_mois'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $stats;
    }

    /**
     * Récupérer les antécédents récents
     */
    public function getRecents($limite = 10) {
        $query = "SELECT a.*, p.nom, p.prenom 
                  FROM " . $this->table . " a
                  JOIN patients p ON a.patient_id = p.id
                  ORDER BY a.date_consultation DESC
                  LIMIT :limite";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
}
?>