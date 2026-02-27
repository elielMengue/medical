<?php
namespace Controllers;

use Models\Soin;
use Models\Patient;
use Models\User;

class SoinController extends BaseController {
    private $soinModel;
    private $patientModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->checkAuth();
        $this->soinModel = new Soin();
        $this->patientModel = new Patient();
        $this->userModel = new User();
    }

    public function index() {
        $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        $soins = $this->soinModel->findByDate($date);
        $data = array('soins' => $soins);
        $this->renderWithLayout('soins/index', $data);
    }

    public function monPlanning() {
        // Vérifier que l'utilisateur est infirmier
        if($_SESSION['user_role'] != 'infirmier') {
            $_SESSION['error'] = "Accès non autorisé";
            header('Location: /projet_medical/app/public/index.php?controller=soin&action=index');
            exit();
        }
        
        $infirmier_id = $_SESSION['user_id'];
        $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        
        $soins = $this->soinModel->findByInfirmierAndDate($infirmier_id, $date);
        $infirmier = $this->userModel->findById($infirmier_id);
        
        $data = array(
            'soins' => $soins,
            'infirmier' => $infirmier
        );
        $this->renderWithLayout('soins/mon_planning', $data);
    }

    public function create() {
        // Admin peut tout faire
        if($_SESSION['user_role'] == 'admin') {
            // Admin peut créer des soins
        } elseif($_SESSION['user_role'] != 'major') {
            $_SESSION['error'] = "Vous n'avez pas les droits pour planifier des soins";
            header('Location: /projet_medical/app/public/index.php?controller=soin&action=index');
            exit();
        }
        
        $patients = $this->soinModel->getPatients();
        $infirmiers = $this->soinModel->getInfirmiers();
        
        $data = array(
            'patients' => $patients,
            'infirmiers' => $infirmiers
        );
        $this->renderWithLayout('soins/create', $data);
    }

    public function store() {
        // Admin peut tout faire
        if($_SESSION['user_role'] == 'admin') {
            // Admin peut créer des soins
        } elseif($_SESSION['user_role'] != 'major') {
            $_SESSION['error'] = "Vous n'avez pas les droits pour planifier des soins";
            header('Location: /projet_medical/app/public/index.php?controller=soin&action=index');
            exit();
        }
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = array();
            
            if(empty($_POST['patient_id'])) $errors[] = "Le patient est obligatoire";
            if(empty($_POST['infirmier_id'])) $errors[] = "L'infirmier est obligatoire";
            if(empty($_POST['type_soin'])) $errors[] = "Le type de soin est obligatoire";
            if(empty($_POST['date_soin'])) $errors[] = "La date est obligatoire";
            if(empty($_POST['heure_soin'])) $errors[] = "L'heure est obligatoire";
            
            if(empty($errors)) {
                $this->soinModel->patient_id = $_POST['patient_id'];
                $this->soinModel->infirmier_id = $_POST['infirmier_id'];
                $this->soinModel->type_soin = $_POST['type_soin'];
                $this->soinModel->description = isset($_POST['description']) ? $_POST['description'] : '';
                $this->soinModel->date_soin = $_POST['date_soin'];
                $this->soinModel->heure_soin = $_POST['heure_soin'];
                $this->soinModel->numero_lit = isset($_POST['numero_lit']) ? $_POST['numero_lit'] : '';
                $this->soinModel->statut = 'planifie';
                $this->soinModel->created_by = $_SESSION['user_id'];
                
                if($this->soinModel->create()) {
                    $_SESSION['success'] = "Soin planifié avec succès";
                    header('Location: /projet_medical/app/public/index.php?controller=soin&action=index');
                    exit();
                } else {
                    $errors[] = "Erreur lors de la planification du soin";
                }
            }
            
            if(!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_input'] = $_POST;
                header('Location: /projet_medical/app/public/index.php?controller=soin&action=create');
                exit();
            }
        }
    }

    public function show() {
        if(isset($_GET['id'])) {
            $soin = $this->soinModel->findById($_GET['id']);
            if($soin) {
                $data = array('soin' => $soin);
                $this->renderWithLayout('soins/show', $data);
            } else {
                $_SESSION['error'] = "Soin non trouvé";
                header('Location: /projet_medical/app/public/index.php?controller=soin&action=index');
                exit();
            }
        }
    }

    public function edit() {
        // Admin peut tout faire
        if($_SESSION['user_role'] == 'admin') {
            // Admin peut modifier
        } elseif($_SESSION['user_role'] != 'major') {
            $_SESSION['error'] = "Vous n'avez pas les droits pour modifier des soins";
            header('Location: /projet_medical/app/public/index.php?controller=soin&action=index');
            exit();
        }
        
        if(isset($_GET['id'])) {
            $soin = $this->soinModel->findById($_GET['id']);
            $patients = $this->soinModel->getPatients();
            $infirmiers = $this->soinModel->getInfirmiers();
            
            if($soin) {
                $data = array(
                    'soin' => $soin,
                    'patients' => $patients,
                    'infirmiers' => $infirmiers
                );
                $this->renderWithLayout('soins/edit', $data);
            } else {
                $_SESSION['error'] = "Soin non trouvé";
                header('Location: /projet_medical/app/public/index.php?controller=soin&action=index');
                exit();
            }
        }
    }

    public function update() {
        // Admin peut tout faire
        if($_SESSION['user_role'] == 'admin') {
            // Admin peut modifier
        } elseif($_SESSION['user_role'] != 'major') {
            $_SESSION['error'] = "Vous n'avez pas les droits pour modifier des soins";
            header('Location: /projet_medical/app/public/index.php?controller=soin&action=index');
            exit();
        }
        
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $errors = array();
            
            if(empty($_POST['patient_id'])) $errors[] = "Le patient est obligatoire";
            if(empty($_POST['infirmier_id'])) $errors[] = "L'infirmier est obligatoire";
            if(empty($_POST['type_soin'])) $errors[] = "Le type de soin est obligatoire";
            
            if(empty($errors)) {
                $this->soinModel->patient_id = $_POST['patient_id'];
                $this->soinModel->infirmier_id = $_POST['infirmier_id'];
                $this->soinModel->type_soin = $_POST['type_soin'];
                $this->soinModel->description = isset($_POST['description']) ? $_POST['description'] : '';
                $this->soinModel->date_soin = $_POST['date_soin'];
                $this->soinModel->heure_soin = $_POST['heure_soin'];
                $this->soinModel->numero_lit = isset($_POST['numero_lit']) ? $_POST['numero_lit'] : '';
                $this->soinModel->statut = $_POST['statut'];
                
                if($this->soinModel->update($_POST['id'])) {
                    $_SESSION['success'] = "Soin mis à jour avec succès";
                    header('Location: /projet_medical/app/public/index.php?controller=soin&action=show&id=' . $_POST['id']);
                    exit();
                } else {
                    $errors[] = "Erreur lors de la mise à jour";
                }
            }
            
            if(!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: /projet_medical/app/public/index.php?controller=soin&action=edit&id=' . $_POST['id']);
                exit();
            }
        }
    }

    public function updateStatut() {
        if($_SESSION['user_role'] != 'infirmier') {
            $_SESSION['error'] = "Seul un infirmier peut marquer un soin comme effectué";
            header('Location: /projet_medical/app/public/index.php?controller=soin&action=index');
            exit();
        }
        
        if(isset($_GET['id']) && isset($_GET['statut'])) {
            $soin_id = $_GET['id'];
            $infirmier_id = $_SESSION['user_id'];
            
            $soin = $this->soinModel->findById($soin_id);
            
            if(!$soin) {
                $_SESSION['error'] = "Soin non trouvé";
                header('Location: /projet_medical/app/public/index.php?controller=soin&action=monPlanning');
                exit();
            }
            
            if($soin['infirmier_id'] != $infirmier_id) {
                $_SESSION['error'] = "Vous ne pouvez pas modifier un soin qui ne vous est pas assigné";
                header('Location: /projet_medical/app/public/index.php?controller=soin&action=monPlanning');
                exit();
            }
            
            if($this->soinModel->updateStatut($soin_id, $_GET['statut'])) {
                $_SESSION['success'] = "Soin marqué comme effectué";
            } else {
                $_SESSION['error'] = "Erreur lors de la mise à jour du statut";
            }
            header('Location: /projet_medical/app/public/index.php?controller=soin&action=monPlanning');
            exit();
        }
    }

    public function delete() {
        // Admin peut supprimer
        if($_SESSION['user_role'] != 'admin') {
            $_SESSION['error'] = "Seul l'administrateur peut supprimer des soins";
            header('Location: /projet_medical/app/public/index.php?controller=soin&action=index');
            exit();
        }
        
        if(isset($_GET['id'])) {
            if($this->soinModel->delete($_GET['id'])) {
                $_SESSION['success'] = "Soin supprimé avec succès";
            } else {
                $_SESSION['error'] = "Erreur lors de la suppression";
            }
            header('Location: /projet_medical/app/public/index.php?controller=soin&action=index');
            exit();
        }
    }
}
?>