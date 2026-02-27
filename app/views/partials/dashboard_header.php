<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' : ''; ?>Gestion Médicale</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/projet_medical/app/public/css/style.css">
    
    <style>
        .dashboard-body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .dashboard-navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        
        .dashboard-navbar .navbar-brand,
        .dashboard-navbar .nav-link {
            color: white !important;
        }
        
        .dashboard-navbar .nav-link:hover {
            color: rgba(255,255,255,0.8) !important;
        }
        
        /* Styles cohérents avec les autres pages */
        .page-header {
            background: linear-gradient(135deg, #212529 0%, #343a40 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            border: none;
            transition: all 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .patients-card .stats-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .soins-card .stats-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .today-card .stats-icon {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        
        .pending-card .stats-icon {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }
    </style>
</head>
<body class="dashboard-body">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark dashboard-navbar">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="/projet_medical/app/public/index.php?controller=dashboard&action=index">
                <i class="bi bi-heart-pulse-fill me-2"></i>
                Gestion Médicale
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/projet_medical/app/public/index.php?controller=dashboard&action=index">
                            <i class="bi bi-speedometer2 me-1"></i>
                            Tableau de bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/projet_medical/app/public/index.php?controller=patient&action=index">
                            <i class="bi bi-people me-1"></i>
                            Patients
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/projet_medical/app/public/index.php?controller=soin&action=index">
                            <i class="bi bi-heart-pulse me-1"></i>
                            Soins
                        </a>
                    </li>
                    <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/projet_medical/app/public/index.php?controller=admin&action=index">
                            <i class="bi bi-gear me-1"></i>
                            Administration
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            <?php echo htmlspecialchars($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="/projet_medical/app/public/index.php?controller=auth&action=profile">
                                    <i class="bi bi-person me-2"></i>
                                    Mon profil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="/projet_medical/app/public/index.php?controller=auth&action=logout">
                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    Déconnexion
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Contenu principal -->
    <main class="container-fluid py-4">