<?php
// Démarrer la session si nécessaire
if(session_id() == '') {
    session_start();
}

// Fonction pour vérifier si un lien est actif
function isActive($controller, $action = null) {
    $currentController = isset($_GET['controller']) ? $_GET['controller'] : '';
    $currentAction = isset($_GET['action']) ? $_GET['action'] : '';
    
    if($currentController == $controller) {
        if($action === null || $currentAction == $action) {
            return 'active';
        }
    }
    return '';
}

// Fonction pour vérifier les permissions (simplifiée pour éviter les erreurs)
function can($action) {
    if(!isset($_SESSION['user_role'])) return false;
    
    $role = $_SESSION['user_role'];
    
    $permissions = array(
        'admin' => array('manage_users', 'view_all', 'create_patient', 'edit_patient', 'delete_patient', 'add_antecedent', 'edit_antecedent', 'delete_antecedent', 'view_stats', 'export_data'),
        'medecin' => array('view_all', 'create_patient', 'edit_patient', 'add_antecedent', 'edit_antecedent', 'delete_antecedent', 'view_stats'),
        'major' => array('view_all', 'create_patient', 'edit_patient', 'add_antecedent', 'view_stats'),
        'infirmier' => array('view_own_profile')
    );
    
    return isset($permissions[$role]) && in_array($action, $permissions[$role]);
}
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top mb-4">
    <div class="container">
        <a class="navbar-brand gradient-text" href="index.php?controller=patient&action=index">
            <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle me-2 floating" style="width: 32px; height: 32px; font-size: 16px;">
                <i class="bi bi-heart-pulse"></i>
            </span>
            Klinik
        </a>
        
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="bi bi-list fs-1 text-primary"></i>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Menu de gauche - visible seulement si connecté -->
            <?php if(isset($_SESSION['user_id'])): ?>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                
                <?php if($_SESSION['user_role'] !== 'infirmier'): ?>
                    <!-- Menu PATIENTS - visible pour tous sauf infirmier -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo isActive('patient'); ?>" href="#" id="patientsDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-people me-1"></i> Patients
                        </a>
                        <ul class="dropdown-menu border-0 shadow-sm rounded-3">
                            <li>
                                <a class="dropdown-item py-2" href="index.php?controller=patient&action=index">
                                    <i class="bi bi-list-ul me-2 text-primary"></i> Liste des patients
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="index.php?controller=patient&action=search">
                                    <i class="bi bi-search me-2 text-success"></i> Rechercher patient
                                </a>
                            </li>
                            
                            <!-- Sous-menu Nouveau patient pour Médecin et Major uniquement -->
                            <?php if($_SESSION['user_role'] === 'medecin' || $_SESSION['user_role'] === 'major'): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item py-2" href="index.php?controller=patient&action=create">
                                    <i class="bi bi-person-plus me-2 text-warning"></i> Nouveau patient
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Menu PLANNING DES SOINS - visible pour tous -->
                <?php if($_SESSION['user_role'] == 'major' || $_SESSION['user_role'] == 'medecin' || $_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'infirmier'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo isActive('soin'); ?>" href="#" id="soinsDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-calendar-heart me-1"></i> Soins
                    </a>
                    <ul class="dropdown-menu border-0 shadow-sm rounded-3">
                        <!-- Planning général - visible pour tous -->
                        <li>
                            <a class="dropdown-item py-2 <?php echo isActive('soin', 'index'); ?>" href="index.php?controller=soin&action=index">
                                <i class="bi bi-calendar-week me-2 text-primary"></i> Planning général
                            </a>
                        </li>
                        
                        <!-- VOIR MON PLANNING - visible uniquement pour INFIRMIER -->
                        <?php if($_SESSION['user_role'] == 'infirmier'): ?>
                        <li>
                            <a class="dropdown-item py-2" href="index.php?controller=soin&action=monPlanning">
                                <i class="bi bi-person-workspace me-2 text-info"></i> Voir mon planning
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <!-- Planifier un soin - pour Major et Admin uniquement -->
                        <?php if($_SESSION['user_role'] == 'major' || $_SESSION['user_role'] == 'admin'): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2" href="index.php?controller=soin&action=create">
                                <i class="bi bi-plus-circle me-2 text-success"></i> Planifier un soin
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Menu UTILISATEURS - visible seulement pour admin -->
                <?php if($_SESSION['user_role'] == 'admin'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-people-fill me-1"></i> Utilisateurs
                    </a>
                    <ul class="dropdown-menu border-0 shadow-sm rounded-3">
                        <li>
                            <a class="dropdown-item py-2" href="index.php?controller=admin&action=users">
                                <i class="bi bi-list-ul me-2 text-primary"></i> Liste des utilisateurs
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="index.php?controller=admin&action=search">
                                <i class="bi bi-search me-2 text-success"></i> Rechercher utilisateur
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item py-2" href="index.php?controller=admin&action=create">
                                <i class="bi bi-person-plus me-2 text-success"></i> Nouvel utilisateur
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
            <?php endif; ?>

            <!-- Menu de droite (utilisateur) -->
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="d-flex flex-column text-end d-none d-lg-block">
                                <span class="fw-bold small text-dark"><?php echo htmlspecialchars($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']); ?></span>
                                <span class="text-muted extra-small" style="font-size: 0.75rem; line-height: 1;">
                                    <?php 
                                    $roleLabels = array(
                                        'admin' => 'Administrateur',
                                        'medecin' => 'Médecin',
                                        'major' => 'Major',
                                        'infirmier' => 'Infirmier'
                                    );
                                    $role = $_SESSION['user_role'];
                                    echo isset($roleLabels[$role]) ? $roleLabels[$role] : ucfirst($role);
                                    ?>
                                </span>
                            </div>
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border: 2px solid var(--primary-light);">
                                <i class="bi bi-person-fill text-primary"></i>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 mt-2">
                             <li>
                                <div class="px-3 py-2 d-lg-none">
                                    <strong class="d-block"><?php echo htmlspecialchars($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom']); ?></strong>
                                    <small class="text-muted"><?php echo ucfirst($_SESSION['user_role']); ?></small>
                                </div>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="index.php?controller=auth&action=profile">
                                    <i class="bi bi-person me-2"></i> Mon profil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item py-2 text-danger" href="index.php?controller=auth&action=logout">
                                    <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-primary px-4 rounded-pill shadow-sm" href="index.php?controller=auth&action=loginForm">
                            Connexion
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Modal de recherche patient (garde pour compatibilité) -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            <div class="modal-header bg-light border-0 pb-0">
                <h5 class="modal-title fw-bold text-primary"><i class="bi bi-search me-2"></i>Rechercher un patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-3">
                <form action="index.php" method="GET" id="searchForm">
                    <input type="hidden" name="controller" value="patient">
                    <input type="hidden" name="action" value="search">
                    
                    <p class="text-muted small mb-4">Renseignez au moins un critère pour lancer la recherche.</p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Nom</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0" 
                                   name="nom" 
                                   placeholder="Ex: DUPONT"
                                   oninput="this.value = this.value.toUpperCase()"
                                   style="text-transform: uppercase;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Prénom</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0" 
                                   name="prenom" 
                                   placeholder="Ex: Jean"
                                   oninput="this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase()">
                        </div>
                        <div class="col-12">
                             <label class="form-label fw-bold small text-uppercase text-muted">Date de naissance</label>
                            <input type="date" class="form-control form-control-lg bg-light border-0" 
                                   name="date_naissance"
                                   placeholder="jj/mm/aaaa">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary px-4">Rechercher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Notifications -->
<?php if(isset($_SESSION) && (isset($_SESSION['success']) || isset($_SESSION['error']) || isset($_SESSION['warning']) || isset($_SESSION['info']))): ?>
<div class="container mb-4">
    <?php 
    $types = array(
        'success' => array('icon' => 'check-circle-fill', 'class' => 'success'),
        'error' => array('icon' => 'exclamation-triangle-fill', 'class' => 'danger'),
        'warning' => array('icon' => 'exclamation-circle-fill', 'class' => 'warning'),
        'info' => array('icon' => 'info-circle-fill', 'class' => 'info')
    );
    
    foreach($types as $key => $config): 
        if(isset($_SESSION[$key])): 
    ?>
    <div class="alert alert-<?php echo $config['class']; ?> border-0 shadow-sm d-flex align-items-center fade show rounded-3" role="alert">
        <i class="bi bi-<?php echo $config['icon']; ?> fs-4 me-3"></i>
        <div>
            <?php echo $_SESSION[$key]; unset($_SESSION[$key]); ?>
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php 
        endif; 
    endforeach; 
    ?>
</div>
<?php endif; ?>

<!-- Styles additionnels -->
<style>
.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.6);
    transform: scale(0);
    animation: ripple-animation 0.6s linear;
    pointer-events: none;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

.btn {
    position: relative;
    overflow: hidden;
}

.gradient-text {
    background: linear-gradient(45deg, #4e73df, #1cc88a);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 700;
}

.floating {
    animation: floating 3s ease-in-out infinite;
}

@keyframes floating {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-5px); }
    100% { transform: translateY(0px); }
}

.nav-link {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 8px;
    margin: 0 2px;
}

.nav-link:hover {
    background-color: rgba(78, 115, 223, 0.1);
}

.dropdown-menu {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-close alerts after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Add interactive effects to navigation
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Add ripple effect to buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Validation du formulaire de recherche
    var searchForm = document.getElementById('searchForm');
    if(searchForm) {
        searchForm.addEventListener('submit', function(e) {
            var nom = document.querySelector('input[name="nom"]').value.trim();
            var prenom = document.querySelector('input[name="prenom"]').value.trim();
            var dateNaiss = document.querySelector('input[name="date_naissance"]').value;
            
            if(nom === '' && prenom === '' && dateNaiss === '') {
                e.preventDefault();
                alert('Veuillez remplir au moins un champ de recherche');
            }
        });
    }
});
</script>