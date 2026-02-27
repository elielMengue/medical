<?php

if(session_id() == '') {
    session_start();
}

// Rôle de l'utilisateur connecté
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';

// Récupérer les informations de l'utilisateur depuis le contrôleur
$user = isset($user) ? $user : array();

// Vérifier si $user est vide
if(empty($user)) {
    echo '<div class="alert alert-danger">Erreur: Données utilisateur non trouvées</div>';
}
?>

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

<!-- BARRE D'EN-TÊTE -->
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="gradient-text">
            <i class="bi bi-person-circle me-2 floating"></i>
            Profil utilisateur
        </h1>
        <p>
            <span class="role-badge">
                <?php 
                $roleLabels = array(
                    'admin' => 'Administrateur',
                    'medecin' => 'Médecin',
                    'major' => 'Major',
                    'infirmier' => 'Infirmier'
                );
                $role = isset($user['role']) ? $user['role'] : '';
                $roleLabel = isset($roleLabels[$role]) ? $roleLabels[$role] : $role;
                echo $roleLabel;
                ?>
            </span>
        </p>
    </div>
    <a href="index.php?controller=admin&action=users" class="btn btn-light">
        <i class="bi bi-arrow-left"></i> Retour à la liste
    </a>
</div>

<div class="row">
    <!-- Colonne de gauche : Informations personnelles -->
    <div class="col-md-6">
        <div class="profile-card">
            <h4 class="mb-3">
                <i class="bi bi-info-circle-fill text-primary"></i>
                Informations personnelles
            </h4>

            <div class="info-label">Nom complet</div>
            <div class="info-value">
                <i class="bi bi-person-badge"></i>
                <?php 
                $nom = isset($user['nom']) ? $user['nom'] : 'Non défini';
                $prenom = isset($user['prenom']) ? $user['prenom'] : '';
                echo htmlspecialchars($prenom . ' ' . strtoupper($nom)); 
                ?>
            </div>

            <div class="info-label">Email</div>
            <div class="info-value">
                <i class="bi bi-envelope"></i>
                <?php echo isset($user['email']) ? htmlspecialchars($user['email']) : 'Non défini'; ?>
            </div>

            <div class="info-label">Matricule</div>
            <div class="info-value">
                <i class="bi bi-person-badge"></i>
                <?php 
                $matricule = isset($user['matricule']) ? $user['matricule'] : '';
                echo !empty($matricule) ? htmlspecialchars($matricule) : 'Non renseigné'; 
                ?>
            </div>

            <div class="info-label">Téléphone</div>
            <div class="info-value">
                <i class="bi bi-telephone"></i>
                <?php 
                $telephone = isset($user['telephone']) ? $user['telephone'] : '';
                echo !empty($telephone) ? htmlspecialchars($telephone) : 'Non renseigné'; 
                ?>
            </div>

            <!-- SERVICE -->
            <div class="info-label">Service</div>
            <div class="info-value">
                <i class="bi bi-building"></i>
                <?php 
                $service = isset($user['service']) ? $user['service'] : '';
                echo !empty($service) ? htmlspecialchars($service) : 'Non renseigné'; 
                ?>
            </div>

            <!-- CENTRE -->
            <div class="info-label">Centre</div>
            <div class="info-value">
                <i class="bi bi-geo-alt"></i>
                <?php 
                $centre = isset($user['centre']) ? $user['centre'] : '';
                echo !empty($centre) ? htmlspecialchars($centre) : 'Non renseigné'; 
                ?>
            </div>
        </div>
    </div>

    <!-- Colonne de droite : Actions -->
    <div class="col-md-6">
        <div class="profile-card">
            <h4 class="mb-3">
                <i class="bi bi-gear text-warning"></i>
                Actions
            </h4>

            <div class="d-flex gap-2">
                <a href="index.php?controller=admin&action=editUser&id=<?php echo isset($user['id']) ? $user['id'] : ''; ?>" 
                   class="btn btn-primary w-100 hover-lift">
                    <i class="bi bi-pencil"></i> Modifier
                </a>
                
                <?php if(isset($user['id']) && $user['id'] != $_SESSION['user_id']): ?>
                <a href="index.php?controller=admin&action=deleteUser&id=<?php echo $user['id']; ?>" 
                   class="btn btn-outline-danger w-100 hover-lift"
                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                    <i class="bi bi-trash"></i> Supprimer
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Styles CSS (les mêmes que votre fichier original) -->
<style>
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
    margin-bottom: 5px;
}

.page-header p {
    position: relative;
    z-index: 2;
    font-size: 1rem;
    opacity: 0.9;
    margin-bottom: 0;
}

.profile-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    border: 1px solid rgba(0,0,0,0.05);
}

.info-label {
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 15px;
}

.info-value {
    font-size: 1.1rem;
    margin-bottom: 15px;
    padding: 10px 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #212529;
}

.info-value i {
    color: #495057;
    margin-right: 10px;
}

.role-badge {
    background: linear-gradient(135deg, #495057 0%, #6c757d 100%);
    color: white;
    padding: 5px 15px;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 600;
    display: inline-block;
}

.btn {
    border-radius: 8px;
    font-weight: 600;
    padding: 12px 20px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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

.btn-outline-danger {
    border-color: #dc3545;
    color: #dc3545;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
}

.btn-light {
    background: white;
    color: #212529;
    border: none;
}

.btn-light:hover {
    background: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255,255,255,0.3);
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
</style>