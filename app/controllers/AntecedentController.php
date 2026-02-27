<?php
namespace Controllers;

use Models\Antecedent;
use Models\Patient;

class AntecedentController extends BaseController {
    private $antecedentModel;
    private $patientModel;

    public function __construct() {
        parent::__construct();
        $this->antecedentModel = new Antecedent();
        $this->patientModel = new Patient();
    }

    /**
     * Afficher le formulaire de création
     */
    public function create() {
        // Vérifier que l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=loginForm');
            exit();
        }
        
        // Vérifier que l'utilisateur a les droits
        $userRole = $_SESSION['user_role'];
        if($userRole != 'medecin' && $userRole != 'major' && $userRole != 'admin') {
            $_SESSION['error'] = "Vous n'avez pas les droits pour ajouter un antécédent";
            header('Location: index.php?controller=patient&action=index');
            exit();
        }
        
        $this->renderWithLayout('antecedents/create');
    }

    /**
     * Sauvegarder un nouvel antécédent
     */
    public function store() {
        // Vérifier que l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=loginForm');
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = array();
            
            // Validation des nouveaux champs obligatoires
            if(empty($_POST['patient_id'])) {
                $errors[] = "Patient non identifié";
            }
            if(empty($_POST['date_consultation'])) {
                $errors[] = "La date de consultation est obligatoire";
            }
            if(empty($_POST['motif_consultation'])) {
                $errors[] = "Le motif de consultation est obligatoire";
            }

            if(empty($errors)) {
                // Remplir le modèle avec les nouveaux champs
                $this->antecedentModel->patient_id = intval($_POST['patient_id']);
                
                // Section 1: Date et motif
                $this->antecedentModel->date_consultation = $_POST['date_consultation'];
                $this->antecedentModel->motif_consultation = trim($_POST['motif_consultation']);
                
                // Section 2: Historique
                $this->antecedentModel->historique_maladie = !empty($_POST['historique_maladie']) ? trim($_POST['historique_maladie']) : null;
                
                // Section 3: Antécédents
                $this->antecedentModel->antecedents_medicaux = !empty($_POST['antecedents_medicaux']) ? trim($_POST['antecedents_medicaux']) : null;
                $this->antecedentModel->antecedents_chirurgicaux = !empty($_POST['antecedents_chirurgicaux']) ? trim($_POST['antecedents_chirurgicaux']) : null;
                $this->antecedentModel->antecedents_familiaux = !empty($_POST['antecedents_familiaux']) ? trim($_POST['antecedents_familiaux']) : null;
                $this->antecedentModel->allergies = !empty($_POST['allergies']) ? trim($_POST['allergies']) : null;
                
                // Section 4: Examen clinique
                $this->antecedentModel->ta = !empty($_POST['ta']) ? trim($_POST['ta']) : null;
                $this->antecedentModel->fc = !empty($_POST['fc']) ? trim($_POST['fc']) : null;
                $this->antecedentModel->temperature = !empty($_POST['temperature']) ? trim($_POST['temperature']) : null;
                $this->antecedentModel->fr = !empty($_POST['fr']) ? trim($_POST['fr']) : null;
                $this->antecedentModel->saturation = !empty($_POST['saturation']) ? trim($_POST['saturation']) : null;
                $this->antecedentModel->poids = !empty($_POST['poids']) ? trim($_POST['poids']) : null;
                
                // Section 5: Examen par appareil
                $this->antecedentModel->appareil_pleuro_pulmonaire = !empty($_POST['appareil_pleuro_pulmonaire']) ? trim($_POST['appareil_pleuro_pulmonaire']) : null;
                $this->antecedentModel->appareil_cardio_vasculaire = !empty($_POST['appareil_cardio_vasculaire']) ? trim($_POST['appareil_cardio_vasculaire']) : null;
                $this->antecedentModel->appareil_digestif = !empty($_POST['appareil_digestif']) ? trim($_POST['appareil_digestif']) : null;
                $this->antecedentModel->appareil_locomoteur = !empty($_POST['appareil_locomoteur']) ? trim($_POST['appareil_locomoteur']) : null;
                $this->antecedentModel->appareil_uro_genital = !empty($_POST['appareil_uro_genital']) ? trim($_POST['appareil_uro_genital']) : null;
                $this->antecedentModel->autre_organe = !empty($_POST['autre_organe']) ? trim($_POST['autre_organe']) : null;
                
                // Section 6: Résumé syndromique
                $this->antecedentModel->resume_syndromique = !empty($_POST['resume_syndromique']) ? trim($_POST['resume_syndromique']) : null;
                
                // Section 7: Diagnostic
                $this->antecedentModel->diagnostic_presomption = !empty($_POST['diagnostic_presomption']) ? trim($_POST['diagnostic_presomption']) : null;
                
                // Section 8: Examens complémentaires
                $this->antecedentModel->examen_complementaire = !empty($_POST['examen_complementaire']) ? trim($_POST['examen_complementaire']) : null;
                
                // Section 9: Traitement
                $this->antecedentModel->traitement_symptomatique = !empty($_POST['traitement_symptomatique']) ? trim($_POST['traitement_symptomatique']) : null;
                
                // NOUVEAUX CHAMPS - Résultats et traitements spécifiques (optionnels à la création)
                $this->antecedentModel->resultat = !empty($_POST['resultat']) ? trim($_POST['resultat']) : null;
                $this->antecedentModel->traitement_specifique = !empty($_POST['traitement_specifique']) ? trim($_POST['traitement_specifique']) : null;

                // Sauvegarder dans la base de données
                if($this->antecedentModel->creer()) {
                    $_SESSION['success'] = "Antécédent ajouté avec succès";
                    header('Location: index.php?controller=patient&action=show&id=' . $_POST['patient_id']);
                    exit();
                } else {
                    $errors[] = "Erreur lors de l'enregistrement dans la base de données";
                }
            }
            
            if(!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_input'] = $_POST;
                header('Location: index.php?controller=antecedent&action=create&patient_id=' . $_POST['patient_id']);
                exit();
            }
        } else {
            // Si ce n'est pas une requête POST
            header('Location: index.php?controller=patient&action=index');
            exit();
        }
    }

    /**
     * Afficher le formulaire de résultats pour un antécédent
     */
    public function resultat() {
        // Vérifier que l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=loginForm');
            exit();
        }
        
        // Vérifier que l'utilisateur a les droits
        $userRole = $_SESSION['user_role'];
        if($userRole != 'medecin' && $userRole != 'major' && $userRole != 'admin') {
            $_SESSION['error'] = "Vous n'avez pas les droits pour ajouter des résultats";
            header('Location: index.php?controller=patient&action=index');
            exit();
        }

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $patient_id = isset($_GET['patient_id']) ? intval($_GET['patient_id']) : 0;
        
        if($id <= 0 || $patient_id <= 0) {
            $_SESSION['error'] = "Paramètres invalides";
            header('Location: index.php?controller=patient&action=index');
            exit();
        }
        
        // Récupérer l'antécédent
        $antecedent = $this->antecedentModel->lireUn($id);
        
        if(!$antecedent) {
            $_SESSION['error'] = "Antécédent non trouvé";
            header('Location: index.php?controller=patient&action=show&id=' . $patient_id);
            exit();
        }
        
        // Récupérer les informations du patient pour affichage
        $this->patientModel->id = $patient_id;
        $patient = $this->patientModel->lireUn();
        
        $data = array(
            'antecedent' => $antecedent,
            'patient_id' => $patient_id,
            'patient' => $patient
        );
        
        $this->renderWithLayout('antecedents/resultat', $data);
    }

    /**
     * Enregistrer les résultats d'un antécédent
     */
    public function saveResultat() {
        // Vérifier que l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=loginForm');
            exit();
        }

        // Vérifier que l'utilisateur a les droits
        $userRole = $_SESSION['user_role'];
        if($userRole != 'medecin' && $userRole != 'major' && $userRole != 'admin') {
            $_SESSION['error'] = "Vous n'avez pas les droits pour ajouter des résultats";
            header('Location: index.php?controller=patient&action=index');
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=patient&action=index');
            exit();
        }
        
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $patient_id = isset($_POST['patient_id']) ? intval($_POST['patient_id']) : 0;
        $resultat = isset($_POST['resultat']) ? trim($_POST['resultat']) : '';
        $traitement_specifique = isset($_POST['traitement_specifique']) ? trim($_POST['traitement_specifique']) : '';
        
        if($id <= 0 || $patient_id <= 0) {
            $_SESSION['error'] = "Paramètres invalides";
            header('Location: index.php?controller=patient&action=index');
            exit();
        }
        
        // Vérifier que l'antécédent existe
        $antecedent = $this->antecedentModel->lireUn($id);
        if(!$antecedent) {
            $_SESSION['error'] = "Antécédent non trouvé";
            header('Location: index.php?controller=patient&action=show&id=' . $patient_id);
            exit();
        }
        
        // Mettre à jour l'antécédent avec les résultats
        $this->antecedentModel->id = $id;
        $this->antecedentModel->resultat = $resultat;
        $this->antecedentModel->traitement_specifique = $traitement_specifique;
        
        if($this->antecedentModel->saveResultat()) {
            $_SESSION['success'] = "Résultats enregistrés avec succès";
        } else {
            $_SESSION['error'] = "Erreur lors de l'enregistrement des résultats";
            $_SESSION['old_input'] = $_POST;
        }
        
        header('Location: index.php?controller=patient&action=show&id=' . $patient_id);
        exit();
    }

    /**
     * Afficher les détails d'un antécédent
     */
    public function show() {
        // Vérifier que l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=loginForm');
            exit();
        }

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if($id <= 0) {
            $_SESSION['error'] = "ID antécédent invalide";
            header('Location: index.php?controller=patient&action=index');
            exit();
        }

        $antecedent = $this->antecedentModel->lireUn($id);
        if(!$antecedent) {
            $_SESSION['error'] = "Antécédent non trouvé";
            header('Location: index.php?controller=patient&action=index');
            exit();
        }

        $data = array('antecedent' => $antecedent);
        $this->renderWithLayout('antecedents/show', $data);
    }

    /**
     * Modifier un antécédent
     */
    public function edit() {
        // Vérifier que l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=loginForm');
            exit();
        }
        
        // Vérifier que l'utilisateur a les droits
        $userRole = $_SESSION['user_role'];
        if($userRole != 'medecin' && $userRole != 'major' && $userRole != 'admin') {
            $_SESSION['error'] = "Vous n'avez pas les droits pour modifier un antécédent";
            header('Location: index.php?controller=patient&action=index');
            exit();
        }

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if($id <= 0) {
            $_SESSION['error'] = "ID antécédent invalide";
            header('Location: index.php?controller=patient&action=index');
            exit();
        }

        $antecedent = $this->antecedentModel->lireUn($id);
        if(!$antecedent) {
            $_SESSION['error'] = "Antécédent non trouvé";
            header('Location: index.php?controller=patient&action=index');
            exit();
        }

        $data = array('antecedent' => $antecedent);
        $this->renderWithLayout('antecedents/edit', $data);
    }

    /**
     * Mettre à jour un antécédent
     */
    public function update() {
        // Vérifier que l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=loginForm');
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=patient&action=index');
            exit();
        }
        
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $patient_id = isset($_POST['patient_id']) ? intval($_POST['patient_id']) : 0;
        
        if($id <= 0 || $patient_id <= 0) {
            $_SESSION['error'] = "Paramètres invalides";
            header('Location: index.php?controller=patient&action=index');
            exit();
        }
        
        $errors = array();
        
        // Validation
        if(empty($_POST['date_consultation'])) {
            $errors[] = "La date de consultation est obligatoire";
        }
        if(empty($_POST['motif_consultation'])) {
            $errors[] = "Le motif de consultation est obligatoire";
        }

        if(empty($errors)) {
            // Remplir le modèle
            $this->antecedentModel->id = $id;
            $this->antecedentModel->date_consultation = $_POST['date_consultation'];
            $this->antecedentModel->motif_consultation = trim($_POST['motif_consultation']);
            $this->antecedentModel->historique_maladie = !empty($_POST['historique_maladie']) ? trim($_POST['historique_maladie']) : null;
            $this->antecedentModel->antecedents_medicaux = !empty($_POST['antecedents_medicaux']) ? trim($_POST['antecedents_medicaux']) : null;
            $this->antecedentModel->antecedents_chirurgicaux = !empty($_POST['antecedents_chirurgicaux']) ? trim($_POST['antecedents_chirurgicaux']) : null;
            $this->antecedentModel->antecedents_familiaux = !empty($_POST['antecedents_familiaux']) ? trim($_POST['antecedents_familiaux']) : null;
            $this->antecedentModel->allergies = !empty($_POST['allergies']) ? trim($_POST['allergies']) : null;
            $this->antecedentModel->ta = !empty($_POST['ta']) ? trim($_POST['ta']) : null;
            $this->antecedentModel->fc = !empty($_POST['fc']) ? trim($_POST['fc']) : null;
            $this->antecedentModel->temperature = !empty($_POST['temperature']) ? trim($_POST['temperature']) : null;
            $this->antecedentModel->fr = !empty($_POST['fr']) ? trim($_POST['fr']) : null;
            $this->antecedentModel->saturation = !empty($_POST['saturation']) ? trim($_POST['saturation']) : null;
            $this->antecedentModel->poids = !empty($_POST['poids']) ? trim($_POST['poids']) : null;
            $this->antecedentModel->appareil_pleuro_pulmonaire = !empty($_POST['appareil_pleuro_pulmonaire']) ? trim($_POST['appareil_pleuro_pulmonaire']) : null;
            $this->antecedentModel->appareil_cardio_vasculaire = !empty($_POST['appareil_cardio_vasculaire']) ? trim($_POST['appareil_cardio_vasculaire']) : null;
            $this->antecedentModel->appareil_digestif = !empty($_POST['appareil_digestif']) ? trim($_POST['appareil_digestif']) : null;
            $this->antecedentModel->appareil_locomoteur = !empty($_POST['appareil_locomoteur']) ? trim($_POST['appareil_locomoteur']) : null;
            $this->antecedentModel->appareil_uro_genital = !empty($_POST['appareil_uro_genital']) ? trim($_POST['appareil_uro_genital']) : null;
            $this->antecedentModel->autre_organe = !empty($_POST['autre_organe']) ? trim($_POST['autre_organe']) : null;
            $this->antecedentModel->resume_syndromique = !empty($_POST['resume_syndromique']) ? trim($_POST['resume_syndromique']) : null;
            $this->antecedentModel->diagnostic_presomption = !empty($_POST['diagnostic_presomption']) ? trim($_POST['diagnostic_presomption']) : null;
            $this->antecedentModel->examen_complementaire = !empty($_POST['examen_complementaire']) ? trim($_POST['examen_complementaire']) : null;
            $this->antecedentModel->traitement_symptomatique = !empty($_POST['traitement_symptomatique']) ? trim($_POST['traitement_symptomatique']) : null;
            $this->antecedentModel->resultat = !empty($_POST['resultat']) ? trim($_POST['resultat']) : null;
            $this->antecedentModel->traitement_specifique = !empty($_POST['traitement_specifique']) ? trim($_POST['traitement_specifique']) : null;

            if($this->antecedentModel->modifier()) {
                $_SESSION['success'] = "Antécédent modifié avec succès";
                header('Location: index.php?controller=patient&action=show&id=' . $patient_id);
                exit();
            } else {
                $errors[] = "Erreur lors de la modification";
            }
        }
        
        if(!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: index.php?controller=antecedent&action=edit&id=' . $id);
            exit();
        }
    }

    /**
     * Supprimer un antécédent
     */
    public function delete() {
        // Vérifier que l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=loginForm');
            exit();
        }

        if(isset($_GET['id']) && isset($_GET['patient_id'])) {
            $id = intval($_GET['id']);
            $patient_id = intval($_GET['patient_id']);
            
            if($this->antecedentModel->supprimer($id)) {
                $_SESSION['success'] = "Antécédent supprimé avec succès";
                header('Location: index.php?controller=patient&action=show&id=' . $patient_id);
            } else {
                $_SESSION['error'] = "Erreur lors de la suppression";
                header('Location: index.php?controller=patient&action=show&id=' . $patient_id);
            }
        } else {
            header('Location: index.php?controller=patient&action=index');
        }
        exit();
    }
}
?>