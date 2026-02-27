<?php
namespace Controllers;

use Models\Patient;
use Models\Soin;
use Models\Antecedent;

class ExportController extends BaseController {
    
    private $patientModel;
    private $soinModel;
    private $antecedentModel;
    
    public function __construct() {
        parent::__construct();
        $this->patientModel = new Patient();
        $this->soinModel = new Soin();
        $this->antecedentModel = new Antecedent();
    }
    
    /**
     * Exporter la liste des patients en CSV
     */
    public function exportPatientsCSV() {
        $this->checkAccess();
        
        // Récupérer tous les patients
        $patients = $this->patientModel->lireTous();
        
        // Définir le nom du fichier
        $filename = 'patients_' . date('Y-m-d') . '.csv';
        
        // Définir les en-têtes HTTP pour le téléchargement
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Créer un fichier temporaire en mémoire
        $output = fopen('php://output', 'w');
        
        // Ajouter le BOM UTF-8 pour Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Définir les en-têtes des colonnes
        $headers = array('ID', 'Nom', 'Prénom', 'Date de naissance', 'Adresse', 'Téléphone', 'Date d\'inscription');
        fputcsv($output, $headers, ';');
        
        // Ajouter les données
        while($row = $patients->fetch(\PDO::FETCH_ASSOC)) {
            $data = array(
                $row['id'],
                $row['nom'],
                $row['prenom'],
                date('d/m/Y', strtotime($row['date_naissance'])),
                $row['adresse'],
                $row['telephone'],
                isset($row['created_at']) ? date('d/m/Y', strtotime($row['created_at'])) : ''
            );
            fputcsv($output, $data, ';');
        }
        
        fclose($output);
        exit();
    }
    
    /**
     * Exporter la liste des soins en CSV
     */
    public function exportSoinsCSV() {
        $this->checkAccess();
        
        // Récupérer tous les soins
        $soins = $this->soinModel->findAll();
        
        // Définir le nom du fichier
        $filename = 'soins_' . date('Y-m-d') . '.csv';
        
        // Définir les en-têtes HTTP pour le téléchargement
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Créer un fichier temporaire en mémoire
        $output = fopen('php://output', 'w');
        
        // Ajouter le BOM UTF-8 pour Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Définir les en-têtes des colonnes
        $headers = array('ID', 'Patient', 'Infirmier', 'Type de soin', 'Date', 'Heure', 'Lit', 'Statut');
        fputcsv($output, $headers, ';');
        
        // Ajouter les données
        while($row = $soins->fetch(\PDO::FETCH_ASSOC)) {
            $data = array(
                $row['id'],
                $row['patient_prenom'] . ' ' . $row['patient_nom'],
                $row['infirmier_prenom'] . ' ' . $row['infirmier_nom'],
                $row['type_soin'],
                date('d/m/Y', strtotime($row['date_soin'])),
                $row['heure_soin'],
                $row['numero_lit'],
                $this->getStatutLabel($row['statut'])
            );
            fputcsv($output, $data, ';');
        }
        
        fclose($output);
        exit();
    }
    
    /**
     * Exporter les antécédents d'un patient
     */
    public function exportAntecedentsPatient() {
        $this->checkAccess();
        
        if(!isset($_GET['patient_id'])) {
            $this->redirectError('index.php?controller=patient&action=index', 'ID patient manquant');
        }
        
        $patient_id = $_GET['patient_id'];
        $patient = $this->patientModel->lireUn($patient_id);
        
        if(!$patient) {
            $this->redirectError('index.php?controller=patient&action=index', 'Patient non trouvé');
        }
        
        $antecedents = $this->antecedentModel->lireParPatient($patient_id);
        
        // Définir le nom du fichier
        $filename = 'antecedents_' . $patient['nom'] . '_' . $patient['prenom'] . '_' . date('Y-m-d') . '.csv';
        $filename = str_replace(' ', '_', $filename);
        
        // Définir les en-têtes HTTP pour le téléchargement
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Créer un fichier temporaire en mémoire
        $output = fopen('php://output', 'w');
        
        // Ajouter le BOM UTF-8 pour Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Ajouter les informations du patient
        fputcsv($output, array('Patient:', $patient['nom'] . ' ' . $patient['prenom']), ';');
        fputcsv($output, array('Date de naissance:', date('d/m/Y', strtotime($patient['date_naissance']))), ';');
        fputcsv($output, array(), ';'); // Ligne vide
        
        // Définir les en-têtes des colonnes
        $headers = array('Type', 'Description', 'Date du diagnostic');
        fputcsv($output, $headers, ';');
        
        // Ajouter les données
        while($row = $antecedents->fetch(\PDO::FETCH_ASSOC)) {
            $data = array(
                $row['type'],
                $row['description'],
                date('d/m/Y', strtotime($row['date_diagnostic']))
            );
            fputcsv($output, $data, ';');
        }
        
        fclose($output);
        exit();
    }
    
