<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Activation des erreurs pour le debug (à désactiver en production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Définir la racine du projet
define('ROOT_PATH', dirname(__DIR__));  // C:\wamp\www\projet_medical\app

// Démarrer la session
if(session_id() == '') {
    session_start();
}

// Autoloader pour PHP 5.3
spl_autoload_register(function ($class) {
    // Convertir les namespace en chemins
    $class = str_replace('\\', '/', $class);
    
    // Adapter la casse des dossiers (majuscules/minuscules)
    $class = str_replace('Controllers/', 'controllers/', $class);
    $class = str_replace('Models/', 'models/', $class);
    $class = str_replace('Views/', 'views/', $class);
    $class = str_replace('Config/', 'config/', $class);
    
    // Construire le chemin complet
    $filePath = ROOT_PATH . '/' . $class . '.php';
    
    // Debug temporaire (à commenter en production)
    // echo "<!-- Chargement: " . $filePath . " -->\n";
    
    if(file_exists($filePath)) {
        require_once $filePath;
    } else {
        // Silencieux en production
        // echo "<!-- Fichier non trouvé: " . $filePath . " -->\n";
    }
});

// Routage simple
$controllerName = isset($_GET['controller']) ? ucfirst($_GET['controller']) . 'Controller' : 'DashboardController';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

$controllerClass = "Controllers\\" . $controllerName;

// Vérifier si le contrôleur existe
if(class_exists($controllerClass)) {
    // Instancier le contrôleur
    $controller = new $controllerClass();
    
    // Vérifier si la méthode existe
    if(method_exists($controller, $action)) {
        // Appeler la méthode
        $controller->$action();
    } else {
        // Méthode non trouvée
        if(isset($_SESSION['user_id'])) {
            echo "<div class='alert alert-danger m-3'>";
            echo "<h4>Erreur 404 - Action non trouvée</h4>";
            echo "<p>L'action '" . htmlspecialchars($action) . "' n'existe pas dans le contrôleur " . $controllerName . ".</p>";
            echo "<a href='index.php?controller=patient&action=index' class='btn btn-primary'>Retour à l'accueil</a>";
            echo "</div>";
        } else {
            header('Location: index.php?controller=auth&action=loginForm');
            exit();
        }
    }
} else {
    // Contrôleur non trouvé
    if(isset($_SESSION['user_id'])) {
        echo "<div class='alert alert-danger m-3'>";
        echo "<h4>Erreur 404 - Contrôleur non trouvé</h4>";
        echo "<p>Le contrôleur '" . htmlspecialchars($controllerName) . "' n'existe pas.</p>";
        echo "<a href='index.php?controller=patient&action=index' class='btn btn-primary'>Retour à l'accueil</a>";
        echo "</div>";
    } else {
        header('Location: index.php?controller=auth&action=loginForm');
        exit();
    }
}
?>