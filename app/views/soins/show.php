<?php
if(session_id() == '') {
    session_start();
}

// Rôle de l'utilisateur connecté
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';

if(!isset($soin) || !$soin) {
    header('Location: index.php?controller=soin&action=index');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du soin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body { 
            background-image: url('/projet_medical/app/public/assets/images/background-log.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            padding-bottom: 20px;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        .container {
            position: relative;
            z-index: 1;
        }
        
      .page-header {
    background: linear-gradient(135deg, #6eb5ff, #4d9eff);
    color: white;
    padding: 25px 30px;
    border-radius: 15px;
    margin-bottom: 25px;
    box-shadow: 0 15px 35px rgba(77, 158, 255, 0.3);
    border: 2px solid #3d8eff;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
    transform: rotate(45deg);
    animation: shimmer 3s infinite;
}

.table-header {
    background: linear-gradient(135deg, #6eb5ff, #4d9eff);
    color: white;
    font-weight: 600;
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    box-shadow: 0 2px 5px rgba(77, 158, 255, 0.2);
}

.table-header i {
    margin-right: 10px;
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
        
        .detail-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .info-row {
            display: flex;
            border-bottom: 1px solid #e9ecef;
            padding: 12px 0;
        }
        
        .info-label {
            width: 200px;
            font-weight: 600;
            color: #495057;
        }
        
        .info-value {
            flex: 1;
            color: #212529;
        }
        
        .statut-badge {
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            display: inline-block;
        }
        .statut-planifie { background-color: #fff3cd; color: #856404; }
        .statut-en_cours { background-color: #d1ecf1; color: #0c5460; }
        .statut-effectue { background-color: #d4edda; color: #155724; }
        .statut-annule { background-color: #f8d7da; color: #721c24; }
        
        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }
        
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(33, 37, 41, 0.15);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #ffffff 0%, #e9ecef 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body>
    <!-- Inclusion de la navbar -->
    

    <div class="container">
        <!-- En-tête -->
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="gradient-text">
                    <i class="bi bi-calendar-heart-fill me-2 floating"></i>
                    Détails du soin
                </h1>
                <p>
                    Planifié le <?php echo date('d/m/Y', strtotime($soin['date_soin'])); ?> à <?php echo $soin['heure_soin']; ?>
                </p>
            </div>
            <div>
                <a href="index.php?controller=soin&action=index" class="btn btn-outline-light hover-lift">
                    <i class="bi bi-arrow-left me-1"></i> Retour
                </a>
            </div>
        </div>

        <!-- Messages de notification -->
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Détails du soin -->
        <div class="detail-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    <i class="bi bi-info-circle-fill text-primary me-2"></i>
                    Informations du soin
                </h4>
                <span class="statut-badge statut-<?php echo $soin['statut']; ?>">
                    <?php 
                    switch($soin['statut']) {
                        case 'planifie': echo 'Planifié'; break;
                        case 'en_cours': echo 'En cours'; break;
                        case 'effectue': echo 'Effectué'; break;
                        case 'annule': echo 'Annulé'; break;
                        default: echo $soin['statut'];
                    }
                    ?>
                </span>
            </div>

            <div class="info-row">
                <div class="info-label"><i class="bi bi-person-badge me-2"></i>Patient</div>
                <div class="info-value">
                    
                        <strong><?php echo htmlspecialchars($soin['patient_prenom'] . ' ' . strtoupper($soin['patient_nom'])); ?></strong>
                    </a>
                </div>
            </div>

            <div class="info-row">
                <div class="info-label"><i class="bi bi-person-badge me-2"></i>Infirmier(ère)</div>
                <div class="info-value">
                    <?php echo htmlspecialchars($soin['infirmier_prenom'] . ' ' . $soin['infirmier_nom']); ?>
                </div>
            </div>

            <div class="info-row">
                <div class="info-label"><i class="bi bi-heart-pulse me-2"></i>Type de soin</div>
                <div class="info-value">
                    <strong><?php echo htmlspecialchars($soin['type_soin']); ?></strong>
                </div>
            </div>

            <?php if(!empty($soin['description'])): ?>
            <div class="info-row">
                <div class="info-label"><i class="bi bi-chat-text me-2"></i>Description</div>
                <div class="info-value"><?php echo nl2br(htmlspecialchars($soin['description'])); ?></div>
            </div>
            <?php endif; ?>

            <div class="info-row">
                <div class="info-label"><i class="bi bi-calendar me-2"></i>Date</div>
                <div class="info-value"><?php echo date('d/m/Y', strtotime($soin['date_soin'])); ?></div>
            </div>

            <div class="info-row">
                <div class="info-label"><i class="bi bi-clock me-2"></i>Heure</div>
                <div class="info-value"><?php echo $soin['heure_soin']; ?></div>
            </div>

            <?php if(!empty($soin['numero_lit'])): ?>
            <div class="info-row">
                <div class="info-label"><i class="bi bi-door-open me-2"></i>Numéro de lit</div>
                <div class="info-value"><?php echo $soin['numero_lit']; ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Boutons d'action -->
        <div class="d-flex justify-content-between">
            <div>
                <a href="index.php?controller=soin&action=index" class="btn btn-secondary hover-lift">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
            </div>
            <div class="d-flex gap-2">
                <?php if(($userRole === 'major' || $userRole === 'admin') && $soin['statut'] !== 'effectue'): ?>
                    <a href="index.php?controller=soin&action=edit&id=<?php echo $soin['id']; ?>" 
                       class="btn btn-warning hover-lift">
                        <i class="bi bi-pencil"></i> Modifier
                    </a>
                <?php endif; ?>
                
                <?php if($userRole === 'infirmier' && $soin['statut'] !== 'effectue'): ?>
                    <a href="index.php?controller=soin&action=updateStatut&id=<?php echo $soin['id']; ?>&statut=effectue" 
                       class="btn btn-success hover-lift"
                       onclick="return confirm('Confirmer que ce soin a été effectué ?')">
                        <i class="bi bi-check-lg"></i> Marquer effectué
                    </a>
                <?php endif; ?>
                
                <?php if($userRole === 'admin'): ?>
                    <a href="index.php?controller=soin&action=delete&id=<?php echo $soin['id']; ?>" 
                       class="btn btn-danger hover-lift"
                       onclick="return confirm('Supprimer ce soin ?')">
                        <i class="bi bi-trash"></i> Supprimer
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>