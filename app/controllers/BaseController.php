<?php
namespace Controllers;

class BaseController {
    
    protected $data = array();
    
    public function __construct() {
        // Démarrer la session si nécessaire
        if(session_id() == '') {
            session_start();
        }
    }
    
    /**
     * Vérifier si l'utilisateur est connecté
     */
    protected function checkAuth() {
        if(!isset($_SESSION['user_id'])) {
            header('Location: /projet_medical/app/public/index.php?controller=auth&action=loginForm');
            exit();
        }
    }
    
    /**
     * Vérifier les droits d'accès généraux
     * Par défaut, bloque l'accès aux infirmiers
     */
    protected function checkAccess() {
        $this->checkAuth();
        
        // Bloquer l'accès aux infirmiers par défaut
        if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'infirmier') {
            $_SESSION['error'] = "Accès non autorisé.";
            header('Location: /projet_medical/app/public/index.php?controller=auth&action=profile');
            exit();
        }
    }
    
    /**
     * Vérifier le rôle de l'utilisateur
     * @param string|array $roles Rôle(s) autorisé(s)
     */
    protected function checkRole($roles) {
        $this->checkAuth();
        
        // Convertir en tableau si c'est une chaîne
        if(is_string($roles)) {
            $roles = array($roles);
        }
        
        if(!in_array($_SESSION['user_role'], $roles)) {
            $_SESSION['error'] = "Accès non autorisé pour votre rôle";
            header('Location: /projet_medical/app/public/index.php?controller=patient&action=index');
            exit();
        }
    }
    
    /**
     * Vérifier si l'utilisateur est admin
     */
    protected function checkAdmin() {
        $this->checkAuth();
        if($_SESSION['user_role'] !== 'admin') {
            $_SESSION['error'] = "Accès non autorisé - Réservé à l'administrateur";
            header('Location: /projet_medical/app/public/index.php?controller=patient&action=index');
            exit();
        }
    }
    
    /**
     * Vérifier si l'utilisateur est médecin
     */
    protected function checkMedecin() {
        $this->checkAuth();
        if($_SESSION['user_role'] !== 'medecin') {
            $_SESSION['error'] = "Accès non autorisé - Réservé aux médecins";
            header('Location: /projet_medical/app/public/index.php?controller=patient&action=index');
            exit();
        }
    }
    
    /**
     * Vérifier si l'utilisateur est major
     */
    protected function checkMajor() {
        $this->checkAuth();
        if($_SESSION['user_role'] !== 'major') {
            $_SESSION['error'] = "Accès non autorisé - Réservé aux majors";
            header('Location: /projet_medical/app/public/index.php?controller=patient&action=index');
            exit();
        }
    }
    
    /**
     * Vérifier si l'utilisateur est médecin ou major
     */
    protected function checkMedecinOrMajor() {
        $this->checkAuth();
        if($_SESSION['user_role'] !== 'medecin' && $_SESSION['user_role'] !== 'major') {
            $_SESSION['error'] = "Accès non autorisé - Réservé aux médecins et majors";
            header('Location: /projet_medical/app/public/index.php?controller=patient&action=index');
            exit();
        }
    }
    
    /**
     * Vérifier si l'utilisateur est infirmier
     */
    protected function checkInfirmier() {
        $this->checkAuth();
        if($_SESSION['user_role'] !== 'infirmier') {
            $_SESSION['error'] = "Accès non autorisé - Réservé aux infirmiers";
            header('Location: /projet_medical/app/public/index.php?controller=patient&action=index');
            exit();
        }
    }
    
    /**
     * Récupérer les données POST
     * @return array
     */
    protected function getPost() {
        $postData = array();
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach($_POST as $key => $value) {
                $postData[$key] = htmlspecialchars(trim($value));
            }
        }
        return $postData;
    }
    
    /**
     * Récupérer les données GET
     * @return array
     */
    protected function getQuery() {
        $queryData = array();
        foreach($_GET as $key => $value) {
            $queryData[$key] = htmlspecialchars(trim($value));
        }
        return $queryData;
    }
    
    /**
     * Récupérer les données d'un formulaire (POST ou GET)
     * @return array
     */
    protected function getRequestData() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->getPost();
        }
        return $this->getQuery();
    }
    
    /**
     * Valider les champs requis
     * @param array $data
     * @param array $required
     * @return array Liste des erreurs
     */
    protected function validateRequired($data, $required) {
        $errors = array();
        foreach($required as $field) {
            // Vérifier d'abord si la clé existe
            if(!isset($data[$field])) {
                $errors[] = "Le champ '" . $field . "' est obligatoire";
            } else {
                // Ensuite, vérifier si la valeur n'est pas vide après trim
                $value = trim($data[$field]);
                if(empty($value)) {
                    $errors[] = "Le champ '" . $field . "' est obligatoire";
                }
            }
        }
        return $errors;
    }
    
    /**
     * Valider un email
     * @param string $email
     * @return bool
     */
    protected function validateEmail($email) {
        $email = trim($email);
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Valider un téléphone (8 chiffres)
     * @param string $telephone
     * @return bool
     */
    protected function validateTelephone($telephone) {
        $telephone = trim($telephone);
        return preg_match('/^[0-9]{8}$/', $telephone) === 1;
    }
    
    /**
     * Valider une date
     * @param string $date
     * @param string $format
     * @return bool
     */
    protected function validateDate($date, $format = 'Y-m-d') {
        $date = trim($date);
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    /**
     * Rediriger avec un message de succès
     * @param string $url
     * @param string $message
     */
    protected function redirectSuccess($url, $message) {
        $_SESSION['success'] = $message;
        header('Location: ' . $url);
        exit();
    }
    
    /**
     * Rediriger avec un message d'erreur
     * @param string $url
     * @param string $message
     */
    protected function redirectError($url, $message) {
        $_SESSION['error'] = $message;
        header('Location: ' . $url);
        exit();
    }
    
    /**
     * Rediriger avec un message d'information
     * @param string $url
     * @param string $message
     */
    protected function redirectInfo($url, $message) {
        $_SESSION['info'] = $message;
        header('Location: ' . $url);
        exit();
    }
    
    /**
     * Rediriger avec un message d'avertissement
     * @param string $url
     * @param string $message
     */
    protected function redirectWarning($url, $message) {
        $_SESSION['warning'] = $message;
        header('Location: ' . $url);
        exit();
    }
    
    /**
     * Rediriger avec un message
     * @param string $url
     * @param string $message
     * @param string $type success|error|warning|info
     */
    protected function redirectWithMessage($url, $message, $type = 'success') {
        $_SESSION[$type] = $message;
        header('Location: ' . $url);
        exit();
    }
    
    /**
     * Rendu d'une vue
     * @param string $view
     * @param array $data
     */
    protected function render($view, $data = array()) {
        // Fusionner les données avec les propriétés de la classe
        if(!empty($this->data)) {
            $data = array_merge($this->data, $data);
        }
        
        // Extraire les variables pour la vue
        extract($data);
        
        // Inclure la vue
        $viewPath = dirname(__DIR__) . '/views/' . $view . '.php';
        if(file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("Vue non trouvée: " . $viewPath);
        }
    }
    
    /**
     * Rendu avec le layout principal
     * @param string $view
     * @param array $data
     */
  protected function renderWithLayout($view, $data = array()) {
    // Fusionner les données avec les propriétés de la classe
    if(!empty($this->data)) {
        $data = array_merge($this->data, $data);
    }
    
    // Extraire les variables pour la vue
    extract($data);
    
    // Capturer le contenu de la vue
    ob_start();
    $viewPath = dirname(__DIR__) . '/views/' . $view . '.php';
    if(file_exists($viewPath)) {
        require_once $viewPath;
    } else {
        die("Vue non trouvée: " . $viewPath);
    }
    $content = ob_get_clean();
    
    // Afficher le layout avec le contenu
    $layoutPath = dirname(__DIR__) . '/views/layouts/main_layout.php';
    if(file_exists($layoutPath)) {
        require_once $layoutPath;
    } else {
        die("Layout non trouvé: " . $layoutPath);
    }
}
    /**
     * Rendu JSON
     * @param mixed $data
     * @param int $statusCode
     */
    protected function renderJson($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    
    /**
     * Vérifier si la requête est AJAX
     * @return bool
     */
    protected function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    
    /**
     * Obtenir l'URL de base
     * @return string
     */
    protected function baseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $script = dirname($_SERVER['SCRIPT_NAME']);
        return $protocol . $host . rtrim($script, '/\\');
    }
    
    /**
     * Obtenir l'utilisateur connecté
     * @return array|null
     */
    protected function getUser() {
        if(isset($_SESSION['user_id'])) {
            return array(
                'id' => $_SESSION['user_id'],
                'nom' => $_SESSION['user_nom'],
                'prenom' => $_SESSION['user_prenom'],
                'email' => $_SESSION['user_email'],
                'role' => $_SESSION['user_role'],
                'matricule' => isset($_SESSION['user_matricule']) ? $_SESSION['user_matricule'] : '',
                'telephone' => isset($_SESSION['user_telephone']) ? $_SESSION['user_telephone'] : ''
            );
        }
        return null;
    }
    
    /**
     * Vérifier si l'utilisateur a une permission spécifique
     * @param string $action
     * @return bool
     */
    protected function can($action) {
        if(!isset($_SESSION['user_role'])) return false;
        
        $role = $_SESSION['user_role'];
        
        $permissions = array(
            'admin' => array(
                'manage_users', 'view_all', 'create_patient', 'edit_patient', 
                'delete_patient', 'add_antecedent', 'edit_antecedent', 'delete_antecedent',
                'view_stats', 'export_data'
            ),
            'medecin' => array(
                'view_all', 'create_patient', 'edit_patient', 
                'add_antecedent', 'edit_antecedent', 'delete_antecedent',
                'view_stats'
            ),
            'major' => array(
                'view_all', 'create_patient', 'edit_patient', 
                'add_antecedent', 'view_stats', 'planifier_soin'
            ),
            'infirmier' => array(
                'view_all', 'effectuer_soin'
            )
        );
        
        return isset($permissions[$role]) && in_array($action, $permissions[$role]);
    }
}
?>