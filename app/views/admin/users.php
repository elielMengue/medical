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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des utilisateurs</title>
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
        
        /* Style pour le bouton Voir */
        .btn-view {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-view:hover {
            background: linear-gradient(135deg, #0b5ed7, #0a58ca);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
            color: white;
        }
        
        .btn-view i {
            margin-right: 5px;
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
        
        /* Badge pour le total */
        .total-badge {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Inclusion de la navbar -->

    <div class="container">
        <!-- En-tête de la page -->
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-0 gradient-text">
                    <i class="bi bi-people-fill me-2 floating"></i>
                    Liste des utilisateurs
                </h1>
                <p class="mb-0 mt-2">
                    <i class="bi bi-calendar"></i> 
                    <?php echo date('d/m/Y'); ?>
                </p>
            </div>
            <div>
                <a href="index.php?controller=admin&action=create" class="btn btn-light me-2 hover-lift">
                    <i class="bi bi-person-plus me-1"></i> Nouvel utilisateur
                </a>
                <span class="total-badge">
                    <i class="bi bi-people"></i> Total: <strong><?php echo isset($users) && is_object($users) ? $users->rowCount() : 0; ?></strong>
                </span>
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

        <!-- Liste des utilisateurs -->
        <div class="table-container">
            <?php if(isset($users) && is_object($users) && $users->rowCount() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>NOM</th>
                                <th>PRÉNOM</th>
                                <th>EMAIL</th>
                                <th class="text-center">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i = 1;
                            while($user = $users->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><strong><?php echo htmlspecialchars(strtoupper($user['nom'])); ?></strong></td>
                                <td><?php echo htmlspecialchars(ucfirst(strtolower($user['prenom']))); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td class="text-center">
                                    <a href="index.php?controller=admin&action=profile&id=<?php echo $user['id']; ?>" 
                                       class="btn-view">
                                        <i class="bi bi-eye"></i> Voir
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-emoji-frown" style="font-size: 4rem; color: #cbd5e1;"></i>
                    <h3 class="mt-3 fw-light">Aucun utilisateur trouvé</h3>
                    <a href="index.php?controller=admin&action=create" class="btn btn-primary mt-3">
                        <i class="bi bi-person-plus me-2"></i> Ajouter un utilisateur
                    </a>
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