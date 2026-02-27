<?php
if(session_id() == '') {
    session_start();
}

// Vérifier que l'utilisateur a les droits
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
if($userRole != 'medecin' && $userRole != 'major' && $userRole != 'admin') {
    header('Location: index.php?controller=patient&action=index');
    exit();
}

// Récupérer l'ID du patient depuis l'URL
$patient_id = isset($_GET['patient_id']) ? intval($_GET['patient_id']) : 0;
if($patient_id <= 0) {
    header('Location: index.php?controller=patient&action=index');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvel antécédent médical</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        
        .main-card {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            color: white;
            padding: 20px 25px;
        }
        
        .card-header h4 {
            margin: 0;
            font-weight: 600;
        }
        
        .section-title {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 12px 20px;
            margin: 0 -25px 20px -25px;
            font-weight: 700;
            color: #0d6efd;
            border-left: 5px solid #0d6efd;
            font-size: 1.1rem;
        }
        
        .section-title i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .form-container {
            padding: 25px;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            background-color: #fff;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.25);
        }
        
        .required:after {
            content: " *";
            color: #dc3545;
            font-weight: bold;
        }
        
        .btn-save {
            background: linear-gradient(135deg, #198754, #157347);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25,135,84,0.3);
        }
        
        .btn-cancel {
            background: #6c757d;
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108,117,125,0.3);
        }
        
        .row {
            margin-bottom: 20px;
        }
        
        .alert-info {
            background: linear-gradient(135deg, #cff4fc, #b6effb);
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
        }
        
        /* Style pour les textarea */
        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }
        
        /* Scrollbar personnalisée */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 5px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="main-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4>
                        <i class="bi bi-heart-pulse-fill me-2"></i>
                        Nouvel antécédent médical
                    </h4>
                    <span class="badge bg-light text-dark">
                        Patient ID: <?php echo $patient_id; ?>
                    </span>
                </div>
            </div>

            <?php if(isset($_SESSION['errors']) && is_array($_SESSION['errors'])): ?>
                <div class="alert alert-danger m-3">
                    <ul class="mb-0">
                        <?php foreach($_SESSION['errors'] as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST" action="index.php?controller=antecedent&action=store">
                    <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                    
                    <!-- SECTION 1: DATE ET MOTIF -->
                    <div class="section-title">
                        <i class="bi bi-calendar-check"></i>
                        Date et motif de consultation
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label required">Date de consultation</label>
                            <input type="date" class="form-control" name="date_consultation" required value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Motif de consultation</label>
                            <input type="text" class="form-control" name="motif_consultation" required placeholder="Raison principale de la consultation">
                        </div>
                    </div>

                    <!-- SECTION 2: HISTORIQUE -->
                    <div class="section-title">
                        <i class="bi bi-clock-history"></i>
                        Historique de la maladie
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <textarea class="form-control" name="historique_maladie" rows="3" placeholder="Décrivez l'évolution de la maladie..."></textarea>
                        </div>
                    </div>

                    <!-- SECTION 3: ANTÉCÉDENTS -->
                    <div class="section-title">
                        <i class="bi bi-files"></i>
                        Antécédents
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Antécédents médicaux</label>
                            <textarea class="form-control" name="antecedents_medicaux" rows="2" placeholder="HTA, Diabète, etc."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Antécédents chirurgicaux</label>
                            <textarea class="form-control" name="antecedents_chirurgicaux" rows="2" placeholder="Appendicectomie, etc."></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Antécédents familiaux</label>
                            <textarea class="form-control" name="antecedents_familiaux" rows="2" placeholder="Maladies familiales..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Allergies</label>
                            <textarea class="form-control" name="allergies" rows="2" placeholder="Allergies connues..."></textarea>
                        </div>
                    </div>

                    <!-- SECTION 4: EXAMEN CLINIQUE DU JOUR -->
                    <div class="section-title">
                        <i class="bi bi-activity"></i>
                        Examen clinique du jour
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tension artérielle</label>
                            <input type="text" class="form-control" name="ta" placeholder="ex: 12/8">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fréquence cardiaque</label>
                            <input type="text" class="form-control" name="fc" placeholder="bpm">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Température</label>
                            <input type="text" class="form-control" name="temperature" placeholder="°C">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fréquence respiratoire</label>
                            <input type="text" class="form-control" name="fr" placeholder="cycles/min">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Saturation O2</label>
                            <input type="text" class="form-control" name="saturation" placeholder="%">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Poids</label>
                            <input type="text" class="form-control" name="poids" placeholder="kg">
                        </div>
                    </div>

                    <!-- SECTION 5: EXAMEN PAR APPAREIL -->
                    <div class="section-title">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                        Examen par appareil
                    </div>

                    <!-- Ligne 1: Appareil pleuro-pulmonaire et cardio-vasculaire -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="bi bi-lungs text-primary me-1"></i>
                                Appareil pleuro-pulmonaire
                            </label>
                            <textarea class="form-control" name="appareil_pleuro_pulmonaire" rows="2" placeholder="Auscultation, percussion, anomalies..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="bi bi-heart-pulse text-primary me-1"></i>
                                Appareil cardio-vasculaire
                            </label>
                            <textarea class="form-control" name="appareil_cardio_vasculaire" rows="2" placeholder="Bruits du cœur, souffles, pouls..."></textarea>
                        </div>
                    </div>

                    <!-- Ligne 2: Appareil digestif et locomoteur -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="bi bi-egg-fried text-primary me-1"></i>
                                Appareil digestif
                            </label>
                            <textarea class="form-control" name="appareil_digestif" rows="2" placeholder="Palpation, douleurs, transit..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="bi bi-person-arms-up text-primary me-1"></i>
                                Appareil locomoteur
                            </label>
                            <textarea class="form-control" name="appareil_locomoteur" rows="2" placeholder="Articulations, muscles, mobilité..."></textarea>
                        </div>
                    </div>

                    <!-- Ligne 3: Appareil uro-génital et Autre organe -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="bi bi-droplet text-primary me-1"></i>
                                Appareil uro-génital
                            </label>
                            <textarea class="form-control" name="appareil_uro_genital" rows="2" placeholder="Miction, douleur, troubles urinaires..."></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="bi bi-plus-circle text-primary me-1"></i>
                                Autre organe
                            </label>
                            <textarea class="form-control" name="autre_organe" rows="2" placeholder="Autres examens spécifiques..."></textarea>
                        </div>
                    </div>

                    <!-- SECTION 6: RÉSUMÉ SYNDROMIQUE -->
                    <div class="section-title mt-4">
                        <i class="bi bi-file-text"></i>
                        Résumé syndromique
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <textarea class="form-control" name="resume_syndromique" rows="2" placeholder="Synthèse des signes et symptômes..."></textarea>
                        </div>
                    </div>

                    <!-- SECTION 7: DIAGNOSTIC -->
                    <div class="section-title">
                        <i class="bi bi-clipboard2-pulse"></i>
                        Diagnostic de présomption
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <textarea class="form-control" name="diagnostic_presomption" rows="2" placeholder="Hypothèses diagnostiques..."></textarea>
                        </div>
                    </div>

                    <!-- SECTION 8: EXAMENS COMPLÉMENTAIRES -->
                    <div class="section-title">
                        <i class="bi bi-microscope"></i>
                        Examens complémentaires
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <textarea class="form-control" name="examen_complementaire" rows="3" placeholder="Biologie, imagerie, examens spécifiques..."></textarea>
                        </div>
                    </div>

                    <!-- SECTION 9: TRAITEMENT -->
                    <div class="section-title">
                        <i class="bi bi-capsule"></i>
                        Traitement symptomatique
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <textarea class="form-control" name="traitement_symptomatique" rows="3" placeholder="Traitement des symptômes..."></textarea>
                        </div>
                    </div>

                    <!-- ALERTE INFO -->
                    <div class="alert alert-info mt-4">
                        <i class="bi bi-info-circle me-2"></i>
                        Les champs marqués d'un <span class="text-danger">*</span> sont obligatoires.
                    </div>

                    <!-- BOUTONS -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php?controller=patient&action=show&id=<?php echo $patient_id; ?>" class="btn btn-cancel">
                            <i class="bi bi-arrow-left"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-save">
                            <i class="bi bi-check-circle"></i> Enregistrer l'antécédent
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>