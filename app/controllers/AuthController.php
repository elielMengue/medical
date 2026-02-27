<?php
namespace Controllers;

use Models\User;
use Models\Patient;

class AuthController extends BaseController {
    private $userModel;
    private $patientModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->patientModel = new Patient();
    }

    /**
     * Afficher le formulaire de connexion
     */
    public function loginForm() {
        // Si déjà connecté, rediriger vers l'accueil
        if(isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=accueil&action=index');
            exit();
        }
        
        // Utilisation du chemin absolu avec DOCUMENT_ROOT
        require_once $_SERVER['DOCUMENT_ROOT'] . '/projet_medical/app/views/auth/login.php';
    }

    /**
     * Traiter la connexion
     */
    /**
 * Traiter la connexion
 */
public function login() {
    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.php?controller=auth&action=loginForm');
        exit();
    }

    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validation
    $errors = array();
    if(empty($email)) $errors[] = "L'email est requis";
    if(empty($password)) $errors[] = "Le mot de passe est requis";

    if(!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: index.php?controller=auth&action=loginForm');
        exit();
    }

    // Vérifier les identifiants
    $user = $this->userModel->findByEmail($email);
    
    if($user && $user['password'] === sha1($password)) {
        // Stocker toutes les informations en session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['user_prenom'] = $user['prenom'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_matricule'] = $user['matricule'];
        $_SESSION['user_telephone'] = $user['telephone'];
        $_SESSION['user_service'] = isset($user['service']) ? $user['service'] : '';
        $_SESSION['user_centre'] = isset($user['centre']) ? $user['centre'] : '';
        
        // SUPPRIMER OU COMMENTER CETTE LIGNE
        // $_SESSION['login_success'] = "Connexion réussie. Bienvenue " . $user['prenom'] . " !";
        
        // TOUS LES UTILISATEURS (admin inclus) vont vers la page d'accueil
        header('Location: index.php?controller=accueil&action=index');
        exit();
    } else {
        $_SESSION['error'] = "Email ou mot de passe incorrect";
        header('Location: index.php?controller=auth&action=loginForm');
        exit();
    }
}
    /**
     * Inscription d'un nouvel utilisateur (via admin uniquement)
     */
    public function register() {
        // Vérifier que l'utilisateur est admin
        if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['error'] = "Accès non autorisé";
            header('Location: index.php?controller=patient&action=index');
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=admin&action=create');
            exit();
        }

        // Récupérer les données du formulaire
        $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
        $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $matricule = isset($_POST['matricule']) ? trim($_POST['matricule']) : '';
        $role = isset($_POST['role']) ? $_POST['role'] : '';
        $telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : '';
        $service = isset($_POST['service']) ? $_POST['service'] : '';
        $centre = isset($_POST['centre']) ? $_POST['centre'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

        // Validation
        $errors = array();
        if(empty($nom)) $errors[] = "Le nom est requis";
        if(empty($prenom)) $errors[] = "Le prénom est requis";
        if(empty($email)) $errors[] = "L'email est requis";
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "L'email n'est pas valide";
        if(empty($matricule)) $errors[] = "Le matricule est requis";
        if(empty($role)) $errors[] = "Le rôle est requis";
        if(empty($password)) $errors[] = "Le mot de passe est requis";
        if(strlen($password) < 6) $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
        if($password !== $confirm_password) $errors[] = "Les mots de passe ne correspondent pas";

        // Vérifier si l'email existe déjà
        if($this->userModel->emailExists($email)) {
            $errors[] = "Cet email est déjà utilisé";
        }

        // Vérifier si le matricule existe déjà
        if($this->userModel->matriculeExists($matricule)) {
            $errors[] = "Ce matricule est déjà utilisé";
        }

        if(!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: index.php?controller=admin&action=create');
            exit();
        }

        // Créer l'utilisateur
        $this->userModel->nom = strtoupper($nom);
        $this->userModel->prenom = ucfirst(strtolower($prenom));
        $this->userModel->email = $email;
        $this->userModel->matricule = $matricule;
        $this->userModel->role = $role;
        $this->userModel->telephone = $telephone;
        $this->userModel->service = $service;
        $this->userModel->centre = $centre;
        $this->userModel->password = $password;

        if($this->userModel->create()) {
            $_SESSION['success'] = "Utilisateur créé avec succès";
            header('Location: index.php?controller=admin&action=users');
        } else {
            $_SESSION['error'] = "Erreur lors de la création de l'utilisateur";
            $_SESSION['old_input'] = $_POST;
            header('Location: index.php?controller=admin&action=create');
        }
        exit();
    }

    /**
     * Déconnexion
     */
    public function logout() {
        // Détruire toutes les variables de session
        $_SESSION = array();
        
        // Détruire la session
        session_destroy();
        
        // Rediriger vers la page de connexion
        header('Location: index.php?controller=auth&action=loginForm');
        exit();
    }

    /**
     * Afficher le profil de l'utilisateur connecté
     */
    // ACTUEL (probablement)


// CORRECTION
/**
 * Afficher le profil de l'utilisateur connecté
 */
public function profile() {
    // Utiliser renderWithLayout pour intégrer le layout principal
    $this->renderWithLayout('auth/profile');
}
    /**
     * Changer le mot de passe
     */
    public function changePassword() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=auth&action=profile');
            exit();
        }

        // Vérifier que l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Vous devez être connecté";
            header('Location: index.php?controller=auth&action=loginForm');
            exit();
        }

        $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
        $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

        // Validation
        $errors = array();
        if(empty($current_password)) $errors[] = "Le mot de passe actuel est requis";
        if(empty($new_password)) $errors[] = "Le nouveau mot de passe est requis";
        if(strlen($new_password) < 6) $errors[] = "Le nouveau mot de passe doit contenir au moins 6 caractères";
        if($new_password !== $confirm_password) $errors[] = "Les nouveaux mots de passe ne correspondent pas";

        // Vérifier l'ancien mot de passe
        $user = $this->userModel->findById($_SESSION['user_id']);
        if($user && $user['password'] !== sha1($current_password)) {
            $errors[] = "Le mot de passe actuel est incorrect";
        }

        if(!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: index.php?controller=auth&action=profile');
            exit();
        }

        // Changer le mot de passe
        if($this->userModel->changePassword($_SESSION['user_id'], $new_password)) {
            $_SESSION['success'] = "Mot de passe changé avec succès";
        } else {
            $_SESSION['error'] = "Erreur lors du changement de mot de passe";
        }

        header('Location: index.php?controller=auth&action=profile');
        exit();
    }
}
?>