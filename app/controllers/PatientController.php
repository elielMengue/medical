<?php
namespace Controllers;

use Models\Patient;
use Models\Antecedent;
use PDO;

class PatientController extends BaseController {
    private $patientModel;

    public function __construct() {
        parent::__construct();
        $this->patientModel = new Patient();
    }

    protected function checkAccess() {
        if(!isset($_SESSION['user_id'])) {
            header('Location: /projet_medical/app/public/index.php?controller=auth&action=loginForm');
            exit();
        }
        
        if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
            return;
        }
        
        if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'infirmier') {
            $_SESSION['error'] = "Accès non autorisé. Les infirmiers n'ont pas accès à la gestion des patients.";
            header('Location: /projet_medical/app/public/index.php?controller=auth&action=profile');
            exit();
        }
    }

   private function checkEditAccess() {
    $this->checkAccess();
    
    $allowed_roles = array('admin', 'medecin', 'major');
    
    if(in_array($_SESSION['user_role'], $allowed_roles)) {
        return;
    }
    
    $_SESSION['error'] = "Vous n'avez pas les droits pour modifier un patient";
    header('Location: index.php?controller=patient&action=search');
    exit();
}
    
    private function checkDeleteAccess() {
        $this->checkAccess();
        
        if($_SESSION['user_role'] == 'admin') {
            return;
        }
        
        $_SESSION['error'] = "Seul l'administrateur peut supprimer des patients";
        header('Location: index.php?controller=patient&action=search');
        exit();
    }

    public function index() {
        header('Location: index.php?controller=patient&action=search');
        exit();
    }

    public function create() {
        $this->checkEditAccess();
        $this->renderWithLayout('patients/create');
    }

    public function store() {
        $this->checkEditAccess();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = array();
            
            if(empty($_POST['nom'])) $errors[] = "Le nom est obligatoire";
            if(empty($_POST['prenom'])) $errors[] = "Le prénom est obligatoire";
            if(empty($_POST['sexe'])) $errors[] = "Le sexe est obligatoire";
            if(empty($_POST['date_naissance'])) $errors[] = "La date de naissance est obligatoire";

            if(empty($errors)) {
                $this->patientModel->nom = $_POST['nom'];
                $this->patientModel->prenom = $_POST['prenom'];
                $this->patientModel->sexe = $_POST['sexe'];
                $this->patientModel->date_naissance = $_POST['date_naissance'];
                $this->patientModel->adresse = !empty($_POST['adresse']) ? $_POST['adresse'] : null;
                $this->patientModel->telephone = !empty($_POST['telephone']) ? $_POST['telephone'] : null;
                $this->patientModel->groupe_sanguin = !empty($_POST['groupe_sanguin']) ? $_POST['groupe_sanguin'] : null; // AJOUTÉ

                $result = $this->patientModel->creer();
                
                if($result === 'duplicate') {
                    $errors[] = "Un patient avec le même nom, prénom et date de naissance existe déjà";
                } elseif($result) {
                    $_SESSION['success'] = "Patient ajouté avec succès";
                    header('Location: index.php?controller=antecedent&action=create&patient_id=' . $result);
                    exit();
                } else {
                    $errors[] = "Erreur lors de l'enregistrement";
                }
            }
            
            if(!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_input'] = $_POST;
                header('Location: index.php?controller=patient&action=create');
                exit();
            }
        }
    }

    public function show() {
        $this->checkAccess();
        
        if(isset($_GET['id'])) {
            $this->patientModel->id = $_GET['id'];
            $patient = $this->patientModel->lireUn();
            
            if($patient) {
                $antecedentModel = new Antecedent();
                $antecedents = $antecedentModel->lireParPatient($_GET['id']);
                $data = array(
                    'patient' => $patient,
                    'antecedents' => $antecedents
                );
                $this->renderWithLayout('patients/show', $data);
            } else {
                $_SESSION['error'] = "Patient non trouvé";
                header('Location: index.php?controller=patient&action=search');
                exit();
            }
        }
    }

    public function edit() {
    // Vérification directe pour admin
    if($_SESSION['user_role'] == 'admin') {
        // Admin peut modifier
    } else {
        // Pour les autres, on utilise checkEditAccess
        $this->checkEditAccess();
    }
    
    if(isset($_GET['id']) && !empty($_GET['id'])) {
        $id = intval($_GET['id']);
        $this->patientModel->id = $id;
        $patient = $this->patientModel->lireUn();
        
        if($patient) {
            $data = array('patient' => $patient);
            $this->renderWithLayout('patients/edit', $data);
        } else {
            $_SESSION['error'] = "Patient non trouvé";
            header('Location: index.php?controller=patient&action=search');
            exit();
        }
    } else {
        $_SESSION['error'] = "ID patient manquant";
        header('Location: index.php?controller=patient&action=search');
        exit();
    }
}

    public function update() {
        $this->checkEditAccess();
        
        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $errors = array();
            
            if(empty($_POST['id'])) $errors[] = "ID patient manquant";
            if(empty($_POST['nom'])) $errors[] = "Le nom est obligatoire";
            if(empty($_POST['prenom'])) $errors[] = "Le prénom est obligatoire";
            if(empty($_POST['sexe'])) $errors[] = "Le sexe est obligatoire";
            if(empty($_POST['date_naissance'])) $errors[] = "La date de naissance est obligatoire";

            if(empty($errors)) {
                $this->patientModel->id = $_POST['id'];
                $this->patientModel->nom = $_POST['nom'];
                $this->patientModel->prenom = $_POST['prenom'];
                $this->patientModel->sexe = $_POST['sexe'];
                $this->patientModel->date_naissance = $_POST['date_naissance'];
                $this->patientModel->adresse = !empty($_POST['adresse']) ? $_POST['adresse'] : null;
                $this->patientModel->telephone = !empty($_POST['telephone']) ? $_POST['telephone'] : null;
                $this->patientModel->groupe_sanguin = !empty($_POST['groupe_sanguin']) ? $_POST['groupe_sanguin'] : null; // AJOUTÉ

                $result = $this->patientModel->modifier();
                
                if($result === 'duplicate') {
                    $errors[] = "Un patient avec le même nom, prénom et date de naissance existe déjà";
                    $_SESSION['errors'] = $errors;
                    header('Location: index.php?controller=patient&action=edit&id=' . $_POST['id']);
                    exit();
                } elseif($result) {
                    $_SESSION['success'] = "Patient modifié avec succès";
                    header('Location: index.php?controller=patient&action=show&id=' . $_POST['id']);
                    exit();
                } else {
                    $errors[] = "Erreur lors de la modification";
                }
            }
            
            if(!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header('Location: index.php?controller=patient&action=edit&id=' . $_POST['id']);
                exit();
            }
        } else {
            header('Location: index.php?controller=patient&action=search');
            exit();
        }
    }

    public function delete() {
        $this->checkDeleteAccess();
        
        if(isset($_GET['id'])) {
            $this->patientModel->id = $_GET['id'];
            
            $antecedentModel = new Antecedent();
            $antecedents = $antecedentModel->lireParPatient($_GET['id']);
            
            if($antecedents && $antecedents->rowCount() > 0) {
                $_SESSION['error'] = "Impossible de supprimer ce patient car il a des antécédents médicaux";
                header('Location: index.php?controller=patient&action=show&id=' . $_GET['id']);
                exit();
            }
            
            if($this->patientModel->supprimer()) {
                $_SESSION['success'] = "Patient supprimé avec succès";
            } else {
                $_SESSION['error'] = "Erreur lors de la suppression";
            }
            header('Location: index.php?controller=patient&action=search');
            exit();
        }
    }

    public function search() {
        $this->checkAccess();
        
        $nom = isset($_GET['nom']) ? trim($_GET['nom']) : '';
        $prenom = isset($_GET['prenom']) ? trim($_GET['prenom']) : '';
        $date_naissance = isset($_GET['date_naissance']) ? $_GET['date_naissance'] : '';
        
        if(empty($nom) && empty($prenom) && empty($date_naissance)) {
            $data = array('patients' => null);
            $this->renderWithLayout('patients/search', $data);
            return;
        }
        
        $patients = $this->patientModel->rechercher($nom, $prenom, $date_naissance);
        
        if($patients->rowCount() == 1) {
            $patient = $patients->fetch(PDO::FETCH_ASSOC);
            header('Location: index.php?controller=patient&action=show&id=' . $patient['id']);
            exit();
        }
        
        $data = array('patients' => $patients);
        $this->renderWithLayout('patients/search', $data);
    }
}
?>