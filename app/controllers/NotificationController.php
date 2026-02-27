<?php
namespace Controllers;

use Config\Database;

class NotificationController extends BaseController {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Obtenir les notifications pour l'utilisateur connecté
     */
    public function getNotifications() {
        $this->checkAccess();
        
        header('Content-Type: application/json');
        
        $notifications = $this->generateNotifications();
        
        echo json_encode(array(
            'notifications' => $notifications,
            'unread_count' => count(array_filter($notifications, function($n) {
                return !$n['is_read'];
            }))
        ));
    }
    
    /**
     * Marquer une notification comme lue
     */
    public function markAsRead() {
        $this->checkAccess();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $notificationId = isset($data['notification_id']) ? $data['notification_id'] : null;
            
            if($notificationId) {
                // Ici on pourrait stocker en base de données
                // Pour l'instant, on simule le succès
                echo json_encode(array('success' => true));
            } else {
                http_response_code(400);
                echo json_encode(array('error' => 'ID de notification manquant'));
            }
        }
    }
    
    /**
     * Supprimer une notification
     */
    public function deleteNotification() {
        $this->checkAccess();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $notificationId = isset($data['notification_id']) ? $data['notification_id'] : null;
            
            if($notificationId) {
                // Ici on pourrait supprimer de la base de données
                // Pour l'instant, on simule le succès
                echo json_encode(array('success' => true));
            } else {
                http_response_code(400);
                echo json_encode(array('error' => 'ID de notification manquant'));
            }
        }
    }
    
    /**
     * Générer les notifications basées sur les données actuelles
     */
    private function generateNotifications() {
        $notifications = array();
        
        // 1. Rendez-vous du jour
        $notifications = array_merge($notifications, $this->getTodayAppointments());
        
        // 2. Soins à venir
        $notifications = array_merge($notifications, $this->getUpcomingSoins());
        
        // 3. Patients sans rendez-vous depuis longtemps
        $notifications = array_merge($notifications, $this->getPatientsWithoutAppointments());
        
        // 4. Soins urgents ou en retard
        $notifications = array_merge($notifications, $this->getUrgentSoins());
        
        // 5. Rappels de médicaments (si disponible)
        $notifications = array_merge($notifications, $this->getMedicationReminders());
        
        // Trier par date de création (les plus récentes en premier)
        usort($notifications, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        // Limiter à 20 notifications
        return array_slice($notifications, 0, 20);
    }
    
    /**
     * Obtenir les rendez-vous du jour
     */
    private function getTodayAppointments() {
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
                  WHERE DATE(s.date_soin) = CURDATE()
                    AND s.statut IN ('planifie', 'en_cours')
                  ORDER BY s.heure_soin ASC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $appointments = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $notifications = array();
        foreach($appointments as $appointment) {
            $time = new \DateTime($appointment['date_heure']);
            $now = new \DateTime();
            $diff = $now->diff($time);
            
            // Notification pour les rendez-vous dans les 2 prochaines heures
            if($time > $now && $diff->h < 2 && $diff->d == 0) {
                $notifications[] = array(
                    'id' => 'rdv_' . $appointment['id'],
                    'type' => 'appointment',
                    'title' => 'Rendez-vous imminent',
                    'message' => $appointment['patient_prenom'] . ' ' . $appointment['patient_nom'] . 
                                  ' - ' . $appointment['motif'] . ' à ' . $time->format('H:i'),
                    'priority' => 'high',
                    'is_read' => false,
                    'created_at' => date('Y-m-d H:i:s'),
                    'icon' => 'bi-calendar-check',
                    'color' => 'danger'
                );
            } else {
                // Notification normale pour les rendez-vous du jour
                $notifications[] = array(
                    'id' => 'rdv_' . $appointment['id'],
                    'type' => 'appointment',
                    'title' => 'Rendez-vous aujourd\'hui',
                    'message' => $appointment['patient_prenom'] . ' ' . $appointment['patient_nom'] . 
                                  ' - ' . $appointment['motif'] . ' à ' . $time->format('H:i'),
                    'priority' => 'medium',
                    'is_read' => false,
                    'created_at' => date('Y-m-d H:i:s'),
                    'icon' => 'bi-calendar-event',
                    'color' => 'info'
                );
            }
        }
        
        return $notifications;
    }
    
    /**
     * Obtenir les soins à venir
     */
    private function getUpcomingSoins() {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT 
                    s.id,
                    s.date_soin,
                    s.heure_soin,
                    s.type_soin,
                    s.statut,
                    p.nom as patient_nom,
                    p.prenom as patient_prenom,
                    u.nom as infirmier_nom,
                    u.prenom as infirmier_prenom
                  FROM soins s
                  JOIN patients p ON s.patient_id = p.id
                  JOIN utilisateurs u ON s.infirmier_id = u.id
                  WHERE s.date_soin >= CURDATE()
                    AND s.date_soin <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                    AND s.statut IN ('planifie', 'en_cours')
                  ORDER BY s.date_soin ASC, s.heure_soin ASC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $soins = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $notifications = array();
        foreach($soins as $soin) {
            $dateTime = new \DateTime($soin['date_soin'] . ' ' . $soin['heure_soin']);
            
            $notifications[] = array(
                'id' => 'soin_' . $soin['id'],
                'type' => 'care',
                'title' => 'Soin planifié',
                'message' => $soin['type_soin'] . ' pour ' . $soin['patient_prenom'] . ' ' . $soin['patient_nom'] .
                            ' le ' . $dateTime->format('d/m') . ' à ' . $dateTime->format('H:i'),
                'priority' => 'medium',
                'is_read' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'icon' => 'bi-heart-pulse',
                'color' => 'warning'
            );
        }
        
        return $notifications;
    }
    
    /**
     * Obtenir les patients sans rendez-vous depuis longtemps
     */
    private function getPatientsWithoutAppointments() {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT 
                    p.id,
                    p.nom,
                    p.prenom,
                    MAX(s.date_soin) as dernier_rdv
                  FROM patients p
                  LEFT JOIN soins s ON p.id = s.patient_id
                  GROUP BY p.id, p.nom, p.prenom
                  HAVING dernier_rdv IS NULL OR dernier_rdv <= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $patients = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $notifications = array();
        foreach($patients as $patient) {
            $notifications[] = array(
                'id' => 'patient_' . $patient['id'],
                'type' => 'patient',
                'title' => 'Patient sans suivi',
                'message' => $patient['prenom'] . ' ' . $patient['nom'] . 
                            ' n\'a pas eu de rendez-vous depuis plus de 6 mois',
                'priority' => 'low',
                'is_read' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'icon' => 'bi-person-exclamation',
                'color' => 'secondary'
            );
        }
        
        return $notifications;
    }
    
    /**
     * Obtenir les soins urgents ou en retard
     */
    private function getUrgentSoins() {
        $database = new Database();
        $conn = $database->getConnection();
        
        $query = "SELECT 
                    s.id,
                    s.date_soin,
                    s.heure_soin,
                    s.type_soin,
                    s.statut,
                    p.nom as patient_nom,
                    p.prenom as patient_prenom
                  FROM soins s
                  JOIN patients p ON s.patient_id = p.id
                  WHERE s.date_soin < CURDATE()
                    AND s.statut = 'planifie'
                  ORDER BY s.date_soin ASC";
        
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $soins = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $notifications = array();
        foreach($soins as $soin) {
            $notifications[] = array(
                'id' => 'urgent_' . $soin['id'],
                'type' => 'urgent',
                'title' => 'Soin en retard',
                'message' => 'Soin ' . $soin['type_soin'] . ' pour ' . $soin['patient_prenom'] . ' ' . $soin['patient_nom'] .
                            ' était prévu le ' . date('d/m/Y', strtotime($soin['date_soin'])),
                'priority' => 'high',
                'is_read' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'icon' => 'bi-exclamation-triangle',
                'color' => 'danger'
            );
        }
        
        return $notifications;
    }
    
    /**
     * Obtenir les rappels de médicaments (simulé)
     */
    private function getMedicationReminders() {
        // Cette fonction serait implémentée si on avait une table de traitements/médicaments
        // Pour l'instant, on retourne un tableau vide
        return array();
    }
}
?>