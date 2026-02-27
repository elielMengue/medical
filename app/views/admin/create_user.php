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
    <title>Créer un utilisateur</title>
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
        
        /* Conteneur principal - IDENTIQUE À create.php */
        .form-container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        /* En-tête du formulaire - IDENTIQUE À create.php */
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
        
        /* Style pour le champ nom - IDENTIQUE À create.php */
        .nom-majuscule {
            text-transform: uppercase;
        }
        
        .nom-majuscule::placeholder {
            text-transform: none;
            color: #6c757d;
        }
        
        /* Styles pour les inputs - IDENTIQUE À create.php */
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
        
        .form-text {
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }
        
        /* Style pour les boutons - IDENTIQUE À create.php */
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
        
        /* Animation shimmer - IDENTIQUE À create.php */
        @keyframes shimmer {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }
        
        /* Effets de survol - IDENTIQUE À create.php */
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(33, 37, 41, 0.15);
        }
        
        /* Texte dégradé - IDENTIQUE À create.php */
        .gradient-text {
            background: linear-gradient(135deg, #ffffff 0%, #e9ecef 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Animation flottante - IDENTIQUE À create.php */
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        
        /* Alertes - IDENTIQUE À create.php */
        .alert {
            border-radius: 10px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }
        
        /* Responsive */
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
            <!-- En-tête IDENTIQUE à celui de create.php -->
            <div class="page-header">
                <h1 class="mb-0 gradient-text">
                    <i class="bi bi-person-plus-fill me-2 floating"></i> 
                    Créer un utilisateur
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

            <form method="POST" action="index.php?controller=auth&action=register">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control form-control-lg nom-majuscule hover-lift" id="nom" name="nom" required 
                               placeholder="Entrez le nom"
                               value="<?php echo isset($_SESSION['old_input']['nom']) ? htmlspecialchars(strtoupper($_SESSION['old_input']['nom'])) : ''; ?>"
                               oninput="this.value = this.value.toUpperCase()">
                    </div>
                    <div class="col-md-6">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control form-control-lg hover-lift" id="prenom" name="prenom" required 
                               placeholder="Entrez le prénom"
                               value="<?php echo isset($_SESSION['old_input']['prenom']) ? htmlspecialchars(ucfirst(strtolower($_SESSION['old_input']['prenom']))) : ''; ?>"
                               oninput="this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase()">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control form-control-lg hover-lift" id="email" name="email" required 
                           placeholder="exemple@medical.com"
                           value="<?php echo isset($_SESSION['old_input']['email']) ? htmlspecialchars($_SESSION['old_input']['email']) : ''; ?>">
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="matricule" class="form-label">Matricule</label>
                        <input type="text" class="form-control form-control-lg hover-lift" id="matricule" name="matricule" required 
                               placeholder="Ex: EMP001"
                               value="<?php echo isset($_SESSION['old_input']['matricule']) ? htmlspecialchars($_SESSION['old_input']['matricule']) : ''; ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="role" class="form-label">Rôle</label>
                        <select class="form-select form-select-lg hover-lift" id="role" name="role" required>
                            <option value="">Choisir un rôle</option>
                            <option value="admin" <?php echo (isset($_SESSION['old_input']['role']) && $_SESSION['old_input']['role'] == 'admin') ? 'selected' : ''; ?>>Administrateur</option>
                            <option value="medecin" <?php echo (isset($_SESSION['old_input']['role']) && $_SESSION['old_input']['role'] == 'medecin') ? 'selected' : ''; ?>>Médecin</option>
                            <option value="major" <?php echo (isset($_SESSION['old_input']['role']) && $_SESSION['old_input']['role'] == 'major') ? 'selected' : ''; ?>>Major</option>
                            <option value="infirmier" <?php echo (isset($_SESSION['old_input']['role']) && $_SESSION['old_input']['role'] == 'infirmier') ? 'selected' : ''; ?>>Infirmier</option>
                        </select>
                    </div>
                </div>

                <!-- NOUVEAUX CHAMPS : Service et Centre (MODIFIÉS SELON VOTRE DEMANDE) -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="service" class="form-label">Service</label>
                        <select class="form-select form-select-lg hover-lift" id="service" name="service">
                            <option value="">Sélectionner un service</option>
                            <option value="urgence medicale" <?php echo (isset($_SESSION['old_input']['service']) && $_SESSION['old_input']['service'] == 'urgence medicale') ? 'selected' : ''; ?>>Urgence médicale</option>
                            <option value="urgence chirurgicale" <?php echo (isset($_SESSION['old_input']['service']) && $_SESSION['old_input']['service'] == 'urgence chirurgicale') ? 'selected' : ''; ?>>Urgence chirurgicale</option>
                        </select>
                        <div class="form-text">Optionnel</div>
                    </div>
                    <div class="col-md-6">
                        <label for="centre" class="form-label">Centre</label>
                        <select class="form-select form-select-lg hover-lift" id="centre" name="centre">
                            <option value="">Sélectionner un centre</option>
                            <option value="amitié" <?php echo (isset($_SESSION['old_input']['centre']) && $_SESSION['old_input']['centre'] == 'amitié') ? 'selected' : ''; ?>>Amitié</option>
                            <option value="communautaire" <?php echo (isset($_SESSION['old_input']['centre']) && $_SESSION['old_input']['centre'] == 'communautaire') ? 'selected' : ''; ?>>Communautaire</option>
                        </select>
                        <div class="form-text">Optionnel</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="tel" class="form-control form-control-lg hover-lift" id="telephone" name="telephone" 
                           placeholder="12 34 56 78"
                           pattern="[0-9]{8}" 
                           title="Le numéro doit contenir 8 chiffres"
                           maxlength="8"
                           value="<?php echo isset($_SESSION['old_input']['telephone']) ? htmlspecialchars($_SESSION['old_input']['telephone']) : ''; ?>">
                    <div class="form-text">Optionnel</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control form-control-lg hover-lift" id="password" name="password" required 
                               placeholder="••••••••">
                    </div>
                    <div class="col-md-6">
                        <label for="confirm_password" class="form-label">Confirmer</label>
                        <input type="password" class="form-control form-control-lg hover-lift" id="confirm_password" name="confirm_password" required 
                               placeholder="••••••••">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php?controller=admin&action=users" class="btn btn-secondary btn-lg hover-lift">
                        <i class="bi bi-arrow-left"></i> Annuler
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
        document.getElementById('telephone').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            var password = document.getElementById('password').value;
            var confirm = document.getElementById('confirm_password').value;
            
            if(password !== confirm) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas !');
            }
        });
    </script>
</body>
</html>