<?php
if(session_id() == '') {
    session_start();
}

// SUPPRIMER OU COMMENTER CES LIGNES
// $loginSuccess = '';
// if(isset($_SESSION['login_success'])) {
//     $loginSuccess = $_SESSION['login_success'];
//     unset($_SESSION['login_success']);
// }

// Déterminer le sexe de l'utilisateur (à adapter selon votre base de données)
$sexe = isset($_SESSION['user_sexe']) ? $_SESSION['user_sexe'] : '';
$prenom = isset($_SESSION['user_prenom']) ? $_SESSION['user_prenom'] : '';
$nom = isset($_SESSION['user_nom']) ? $_SESSION['user_nom'] : '';

// Déterminer la civilité en fonction du sexe
if($sexe == 'F' || $sexe == 'f' || $sexe == 'femme' || $sexe == 'Femme') {
    $civilite = "Madame";
} elseif($sexe == 'H' || $sexe == 'h' || $sexe == 'homme' || $sexe == 'Homme') {
    $civilite = "Monsieur";
} else {
    // Par défaut, essayer de déduire par la terminaison du prénom (approximation)
    $derniereLettre = substr($prenom, -1);
    if($derniereLettre == 'e' || $derniereLettre == 'E') {
        $civilite = "Madame";
    } else {
        $civilite = "Monsieur";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Gestion Médicale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        /* Supprimer la carte blanche du layout pour cette page */
        .content-card {
            background: transparent !important;
            backdrop-filter: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        
        .content {
            padding: 0 !important;
        }
        
        /* Styles pour la page d'accueil */
        .accueil-container {
            position: relative;
            width: 100%;
            min-height: calc(100vh - 70px);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .background-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }
        
        .background-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: blur(5px);
        }
        
        .content-overlay {
            position: relative;
            z-index: 1;
            width: 90%;
            max-width: 1200px;
            padding: 20px;
        }
        
        /* Carte de bienvenue */
        .welcome-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: fadeInDown 0.8s ease;
            text-align: center;
        }
        
        .welcome-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 15px;
        }
        
        .welcome-title span {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .welcome-subtitle {
            font-size: 1.3rem;
            color: #495057;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .welcome-subtitle i {
            color: #0d6efd;
            font-size: 1.8rem;
        }
        
        /* Message d'instruction */
        .instruction-message {
            font-size: 1.2rem;
            color: #0d6efd;
            background: rgba(13, 110, 253, 0.1);
            padding: 15px 25px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 15px;
            margin-top: 10px;
            border: 1px solid rgba(13, 110, 253, 0.3);
            animation: pulse 2s infinite;
        }
        
        .instruction-message i {
            font-size: 1.5rem;
            color: #0d6efd;
            animation: bounce 2s infinite;
        }
        
        .badge-role {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            color: white;
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 500;
            display: inline-block;
            margin-top: 15px;
        }
        
        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes bounce {
            0%, 100% {
                transform: translateX(0);
            }
            50% {
                transform: translateX(5px);
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(13, 110, 253, 0);
            }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        
        @media (max-width: 768px) {
            .welcome-title {
                font-size: 1.6rem;
            }
            
            .welcome-subtitle {
                font-size: 1.1rem;
                flex-direction: column;
            }
            
            .instruction-message {
                font-size: 1rem;
                padding: 12px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="accueil-container">
        <!-- Image de fond -->
        <div class="background-image">
            <img src="/projet_medical/app/public/assets/images/background-log.jpg" alt="Accueil">
        </div>
        
        <!-- Contenu par-dessus l'image -->
        <div class="content-overlay">
            <!-- SUPPRIMER OU COMMENTER CE BLOC -->
            <!-- Message de succès de connexion -->
            <?php /* if(!empty($loginSuccess)): ?>
            <div class="alert alert-success-custom alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php echo $loginSuccess; ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; */ ?>
            
            <!-- Carte de bienvenue -->
            <div class="welcome-card">
                <div class="welcome-title">
                    <i class="bi bi-heart-pulse-fill me-2 floating" style="color: #0d6efd;"></i>
                    Bienvenue <span><?php echo $civilite . ' ' . htmlspecialchars($prenom . ' ' . $nom); ?></span>
                </div>
                <div class="welcome-subtitle">
                    <i class="bi bi-info-circle"></i>
                    Vous êtes connecté au système de gestion des antécédents médicaux
                </div>
                
                <!-- Message d'instruction -->
                <div class="instruction-message">
                    <i class="bi bi-arrow-left-circle-fill"></i>
                    Veuillez choisir une tâche dans la colonne de gauche pour démarrer votre travail
                    <i class="bi bi-arrow-left-circle-fill"></i>
                </div>
                
                
            </div>
        </div>
    </div>
</body>
</html>