<?php
namespace Controllers;

use Models\Soins;
use Models\Patient;

class SoinsController extends BaseController {
    private $soinsModel;
    private $patientModel;
    
    public function __construct() {
        parent::__construct();
        $this->soinsModel = new Soins();
        $this->patientModel = new Patient();
    }
    
    // Afficher le formulaire de création d'un soin
    public function create() {
        $this->checkEditAccess();
        
        if(isset($_GET['patient_id'])) {
            $this->patientModel->id = $_GET['patient_id'];
            $patient = $this->patientModel->lireUn();
            
            if($patient) {
                require_once dirname(__DIR__) . '/views/soins/create.php';
            } else {
                $_SESSION['error'] = "Patient non trouvé";
                header('Location: index.php?controller=patient&action=index');
                exit();
            }
        } else {
            $_SESSION['error'] = "ID patient manquant";
            header('Location: index.php?controller=patient&action=index');
            exit();
        }
    }
    
    // Enregistrer un nouveau soin
    public function store() {
        $this->checkEditAccess();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = array();
            
            if(empty($_POST['patient_id'])) {
                $errors[] = "ID patient manquant";
            }
            if(empty($_POST['date_soin'])) {
                $errors[] = "La date du soin est obligatoire";
            }
            if(empty($_POST['heure_soin'])) {
                $errors[] = "L'heure du soin est obligatoire";
            }
            if(empty($_POST['type_soin'])) {
                $errors[] = "Le type de soin est obligatoire";
            }
            if(empty($_POST['infirmier_id'])) {
                $errors[] = "L'infirmier est obligatoire";
            }
            
            if(empty($errors)) {
                $this->soinsModel->patient_id = $_POST['patient_id'];
                $this->soinsModel->infirmier_id = $_POST['infirmier_id'];
                $this->soinsModel->date_soin = $_POST['date_soin'];
                $this->soinsModel->heure_soin = $_POST['heure_soin'];
                $this->soinsModel->type_soin = $_POST['type_soin'];
                $this->soinsModel->description = !empty($_POST['description']) ? $_POST['description'] : null;
                $this->soinsModel->numero_lit = !empty($_POST['numero_lit']) ? $_POST['numero_lit'] : null;
                $this->soinsModel->statut = !empty($_POST['statut']) ? $_POST['statut'] : 'planifie';
                $this->soinsModel->created_by = $_SESSION['user_id'];
                
                if($this->soinsModel->creer()) {
                    $_SESSION['success'] = "Soin créé avec succès";
                    header('Location: index.php?controller=patient&action=show&id=' . $_POST['patient_id']);
                    exit();
                } else {
                    $errors[] = "Erreur lors de la création du soin";
                }
            }
            
            if(!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: index.php?controller=soins&action=create&patient_id=' . $_POST['patient_id']);
                exit();
            }
        }
    }
    
    // Afficher le formulaire d'édition d'un soin
    public function edit() {
        $this->checkEditAccess();
        
        if(isset($_GET['id'])) {
            $this->soinsModel->id = $_GET['id'];
            $soin = $this->soinsModel->lireUn();
            
            if($soin) {
                $this->patientModel->id = $soin['patient_id'];
                $patient = $this->patientModel->lireUn();
                
                require_once dirname(__DIR__) . '/views/soins/edit.php';
            } else {
                $_SESSION['error'] = "Soin non trouvé";
                header('Location: index.php?controller=patient&action=index');
                exit();
            }
        } else {
            $_SESSION['error'] = "ID soin manquant";
            header('Location: index.php?controller=patient&action=index');
            exit();
        }
    }
    
    // Mettre à jour un soin
    public function update() {
        $this->checkEditAccess();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = array();
            
            if(empty($_POST['id'])) {
                $errors[] = "ID soin manquant";
            }
            if(empty($_POST['date_soin'])) {
                $errors[] = "La date du soin est obligatoire";
            }
            if(empty($_POST['heure_soin'])) {
                $errors[] = "L'heure du soin est obligatoire";
            }
            if(empty($_POST['type_soin'])) {
                $errors[] = "Le type de soin est obligatoire";
            }
            if(empty($_POST['infirmier_id'])) {
                $errors[] = "L'infirmier est obligatoire";
            }
            
            if(empty($errors)) {
                $this->soinsModel->id = $_POST['id'];
                $this->soinsModel->infirmier_id = $_POST['infirmier_id'];
                $this->soinsModel->date_soin = $_POST['date_soin'];
                $this->soinsModel->heure_soin = $_POST['heure_soin'];
                $this->soinsModel->type_soin = $_POST['type_soin'];
                $this->soinsModel->description = !empty($_POST['description']) ? $_POST['description'] : null;
                $this->soinsModel->numero_lit = !empty($_POST['numero_lit']) ? $_POST['numero_lit'] : null;
                $this->soinsModel->statut = $_POST['statut'];
                
                if($this->soinsModel->modifier()) {
                    $_SESSION['success'] = "Soin modifié avec succès";
                    header('Location: index.php?controller=patient&action=show&id=' . $_POST['patient_id']);
                    exit();
                } else {
                    $errors[] = "Erreur lors de la modification du soin";
                }
            }
            
            if(!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: index.php?controller=soins&action=edit&id=' . $_POST['id']);
                exit();
            }
        }
    }
    
    // Supprimer un soin
    public function delete() {
        $this->checkEditAccess();
        
        if(isset($_GET['id'])) {
            $this->soinsModel->id = $_GET['id'];
            $soin = $this->soinsModel->lireUn();
            
            if($soin) {
                if($this->soinsModel->supprimer()) {
                    $_SESSION['success'] = "Soin supprimé avec succès";
                } else {
                    $_SESSION['error'] = "Erreur lors de la suppression du soin";
                }
                header('Location: index.php?controller=patient&action=show&id=' . $soin['patient_id']);
                exit();
            } else {
                $_SESSION['error'] = "Soin non trouvé";
                header('Location: index.php?controller=patient&action=index');
                exit();
            }
        } else {
            $_SESSION['error'] = "ID soin manquant";
            header('Location: index.php?controller=patient&action=index');
            exit();
        }
    }
    
    // Liste des soins (calendrier)
    public function index() {
        $this->checkAccess();
        
        // Récupérer les soins des 3 derniers mois et des 3 prochains mois
        $date_debut = date('Y-m-d', strtotime('-3 months'));
        $date_fin = date('Y-m-d', strtotime('+3 months'));
        
        $soins = $this->soinsModel->lireParPeriode($date_debut, $date_fin);
        
        require_once dirname(__DIR__) . '/views/soins/index.php';
    }
}
?>