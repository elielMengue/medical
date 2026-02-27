<?php
if(session_id() == '') {
    session_start();
}

// Rôle de l'utilisateur connecté
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
if($userRole != 'medecin' && $userRole != 'major' && $userRole != 'admin') {
    $_SESSION['error'] = "Vous n'avez pas les droits pour ajouter des résultats";
    header('Location: index.php?controller=patient&action=show&id=' . $patient_id);
    exit();
}

if(!isset($antecedent) || !$antecedent) {
    header('Location: index.php?controller=patient&action=index');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats d'antécédent</title>
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
        
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
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
        
        .page-header h1 {
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            padding: 12px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #495057;
            box-shadow: 0 0 0 0.25rem rgba(33, 37, 41, 0.25);
        }
        
        .form-label {
            font-weight: 600;
            color: #212529;
            margin-bottom: 0.5rem;
        }
        
        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 12px 25px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #28a745, #218838);
            border: none;
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #218838, #1e7e34);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            border: none;
            color: white;
        }
        
        .btn-secondary:hover {
            background: linear-gradient(135deg, #5a6268, #545b62);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
        }
        
        .info-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #4d9eff;
        }
        
        .info-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
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
            .page-header h1 {
                font-size: 1.5rem;
            }
            
            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container hover-lift">
            <!-- En-tête -->
            <div class="page-header">
                <h1 class="mb-0 gradient-text">
                    <i class="bi bi-file-medical me-2 floating"></i>
                    Résultats et traitements
                </h1>
                <p class="mb-0 mt-2">
                    <i class="bi bi-calendar-check me-1"></i>
                    Consultation du <?php echo date('d/m/Y', strtotime($antecedent['date_consultation'])); ?> - 
                    <?php echo htmlspecialchars($antecedent['motif_consultation']); ?>
                </p>
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

            <?php if(isset($_SESSION['errors']) && is_array($_SESSION['errors'])): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach($_SESSION['errors'] as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>

            <!-- Informations de l'antécédent -->
            <div class="info-card">
                <div class="info-title">
                    <i class="bi bi-info-circle"></i> Antécédent du <?php echo date('d/m/Y', strtotime($antecedent['date_consultation'])); ?>
                </div>
                <p><strong>Motif :</strong> <?php echo htmlspecialchars($antecedent['motif_consultation']); ?></p>
                <?php if(!empty($antecedent['diagnostic_presomption'])): ?>
                    <p><strong>Diagnostic :</strong> <?php echo htmlspecialchars($antecedent['diagnostic_presomption']); ?></p>
                <?php endif; ?>
            </div>

            <form method="POST" action="index.php?controller=antecedent&action=saveResultat">
                <input type="hidden" name="id" value="<?php echo $antecedent['id']; ?>">
                <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">

                <!-- Champ Résultat -->
                <div class="mb-4">
                    <label for="resultat" class="form-label">Résultats d'examens</label>
                    <textarea class="form-control form-control-lg hover-lift" id="resultat" name="resultat" rows="5" 
                              placeholder="Saisissez les résultats des examens complémentaires..."><?php echo isset($_SESSION['old_input']['resultat']) ? htmlspecialchars($_SESSION['old_input']['resultat']) : (isset($antecedent['resultat']) ? htmlspecialchars($antecedent['resultat']) : ''); ?></textarea>
                    <div class="form-text">Résultats de biologie, imagerie, examens spécifiques...</div>
                </div>

                <!-- Champ Traitement spécifique -->
                <div class="mb-4">
                    <label for="traitement_specifique" class="form-label">Traitements spécifiques</label>
                    <textarea class="form-control form-control-lg hover-lift" id="traitement_specifique" name="traitement_specifique" rows="5" 
                              placeholder="Saisissez les traitements spécifiques prescrits..."><?php echo isset($_SESSION['old_input']['traitement_specifique']) ? htmlspecialchars($_SESSION['old_input']['traitement_specifique']) : (isset($antecedent['traitement_specifique']) ? htmlspecialchars($antecedent['traitement_specifique']) : ''); ?></textarea>
                    <div class="form-text">Traitements spécifiques, prescriptions particulières...</div>
                </div>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> 
                    Ces informations complètent l'antécédent médical avec les résultats d'examens et les traitements spécifiques.
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php?controller=patient&action=show&id=<?php echo $patient_id; ?>" class="btn btn-secondary btn-lg hover-lift">
                        <i class="bi bi-arrow-left"></i> Retour au patient
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg hover-lift">
                        <i class="bi bi-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php 
    if(isset($_SESSION['old_input'])) {
        unset($_SESSION['old_input']);
    }
    ?>

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