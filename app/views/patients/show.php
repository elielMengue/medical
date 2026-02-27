<?php
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
    <title>Détails du patient</title>
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
        
        /* TABLE CONTAINER */
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        
        /* TABLE HEADER - BLEU CLAIR */
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
        
        /* TABLE DES INFORMATIONS AVEC NOM INTÉGRÉ */
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-table td {
            padding: 15px 12px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .info-table tr:last-child td {
            border-bottom: none;
        }
        
        .info-table tbody tr:hover {
            background-color: #f8f9ff;
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 8px 25px rgba(77, 158, 255, 0.15);
            border-radius: 8px;
        }
        
        /* STYLE POUR LES INFORMATIONS EN GRAS */
        .info-label {
            font-weight: 700;
            color: #212529;
            font-size: 1rem;
            margin-right: 5px;
        }
        
        .info-value {
            font-weight: 600;
            color: #495057;
        }
        
        .age-badge {
            background: linear-gradient(135deg, #6eb5ff, #4d9eff);
            color: white;
            font-weight: 500;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            display: inline-block;
        }
        
        .telephone-link {
            color: #212529;
            text-decoration: none;
            font-weight: 600;
        }
        
        .telephone-link:hover {
            color: #0d6efd;
            text-decoration: underline;
        }
        
        /* CARTE D'ANTÉCÉDENT */
        .antecedent-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #4d9eff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .antecedent-header {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .antecedent-date {
            font-weight: 700;
            color: #4d9eff;
            font-size: 1rem;
        }
        
        .antecedent-motif {
            font-weight: 600;
            color: #495057;
        }
        
        .antecedent-section {
            margin-top: 15px;
            padding: 10px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .antecedent-section-title {
            font-weight: 700;
            color: #495057;
            font-size: 0.9rem;
            text-transform: uppercase;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #4d9eff;
        }
        
        .antecedent-section-title i {
            margin-right: 5px;
            color: #4d9eff;
        }
        
        .antecedent-field {
            margin-bottom: 8px;
        }
        
        .antecedent-field-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.8rem;
            text-transform: uppercase;
        }
        
        .antecedent-field-value {
            color: #212529;
            font-size: 0.95rem;
            padding-left: 10px;
        }
        
        .badge-clinique {
            background: #e7f1ff;
            color: #4d9eff;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-right: 5px;
        }
        
        /* BOUTONS ICÔNES SANS TEXTE */
        .btn-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            border: none;
            color: white;
        }
        
        .btn-icon i {
            font-size: 1.2rem;
        }
        
        .btn-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn-icon-modifier {
            background: linear-gradient(135deg, #6eb5ff, #4d9eff);
        }
        
        .btn-icon-modifier:hover {
            background: linear-gradient(135deg, #5da6ff, #3c8eff);
        }
        
        .btn-icon-supprimer {
            background: linear-gradient(135deg, #dc3545, #bb2d3b);
        }
        
        .btn-icon-supprimer:hover {
            background: linear-gradient(135deg, #bb2d3b, #9c2530);
        }
        
        .btn-icon-ajouter {
            background: linear-gradient(135deg, #28a745, #218838);
        }
        
        .btn-icon-ajouter:hover {
            background: linear-gradient(135deg, #218838, #1e7e34);
        }
        
        /* Style pour le bouton Résultats */
        .btn-resultat {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 5px 12px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-resultat:hover {
            background: linear-gradient(135deg, #138496, #117a8b);
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(23, 162, 184, 0.3);
            color: white;
        }
        
        .gap-2 {
            gap: 10px;
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
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
        
        /* Badge pour indiquer des résultats existants */
        .badge-resultat {
            background: #28a745;
            color: white;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: 5px;
        }
        
        @media (max-width: 768px) {
            .info-table td {
                display: block;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
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

        <!-- Informations personnelles - AVEC NOM INTÉGRÉ DANS LA TABLE -->
        <div class="table-container">
            <table class="info-table">
                <!-- Première ligne : Nom du patient fusionné sur 4 colonnes - BLEU CLAIR -->
                <tr>
                    <td colspan="4" style="background: linear-gradient(135deg, #6eb5ff, #4d9eff); padding: 20px; border-radius: 10px 10px 0 0;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="mb-0">
                                    <i class="bi bi-person-circle me-2" style="color: white; font-size: 2rem;"></i>
                                    <span class="gradient-text" style="font-size: 1.8rem; margin-right: 10px;">Dossier médical de</span>
                                    <?php 
                                    // Ajouter la civilité selon le sexe
                                    $civilite = '';
                                    if(isset($patient['sexe'])) {
                                        $civilite = ($patient['sexe'] == 'M') ? 'M. ' : 'Mme ';
                                    }
                                    echo '<span style="color: white; font-size: 2rem; opacity: 0.9;">' . htmlspecialchars($civilite . $patient['prenom'] . ' ' . $patient['nom']) . '</span>'; 
                                    ?>
                                </h2>
                                <p class="mb-0 mt-2" style="color: rgba(255,255,255,0.8);">
                                    <i class="bi bi-calendar" style="color: rgba(255,255,255,0.8);"></i> 
                                    <span style="color: rgba(255,255,255,0.8);">Inscrit le <?php echo isset($patient['created_at']) ? date('d/m/Y', strtotime($patient['created_at'])) : date('d/m/Y'); ?></span>
                                </p>
                            </div>
                            <div></div>
                        </div>
                    </td>
                </tr>
                <!-- Deuxième ligne : Informations en gras (sans en-têtes) -->
                <tr>
                    <td colspan="4" style="padding: 20px;">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <span class="info-label">Né le :</span>
                                <span class="info-value"><?php echo date('d/m/Y', strtotime($patient['date_naissance'])); ?></span>
                            </div>
                            <div class="col-md-2 mb-2">
                                <span class="info-label">Âge :</span>
                                <span class="info-value">
                                    <?php 
                                        $birthDate = new DateTime($patient['date_naissance']);
                                        $today = new DateTime();
                                        $age = $birthDate->diff($today)->y;
                                        echo $age . ' ans';
                                    ?>
                                </span>
                            </div>
                            <div class="col-md-3 mb-2">
                                <span class="info-label">Téléphone :</span>
                                <span class="info-value">
                                    <?php if(!empty($patient['telephone'])): ?>
                                        <a href="tel:<?php echo $patient['telephone']; ?>" class="telephone-link">
                                            <?php 
                                            $tel = $patient['telephone'];
                                            if(strlen($tel) == 8) {
                                                echo substr($tel, 0, 2) . ' ' . 
                                                     substr($tel, 2, 2) . ' ' . 
                                                     substr($tel, 4, 2) . ' ' . 
                                                     substr($tel, 6, 2);
                                            } else {
                                                echo $tel;
                                            }
                                            ?>
                                        </a>
                                    <?php else: ?>
                                        Non renseigné
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="col-md-4 mb-2">
                                <span class="info-label">Adresse :</span>
                                <span class="info-value">
                                    <?php if(!empty($patient['adresse'])): ?>
                                        <?php 
                                        $adresse = $patient['adresse'];
                                        if(strlen($adresse) > 40) {
                                            echo substr(htmlspecialchars($adresse), 0, 40) . '...';
                                        } else {
                                            echo htmlspecialchars($adresse);
                                        }
                                        ?>
                                    <?php else: ?>
                                        Non renseignée
                                    <?php endif; ?>
                                </span>
                            </div>
                            <!-- GROUPE SANGUIN - AJOUTÉ APRÈS L'ADRESSE -->
                            <div class="col-md-4 mb-2">
                                <span class="info-label">Groupe sanguin :</span>
                                <span class="info-value">
                                    <?php if(!empty($patient['groupe_sanguin'])): ?>
                                        <span class="badge bg-primary" style="font-size: 0.9rem; padding: 5px 10px;">
                                            <?php echo htmlspecialchars($patient['groupe_sanguin']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">Non renseigné</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
            
            <!-- Boutons icônes Modifier et Supprimer (sans texte) -->
            <div class="d-flex justify-content-end mt-3 gap-2">
                <?php if($userRole === 'medecin' || $userRole === 'major' || $userRole === 'admin'): ?>
                <!-- BOUTON MODIFIER - Lien direct vers la page d'édition -->
                <a href="index.php?controller=patient&action=edit&id=<?php echo $patient['id']; ?>" 
                   class="btn-icon btn-icon-modifier" 
                   title="Modifier le patient">
                    <i class="bi bi-pencil"></i>
                </a>
                <?php endif; ?>
                
                <?php if($userRole === 'admin'): ?>
                <a href="index.php?controller=patient&action=delete&id=<?php echo $patient['id']; ?>" 
                   class="btn-icon btn-icon-supprimer" 
                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce patient ?\nCette action est irréversible et supprimera tous ses antécédents.')"
                   title="Supprimer le patient">
                    <i class="bi bi-trash"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Antécédents médicaux -->
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="table-header mb-0" style="padding: 10px 15px; font-size: 0.9rem;">
                    <i class="bi bi-heart-pulse-fill me-2"></i>
                    ANTÉCÉDENTS MÉDICAUX
                </div>
                
                <!-- Bouton icône Nouvel antécédent (sans texte) -->
                <?php if($userRole === 'medecin' || $userRole === 'major' || $userRole === 'admin'): ?>
                <a href="index.php?controller=antecedent&action=create&patient_id=<?php echo $patient['id']; ?>" 
                   class="btn-icon btn-icon-ajouter"
                   title="Ajouter un antécédent">
                    <i class="bi bi-plus-lg"></i>
                </a>
                <?php endif; ?>
            </div>

            <?php if(isset($antecedents) && $antecedents->rowCount() > 0): ?>
                <?php while($ant = $antecedents->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="antecedent-card">
                    <!-- En-tête de l'antécédent avec bouton Résultats -->
                    <div class="antecedent-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="antecedent-date">
                                    <i class="bi bi-calendar-check"></i>
                                    <?php echo date('d/m/Y', strtotime($ant['date_consultation'])); ?>
                                </span>
                                <span class="antecedent-motif ms-3">
                                    <i class="bi bi-chat-text"></i>
                                    <?php echo htmlspecialchars($ant['motif_consultation']); ?>
                                </span>
                                
                            </div>
                            
                            <!-- BOUTON RÉSULTAT AJOUTÉ ICI -->
                            <?php if($userRole === 'medecin' || $userRole === 'major' || $userRole === 'admin'): ?>
                            <a href="index.php?controller=antecedent&action=resultat&id=<?php echo $ant['id']; ?>&patient_id=<?php echo $patient['id']; ?>" 
                               class="btn-resultat">
                                <i class="bi bi-file-medical"></i> Résultats
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Historique de la maladie -->
                    <?php if(!empty($ant['historique_maladie'])): ?>
                    <div class="antecedent-section">
                        <div class="antecedent-section-title">
                            <i class="bi bi-clock-history"></i> Historique de la maladie
                        </div>
                        <div class="antecedent-field-value">
                            <?php echo nl2br(htmlspecialchars($ant['historique_maladie'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Antécédents personnels -->
                    <?php if(!empty($ant['antecedents_medicaux']) || !empty($ant['antecedents_chirurgicaux']) || !empty($ant['antecedents_familiaux']) || !empty($ant['allergies'])): ?>
                    <div class="antecedent-section">
                        <div class="antecedent-section-title">
                            <i class="bi bi-files"></i> Antécédents
                        </div>
                        <div class="row">
                            <?php if(!empty($ant['antecedents_medicaux'])): ?>
                            <div class="col-md-6">
                                <div class="antecedent-field">
                                    <span class="antecedent-field-label">Médicaux :</span>
                                    <span class="antecedent-field-value"><?php echo nl2br(htmlspecialchars($ant['antecedents_medicaux'])); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($ant['antecedents_chirurgicaux'])): ?>
                            <div class="col-md-6">
                                <div class="antecedent-field">
                                    <span class="antecedent-field-label">Chirurgicaux :</span>
                                    <span class="antecedent-field-value"><?php echo nl2br(htmlspecialchars($ant['antecedents_chirurgicaux'])); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($ant['antecedents_familiaux'])): ?>
                            <div class="col-md-6">
                                <div class="antecedent-field">
                                    <span class="antecedent-field-label">Familiaux :</span>
                                    <span class="antecedent-field-value"><?php echo nl2br(htmlspecialchars($ant['antecedents_familiaux'])); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($ant['allergies'])): ?>
                            <div class="col-md-6">
                                <div class="antecedent-field">
                                    <span class="antecedent-field-label">Allergies :</span>
                                    <span class="antecedent-field-value"><?php echo nl2br(htmlspecialchars($ant['allergies'])); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Examen clinique du jour -->
                    <?php if(!empty($ant['ta']) || !empty($ant['fc']) || !empty($ant['temperature']) || !empty($ant['fr']) || !empty($ant['saturation']) || !empty($ant['poids'])): ?>
                    <div class="antecedent-section">
                        <div class="antecedent-section-title">
                            <i class="bi bi-activity"></i> Examen clinique du jour
                        </div>
                        <div class="row">
                            <?php if(!empty($ant['ta'])): ?>
                            <div class="col-md-4">
                                <span class="badge-clinique">TA</span> <?php echo htmlspecialchars($ant['ta']); ?>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($ant['fc'])): ?>
                            <div class="col-md-4">
                                <span class="badge-clinique">FC</span> <?php echo htmlspecialchars($ant['fc']); ?> bpm
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($ant['temperature'])): ?>
                            <div class="col-md-4">
                                <span class="badge-clinique">T°</span> <?php echo htmlspecialchars($ant['temperature']); ?> °C
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($ant['fr'])): ?>
                            <div class="col-md-4">
                                <span class="badge-clinique">FR</span> <?php echo htmlspecialchars($ant['fr']); ?> /min
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($ant['saturation'])): ?>
                            <div class="col-md-4">
                                <span class="badge-clinique">SpO2</span> <?php echo htmlspecialchars($ant['saturation']); ?>%
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($ant['poids'])): ?>
                            <div class="col-md-4">
                                <span class="badge-clinique">Poids</span> <?php echo htmlspecialchars($ant['poids']); ?> kg
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Examen par appareil -->
                    <?php if(!empty($ant['appareil_pleuro_pulmonaire']) || !empty($ant['appareil_cardio_vasculaire']) || !empty($ant['appareil_digestif']) || !empty($ant['appareil_locomoteur']) || !empty($ant['appareil_uro_genital']) || !empty($ant['autre_organe'])): ?>
                    <div class="antecedent-section">
                        <div class="antecedent-section-title">
                            <i class="bi bi-grid-3x3-gap-fill"></i> Examen par appareil
                        </div>
                        <div class="row">
                            <?php if(!empty($ant['appareil_pleuro_pulmonaire'])): ?>
                            <div class="col-md-6">
                                <div class="antecedent-field">
                                    <span class="antecedent-field-label">Pleuro-pulmonaire :</span>
                                    <span class="antecedent-field-value"><?php echo nl2br(htmlspecialchars($ant['appareil_pleuro_pulmonaire'])); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($ant['appareil_cardio_vasculaire'])): ?>
                            <div class="col-md-6">
                                <div class="antecedent-field">
                                    <span class="antecedent-field-label">Cardio-vasculaire :</span>
                                    <span class="antecedent-field-value"><?php echo nl2br(htmlspecialchars($ant['appareil_cardio_vasculaire'])); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($ant['appareil_digestif'])): ?>
                            <div class="col-md-6">
                                <div class="antecedent-field">
                                    <span class="antecedent-field-label">Digestif :</span>
                                    <span class="antecedent-field-value"><?php echo nl2br(htmlspecialchars($ant['appareil_digestif'])); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($ant['appareil_locomoteur'])): ?>
                            <div class="col-md-6">
                                <div class="antecedent-field">
                                    <span class="antecedent-field-label">Locomoteur :</span>
                                    <span class="antecedent-field-value"><?php echo nl2br(htmlspecialchars($ant['appareil_locomoteur'])); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($ant['appareil_uro_genital'])): ?>
                            <div class="col-md-6">
                                <div class="antecedent-field">
                                    <span class="antecedent-field-label">Uro-génital :</span>
                                    <span class="antecedent-field-value"><?php echo nl2br(htmlspecialchars($ant['appareil_uro_genital'])); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($ant['autre_organe'])): ?>
                            <div class="col-md-6">
                                <div class="antecedent-field">
                                    <span class="antecedent-field-label">Autre :</span>
                                    <span class="antecedent-field-value"><?php echo nl2br(htmlspecialchars($ant['autre_organe'])); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Résumé syndromique et Diagnostic -->
                    <?php if(!empty($ant['resume_syndromique']) || !empty($ant['diagnostic_presomption'])): ?>
                    <div class="antecedent-section">
                        <div class="row">
                            <?php if(!empty($ant['resume_syndromique'])): ?>
                            <div class="col-md-6">
                                <div class="antecedent-section-title" style="font-size: 0.9rem;">
                                    <i class="bi bi-file-text"></i> Résumé syndromique
                                </div>
                                <div class="antecedent-field-value">
                                    <?php echo nl2br(htmlspecialchars($ant['resume_syndromique'])); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($ant['diagnostic_presomption'])): ?>
                            <div class="col-md-6">
                                <div class="antecedent-section-title" style="font-size: 0.9rem;">
                                    <i class="bi bi-clipboard2-pulse"></i> Diagnostic présomption
                                </div>
                                <div class="antecedent-field-value">
                                    <?php echo nl2br(htmlspecialchars($ant['diagnostic_presomption'])); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Examens complémentaires -->
                    <?php if(!empty($ant['examen_complementaire'])): ?>
                    <div class="antecedent-section">
                        <div class="antecedent-section-title">
                            <i class="bi bi-microscope"></i> Examens complémentaires
                        </div>
                        <div class="antecedent-field-value">
                            <?php echo nl2br(htmlspecialchars($ant['examen_complementaire'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Traitement symptomatique -->
                    <?php if(!empty($ant['traitement_symptomatique'])): ?>
                    <div class="antecedent-section">
                        <div class="antecedent-section-title">
                            <i class="bi bi-capsule"></i> Traitement symptomatique
                        </div>
                        <div class="antecedent-field-value">
                            <?php echo nl2br(htmlspecialchars($ant['traitement_symptomatique'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- RÉSULTATS ET TRAITEMENTS SPÉCIFIQUES - NOUVELLE SECTION -->
                    <?php if(!empty($ant['resultat']) || !empty($ant['traitement_specifique'])): ?>
                    <div class="antecedent-section">
                        <div class="antecedent-section-title">
                            <i class="bi bi-file-medical"></i> Résultats et traitements spécifiques
                        </div>
                        <div class="row">
                            <?php if(!empty($ant['resultat'])): ?>
                            <div class="col-md-6">
                                <div class="antecedent-field">
                                    <span class="antecedent-field-label">Résultats d'examens :</span>
                                    <div class="antecedent-field-value mt-1">
                                        <?php echo nl2br(htmlspecialchars($ant['resultat'])); ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($ant['traitement_specifique'])): ?>
                            <div class="col-md-6">
                                <div class="antecedent-field">
                                    <span class="antecedent-field-label">Traitements spécifiques :</span>
                                    <div class="antecedent-field-value mt-1">
                                        <?php echo nl2br(htmlspecialchars($ant['traitement_specifique'])); ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-emoji-neutral" style="font-size: 4rem; color: #cbd5e1;"></i>
                    <h3 class="mt-3 fw-light">Aucun antécédent médical</h3>
                    <?php if($userRole === 'medecin' || $userRole === 'major' || $userRole === 'admin'): ?>
                        <p class="text-muted mb-0">Cliquez sur le bouton <i class="bi bi-plus-lg"></i> pour en créer un.</p>
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