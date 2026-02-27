<?php
namespace Controllers;

use Models\User;
use Models\Patient;

class AdminController extends BaseController {
    private $userModel;
    private $patientModel;

    public function __construct() {
        parent::__construct();
        
        // Vérifier que l'utilisateur est admin
        $this->checkAdmin();
        
        $this->userModel = new User();
        $this->patientModel = new Patient();
    }

    // Vérifier les droits admin (protected pour héritage)
    protected function checkAdmin() {
        if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['error'] = "Accès non autorisé. Zone réservée aux administrateurs.";
            header('Location: /projet_medical/app/public/index.php?controller=patient&action=index');
            exit();
        }
    }

    /**
     * Tableau de bord admin
     */
    public function dashboard() {
        // Statistiques
        $totalUsers = $this->userModel->countAll();
        $totalPatients = $this->patientModel->compter();
        $usersByRole = $this->userModel->countByRole();
        
        $data = array(
            'totalUsers' => $totalUsers,
            'totalPatients' => $totalPatients,
            'usersByRole' => $usersByRole
        );
        
        $this->renderWithLayout('admin/dashboard', $data);
    }

    /**
     * Liste des utilisateurs
     */
    public function users() {
        $users = $this->userModel->findAll();
        $data = array('users' => $users);
        $this->renderWithLayout('admin/users', $data);
    }

    /**
     * Rechercher des utilisateurs
     */
    public function search() {
        // Récupérer les critères de recherche
        $nom = isset($_GET['nom']) ? trim($_GET['nom']) : '';
        $prenom = isset($_GET['prenom']) ? trim($_GET['prenom']) : '';
        $email = isset($_GET['email']) ? trim($_GET['email']) : '';
        $role = isset($_GET['role']) ? $_GET['role'] : '';
        
        // Construire la requête
        $query = "SELECT * FROM utilisateurs WHERE 1=1";
        $params = array();
        
        if(!empty($nom)) {
            $query .= " AND nom LIKE :nom";
            $params[':nom'] = '%' . strtoupper($nom) . '%';
        }
        
        if(!empty($prenom)) {
            $query .= " AND prenom LIKE :prenom";
            $params[':prenom'] = '%' . ucfirst(strtolower($prenom)) . '%';
        }
        
        if(!empty($email)) {
            $query .= " AND email LIKE :email";
            $params[':email'] = '%' . $email . '%';
        }
        
        if(!empty($role)) {
            $query .= " AND role = :role";
            $params[':role'] = $role;
        }
        
        $query .= " ORDER BY nom, prenom ASC";
        
        try {
            $database = new \Config\Database();
            $conn = $database->getConnection();
            $stmt = $conn->prepare($query);
            
            foreach($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $users = $stmt;
            
            $data = array('users' => $users);
            $this->renderWithLayout('admin/search_users', $data);
            
        } catch(Exception $e) {
            $_SESSION['error'] = "Erreur lors de la recherche";
            header('Location: index.php?controller=admin&action=users');
            exit();
        }
    }

    /**
     * Statistiques détaillées
     */
    public function stats() {
        $totalPatients = $this->patientModel->compter();
        $usersByRole = $this->userModel->countByRole();
        
        $data = array(
            'totalPatients' => $totalPatients,
            'usersByRole' => $usersByRole
        );
        
        $this->renderWithLayout('admin/stats', $data);
    }

    /**
     * Créer un utilisateur (via admin)
     */
    public function create() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Rediriger vers AuthController pour la création
            header('Location: /projet_medical/app/public/index.php?controller=auth&action=register');
            exit();
        }
        $this->renderWithLayout('admin/create_user');
    }

    /**
     * Modifier un utilisateur
     */
    public function editUser() {
        if(isset($_GET['id'])) {
            $user = $this->userModel->findById($_GET['id']);
            $data = array('user' => $user);
            $this->renderWithLayout('admin/edit_user', $data);
        }
    }

    /**
     * Mettre à jour un utilisateur
     */
   /**
 * Mettre à jour un utilisateur
 */
