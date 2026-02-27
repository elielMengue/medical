<?php
namespace Controllers;

use Models\Soin;
use Models\Patient;

class DashboardController extends BaseController {
    
    private $soinModel;
    private $patientModel;
    
    public function __construct() {
        parent::__construct();
        $this->checkAuth();
        $this->soinModel = new Soin();
        $this->patientModel = new Patient();
    }
    
    public function index() {
        // Récupérer les statistiques
        $stats = array(
            'total_patients' => $this->patientModel->compter(),
            'total_soins' => $this->soinModel->countAll(),
            'soins_aujourdhui' => $this->soinModel->countByDate(date('Y-m-d')),
            'soins_en_cours' => $this->soinModel->countByStatut('en_cours')
        );
        
        // Récupérer les soins du jour
        $soinsAujourdhui = $this->soinModel->findByDate(date('Y-m-d'));
        
        // Récupérer les données pour les graphiques
        $soinsParMois = $this->soinModel->getSoinsParMois();
        $soinsParStatut = $this->soinModel->countByStatutAll();
        
        // Alertes éventuelles
        $alertes = $this->getAlertes();
        
        $data = array(
            'stats' => $stats,
            'soinsAujourdhui' => $soinsAujourdhui,
            'soinsParMois' => $soinsParMois,
            'soinsParStatut' => $soinsParStatut,
            'alertes' => $alertes
        );
        
        // Passer les données à la vue
        $this->renderWithLayout('dashboard/index', $data);
    }
    
    private function getAlertes() {
        $alertes = array();
        
        // Vérifier les soins en retard
        $soinsRetard = $this->soinModel->findSoinsEnRetard();
        if($soinsRetard && $soinsRetard->rowCount() > 0) {
            $alertes[] = array(
                'type' => 'warning',
                'icon' => 'bi-exclamation-triangle-fill',
                'titre' => 'Soins en retard',
                'message' => $soinsRetard->rowCount() . ' soin(s) n\'ont pas été effectués'
            );
        }
        
        // Vérifier les patients sans antécédents
        $patientsSansAntecedents = $this->patientModel->findWithoutAntecedents();
        if($patientsSansAntecedents && $patientsSansAntecedents->rowCount() > 0) {
            $alertes[] = array(
                'type' => 'info',
                'icon' => 'bi-info-circle-fill',
                'titre' => 'Dossiers incomplets',
                'message' => $patientsSansAntecedents->rowCount() . ' patient(s) sans antécédents médicaux'
            );
        }
        
        return $alertes;
    }
}
?>