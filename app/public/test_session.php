<!-- app/views/partials/navbar.php -->
<?php
if(session_id() == '') {
    session_start();
}

// Forcer l'affichage pour le debug
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'non_connecte';
$userName = isset($_SESSION['user_prenom']) ? $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'] : 'Invité';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Gestion Médicale</a>
        
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php?controller=patient&action=index">Accueil</a>
                </li>
            </ul>
            
            <ul class="navbar-nav">
                <li class="nav-item">
                    <span class="nav-link">
                        <?php 
                        if($userRole != 'non_connecte') {
                            // Libellé du rôle
                            $labels = array(
                                'admin' => 'Admin',
                                'medecin' => 'Médecin',
                                'major' => 'Major',
                                'infirmier' => 'Infirmier'
                            );
                            $label = isset($labels[$userRole]) ? $labels[$userRole] : $userRole;
                            echo "<span class='badge bg-light text-dark'>$label</span> ";
                            echo htmlspecialchars($userName);
                        } else {
                            echo "<a href='index.php?controller=auth&action=loginForm' class='text-white'>Connexion</a>";
                        }
                        ?>
                    </span>
                </li>
                <?php if($userRole != 'non_connecte'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?controller=auth&action=logout">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>