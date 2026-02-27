<?php
if(session_id() == '') {
    session_start();
}

// Fonction pour vérifier si une section est ouverte
function isSectionOpen($controllers) {
    $currentController = isset($_GET['controller']) ? $_GET['controller'] : '';
    return in_array($currentController, $controllers) ? 'menu-open' : '';
}

// Déterminer si c'est la page de connexion
$currentController = isset($_GET['controller']) ? $_GET['controller'] : '';
$currentAction = isset($_GET['action']) ? $_GET['action'] : '';
$isLoginPage = ($currentController == 'auth' && $currentAction == 'loginForm');

// Déterminer le texte de la navbar en fonction du centre et du service de l'utilisateur connecté
$navbarText = "Gestion des antécédents médicaux"; // Texte par défaut

if(isset($_SESSION['user_id'])) {
    // Récupérer les valeurs de session avec des valeurs par défaut
    $userCentre = isset($_SESSION['user_centre']) ? $_SESSION['user_centre'] : '';
    $userService = isset($_SESSION['user_service']) ? $_SESSION['user_service'] : '';
    
    // Nettoyer les valeurs (enlever les espaces, mettre en minuscules)
    $userCentre = trim(strtolower($userCentre));
    $userService = trim(strtolower($userService));
    
    // Appliquer la logique
    if($userCentre == 'communautaire' && $userService == 'urgence medicale') {
        $navbarText = "Centre Hospitalier Universitaire Communautaire / Urgence médicale";
    } elseif($userCentre == 'communautaire' && $userService == 'urgence chirurgicale') {
        $navbarText = "Centre Hospitalier Universitaire Communautaire / Urgence chirurgicale";
    } elseif($userCentre == 'amitié' && $userService == 'urgence medicale') {
        $navbarText = "Hôpital Amitié / Urgence médicale";
    } elseif($userCentre == 'amitié' && $userService == 'urgence chirurgicale') {
        $navbarText = "Hôpital Amitié / Urgence chirurgicale";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Médicale</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* STYLE POUR LA PAGE DE CONNEXION */
        <?php if($isLoginPage): ?>
        body {
            background-image: url('/projet_medical/app/public/assets/images/background-logue.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.5);
            z-index: 0;
        }
        
        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        <?php else: ?>
        /* STYLE POUR LES AUTRES PAGES */
        body {
            margin: 0;
            padding: 0;
            height: 100%;
        }
        
        /* NAVBAR FIXE EN HAUT - IDENTIQUE SUR TOUTES LES PAGES */
        .app-navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 70px;
            background: linear-gradient(135deg, #212529 0%, #343a40 100%) !important;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .app-navbar .navbar-brand {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            font-size: 1.3rem !important;
            font-weight: 600 !important;
            margin: 0 auto !important;
            text-align: center;
            max-width: 80%;
        }
        
        .app-navbar .navbar-brand i {
            font-size: 1.8rem !important;
            flex-shrink: 0;
        }
        
        .app-navbar .navbar-brand span {
            white-space: normal;
            line-height: 1.3;
        }
        
        /* TEXTE AVEC DÉGRADÉ - COULEURS FIXES */
        .gradient-text {
            background: linear-gradient(135deg, #4e73df, #1cc88a) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
            font-weight: 700 !important;
        }
        
        /* Conteneur principal SOUS la navbar */
        .main-container {
            display: flex;
            margin-top: 70px;
            height: calc(100vh - 70px);
        }
        
        /* Colonne de gauche - MENUS DÉROULANTS */
        .sidebar {
            width: 320px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-right: 1px solid #dee2e6;
            overflow-y: auto;
            padding: 0;
            padding-bottom: 220px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            position: relative;
        }
        
        .sidebar-menu {
            width: 100%;
            border-collapse: collapse;
            position: relative;
            z-index: 1;
        }
        
        .sidebar-menu tr {
            display: block;
            margin-bottom: 5px;
            background-color: #e6f0ff !important; /* Bleu très clair */
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .sidebar-menu tr:hover {
            background-color: #d1e0ff !important; /* Bleu un peu plus foncé au survol */
            transform: translateX(5px);
        }
        
        .sidebar-menu td {
            padding: 0;
            display: block;
            width: 100%;
        }
        
        /* MENUS EN BLEU CLAIR */
        .menu-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            color: #0047b3 !important;
            text-decoration: none;
            border-left: 4px solid transparent;
            cursor: pointer;
            font-weight: 500;
            background-color: transparent;
            width: 100%;
            border: none;
        }
        
        .menu-item:hover {
            background-color: #c2d9f0;
            border-left-color: #0047b3;
        }
        
        .menu-item i:first-child {
            width: 24px;
            margin-right: 10px;
            font-size: 1.2rem;
            color: #0047b3 !important;
        }
        
        .menu-item .menu-title {
            flex: 1;
        }
        
        .menu-item .arrow {
            transition: transform 0.3s ease;
            color: #0047b3 !important;
        }
        
        .menu-open .menu-item .arrow {
            transform: rotate(90deg);
        }
        
        .menu-item.active {
            background-color: #b3d1ff !important;
            border-left-color: #0047b3 !important;
            color: #002b80 !important;
            font-weight: 600;
        }
        
        .menu-item.active i {
            color: #002b80 !important;
        }
        
        .submenu {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            background-color: #ffd9e6; /* Rose clair */
        }
        
        .menu-open .submenu {
            max-height: 500px;
            transition: max-height 0.5s ease-in;
        }
        
        .submenu-item {
            display: flex;
            align-items: center;
            padding: 10px 20px 10px 54px;
            color: #b3005c !important; /* Rose foncé pour le texte */
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .submenu-item:hover {
            background-color: #ffc2d1 !important; /* Rose plus foncé au survol */
            color: #99004d !important;
            border-left-color: #ff66a3 !important;
            transform: translateX(5px);
        }
        
        .submenu-item i {
            width: 20px;
            margin-right: 10px;
            font-size: 0.9rem;
            color: #b3005c !important; /* Rose foncé pour les icônes */
        }
        
        .submenu-item.active {
            background-color: #ffb3c6 !important; /* Rose moyen pour l'élément actif */
            color: #800040 !important;
            font-weight: 500;
            border-left-color: #ff4d94 !important;
        }
        
        .submenu-item.active i {
            color: #800040 !important;
        }
        
        /* Séparateur pour les menus */
        .menu-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #0047b3, transparent);
            margin: 15px 20px;
        }
        
        /* Style pour l'image dans le menu - FIXE EN BAS */
        .menu-image {
            position: absolute;
            bottom: 20px;
            left: 0;
            width: 100%;
            padding: 0 10px;
            overflow: hidden;
            z-index: 0;
        }
        
        .menu-image img {
            width: 100%;
            height: auto;
            display: block;
            filter: blur(2px);
            transition: filter 0.3s ease;
            border-radius: 10px;
        }
        
        .menu-image img:hover {
            filter: blur(0);
        }
        
        /* Colonne de droite - CONTENU */
        .content {
            flex: 1;
            background-image: url('/projet_medical/app/public/assets/images/background-log.jpg');
            background-size: cover;
            background-position: center;
            overflow-y: auto;
            padding: 20px;
        }
        
        .content-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            min-height: 100%;
        }
        
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                display: none;
            }
            
            .content {
                width: 100%;
            }
            
            .app-navbar .navbar-brand {
                font-size: 1rem !important;
                max-width: 95%;
            }
        }
        <?php endif; ?>
    </style>
</head>
<body>
    <?php if($isLoginPage): ?>
        <!-- PAGE DE CONNEXION : UNIQUEMENT LE FORMULAIRE -->
        <div class="login-wrapper">
            <?php echo isset($content) ? $content : ''; ?>
        </div>
    <?php else: ?>
        <!-- AUTRES PAGES : NAVBAR + MENUS + CONTENU -->
        <!-- NAVBAR - IDENTIQUE SUR TOUTES LES PAGES -->
        <div class="app-navbar">
            <div class="navbar-brand">
                <i class="bi bi-heart-pulse-fill gradient-text"></i>
                <span class="gradient-text"><?php echo $navbarText; ?></span>
            </div>
        </div>

        <!-- Conteneur principal avec 2 colonnes -->
        <div class="main-container">
            <!-- Colonne de gauche : Menus déroulants -->
            <div class="sidebar">
                <table class="sidebar-menu">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        
                        <!-- MENU ACCUEIL -->
                        <tr>
                            <td>
                                <a href="index.php?controller=accueil&action=index" class="menu-item <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'accueil') ? 'active' : ''; ?>">
                                    <i class="bi bi-house-door"></i>
                                    <span class="menu-title">Accueil</span>
                                </a>
                            </td>
                        </tr>
                        
                        <!-- MENU GESTION DES PATIENTS - ADMIN A TOUS LES DROITS -->
                        <?php if($_SESSION['user_role'] != 'infirmier' || $_SESSION['user_role'] == 'admin'): ?>
                        <tr class="<?php echo isSectionOpen(array('patient')); ?>">
                            <td>
                                <div class="menu-item" onclick="toggleMenu(this)">
                                    <i class="bi bi-people"></i>
                                    <span class="menu-title">Gestion des patients</span>
                                    <i class="bi bi-chevron-right arrow"></i>
                                </div>
                                <ul class="submenu">
                                    
                                    <li>
                                        <a href="index.php?controller=patient&action=search" class="submenu-item <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'patient' && $_GET['action'] == 'search') ? 'active' : ''; ?>">
                                            <i class="bi bi-search"></i>
                                            Rechercher patient
                                        </a>
                                    </li>
                                    <!-- ADMIN PEUT VOIR ET CREER DES PATIENTS -->
                                    <?php if($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'medecin' || $_SESSION['user_role'] == 'major'): ?>
                                    <li>
                                        <a href="index.php?controller=patient&action=create" class="submenu-item <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'patient' && $_GET['action'] == 'create') ? 'active' : ''; ?>">
                                            <i class="bi bi-person-plus"></i>
                                            Nouveau patient
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </td>
                        </tr>
                        <?php endif; ?>
                        
                        <!-- MENU PLANNING DES SOINS -->
                        <tr class="<?php echo isSectionOpen(array('soin')); ?>">
                            <td>
                                <div class="menu-item" onclick="toggleMenu(this)">
                                    <i class="bi bi-calendar-heart"></i>
                                    <span class="menu-title">Planning des soins</span>
                                    <i class="bi bi-chevron-right arrow"></i>
                                </div>
                                <ul class="submenu">
                                    <li>
                                        <a href="index.php?controller=soin&action=index" class="submenu-item <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'soin' && $_GET['action'] == 'index') ? 'active' : ''; ?>">
                                            <i class="bi bi-calendar-week"></i>
                                            Planning général
                                        </a>
                                    </li>
                                    <?php if($_SESSION['user_role'] == 'infirmier'): ?>
                                    <li>
                                        <a href="index.php?controller=soin&action=monPlanning" class="submenu-item <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'soin' && $_GET['action'] == 'monPlanning') ? 'active' : ''; ?>">
                                            <i class="bi bi-person-workspace"></i>
                                            Mon planning
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                    <!-- ADMIN PEUT PLANIFIER DES SOINS -->
                                    <?php if($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'major'): ?>
                                    <li>
                                        <a href="index.php?controller=soin&action=create" class="submenu-item <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'soin' && $_GET['action'] == 'create') ? 'active' : ''; ?>">
                                            <i class="bi bi-plus-circle"></i>
                                            Planifier un soin
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </td>
                        </tr>
                        
                        <!-- MENU GESTION DES UTILISATEURS (admin seulement) -->
                        <?php if($_SESSION['user_role'] == 'admin'): ?>
                        <tr class="<?php echo isSectionOpen(array('admin')); ?>">
                            <td>
                                <div class="menu-item" onclick="toggleMenu(this)">
                                    <i class="bi bi-people-fill"></i>
                                    <span class="menu-title">Gestion des utilisateurs</span>
                                    <i class="bi bi-chevron-right arrow"></i>
                                </div>
                                <ul class="submenu">
                                    <li>
                                        <a href="index.php?controller=admin&action=users" class="submenu-item <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'admin' && $_GET['action'] == 'users') ? 'active' : ''; ?>">
                                            <i class="bi bi-list-ul"></i>
                                            Liste utilisateurs
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?controller=admin&action=search" class="submenu-item <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'admin' && $_GET['action'] == 'search') ? 'active' : ''; ?>">
                                            <i class="bi bi-search"></i>
                                            Rechercher
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?controller=admin&action=create" class="submenu-item <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'admin' && $_GET['action'] == 'create') ? 'active' : ''; ?>">
                                            <i class="bi bi-person-plus"></i>
                                            Nouvel utilisateur
                                        </a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <?php endif; ?>
                        
                        <!-- MENU GESTION DES RAPPORTS (admin et major) -->
                        <?php if($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'major'): ?>
                        <tr>
                            <td>
                                <a href="index.php?controller=rapport&action=index" class="menu-item <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'rapport') ? 'active' : ''; ?>">
                                    <i class="bi bi-file-bar-graph"></i>
                                    <span class="menu-title">Gestion des rapports</span>
                                </a>
                            </td>
                        </tr>
                        <?php endif; ?>
                        
                        <!-- SÉPARATEUR -->
                        <tr>
                            <td>
                                <div class="menu-divider"></div>
                            </td>
                        </tr>
                        
                        <!-- MENU MON PROFIL -->
                        <tr>
                            <td>
                                <a href="index.php?controller=auth&action=profile" class="menu-item <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'auth' && $_GET['action'] == 'profile') ? 'active' : ''; ?>">
                                    <i class="bi bi-person-circle"></i>
                                    <span class="menu-title">Mon profil</span>
                                </a>
                            </td>
                        </tr>
                        
                        
                        
                    <?php else: ?>
                        <!-- Menu pour les non-connectés -->
                        <tr>
                            <td>
                                <a href="index.php?controller=antecedent&action=index" class="menu-item active">
                                    <i class="bi bi-house-door"></i>
                                    <span class="menu-title">Accueil</span>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="index.php?controller=auth&action=loginForm" class="menu-item">
                                    <i class="bi bi-box-arrow-in-right"></i>
                                    <span class="menu-title">Connexion</span>
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
                
                <!-- IMAGE FLOUE FIXE EN BAS -->
                <div class="menu-image">
                    <img src="/projet_medical/app/public/assets/images/background-logue.jpg" 
                         alt="Image médicale">
                </div>
            </div>
            
            <!-- Colonne de droite : Contenu -->
            <div class="content">
                <div class="content-card">
                    <?php echo isset($content) ? $content : ''; ?>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        
        <script>
            function toggleMenu(element) {
                const parent = element.parentElement.parentElement;
                parent.classList.toggle('menu-open');
            }
            
            document.addEventListener('DOMContentLoaded', function() {
                const activeSubmenu = document.querySelector('.submenu-item.active');
                if (activeSubmenu) {
                    const parentMenu = activeSubmenu.closest('tr');
                    if (parentMenu) {
                        parentMenu.classList.add('menu-open');
                    }
                }
            });
        </script>
    <?php endif; ?>
</body>
</html>