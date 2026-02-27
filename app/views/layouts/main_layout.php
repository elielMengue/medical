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
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Background pattern pour la zone de contenu */
        .content-bg {
            background: linear-gradient(180deg, #E0F2FE 0%, #F0F9FF 100%);
            position: relative;
        }
        .content-bg::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 200px;
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 50 Q25 30 50 50 T100 50 L100 100 L0 100 Z' fill='rgba(255,255,255,0.3)'/%3E%3C/svg%3E");
            background-size: 200px 200px;
            background-repeat: repeat-x;
            opacity: 0.5;
        }
        /* Dégradés pour les cartes */
        .card-gradient-purple {
            background: linear-gradient(135deg, #E9D5FF 0%, #DDD6FE 100%);
        }
        .card-gradient-green {
            background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%);
        }
        .card-gradient-blue-green {
            background: linear-gradient(135deg, #A5F3FC 0%, #67E8F9 100%);
        }
        .card-gradient-pink {
            background: linear-gradient(135deg, #FCE7F3 0%, #FBCFE8 100%);
        }
        /* Login page background overlay */
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
        <?php endif; ?>
    </style>
</head>
<body class="bg-gray-50 h-screen overflow-hidden">
    <?php if($isLoginPage): ?>
        <!-- PAGE DE CONNEXION : UNIQUEMENT LE FORMULAIRE -->
        <div class="login-wrapper">
            <?php echo isset($content) ? $content : ''; ?>
        </div>
    <?php else: ?>
        <div class="flex h-full">
            <!-- Sidebar -->
            <aside class="w-64 bg-white border-r border-gray-200 flex flex-col rounded-r-2xl">
                <!-- Logo et Header -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center space-x-3 mb-4">
                        <!-- Logo hexagonale stylisée -->
                        <div class="relative">
                            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 2L32 8V18L20 24L8 18V8L20 2Z" fill="url(#gradient1)"/>
                                <path d="M20 16L28 20V28L20 32L12 28V20L20 16Z" fill="url(#gradient2)"/>
                                <defs>
                                    <linearGradient id="gradient1" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#10B981;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#059669;stop-opacity:1" />
                                    </linearGradient>
                                    <linearGradient id="gradient2" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#34D399;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#10B981;stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900">MediSphere</span>
                    </div>
                    <!-- Section Hôpital -->
                    <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="bg-white rounded-xl p-3 border border-gray-100 shadow-sm">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900 text-sm"><?php echo isset($navbarText) ? htmlspecialchars($navbarText) : 'Hôpital'; ?></div>
                                <div class="text-xs text-gray-500 mt-0.5"><?php echo isset($_SESSION['user_service']) ? htmlspecialchars($_SESSION['user_service']) : ''; ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Navigation Menu -->
                <nav class="flex-1 overflow-auto py-4 px-3">
                    <ul class="space-y-1">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <!-- Patient Management -->
                            <li class="mb-4">
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">Gestion des Patients</div>
                                <ul class="space-y-1">
                                    <li>
                                        <a href="index.php?controller=accueil&action=index" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'accueil') ? 'bg-blue-50 text-blue-700 font-medium' : ''; ?>">
                                            <svg class="w-5 h-5 mr-3 <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'accueil') ? 'text-blue-600' : 'text-gray-400'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                            </svg>
                                            Dashboard
                                        </a>
                                    </li>
                                    <?php if($_SESSION['user_role'] != 'infirmier' || $_SESSION['user_role'] == 'admin'): ?>
                                    <li>
                                        <a href="index.php?controller=soin&action=index" class="flex items-center px-3 py-2 <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'soin' && isset($_GET['action']) && $_GET['action'] == 'index') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-gray-50'; ?> rounded-lg transition-colors">
                                            <svg class="w-5 h-5 mr-3 <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'soin' && isset($_GET['action']) && $_GET['action'] == 'index') ? 'text-blue-600' : 'text-gray-400'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            Appointments
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?controller=patient&action=search" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'patient' && isset($_GET['action']) && $_GET['action'] == 'search') ? 'bg-blue-50 text-blue-700 font-medium' : ''; ?>">
                                            <svg class="w-5 h-5 mr-3 <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'patient' && isset($_GET['action']) && $_GET['action'] == 'search') ? 'text-blue-600' : 'text-gray-400'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                            Patients
                                        </a>
                                    </li>
                                    <?php if($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'medecin' || $_SESSION['user_role'] == 'major'): ?>
                                    <li>
                                        <a href="index.php?controller=patient&action=create" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'patient' && isset($_GET['action']) && $_GET['action'] == 'create') ? 'bg-blue-50 text-blue-700 font-medium' : ''; ?>">
                                            <svg class="w-5 h-5 mr-3 <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'patient' && isset($_GET['action']) && $_GET['action'] == 'create') ? 'text-blue-600' : 'text-gray-400'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Medical Records
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </ul>
                            </li>

                            <!-- Planning des soins -->
                            <li class="mb-4">
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">Planning</div>
                                <ul class="space-y-1">
                                    <?php if($_SESSION['user_role'] == 'infirmier'): ?>
                                    <li>
                                        <a href="index.php?controller=soin&action=monPlanning" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'soin' && isset($_GET['action']) && $_GET['action'] == 'monPlanning') ? 'bg-blue-50 text-blue-700 font-medium' : ''; ?>">
                                            <svg class="w-5 h-5 mr-3 <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'soin' && isset($_GET['action']) && $_GET['action'] == 'monPlanning') ? 'text-blue-600' : 'text-gray-400'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            Mon planning
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                    <?php if($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'major'): ?>
                                    <li>
                                        <a href="index.php?controller=soin&action=create" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'soin' && isset($_GET['action']) && $_GET['action'] == 'create') ? 'bg-blue-50 text-blue-700 font-medium' : ''; ?>">
                                            <svg class="w-5 h-5 mr-3 <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'soin' && isset($_GET['action']) && $_GET['action'] == 'create') ? 'text-blue-600' : 'text-gray-400'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Planifier un soin
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </li>

                            <!-- Staff Management -->
                            <?php if($_SESSION['user_role'] == 'admin'): ?>
                            <li class="mb-4">
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">Gestion du Personnel</div>
                                <ul class="space-y-1">
                                    <li>
                                        <a href="index.php?controller=admin&action=users" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'admin' && isset($_GET['action']) && $_GET['action'] == 'users') ? 'bg-blue-50 text-blue-700 font-medium' : ''; ?>">
                                            <svg class="w-5 h-5 mr-3 <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'admin' && isset($_GET['action']) && $_GET['action'] == 'users') ? 'text-blue-600' : 'text-gray-400'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            Liste utilisateurs
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?controller=admin&action=search" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'admin' && isset($_GET['action']) && $_GET['action'] == 'search') ? 'bg-blue-50 text-blue-700 font-medium' : ''; ?>">
                                            <svg class="w-5 h-5 mr-3 <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'admin' && isset($_GET['action']) && $_GET['action'] == 'search') ? 'text-blue-600' : 'text-gray-400'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                            Rechercher
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.php?controller=admin&action=create" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'admin' && isset($_GET['action']) && $_GET['action'] == 'create') ? 'bg-blue-50 text-blue-700 font-medium' : ''; ?>">
                                            <svg class="w-5 h-5 mr-3 <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'admin' && isset($_GET['action']) && $_GET['action'] == 'create') ? 'text-blue-600' : 'text-gray-400'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                            </svg>
                                            Nouvel utilisateur
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <?php endif; ?>

                            <!-- Reports -->
                            <?php if($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'major'): ?>
                            <li class="mb-4">
                                <ul class="space-y-1">
                                    <li>
                                        <a href="index.php?controller=rapport&action=index" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'rapport') ? 'bg-blue-50 text-blue-700 font-medium' : ''; ?>">
                                            <svg class="w-5 h-5 mr-3 <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'rapport') ? 'text-blue-600' : 'text-gray-400'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                            </svg>
                                            Reports
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <?php endif; ?>

                            <!-- Other -->
                            <li>
                                <ul class="space-y-1">
                                    <li>
                                        <a href="index.php?controller=auth&action=profile" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'auth' && isset($_GET['action']) && $_GET['action'] == 'profile') ? 'bg-blue-50 text-blue-700 font-medium' : ''; ?>">
                                            <svg class="w-5 h-5 mr-3 <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'auth' && isset($_GET['action']) && $_GET['action'] == 'profile') ? 'text-blue-600' : 'text-gray-400'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            Mon profil
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Settings
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li>
                                <a href="index.php?controller=antecedent&action=index" class="flex items-center px-3 py-2 bg-blue-50 text-blue-700 font-medium rounded-lg">
                                    <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    Accueil
                                </a>
                            </li>
                            <li>
                                <a href="index.php?controller=auth&action=loginForm" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                    Connexion
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </aside>

            <!-- Content Area -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Top Bar / Header -->
                <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-bl-2xl">
                    <div class="flex items-center space-x-4">
                        <!-- Back button -->
                        <button onclick="history.back()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <!-- Page Title -->
                        <?php
                        $pageTitle = "Dashboard";
                        if(isset($_GET['controller'])) {
                            $controller = $_GET['controller'];
                            if($controller == 'soin') $pageTitle = "Appointments";
                            elseif($controller == 'patient') $pageTitle = "Patients";
                            elseif($controller == 'admin') $pageTitle = "Administration";
                            elseif($controller == 'rapport') $pageTitle = "Reports";
                            elseif($controller == 'auth') $pageTitle = "Profile";
                            elseif($controller == 'accueil') $pageTitle = "Dashboard";
                        }
                        ?>
                        <h1 class="text-2xl font-bold text-gray-900"><?php echo $pageTitle; ?></h1>
                    </div>

                    <!-- Global Search -->
                    <div class="flex-1 max-w-md mx-8">
                        <div class="relative">
                            <input type="text" placeholder="Search Here..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications & Profile -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="relative p-2 hover:bg-gray-100 rounded-full transition-colors">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="absolute top-1 right-1 block w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        <!-- User Profile -->
                        <div class="flex items-center space-x-3 cursor-pointer hover:bg-gray-50 rounded-lg px-3 py-2 transition-colors">
                            <img src="https://via.placeholder.com/40" alt="Profile" class="w-10 h-10 rounded-full border-2 border-gray-200">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-gray-900"><?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Utilisateur'; ?></span>
                                <span class="text-xs text-gray-500"><?php echo isset($_SESSION['user_role']) ? ucfirst(htmlspecialchars($_SESSION['user_role'])) : ''; ?></span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </header>
                <!-- Main Content -->
                <main class="flex-1 overflow-auto content-bg relative">
                    <div class="p-6 relative z-10">
                        <?php echo isset($content) ? $content : ''; ?>
                    </div>
                </main>
            </div>
        </div>
        <script>
            function toggleMenu(button) {
                const submenu = button.nextElementSibling;
                if (submenu) submenu.classList.toggle('hidden');
            }
        </script>
    <?php endif; ?>
</body>
</html>