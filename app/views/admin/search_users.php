<?php
// Démarrer la session pour les messages
if(session_id() == '') {
    session_start();
}

// Vérifier que l'utilisateur est admin
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php?controller=patient&action=index');
    exit();
}

// Vérifier si des critères ont été soumis
$hasCriteria = isset($_GET['nom']) || isset($_GET['prenom']) || isset($_GET['email']) || isset($_GET['role']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche d'utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body { 
            background-image: url('/projet_medical/app/public/assets/images/background-user.jpg');
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
        
        /* Style pour le champ nom en majuscules */
        .nom-majuscule {
            text-transform: uppercase;
        }
        
        .nom-majuscule::placeholder {
            text-transform: none;
            color: #6c757d;
        }
        
        /* Style pour le champ prénom */
        .prenom-premiere-maj {
            text-transform: capitalize;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #495057;
            box-shadow: 0 0 0 0.25rem rgba(33, 37, 41, 0.25);
        }
        
        .btn-search {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            float: right;
        }
        
        .btn-search:hover {
            background: linear-gradient(135deg, #0b5ed7, #0a58ca);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
        }
        
        /* TABLEAU DES RÉSULTATS */
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
        
        .badge-role {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .badge-admin {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }
        
        .badge-medecin {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: white;
        }
        
        .badge-major {
            background: linear-gradient(135deg, #fd7e14 0%, #dc6b12 100%);
            color: white;
        }
        
        .badge-infirmier {
            background: linear-gradient(135deg, #198754 0%, #157347 100%);
            color: white;
        }
        
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
        
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <!-- Inclusion de la navbar -->
   

    <div class="container">
        <!-- En-tête de la page -->
        <div class="page-header">
            <div>
                <h1 class="mb-0 gradient-text">
                    <i class="bi bi-search me-2 floating"></i>
                    Recherche d'utilisateurs
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
                <input type="hidden" name="controller" value="admin">
                <input type="hidden" name="action" value="search">
                
                <div class="col-md-3">
                    <label class="form-label">Nom</label>
                    <input type="text" class="form-control nom-majuscule" name="nom" 
                           value="<?php echo isset($_GET['nom']) ? htmlspecialchars($_GET['nom']) : ''; ?>"
                           placeholder="Ex: DUPONT"
                           oninput="this.value = this.value.toUpperCase()">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Prénom</label>
                    <input type="text" class="form-control" name="prenom" 
                           value="<?php echo isset($_GET['prenom']) ? htmlspecialchars($_GET['prenom']) : ''; ?>"
                           placeholder="Ex: Jean"
                           oninput="this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase()">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" 
                           value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>"
                           placeholder="exemple@medical.com">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Rôle</label>
                    <select class="form-select" name="role">
                        <option value="">Tous les rôles</option>
                        <option value="admin" <?php echo (isset($_GET['role']) && $_GET['role'] == 'admin') ? 'selected' : ''; ?>>Administrateur</option>
                        <option value="medecin" <?php echo (isset($_GET['role']) && $_GET['role'] == 'medecin') ? 'selected' : ''; ?>>Médecin</option>
                        <option value="major" <?php echo (isset($_GET['role']) && $_GET['role'] == 'major') ? 'selected' : ''; ?>>Major</option>
                        <option value="infirmier" <?php echo (isset($_GET['role']) && $_GET['role'] == 'infirmier') ? 'selected' : ''; ?>>Infirmier</option>
                    </select>
                </div>
                
                <div class="col-12 mt-4 clearfix">
                    <button type="submit" class="btn-search">
                        <i class="bi bi-search"></i> Rechercher
                    </button>
                </div>
            </form>
        </div>

        <!-- Résultats de la recherche -->
        <div class="table-container">
            <?php if($hasCriteria): ?>
                <?php if(isset($users) && is_object($users) && $users->rowCount() > 0): ?>
                    <h5 class="search-title mb-3">
                        <i class="bi bi-list-ul"></i>
                        Résultats (<?php echo $users->rowCount(); ?> utilisateur(s) trouvé(s))
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>MATRICULE</th>
                                    <th>NOM</th>
                                    <th>PRÉNOM</th>
                                    <th>EMAIL</th>
                                    <th>RÔLE</th>
                                    <th>TÉLÉPHONE</th>
                                    <th class="text-center">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                while($user = $users->fetch(PDO::FETCH_ASSOC)): 
                                    $badgeClass = '';
                                    switch($user['role']) {
                                        case 'admin': $badgeClass = 'badge-admin'; break;
                                        case 'medecin': $badgeClass = 'badge-medecin'; break;
                                        case 'major': $badgeClass = 'badge-major'; break;
                                        case 'infirmier': $badgeClass = 'badge-infirmier'; break;
                                    }
                                    
                                    $roleLabels = array(
                                        'admin' => 'Administrateur',
                                        'medecin' => 'Médecin',
                                        'major' => 'Major',
                                        'infirmier' => 'Infirmier'
                                    );
                                    $roleLabel = isset($roleLabels[$user['role']]) ? $roleLabels[$user['role']] : ucfirst($user['role']);
                                ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><strong><?php echo htmlspecialchars($user['matricule']); ?></strong></td>
                                    <td><?php echo htmlspecialchars(strtoupper($user['nom'])); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst(strtolower($user['prenom']))); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><span class="badge-role <?php echo $badgeClass; ?>"><?php echo $roleLabel; ?></span></td>
                                    <td><?php echo !empty($user['telephone']) ? htmlspecialchars($user['telephone']) : '-'; ?></td>
                                    <td class="text-center action-buttons">
                                        <a href="index.php?controller=admin&action=editUser&id=<?php echo $user['id']; ?>" 
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="index.php?controller=admin&action=deleteUser&id=<?php echo $user['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Supprimer cet utilisateur ?')"
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
                    
                <?php elseif(isset($users) && is_object($users) && $users->rowCount() == 0): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-emoji-frown" style="font-size: 4rem; color: #cbd5e1;"></i>
                        <h3 class="mt-3 fw-light">Aucun résultat</h3>
                        <p class="text-muted">Aucun utilisateur ne correspond à vos critères.</p>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-search" style="font-size: 4rem; color: #cbd5e1;"></i>
                    <h3 class="mt-3 fw-light">Recherchez des utilisateurs</h3>
                    <p class="text-muted">Utilisez le formulaire ci-dessus.</p>
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