public function updateUser() {
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer l'ID depuis l'URL ou depuis le formulaire
        $id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['id']) ? intval($_POST['id']) : 0);
        
        if($id <= 0) {
            $_SESSION['error'] = "ID utilisateur invalide";
            header('Location: index.php?controller=admin&action=users');
            return;
        }
        
        // Récupérer les données du formulaire
        $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
        $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $role = isset($_POST['role']) ? $_POST['role'] : '';
        $matricule = isset($_POST['matricule']) ? trim($_POST['matricule']) : '';
        $telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : '';
        $service = isset($_POST['service']) ? $_POST['service'] : '';
        $centre = isset($_POST['centre']) ? $_POST['centre'] : '';
        
        // Validation basique
        $errors = array();
        if(empty($nom)) $errors[] = "Le nom est requis";
        if(empty($prenom)) $errors[] = "Le prénom est requis";
        if(empty($email)) $errors[] = "L'email est requis";
        if(empty($role)) $errors[] = "Le rôle est requis";
        if(empty($matricule)) $errors[] = "Le matricule est requis";
        
        if(!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: index.php?controller=admin&action=editUser&id=' . $id);
            return;
        }
        
        // Mettre à jour l'utilisateur
        $this->userModel->nom = $nom;
        $this->userModel->prenom = $prenom;
        $this->userModel->email = $email;
        $this->userModel->role = $role;
        $this->userModel->matricule = $matricule;
        $this->userModel->telephone = $telephone;
        $this->userModel->service = $service;
        $this->userModel->centre = $centre;
        
        if($this->userModel->update($id)) {
            $_SESSION['success'] = "Utilisateur modifié avec succès";
            
            // Si un nouveau mot de passe est fourni
            if(!empty($_POST['password'])) {
                $password = $_POST['password'];
                $confirm = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
                
                if($password === $confirm) {
                    $this->userModel->changePassword($id, $password);
                    $_SESSION['success'] = "Utilisateur et mot de passe modifiés avec succès";
                } else {
                    $_SESSION['error'] = "Les mots de passe ne correspondent pas";
                }
            }
        } else {
            $_SESSION['error'] = "Erreur lors de la modification";
        }
        
        header('Location: index.php?controller=admin&action=users');
        exit();
    } else {
        // Si ce n'est pas une requête POST, rediriger
        header('Location: index.php?controller=admin&action=users');
        exit();
    }
}

    /**
     * Supprimer un utilisateur
     */
    public function deleteUser() {
        if(isset($_GET['id'])) {
            // Empêcher l'admin de supprimer son propre compte
            if($_GET['id'] == $_SESSION['user_id']) {
                $_SESSION['error'] = "Vous ne pouvez pas supprimer votre propre compte";
                header('Location: /projet_medical/app/public/index.php?controller=admin&action=users');
                exit();
            }
            
            if($this->userModel->delete($_GET['id'])) {
                $_SESSION['success'] = "Utilisateur supprimé avec succès";
            } else {
                $_SESSION['error'] = "Erreur lors de la suppression";
            }
            header('Location: /projet_medical/app/public/index.php?controller=admin&action=users');
            exit();
        }
    }

    /**
     * Profil utilisateur (pour admin) - MODIFIÉE pour accepter un ID
     */
        /**
     * Profil admin et consultation des profils utilisateurs
     */
    /**
 * Profil admin et consultation des profils utilisateurs
 */
public function profile() {
    // Vérifier si un ID est passé dans l'URL (consultation d'un autre profil)
    $userId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if($userId > 0) {
        // Consultation du profil d'un autre utilisateur
        $user = $this->userModel->findById($userId);
        
        if(!$user) {
            $_SESSION['error'] = "Utilisateur non trouvé";
            header('Location: index.php?controller=admin&action=users');
            return;
        }
        
        // Passer les données à la vue
        $data = array('user' => $user);
        
        // Utiliser renderWithLayout comme dans vos autres méthodes
        $this->renderWithLayout('admin/profile', $data);
    } else {
        // Affichage de son propre profil
        $this->renderWithLayout('auth/profile');
    }
}
}
?>