    /**
     * Exporter le dossier médical complet d'un patient
     * (Informations personnelles + antécédents + soins)
     */
    public function exportPatientMedicalFile() {
        $this->checkAccess();
        
        // Vérifier plusieurs sources possibles pour l'ID
        $patient_id = null;
        
        if(isset($_GET['patient_id'])) {
            $patient_id = $_GET['patient_id'];
        } elseif(isset($_GET['id'])) {
            $patient_id = $_GET['id'];  // Accepter aussi 'id'
        }
        
        if($patient_id === null) {
            $_SESSION['error'] = "ID patient manquant dans la requête";
            header('Location: index.php?controller=patient&action=index');
            exit();
        }
        
        // Nettoyer l'ID
        $patient_id = intval($patient_id);
        
        if($patient_id <= 0) {
            $_SESSION['error'] = "ID patient invalide";
            header('Location: index.php?controller=patient&action=index');
            exit();
        }
        
        // Récupérer le patient
        $patient = $this->patientModel->lireUn($patient_id);
        
        if(!$patient) {
            $_SESSION['error'] = "Patient non trouvé (ID: $patient_id)";
            header('Location: index.php?controller=patient&action=index');
            exit();
        }
        
        // Récupérer les antécédents du patient
        $antecedents = $this->antecedentModel->lireParPatient($patient_id);
        
        // Récupérer les soins du patient
        $soins = $this->soinModel->findByPatient($patient_id);
        
        // Définir le nom du fichier
        $filename = 'dossier_medical_' . $patient['nom'] . '_' . $patient['prenom'] . '_' . date('Y-m-d') . '.csv';
        $filename = str_replace(' ', '_', $filename);
        
        // Définir les en-têtes HTTP pour le téléchargement
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Créer un fichier temporaire en mémoire
        $output = fopen('php://output', 'w');
        
        // Ajouter le BOM UTF-8 pour Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // === SECTION 1: INFORMATIONS PATIENT ===
        fputcsv($output, array('DOSSIER MÉDICAL PATIENT'), ';');
        fputcsv($output, array('Généré le:', date('d/m/Y H:i:s')), ';');
        fputcsv($output, array(), ';'); // Ligne vide
        
        fputcsv($output, array('INFORMATIONS PERSONNELLES'), ';');
        fputcsv($output, array('Nom:', $patient['nom']), ';');
        fputcsv($output, array('Prénom:', $patient['prenom']), ';');
        fputcsv($output, array('Date de naissance:', date('d/m/Y', strtotime($patient['date_naissance']))), ';');
        fputcsv($output, array('Adresse:', $patient['adresse']), ';');
        fputcsv($output, array('Téléphone:', $patient['telephone']), ';');
        fputcsv($output, array('Inscrit le:', isset($patient['created_at']) ? date('d/m/Y', strtotime($patient['created_at'])) : ''), ';');
        fputcsv($output, array(), ';'); // Ligne vide
        fputcsv($output, array(), ';'); // Ligne vide
        
        // === SECTION 2: ANTÉCÉDENTS MÉDICAUX ===
        fputcsv($output, array('ANTÉCÉDENTS MÉDICAUX'), ';');
        
        if($antecedents && $antecedents->rowCount() > 0) {
            $headers = array('Type', 'Description', 'Date du diagnostic');
            fputcsv($output, $headers, ';');
            
            while($row = $antecedents->fetch(\PDO::FETCH_ASSOC)) {
                $data = array(
                    $row['type'],
                    $row['description'],
                    date('d/m/Y', strtotime($row['date_diagnostic']))
                );
                fputcsv($output, $data, ';');
            }
        } else {
            fputcsv($output, array('Aucun antécédent médical enregistré'), ';');
        }
        
        fputcsv($output, array(), ';'); // Ligne vide
        fputcsv($output, array(), ';'); // Ligne vide
        
        // === SECTION 3: SOINS ===
        fputcsv($output, array('HISTORIQUE DES SOINS'), ';');
        
        if($soins && $soins->rowCount() > 0) {
            $headers = array('Date', 'Heure', 'Type de soin', 'Infirmier', 'Lit', 'Statut', 'Description');
            fputcsv($output, $headers, ';');
            
            while($row = $soins->fetch(\PDO::FETCH_ASSOC)) {
                $data = array(
                    date('d/m/Y', strtotime($row['date_soin'])),
                    $row['heure_soin'],
                    $row['type_soin'],
                    $row['infirmier_prenom'] . ' ' . $row['infirmier_nom'],
                    $row['numero_lit'],
                    $this->getStatutLabel($row['statut']),
                    $row['description']
                );
                fputcsv($output, $data, ';');
            }
        } else {
            fputcsv($output, array('Aucun soin enregistré'), ';');
        }
        
        fclose($output);
        exit();
    }
    
