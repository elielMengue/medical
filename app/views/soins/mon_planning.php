<?php
if(session_id() == '') {
    session_start();
}

// Rôle de l'utilisateur connecté
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';

// Déterminer la date à afficher (aujourd'hui par défaut)
$dateAffichage = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$dateFormatee = date('d/m/Y', strtotime($dateAffichage));

// Titre de la page
$pageTitle = "Mon planning du " . $dateFormatee;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body { 
            background-image: url('/projet_medical/app/public/assets/images/background-soin.jpg');
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
        
        /* PAGE HEADER - AVEC BOUTONS INTÉGRÉS */
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
        
        .header-content {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .header-title h1 {
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        
        .header-title p {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        .header-nav {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        /* BOUTONS DE NAVIGATION BLANCS */
        .btn-date-nav {
            border-radius: 6px;
            padding: 6px 12px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #dee2e6;
            background-color: white;
            color: #495057;
            white-space: nowrap;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        
        .btn-date-nav i {
            font-size: 0.85rem;
            color: #495057;
        }
        
        .btn-date-nav:hover {
            background-color: #f8f9fa;
            color: #212529;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(33, 37, 41, 0.2);
        }
        
        .date-picker-form {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            padding: 2px 2px 2px 8px;
            border-radius: 30px;
            border: 1px solid #495057;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: inline-flex;
            align-items: center;
        }
        
        .date-picker {
            border: none;
            background: transparent;
            padding: 4px 6px;
            font-weight: 500;
            font-size: 0.8rem;
            color: #212529;
            width: 120px;
        }
        
        .date-picker:focus {
            outline: none;
        }
        
        .btn-changer {
            background: linear-gradient(135deg, #212529 0%, #343a40 100%);
            color: white;
            border: none;
            border-radius: 30px;
            padding: 4px 10px;
            font-weight: 600;
            font-size: 0.75rem;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .btn-changer:hover {
            background: linear-gradient(135deg, #343a40 0%, #495057 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(33, 37, 41, 0.3);
        }
        
        /* TABLEAU */
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .table th {
            background: linear-gradient(135deg, #343a40 0%, #495057 100%);
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 10px;
        }
        
        .table td {
            padding: 12px 10px;
            vertical-align: middle;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9ff;
        }
        
        .statut-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
        }
        .statut-planifie { background-color: #fff3cd; color: #856404; }
        .statut-en_cours { background-color: #d1ecf1; color: #0c5460; }
        .statut-effectue { background-color: #d4edda; color: #155724; }
        .statut-annule { background-color: #f8d7da; color: #721c24; }
        
        .action-buttons .btn {
            margin: 0 3px;
            border-radius: 6px;
            padding: 4px 8px;
            transition: all 0.3s ease;
        }
        
        .action-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn-outline-info {
            border-color: #0dcaf0;
            color: #0dcaf0;
        }
        .btn-outline-info:hover {
            background-color: #0dcaf0;
            color: white;
        }
        .btn-outline-success {
            border-color: #28a745;
            color: #28a745;
        }
        .btn-outline-success:hover {
            background-color: #28a745;
            color: white;
        }
        
        .patient-link {
            color: #212529;
            font-weight: 600;
            text-decoration: none;
        }
        .patient-link:hover {
            text-decoration: underline;
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
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                align-items: stretch;
            }
            
            .header-title {
                text-align: center;
            }
            
            .header-nav {
                justify-content: center;
            }
            
            .page-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Inclusion de la navbar -->
  

    <div class="container">
        <!-- BARRE D'EN-TÊTE AVEC BOUTONS INTÉGRÉS (SANS NOM D'INFIRMIER) -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-title">
                    <h1 class="gradient-text">
                        <i class="bi bi-person-workspace me-2 floating"></i>
                        Mon planning
                    </h1>
                    <p>
                        <i class="bi bi-calendar3 me-1"></i> <?php echo $dateFormatee; ?>
                    </p>
                </div>
                
                <div class="header-nav">
                    <!-- Bouton Jour précédent -->
                    <a href="index.php?controller=soin&action=monPlanning&date=<?php echo date('Y-m-d', strtotime($dateAffichage . ' -1 day')); ?>" 
                       class="btn-date-nav hover-lift">
                        <i class="bi bi-arrow-left me-1"></i> Précédent
                    </a>
                    
                    <!-- Sélecteur de date -->
                    <form method="GET" class="date-picker-form">
                        <input type="hidden" name="controller" value="soin">
                        <input type="hidden" name="action" value="monPlanning">
                        <input type="date" name="date" class="date-picker border-0" value="<?php echo $dateAffichage; ?>">
                        <button type="submit" class="btn-changer hover-lift">
                            <i class="bi bi-check"></i>
                        </button>
                    </form>
                    
                    <!-- Bouton Jour suivant -->
                    <a href="index.php?controller=soin&action=monPlanning&date=<?php echo date('Y-m-d', strtotime($dateAffichage . ' +1 day')); ?>" 
                       class="btn-date-nav hover-lift">
                        Suivant <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Tableau des soins -->
        <div class="table-container">
            <?php if(isset($soins) && $soins->rowCount() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Heure</th>
                                <th>Patient</th>
                                <th>Type de soin</th>
                                <th>Description</th>
                                <th>Lit</th>
                                <th>Statut</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($soin = $soins->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><strong><?php echo date('H:i', strtotime($soin['heure_soin'])); ?></strong></td>
                                <td>
                                    <a href="index.php?controller=patient&action=show&id=<?php echo $soin['patient_id']; ?>" 
                                       class="patient-link">
                                        <?php echo htmlspecialchars($soin['patient_prenom'] . ' ' . $soin['patient_nom']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($soin['type_soin']); ?></td>
                                <td>
                                    <?php if(!empty($soin['description'])): ?>
                                        <?php echo htmlspecialchars(substr($soin['description'], 0, 50)) . (strlen($soin['description']) > 50 ? '...' : ''); ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(!empty($soin['numero_lit'])): ?>
                                        <span class="badge bg-secondary">Lit <?php echo $soin['numero_lit']; ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
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
                                </td>
                                <td class="text-center action-buttons">
                                    <a href="index.php?controller=soin&action=show&id=<?php echo $soin['id']; ?>" 
                                       class="btn btn-sm btn-outline-info" title="Voir détails">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if($soin['statut'] !== 'effectue'): ?>
                                        <a href="index.php?controller=soin&action=updateStatut&id=<?php echo $soin['id']; ?>&statut=effectue" 
                                           class="btn btn-sm btn-outline-success" 
                                           onclick="return confirm('Confirmer que ce soin a été effectué ?')"
                                           title="Marquer effectué">
                                            <i class="bi bi-check-lg"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3 text-muted">
                    <i class="bi bi-info-circle"></i>
                    Total: <strong><?php echo $soins->rowCount(); ?></strong> soin(s) pour cette date
                </div>
                
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x" style="font-size: 4rem; color: #cbd5e1;"></i>
                    <h3 class="mt-4 fw-light">Aucun soin pour cette date</h3>
                    <p class="text-muted mb-4">Vous n'avez aucun soin planifié pour le <?php echo $dateFormatee; ?>.</p>
                    <a href="index.php?controller=soin&action=index" class="btn btn-primary btn-lg px-5 hover-lift">
                        <i class="bi bi-calendar-week me-2"></i> Voir le planning général
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-fermeture des alertes
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>