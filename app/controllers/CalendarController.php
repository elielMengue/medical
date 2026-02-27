<?php
namespace Controllers;

use Config\Database;
use Models\Patient;
use Models\Soin;

class CalendarController extends BaseController {
    
    private $patientModel;
    private $soinModel;
    
    public function __construct() {
        parent::__construct();
        $this->patientModel = new Patient();
        $this->soinModel = new Soin();
    }
    
    /**
     * Afficher le calendrier médical
     */
    public function index() {
        $this->checkAccess();
        
        // Récupérer les données pour le calendrier
        $appointments = $this->getAppointments();
        $soins = $this->getSoinsForCalendar();
        $stats = $this->getCalendarStats();
        $upcomingAppointments = $this->getUpcomingAppointments();
        $patients = $this->patientModel->lireTous()->fetchAll(\PDO::FETCH_ASSOC);
        
        $data = array(
            'appointments' => $appointments,
            'soins' => $soins,
            'stats' => $stats,
            'upcomingAppointments' => $upcomingAppointments,
            'patients' => $patients
        );
        
        $this->renderWithLayout('calendar/index', $data);
    }
    
    /**
     * Obtenir les rendez-vous pour le calendrier
     */
    private function getAppointments() {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT 
                    s.id,
                    s.patient_id,
                    CONCAT(s.date_soin, ' ', s.heure_soin) as date_heure,
                    60 as duree,
                    s.type_soin as motif,
                    s.statut,
                    CASE 
                        WHEN s.statut = 'planifie' THEN '#007bff'
                        WHEN s.statut = 'en_cours' THEN '#ffc107'
                        WHEN s.statut = 'effectue' THEN '#28a745'
                        WHEN s.statut = 'annule' THEN '#dc3545'
                        ELSE '#6c757d'
                    END as couleur,
                    p.nom as patient_nom,
                    p.prenom as patient_prenom
                  FROM soins s
                  JOIN patients p ON s.patient_id = p.id
                  WHERE s.date_soin >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
                    AND s.date_soin <= DATE_ADD(CURDATE(), INTERVAL 3 MONTH)
                  ORDER BY s.date_soin ASC, s.heure_soin ASC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $appointments = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Formater pour FullCalendar
        $events = array();
        foreach($appointments as $appointment) {
            $events[] = array(
                'id' => 'rdv_' . $appointment['id'],
                'title' => $appointment['patient_prenom'] . ' ' . $appointment['patient_nom'] . ' - ' . $appointment['motif'],
                'start' => $appointment['date_heure'],
                'end' => date('Y-m-d\TH:i:s', strtotime($appointment['date_heure'] . ' + ' . $appointment['duree'] . ' minutes')),
                'color' => $appointment['couleur'] ?: '#007bff',
                'textColor' => '#ffffff',
                'type' => 'appointment'
            );
        }
        
        return $events;
    }
    
    /**
     * Obtenir les soins pour le calendrier
     */
    private function getSoinsForCalendar() {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT 
                    s.id,
                    s.patient_id,
                    s.date_soin,
                    s.heure_soin,
                    s.type_soin,
                    s.statut,
                    s.infirmier_id,
                    p.nom as patient_nom,
                    p.prenom as patient_prenom,
                    u.nom as infirmier_nom,
                    u.prenom as infirmier_prenom
                  FROM soins s
                  JOIN patients p ON s.patient_id = p.id
                  JOIN utilisateurs u ON s.infirmier_id = u.id
                  WHERE s.date_soin >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
                    AND s.date_soin <= DATE_ADD(CURDATE(), INTERVAL 1 MONTH)
                  ORDER BY s.date_soin ASC, s.heure_soin ASC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $soins = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Formater pour FullCalendar
        $events = array();
        foreach($soins as $soin) {
            $startTime = $soin['date_soin'] . ' ' . $soin['heure_soin'];
            $events[] = array(
                'id' => 'soin_' . $soin['id'],
                'title' => $soin['patient_prenom'] . ' ' . $soin['patient_nom'] . ' - ' . $soin['type_soin'],
                'start' => $startTime,
                'color' => $this->getSoinColor($soin['statut']),
                'textColor' => '#ffffff',
                'type' => 'soin'
            );
        }
        
        return $events;
    }
    
