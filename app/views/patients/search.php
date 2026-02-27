<?php
// Démarrer la session pour les messages
if(session_id() == '') {
    session_start();
}

// Rôle de l'utilisateur connecté
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';

// Vérifier si des critères ont été soumis
$hasCriteria = isset($_GET['nom']) || isset($_GET['prenom']) || isset($_GET['date_naissance']);

// Vérifier si $patients est un objet valide
$hasResults = isset($patients) && is_object($patients);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche de patients</title>
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
        
        /* CARTE DE RECHERCHE */
        .search-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        
        .search-title {
            color: #212529;
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #4d9eff;
            box-shadow: 0 0 0 0.25rem rgba(77, 158, 255, 0.25);
        }
        
        .btn-search {
            background: linear-gradient(135deg, #6eb5ff, #4d9eff);
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            float: right;
        }
        
        .btn-search:hover {
            background: linear-gradient(135deg, #5da6ff, #3c8eff);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(77, 158, 255, 0.4);
        }
        
        /* RÉSULTATS DE RECHERCHE - STYLE TABLEAU */
        .results-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th {
            background: linear-gradient(135deg, #6eb5ff, #4d9eff);
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 10px;
            text-align: left;
        }
        
        .table td {
            padding: 12px 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9ff;
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
        
        .action-buttons {
            display: flex;
            gap: 5px;
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
        
        .no-results {
            text-align: center;
            padding: 50px 20px;
        }
        
        .no-results i {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 20px;
        }
        
        .no-results h3 {
            color: #6c757d;
            font-weight: 300;
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
        
        .gradient-text {
            background: linear-gradient(135deg, #ffffff 0%, #e9ecef 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête de la page -->
        <div class="page-header">
            <div>
                <h1 class="mb-0 gradient-text">
                    <i class="bi bi-search me-2 floating"></i>
                    Recherche de patients
                </h1>
                <p class="mb-0 mt-2">
                    <i class="bi bi-calendar"></i> 
                    <?php echo date('d/m/Y'); ?>
                </p>
            </div>
        </div>

        <!-- Formulaire de recherche -->
        <div class="search-card">
            <h5 class="search-title">
                <i class="bi bi-funnel"></i>
                Critères de recherche
            </h5>
            
            <form action="index.php" method="GET" class="row g-3">
                <input type="hidden" name="controller" value="patient">
                <input type="hidden" name="action" value="search">
                
                <div class="col-md-4">
                    <label class="form-label">Nom</label>
                    <input type="text" class="form-control" name="nom" 
                           value="<?php echo isset($_GET['nom']) ? htmlspecialchars($_GET['nom']) : ''; ?>"
                           placeholder="Ex: DUPONT">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Prénom</label>
                    <input type="text" class="form-control" name="prenom" 
                           value="<?php echo isset($_GET['prenom']) ? htmlspecialchars($_GET['prenom']) : ''; ?>"
                           placeholder="Ex: Jean">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Date de naissance</label>
                    <input type="date" class="form-control" name="date_naissance" 
                           value="<?php echo isset($_GET['date_naissance']) ? htmlspecialchars($_GET['date_naissance']) : ''; ?>">
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn-search">
                        <i class="bi bi-search"></i> Rechercher
                    </button>
                </div>
            </form>
        </div>

        <!-- Résultats de la recherche - FORMAT TABLEAU -->
        <div class="results-container">
            <?php if($hasCriteria): ?>
                <?php if($hasResults && $patients->rowCount() > 0): ?>
                    <h5 class="search-title mb-3">
                        <i class="bi bi-list-ul"></i>
                        Résultats (<?php echo $patients->rowCount(); ?> patient(s) trouvé(s))
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>NOM</th>
                                    <th>PRÉNOM</th>
                                    <th>SEXE</th>
                                    <th>NÉ LE</th>
                                    <th>ÂGE</th>
                                    <th>TÉLÉPHONE</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                while($row = $patients->fetch(PDO::FETCH_ASSOC)): 
                                    // Calcul de l'âge
                                    $age = 'N/A';
                                    if(!empty($row['date_naissance'])) {
                                        try {
                                            $birthDate = new DateTime($row['date_naissance']);
                                            $today = new DateTime();
                                            $age = $birthDate->diff($today)->y;
                                        } catch(Exception $e) {
                                            $age = 'Date invalide';
                                        }
                                    }
                                    
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
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><strong><?php echo htmlspecialchars(strtoupper($row['nom'])); ?></strong></td>
                                    <td><?php echo htmlspecialchars(ucfirst(strtolower($row['prenom']))); ?></td>
                                    <td>
                                        <?php 
                                        if(!empty($row['sexe'])) {
                                            echo ($row['sexe'] == 'M') ? 'M' : 'F';
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($row['date_naissance'])); ?></td>
                                    <td><span class="badge-age"><?php echo $age; ?> ans</span></td>
                                    <td>
                                        <?php if(!empty($row['telephone'])): ?>
                                            <?php echo $telFormate; ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="action-buttons">
                                        <!-- Bouton VOIR - dossier médical -->
                                        <a href="index.php?controller=patient&action=show&id=<?php echo $row['id']; ?>" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="Voir dossier médical">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        <!-- Bouton MODIFIER - pour Admin, Médecin et Major -->
                                        <?php if($userRole === 'admin' || $userRole === 'medecin' || $userRole === 'major'): ?>
                                        <a href="index.php?controller=patient&action=edit&id=<?php echo $row['id']; ?>" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    
                <?php elseif($hasResults && $patients->rowCount() == 0): ?>
                    <div class="no-results">
                        <i class="bi bi-emoji-frown"></i>
                        <h3>Aucun résultat</h3>
                        <p class="text-muted">Aucun patient ne correspond à vos critères de recherche.</p>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="no-results">
                    <i class="bi bi-search"></i>
                    <h3>Recherchez des patients</h3>
                    <p class="text-muted">Utilisez le formulaire ci-dessus pour rechercher des patients.</p>
                </div>
            <?php endif; ?>
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