<?php
if(session_id() == '') {
    session_start();
}

// Vérifier que l'utilisateur a le droit de créer (Major ou Admin)
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
if($userRole != 'major' && $userRole != 'admin') {
    $_SESSION['error'] = "Vous n'avez pas les droits pour planifier des soins";
    header('Location: index.php?controller=soin&action=index');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planifier un soin</title>
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
            max-width: 700px;
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
        
        .form-label {
            font-weight: 600;
            color: #212529;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 12px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #495057;
            box-shadow: 0 0 0 0.25rem rgba(33, 37, 41, 0.25);
        }
        
        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 12px 25px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #212529 0%, #343a40 100%);
            border: none;
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #343a40 0%, #495057 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(33, 37, 41, 0.3);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            border: none;
            color: white;
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
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
        
        .alert {
            border-radius: 10px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
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
    <!-- Inclusion de la navbar -->
    

    <div class="container">
        <div class="form-container hover-lift">
            <div class="page-header">
                <h1 class="mb-0 gradient-text">
                    <i class="bi bi-calendar-plus me-2 floating"></i> 
                    Planifier un soin
                </h1>
                <p class="mb-0 mt-2">
                    <i class="bi bi-pencil-square me-1"></i>
                    Remplissez les informations ci-dessous
                </p>
            </div>
            
            <?php if(isset($_SESSION['errors']) && is_array($_SESSION['errors'])): ?>
                <div class="alert alert-danger">
                    <h5><i class="bi bi-exclamation-triangle"></i> Erreurs :</h5>
                    <ul class="mb-0">
                        <?php foreach($_SESSION['errors'] as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>

            <form method="POST" action="index.php?controller=soin&action=store">
                <!-- Patient -->
                <div class="mb-3">
                    <label for="patient_id" class="form-label">Patient <span class="text-danger">*</span></label>
                    <select class="form-select form-select-lg" id="patient_id" name="patient_id" required>
                        <option value="">Sélectionner un patient</option>
                        <?php if(isset($patients) && is_array($patients)): ?>
                            <?php foreach($patients as $patient): ?>
                                <option value="<?php echo $patient['id']; ?>" 
                                    <?php echo (isset($_SESSION['old_input']['patient_id']) && $_SESSION['old_input']['patient_id'] == $patient['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($patient['nom'] . ' ' . $patient['prenom']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Infirmier -->
                <div class="mb-3">
                    <label for="infirmier_id" class="form-label">Infirmier(ère) <span class="text-danger">*</span></label>
                    <select class="form-select form-select-lg" id="infirmier_id" name="infirmier_id" required>
                        <option value="">Sélectionner un infirmier</option>
                        <?php if(isset($infirmiers) && is_array($infirmiers)): ?>
                            <?php foreach($infirmiers as $infirmier): ?>
                                <option value="<?php echo $infirmier['id']; ?>"
                                    <?php echo (isset($_SESSION['old_input']['infirmier_id']) && $_SESSION['old_input']['infirmier_id'] == $infirmier['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($infirmier['prenom'] . ' ' . $infirmier['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Type de soin -->
                <div class="mb-3">
                    <label for="type_soin" class="form-label">Type de soin <span class="text-danger">*</span></label>
                    <select class="form-select form-select-lg" id="type_soin" name="type_soin" required>
                        <option value="">Choisir un type...</option>
                        <option value="Prise de sang">Prise de sang</option>
                        <option value="Pansement">Pansement</option>
                        <option value="Injection">Injection</option>
                        <option value="Perfusion">Perfusion</option>
                        <option value="Constantes">Prise des constantes</option>
                        <option value="Toilette">Toilette</option>
                        <option value="Levé">Aide au levé</option>
                        <option value="Repas">Aide à la prise de repas</option>
                        <option value="Médicament">Administration médicament</option>
                        <option value="Rééducation">Rééducation</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="description" class="form-label">Description / Instructions</label>
                    <textarea class="form-control" id="description" name="description" rows="3" 
                              placeholder="Indications particulières..."><?php echo isset($_SESSION['old_input']['description']) ? htmlspecialchars($_SESSION['old_input']['description']) : ''; ?></textarea>
                </div>

                <!-- Date et Heure -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="date_soin" class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="date_soin" name="date_soin" 
                               value="<?php echo isset($_SESSION['old_input']['date_soin']) ? $_SESSION['old_input']['date_soin'] : date('Y-m-d'); ?>" 
                               required>
                    </div>
                    <div class="col-md-6">
                        <label for="heure_soin" class="form-label">Heure <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" id="heure_soin" name="heure_soin" 
                               value="<?php echo isset($_SESSION['old_input']['heure_soin']) ? $_SESSION['old_input']['heure_soin'] : date('H:i'); ?>" 
                               required>
                    </div>
                </div>

                <!-- Numéro de lit -->
                <div class="mb-3">
                    <label for="numero_lit" class="form-label">Numéro de lit</label>
                    <input type="text" class="form-control" id="numero_lit" name="numero_lit" 
                           placeholder="Ex: A12, B7, ..."
                           value="<?php echo isset($_SESSION['old_input']['numero_lit']) ? htmlspecialchars($_SESSION['old_input']['numero_lit']) : ''; ?>">
                    <div class="form-text">Optionnel</div>
                </div>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> 
                    Les champs marqués d'un <span class="text-danger">*</span> sont obligatoires.
                </div>

                <!-- Boutons -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php?controller=soin&action=index" class="btn btn-secondary btn-lg hover-lift">
                        <i class="bi bi-arrow-left"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg hover-lift">
                        <i class="bi bi-save"></i> Planifier
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