    /**
     * Obtenir la couleur selon le statut du soin
     */
    private function getSoinColor($statut) {
        $colors = array(
            'planifie' => '#ffc107',
            'en_cours' => '#17a2b8',
            'effectue' => '#28a745',
            'annule' => '#dc3545'
        );
        
        return isset($colors[$statut]) ? $colors[$statut] : '#6c757d';
    }
    
    /**
     * Obtenir les statistiques du calendrier
     */
    private function getCalendarStats() {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Rendez-vous du mois (utiliser soins comme équivalent)
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN statut = 'planifie' THEN 1 ELSE 0 END) as confirmes,
                    SUM(CASE WHEN statut = 'en_cours' THEN 1 ELSE 0 END) as en_attente
                  FROM soins
                  WHERE MONTH(date_soin) = MONTH(CURDATE())
                    AND YEAR(date_soin) = YEAR(CURDATE())";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $rdvStats = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // Soins du mois
        $query = "SELECT COUNT(*) as total
                  FROM soins
                  WHERE MONTH(date_soin) = MONTH(CURDATE())
                    AND YEAR(date_soin) = YEAR(CURDATE())";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $soinsStats = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return array(
            'total_rendezvous' => $rdvStats['total'] ?: 0,
            'rendezvous_confirmes' => $rdvStats['confirmes'] ?: 0,
            'rendezvous_en_attente' => $rdvStats['en_attente'] ?: 0,
            'soins_planifies' => $soinsStats['total'] ?: 0
        );
    }
    
    /**
     * Obtenir les prochains rendez-vous
     */
    private function getUpcomingAppointments() {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT 
                    s.id,
                    CONCAT(s.date_soin, ' ', s.heure_soin) as date_heure,
                    s.type_soin as motif,
                    s.statut,
                    p.nom as patient_nom,
                    p.prenom as patient_prenom
                  FROM soins s
                  JOIN patients p ON s.patient_id = p.id
                  WHERE CONCAT(s.date_soin, ' ', s.heure_soin) >= NOW()
                    AND s.statut IN ('planifie', 'en_cours')
                  ORDER BY s.date_soin ASC, s.heure_soin ASC
                  LIMIT 5";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Créer un nouveau rendez-vous (API)
     */
    public function createAppointment() {
        $this->checkAccess();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if(empty($data['patient_id']) || empty($data['date_heure'])) {
                http_response_code(400);
                echo json_encode(array('error' => 'Données manquantes'));
                return;
            }
            
            $database = new Database();
            $conn = $database->getConnection();
            
            // Extraire date et heure
            $dateTime = new \DateTime($data['date_heure']);
            $date_soin = $dateTime->format('Y-m-d');
            $heure_soin = $dateTime->format('H:i:s');
            
            $query = "INSERT INTO soins (patient_id, date_soin, heure_soin, type_soin, statut, remarques, created_at) 
                      VALUES (:patient_id, :date_soin, :heure_soin, :type_soin, :statut, :remarques, NOW())";
            
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':patient_id', $data['patient_id']);
            $stmt->bindParam(':date_soin', $date_soin);
            $stmt->bindParam(':heure_soin', $heure_soin);
            $stmt->bindParam(':type_soin', $data['motif']);
            $stmt->bindParam(':statut', $data['statut'] ?: 'planifie');
            $stmt->bindParam(':remarques', $data['remarques'] ?: null);
            
            if($stmt->execute()) {
                echo json_encode(array('success' => true, 'id' => $conn->lastInsertId()));
            } else {
                http_response_code(500);
                echo json_encode(array('error' => 'Erreur lors de la création'));
            }
        }
    }
    
    /**
     * Obtenir tous les événements pour le calendrier (API)
     */
    public function getEvents() {
        $this->checkAccess();
        
        header('Content-Type: application/json');
        
        $appointments = $this->getAppointments();
        $soins = $this->getSoinsForCalendar();
        
        $events = array_merge($appointments, $soins);
        echo json_encode($events);
    }
}
?>