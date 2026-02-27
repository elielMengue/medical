<?php
namespace Controllers;

use Models\Patient;
use Models\Soin;
use Models\Antecedent;

class RapportController extends BaseController {
    
    private $patientModel;
    private $soinModel;
    private $antecedentModel;
    
    public function __construct() {
        parent::__construct();
        $this->checkAccess();
        $this->patientModel = new Patient();
        $this->soinModel = new Soin();
        $this->antecedentModel = new Antecedent();
    }
    
    protected function checkAccess() {
        if(!isset($_SESSION['user_id'])) {
            header('Location: /projet_medical/app/public/index.php?controller=auth&action=loginForm');
            exit();
        }
        
        // Admin et Major seulement
        if($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'major') {
            $_SESSION['error'] = "Accès non autorisé";
            header('Location: index.php?controller=patient&action=index');
            exit();
        }
    }
    
    public function index() {
        $data = array(
            'total_patients' => $this->patientModel->compter(),
            'total_soins' => $this->soinModel->countAll(),
            'soins_aujourdhui' => $this->soinModel->countByDate(date('Y-m-d')),
            'patients_sans_antecedents' => $this->patientModel->countWithoutAntecedents()
        );
        
        $this->renderWithLayout('rapports/index', $data);
    }
    
    public function patients() {
        $patients = $this->patientModel->lireTous();
        $data = array('patients' => $patients);
        $this->renderWithLayout('rapports/patients', $data);
    }
    
    public function soins() {
        $date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-m-01');
        $date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : date('Y-m-d');
        
        $soins = $this->soinModel->getSoinsByPeriode($date_debut, $date_fin);
        $stats = $this->soinModel->getStatsByPeriode($date_debut, $date_fin);
        
        $data = array(
            'soins' => $soins,
            'stats' => $stats,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin
        );
        $this->renderWithLayout('rapports/soins', $data);
    }
    
    public function antecedents() {
        $patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : null;
        
        if($patient_id) {
            $patient = $this->patientModel->lireUn($patient_id);
            $antecedents = $this->antecedentModel->lireParPatient($patient_id);
            $data = array(
                'patient' => $patient,
                'antecedents' => $antecedents,
                'mode' => 'patient'
            );
        } else {
            // Tous les antécédents (à implémenter si nécessaire)
            $data = array('mode' => 'global');
        }
        
        $this->renderWithLayout('rapports/antecedents', $data);
    }
    
    public function exportPDF() {
        // À implémenter pour l'export PDF
        $_SESSION['info'] = "Fonctionnalité en cours de développement";
        header('Location: index.php?controller=rapport&action=index');
        exit();
    }
    
    public function exportExcel() {
        // À implémenter pour l'export Excel
        $_SESSION['info'] = "Fonctionnalité en cours de développement";
        header('Location: index.php?controller=rapport&action=index');
        exit();
    }
}
?>