    /**
     * Exporter les statistiques en PDF (simulation)
     */
    public function exportStatsPDF() {
        $this->checkRole(array('admin', 'medecin'));
        
        // Simulation d'export PDF
        $this->redirectInfo('index.php?controller=admin&action=stats', 'Fonctionnalité en cours de développement');
    }
    
    /**
     * Exporter la liste des patients en PDF (simulation)
     */
    public function exportPatientsPDF() {
        $this->checkAccess();
        
        // Vérifier que l'utilisateur a les droits
        if($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'medecin') {
            $_SESSION['error'] = "Vous n'avez pas les droits pour exporter en PDF";
            header('Location: index.php?controller=patient&action=index');
            exit();
        }
        
        // Simulation d'export PDF
        $_SESSION['info'] = "L'export PDF est en cours de développement. Veuillez utiliser l'export CSV pour le moment.";
        header('Location: index.php?controller=patient&action=index');
        exit();
    }
    
    /**
     * Exporter en JSON
     */
    public function exportPatientsJSON() {
        $this->checkAccess();
        
        $patients = $this->patientModel->lireTous();
        
        $data = array();
        while($row = $patients->fetch(\PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
        
        $this->renderJson($data);
    }
    
    /**
     * Exporter en XML (simulation)
     */
    public function exportPatientsXML() {
        $this->checkAccess();
        
        $patients = $this->patientModel->lireTous();
        
        header('Content-Type: application/xml; charset=utf-8');
        header('Content-Disposition: attachment; filename="patients_' . date('Y-m-d') . '.xml"');
        
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<patients>';
        
        while($row = $patients->fetch(\PDO::FETCH_ASSOC)) {
            echo '<patient>';
            echo '<id>' . $row['id'] . '</id>';
            echo '<nom>' . htmlspecialchars($row['nom']) . '</nom>';
            echo '<prenom>' . htmlspecialchars($row['prenom']) . '</prenom>';
            echo '<date_naissance>' . $row['date_naissance'] . '</date_naissance>';
            echo '<adresse>' . htmlspecialchars($row['adresse']) . '</adresse>';
            echo '<telephone>' . $row['telephone'] . '</telephone>';
            echo '</patient>';
        }
        
        echo '</patients>';
        exit();
    }
    
    /**
     * Exporter un récapitulatif patient en PDF
     */
    public function exportPatientRecapPDF() {
        $this->checkAccess();
        
        if(!isset($_GET['patient_id'])) {
            $this->redirectError('index.php?controller=patient&action=index', 'ID patient manquant');
        }
        
        $patient_id = $_GET['patient_id'];
        
        // Simulation d'export PDF
        $_SESSION['info'] = "L'export PDF du récapitulatif patient est en cours de développement.";
        header('Location: index.php?controller=patient&action=show&id=' . $patient_id);
        exit();
    }
    
    /**
     * Obtenir le libellé du statut
     * @param string $statut
     * @return string
     */
    private function getStatutLabel($statut) {
        $labels = array(
            'planifie' => 'Planifié',
            'en_cours' => 'En cours',
            'effectue' => 'Effectué',
            'annule' => 'Annulé'
        );
        
        return isset($labels[$statut]) ? $labels[$statut] : $statut;
    }
}
?>