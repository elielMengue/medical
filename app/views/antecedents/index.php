<?php
// Démarrer la session pour les messages
if(session_id() == '') {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des antécédents médicaux</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body { 
            background-image: url('/projet_medical/app/public/assets/images/background-lo.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            padding-bottom: 20px;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 0;
        }
        
        .container {
            position: relative;
            z-index: 1;
            padding-top: 50px;
        }
        
        /* PAGE HEADER */
        .page-header {
            background: linear-gradient(135deg, #212529 0%, #343a40 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 15px 35px rgba(33, 37, 41, 0.3);
            border: 2px solid #495057;
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.05), transparent);
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }
        
        .page-header h1 {
            position: relative;
            z-index: 2;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0;
        }
        
        /* GRADIENT TEXT */
        .gradient-text {
            background: linear-gradient(135deg, #ffffff 0%, #e9ecef 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* CARTE D'INFORMATION */
        .info-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border: 1px solid rgba(255,255,255,0.2);
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .info-card i {
            font-size: 4rem;
            color: #212529;
            margin-bottom: 20px;
        }
        
        .info-card h2 {
            color: #212529;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .info-card p {
            color: #495057;
            font-size: 1.1rem;
            margin-bottom: 25px;
        }
        
        .btn-auth {
            background: #0d6efd;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-auth:hover {
            background: #0b5ed7;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
        }
        
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }
        
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .page-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Barre de titre -->
        <div class="page-header">
            <h1 class="gradient-text">
                <i class="bi bi-heart-pulse-fill me-2 floating"></i>
                Gestion des antécédents médicaux
            </h1>
        </div>

        <!-- Carte d'information -->
        <div class="info-card">
            <i class="bi bi-heart-pulse-fill floating"></i>
            <h2>Bienvenue</h2>
            <p>
                Cette application permet de gérer les antécédents médicaux des patients.
                Pour accéder à la plateforme, veuillez vous authentifier.
            </p>
            <a href="/projet_medical/app/views/auth/login.php" class="btn-auth">
                <i class="bi bi-box-arrow-in-right me-2"></i> S'authentifier
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>