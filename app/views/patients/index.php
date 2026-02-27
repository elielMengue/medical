<?php
// Démarrer la session pour les messages
if(session_id() == '') {
    session_start();
}

// Rôle de l'utilisateur connecté
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des patients</title>
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
        
        /* PAGE HEADER - BLEU CLAIR */
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
        
        /* TABLE CONTAINER */
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        /* STYLE POUR LES LIGNES DE PATIENTS */
        .patient-row {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.3s ease;
        }
        
        .patient-row:hover {
            background-color: #f8f9ff;
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 8px 25px rgba(77, 158, 255, 0.15);
            border-radius: 8px;
        }
        
        .patient-row:last-child {
            border-bottom: none;
        }
        
        .patient-number {
            width: 50px;
            font-weight: 700;
            color: #6c757d;
            text-align: center;
        }
        
        .patient-info {
            flex: 1;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .patient-info-item {
            flex: 1;
            min-width: 120px;
        }
        
        .patient-info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #6c757d;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        
        .patient-info-value {
            font-weight: 600;
            color: #212529;
        }
        
        .patient-nom {
            font-weight: 700;
            color: #2c3e50;
            font-size: 1.1rem;
        }
        
        .badge-age {
            background: linear-gradient(135deg, #6eb5ff, #4d9eff);
            color: white;
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            display: inline-block;
        }
        
        .adresse-cell {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* BOUTON NOUVEAU PATIENT */
        .btn-blue-light {
            background: linear-gradient(135deg, #6eb5ff, #4d9eff);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-blue-light:hover {
            background: linear-gradient(135deg, #5da6ff, #3c8eff);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(77, 158, 255, 0.3);
        }
        
        /* BOUTONS D'ACTION */
        .action-buttons {
            display: flex;
            gap: 5px;
            margin-left: 15px;
        }
        
        .action-buttons .btn {
            margin: 0;
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
            
            .patient-row {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .patient-number {
                width: auto;
                margin-bottom: 10px;
            }
            
            .action-buttons {
                margin-left: 0;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête de la page - BLEU CLAIR -->
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-0 gradient-text">
                    <i class="bi bi-people-fill me-2 floating"></i>
                    Gestion des patients
                </h1>
                <p class="mb-0 mt-2">
                    <i class="bi bi-calendar"></i> 
                    <?php echo date('d/m/Y'); ?>
                </p>
            </div>
            <div>
                <!-- Bouton Nouveau patient - pour Admin, Médecin et Major -->
                <?php if($userRole === 'admin' || $userRole === 'medecin' || $userRole === 'major'): ?>
                    <a href="index.php?controller=patient&action=create" class="btn-blue-light">
                        <i class="bi bi-plus-circle-fill me-2"></i>
                        Nouveau patient
                    </a>
                <?php endif; ?>
            </div>
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

        <!-- Indicateur de recherche -->
        <?php if(isset($_GET['nom']) || isset($_GET['prenom']) || isset($_GET['date_naissance'])): ?>
        <div class="alert" style="background: linear-gradient(135deg, #6eb5ff20, #4d9eff20); border-left: 4px solid #4d9eff; color: #0047b3;">
            <i class="bi bi-search me-2"></i>
            <strong>Résultats de recherche :</strong>
            <?php 
            $criteria = array();
            if(!empty($_GET['nom'])) $criteria[] = "Nom: " . htmlspecialchars($_GET['nom']);
            if(!empty($_GET['prenom'])) $criteria[] = "Prénom: " . htmlspecialchars($_GET['prenom']);
            if(!empty($_GET['date_naissance'])) $criteria[] = "Date naiss: " . date('d/m/Y', strtotime($_GET['date_naissance']));
            echo implode(' | ', $criteria);
            ?>
            <a href="index.php?controller=patient&action=index" class="btn btn-sm btn-outline-secondary float-end">
                <i class="bi bi-x-circle"></i> Effacer
            </a>
        </div>
        <?php endif; ?>

        <!-- Liste des patients -->
        <div class="table-container">
            <?php if(isset($patients) && $patients->rowCount() > 0): ?>
                <div class="patients-list">
                    <?php 
                    
                   
$i = 1;
while($row = $patients->fetch(PDO::FETCH_ASSOC)): 
    // Plusieurs méthodes de calcul de l'âge pour trouver le problème
    $age = 'N/A';
    
    if(!empty($row['date_naissance'])) {
        // Méthode 1: DateTime (la plus fiable)
        try {
            $birthDate = new DateTime($row['date_naissance']);
            $today = new DateTime();
            $age = $birthDate->diff($today)->y;
        } catch(Exception $e) {
            // Méthode 2: Calcul manuel si DateTime échoue
            $birth = strtotime($row['date_naissance']);
            if($birth !== false) {
                $today = time();
                $age = floor(($today - $birth) / (365.25 * 24 * 60 * 60));
            } else {
                $age = 'Date invalide';
            }
        }
    }
?>
                        
                        
                        // Formatage du téléphone
                        $tel = !empty($row['telephone']) ? $row['telephone'] : '';
                        $telFormate = '';
                        if(strlen($tel) == 8) {
                            $telFormate = substr($tel, 0, 2) . ' ' . 
                                         substr($tel, 2, 2) . ' ' . 
                                         substr($tel, 4, 2) . ' ' . 
                                         substr($tel, 6, 2);
                        } else {
                            $telFormate = $tel;
                        }
                    ?>
                    <div class="patient-row">
                        <div class="patient-number">#<?php echo $i++; ?></div>
                        
                        <div class="patient-info">
                            <div class="patient-info-item">
                                <div class="patient-info-label">Nom</div>
                                <div class="patient-info-value patient-nom"><?php echo htmlspecialchars(strtoupper($row['nom'])); ?></div>
                            </div>
                            
                            <div class="patient-info-item">
                                <div class="patient-info-label">Prénom</div>
                                <div class="patient-info-value"><?php echo htmlspecialchars(ucfirst(strtolower($row['prenom']))); ?></div>
                            </div>
                            
                            <div class="patient-info-item">
                                <div class="patient-info-label">Sexe</div>
                                <div class="patient-info-value">
                                    <?php 
                                    if(!empty($row['sexe'])) {
                                        echo ($row['sexe'] == 'M') ? 'Masculin' : 'Féminin';
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </div>
                            </div>
                            
                            <div class="patient-info-item">
                                <div class="patient-info-label">Né le</div>
                                <div class="patient-info-value"><?php echo date('d/m/Y', strtotime($row['date_naissance'])); ?></div>
                            </div>
                            
                            <div class="patient-info-item">
                                <div class="patient-info-label">Âge</div>
                                <div class="patient-info-value">
                                    <span class="badge-age"><?php echo $age; ?> ans</span>
                                </div>
                            </div>
                            
                            <div class="patient-info-item">
                                <div class="patient-info-label">Téléphone</div>
                                <div class="patient-info-value">
                                    <?php if(!empty($row['telephone'])): ?>
                                        <a href="tel:<?php echo $row['telephone']; ?>" class="text-decoration-none">
                                            <i class="bi bi-telephone-fill me-1 text-primary"></i>
                                            <?php echo $telFormate; ?>
                                        </a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="patient-info-item">
                                <div class="patient-info-label">Adresse</div>
                                <div class="patient-info-value adresse-cell" title="<?php echo isset($row['adresse']) ? htmlspecialchars($row['adresse']) : ''; ?>">
                                    <?php if(!empty($row['adresse'])): ?>
                                        <i class="bi bi-geo-alt-fill me-1 text-primary"></i>
                                        <?php 
                                        $adresse = $row['adresse'];
                                        if(strlen($adresse) > 30) {
                                            echo substr(htmlspecialchars($adresse), 0, 30) . '...';
                                        } else {
                                            echo htmlspecialchars($adresse);
                                        }
                                        ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            <!-- Bouton VOIR - pour tout le monde (sauf infirmier) -->
                            <?php if($userRole !== 'infirmier'): ?>
                            <a href="index.php?controller=patient&action=show&id=<?php echo $row['id']; ?>" 
                               class="btn btn-sm btn-outline-info" title="Voir détails">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php endif; ?>
                            
                            <!-- Bouton MODIFIER - pour Admin, Médecin et Major -->
                            <?php if($userRole === 'admin' || $userRole === 'medecin' || $userRole === 'major'): ?>
                            <a href="index.php?controller=patient&action=edit&id=<?php echo $row['id']; ?>" 
                               class="btn btn-sm btn-outline-warning" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php endif; ?>
                            
                            <!-- Bouton DOSSIER MÉDICAL - pour Admin, Médecin et Major -->
                            <?php if($userRole === 'admin' || $userRole === 'medecin' || $userRole === 'major'): ?>
                            <a href="index.php?controller=export&action=exportPatientMedicalFile&id=<?php echo $row['id']; ?>" 
                               class="btn btn-sm btn-outline-primary" title="Exporter dossier médical">
                                <i class="bi bi-file-medical"></i>
                            </a>
                            <?php endif; ?>
                            
                            <!-- Bouton SUPPRIMER - pour ADMIN UNIQUEMENT -->
                            <?php if($userRole === 'admin'): ?>
                            <a href="index.php?controller=patient&action=delete&id=<?php echo $row['id']; ?>" 
                               class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce patient ?\nCette action est irréversible.')"
                               title="Supprimer">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                
                <!-- Statistiques -->
                <div class="mt-4 d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        <i class="bi bi-info-circle"></i>
                        Total: <strong><?php echo $patients->rowCount(); ?></strong> patient(s)
                    </div>
                </div>
                
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-emoji-frown" style="font-size: 4rem; color: #cbd5e1;"></i>
                    <h3 class="mt-3 fw-light">Aucun patient trouvé</h3>
                    <?php if($userRole === 'admin' || $userRole === 'medecin' || $userRole === 'major'): ?>
                        <a href="index.php?controller=patient&action=create" class="btn btn-primary mt-3">
                            <i class="bi bi-plus-circle me-2"></i> Ajouter un patient
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