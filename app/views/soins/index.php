<?php
// Démarrer la session pour les messages
if(session_id() == '') {
    session_start();
}

// Rôle de l'utilisateur connecté
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';

// Déterminer la date à afficher (aujourd'hui par défaut)
$dateAffichage = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$dateFormatee = date('d/m/Y', strtotime($dateAffichage));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning des soins</title>
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
        
        /* PAGE HEADER */
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
        
        .page-header h1 {
            position: relative;
            z-index: 2;
            font-weight: 700;
            font-size: 2rem;
        }
        
        .page-header p {
            position: relative;
            z-index: 2;
            font-size: 1rem;
            opacity: 0.9;
        }
        
        /* BARRE DE BOUTONS INDÉPENDANTS */
        .boutons-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        
        .bouton-nav {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
            cursor: pointer;
        }
        
        .bouton-nav:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.4);
        }
        
        .bouton-nav i {
            font-size: 1.1rem;
        }
        
        .date-picker-container {
            display: inline-flex;
            align-items: center;
        }
        
        .date-picker {
            border-radius: 30px;
            border: 2px solid #dee2e6;
            padding: 10px 15px;
            font-weight: 500;
            font-size: 1rem;
            background: white;
        }
        
        .date-picker:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
            outline: none;
        }
        
        /* TABLE CONTAINER */
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .table th {
            background: linear-gradient(135deg, #343a40 0%, #495057 100%);
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 15px 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .table td {
            padding: 12px;
            vertical-align: middle;
            border-color: #f1f5f9;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9ff;
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.15);
            border-radius: 8px;
        }
        
        /* BOUTONS D'ACTION */
        .action-buttons .btn {
            margin: 0 2px;
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
        .btn-outline-warning {
            border-color: #ffc107;
            color: #ffc107;
        }
        .btn-outline-warning:hover {
            background-color: #ffc107;
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
        .btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
        }
        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: white;
        }
        .btn-outline-primary {
            border-color: #212529;
            color: #212529;
        }
        .btn-outline-primary:hover {
            background-color: #212529;
            color: white;
        }
        
        .badge-statut {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-planifie { background-color: #fff3cd; color: #856404; }
        .badge-en_cours { background-color: #d1ecf1; color: #0c5460; }
        .badge-effectue { background-color: #d4edda; color: #155724; }
        .badge-annule { background-color: #f8d7da; color: #721c24; }
        
        .patient-link {
            font-weight: 700;
            color: #2c3e50;
            text-decoration: none;
        }
        
        .patient-link:hover {
            text-decoration: underline;
            color: #0d6efd;
        }
        
        .row-number {
            font-weight: 700;
            color: #6c757d;
        }
        
        /* ANIMATIONS */
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
            .page-header h1 {
                font-size: 1.5rem;
            }
            
            .page-header {
                padding: 20px;
            }
            
            .boutons-container {
                flex-direction: column;
                align-items: center;
            }
            
            .bouton-nav {
                width: 100%;
                max-width: 250px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête de la page -->
        <div class="page-header">
            <div>
                <h1 class="mb-0 gradient-text">
                    <i class="bi bi-calendar-heart-fill me-2 floating"></i>
                    Planning des soins
                </h1>
                <p class="mb-0 mt-2">
                    <i class="bi bi-calendar"></i> 
                    <?php echo $dateFormatee; ?>
                </p>
            </div>
        </div>

        <!-- BOUTONS INDÉPENDANTS (pas dans une table) -->
        <div class="boutons-container">
            <a href="index.php?controller=soin&action=index&date=<?php echo date('Y-m-d', strtotime($dateAffichage . ' -1 day')); ?>" 
               class="bouton-nav">
                <i class="bi bi-arrow-left"></i> Précédent
            </a>
            
            <div class="date-picker-container">
                <form method="GET">
                    <input type="hidden" name="controller" value="soin">
                    <input type="hidden" name="action" value="index">
                    <input type="date" name="date" class="date-picker" value="<?php echo $dateAffichage; ?>" onchange="this.form.submit()">
                </form>
            </div>
            
            <a href="index.php?controller=soin&action=index&date=<?php echo date('Y-m-d', strtotime($dateAffichage . ' +1 day')); ?>" 
               class="bouton-nav">
                Suivant <i class="bi bi-arrow-right"></i>
            </a>
            
            <!-- Bouton Nouveau soin indépendant aussi -->
            <?php if($userRole === 'major' || $userRole === 'admin'): ?>
                <a href="index.php?controller=soin&action=create" class="bouton-nav">
                    <i class="bi bi-plus-circle-fill"></i> Nouveau soin
                </a>
            <?php endif; ?>
        </div>

        <!-- Messages de notification -->
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Indicateur de date -->
        <?php if(isset($_GET['date']) && $_GET['date'] != date('Y-m-d')): ?>
        <div class="alert alert-info">
            <i class="bi bi-calendar me-2"></i>
            <strong>Affichage du :</strong> <?php echo $dateFormatee; ?>
            <a href="index.php?controller=soin&action=index" class="btn btn-sm btn-outline-secondary float-end">
                <i class="bi bi-calendar-check"></i> Aujourd'hui
            </a>
        </div>
        <?php endif; ?>

        <!-- Liste des soins (dans une table) -->
        <div class="table-container">
            <?php if(isset($soins) && $soins->rowCount() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>HEURE</th>
                                <th>PATIENT</th>
                                <th>INFIRMIER</th>
                                <th>TYPE DE SOIN</th>
                                <th>LIT</th>
                                <th class="text-center">STATUT</th>
                                <th class="text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i = 1;
                            while($soin = $soins->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                            <tr>
                                <td class="text-center"><span class="row-number"><?php echo $i++; ?></span></td>
                                <td><strong><?php echo date('H:i', strtotime($soin['heure_soin'])); ?></strong></td>
                                <td>
                                   
                                       <class="patient-link">
                                        <?php echo htmlspecialchars($soin['patient_prenom'] . ' ' . $soin['patient_nom']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($soin['infirmier_prenom'] . ' ' . $soin['infirmier_nom']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($soin['type_soin']); ?>
                                    <?php if(!empty($soin['description'])): ?>
                                        <i class="bi bi-chat-text ms-1 text-muted" 
                                           title="<?php echo htmlspecialchars($soin['description']); ?>"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(!empty($soin['numero_lit'])): ?>
                                        <span class="badge bg-secondary">Lit <?php echo $soin['numero_lit']; ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php 
                                    $badgeClass = '';
                                    switch($soin['statut']) {
                                        case 'planifie':
                                            $badgeClass = 'badge-planifie';
                                            $statutLabel = 'Planifié';
                                            break;
                                        case 'en_cours':
                                            $badgeClass = 'badge-en_cours';
                                            $statutLabel = 'En cours';
                                            break;
                                        case 'effectue':
                                            $badgeClass = 'badge-effectue';
                                            $statutLabel = 'Effectué';
                                            break;
                                        case 'annule':
                                            $badgeClass = 'badge-annule';
                                            $statutLabel = 'Annulé';
                                            break;
                                        default:
                                            $badgeClass = 'bg-secondary';
                                            $statutLabel = $soin['statut'];
                                    }
                                    ?>
                                    <span class="badge-statut <?php echo $badgeClass; ?>"><?php echo $statutLabel; ?></span>
                                </td>
                                <td class="text-center action-buttons">
                                    <!-- Bouton VOIR - pour tout le monde -->
                                    <a href="index.php?controller=soin&action=show&id=<?php echo $soin['id']; ?>" 
                                       class="btn btn-sm btn-outline-info" title="Voir détails">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <!-- Bouton MODIFIER - pour Major et Admin -->
                                    <?php if($userRole === 'major' || $userRole === 'admin'): ?>
                                        <a href="index.php?controller=soin&action=edit&id=<?php echo $soin['id']; ?>" 
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <!-- Bouton EFFECTUER - pour Infirmier -->
                                    <?php if($userRole === 'infirmier' && $soin['statut'] !== 'effectue'): ?>
                                        <a href="index.php?controller=soin&action=updateStatut&id=<?php echo $soin['id']; ?>&statut=effectue" 
                                           class="btn btn-sm btn-outline-success" 
                                           onclick="return confirm('Confirmer que ce soin a été effectué ?')"
                                           title="Marquer effectué">
                                            <i class="bi bi-check-lg"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <!-- Bouton SUPPRIMER - pour Admin UNIQUEMENT -->
                                    <?php if($userRole === 'admin'): ?>
                                        <a href="index.php?controller=soin&action=delete&id=<?php echo $soin['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Supprimer ce soin ?')"
                                           title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Statistiques -->
                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        <i class="bi bi-info-circle"></i>
                        Total: <strong><?php echo $soins->rowCount(); ?></strong> soin(s) pour cette date
                    </div>
                </div>
                
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x" style="font-size: 4rem; color: #cbd5e1;"></i>
                    <h3 class="mt-3 fw-light">Aucun soin planifié</h3>
                    <p class="text-muted mb-3">Pour le <?php echo $dateFormatee; ?></p>
                    <?php if($userRole === 'major' || $userRole === 'admin'): ?>
                        <a href="index.php?controller=soin&action=create" class="btn btn-primary mt-3">
                            <i class="bi bi-plus-circle me-2"></i> Planifier un soin
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-fermeture des alertes après 5 secondes
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>