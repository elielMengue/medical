<?php require_once dirname(__DIR__) . '/partials/dashboard_header.php'; ?>

<div class="container-fluid">
    <!-- En-tête du dashboard -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="display-6 fw-bold text-primary">
                    <i class="bi bi-speedometer2 me-3"></i>
                    Tableau de Bord Médical
                </h1>
                <p class="text-muted mb-0">
                    Vue d'ensemble de l'activité hospitalière
                </p>
            </div>
            <div class="col-auto">
                <div class="d-flex align-items-center bg-white rounded-pill px-3 py-2 shadow-sm">
                    <div class="me-3 text-center">
                        <small class="text-muted d-block">Date</small>
                        <strong class="text-primary"><?php echo date('d/m/Y'); ?></strong>
                    </div>
                    <div class="text-center">
                        <small class="text-muted d-block">Heure</small>
                        <strong class="text-primary" id="live-time"><?php echo date('H:i'); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card patients-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-1">Patients</h6>
                            <h2 class="mb-0 fw-bold text-primary"><?php echo $stats['total_patients']; ?></h2>
                            <small class="text-success">
                                <i class="bi bi-arrow-up"></i> Total enregistrés
                            </small>
                        </div>
                        <div class="stats-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card soins-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-1">Soins Total</h6>
                            <h2 class="mb-0 fw-bold text-info"><?php echo $stats['total_soins']; ?></h2>
                            <small class="text-info">
                                <i class="bi bi-activity"></i> Depuis le début
                            </small>
                        </div>
                        <div class="stats-icon">
                            <i class="bi bi-heart-pulse-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card today-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-1">Aujourd'hui</h6>
                            <h2 class="mb-0 fw-bold text-warning"><?php echo $stats['soins_aujourdhui']; ?></h2>
                            <small class="text-warning">
                                <i class="bi bi-calendar-check"></i> Soins planifiés
                            </small>
                        </div>
                        <div class="stats-icon">
                            <i class="bi bi-calendar-date-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card status-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-1">En Cours</h6>
                            <h2 class="mb-0 fw-bold text-success"><?php echo $stats['soins_en_cours']; ?></h2>
                            <small class="text-success">
                                <i class="bi bi-play-circle"></i> Actuellement
                            </small>
                        </div>
                        <div class="stats-icon">
                            <i class="bi bi-clock-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et tableaux -->
    <div class="row">
        <!-- Graphique des soins par mois -->
        <div class="col-lg-8 mb-4">
            <div class="chart-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Évolution des Soins
                    </h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active" data-period="month">Mois</button>
                        <button type="button" class="btn btn-outline-primary" data-period="week">Semaine</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="soinsChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Répartition par statut -->
        <div class="col-lg-4 mb-4">
            <div class="chart-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart me-2"></i>
                        Statut des Soins
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Soins du jour -->
    <div class="row">
        <div class="col-12">
            <div class="today-care-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-list-check me-2"></i>
                        Planning du Jour
                    </h5>
                    <span class="badge bg-primary rounded-pill"><?php echo count($soinsAujourdhui); ?> soins</span>
                </div>
                <div class="card-body p-0">
                    <?php if(!empty($soinsAujourdhui)): ?>
                    <div class="today-care-list">
                        <?php foreach($soinsAujourdhui as $soin): ?>
                        <div class="care-item d-flex align-items-center p-3 border-bottom">
                            <div class="care-time me-3">
                                <div class="time-badge">
                                    <?php echo date('H:i', strtotime($soin['heure_soin'])); ?>
                                </div>
                            </div>
                            <div class="care-info flex-grow-1">
                                <h6 class="mb-1"><?php echo htmlspecialchars($soin['type_soin']); ?></h6>
                                <p class="mb-0 text-muted">
                                    <i class="bi bi-person me-1"></i>
                                    <?php echo htmlspecialchars($soin['patient_nom'] . ' ' . $soin['patient_prenom']); ?>
                                    <span class="mx-2">•</span>
                                    <i class="bi bi-heart-pulse me-1"></i>
                                    Lit <?php echo htmlspecialchars($soin['numero_lit']); ?>
                                </p>
                            </div>
                            <div class="care-status">
                                <span class="badge bg-<?php echo getStatusColor($soin['statut']); ?>">
                                    <?php echo getStatusLabel($soin['statut']); ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x display-1 text-muted"></i>
                        <p class="text-muted mt-3">Aucun soin planifié pour aujourd'hui</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts pour les graphiques -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique des soins par mois
    const soinsCtx = document.getElementById('soinsChart').getContext('2d');
    const soinsChart = new Chart(soinsCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($soinsParMois, 'mois')); ?>,
            datasets: [{
                label: 'Soins par mois',
                data: <?php echo json_encode(array_column($soinsParMois, 'nombre')); ?>,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Graphique en camembert des statuts
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(array_column($soinsParStatut, 'statut')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($soinsParStatut, 'nombre')); ?>,
                backgroundColor: [
                    '#ffc107', // planifie
                    '#17a2b8', // en_cours
                    '#28a745', // effectue
                    '#dc3545'  // annule
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Horloge en direct
    function updateClock() {
        const now = new Date();
        document.getElementById('live-time').textContent = 
            now.getHours().toString().padStart(2, '0') + ':' + 
            now.getMinutes().toString().padStart(2, '0');
    }
    setInterval(updateClock, 1000);
});
</script>

<style>
/* Styles spécifiques au dashboard */
.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 20px;
    margin-bottom: 2rem;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.stats-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    height: 100%;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.patients-card .stats-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.soins-card .stats-icon {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.today-card .stats-icon {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.status-card .stats-icon {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.chart-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    height: 100%;
}

.today-care-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.today-care-list {
    max-height: 400px;
    overflow-y: auto;
}

.care-item {
    transition: all 0.3s ease;
}

.care-item:hover {
    background-color: #f8f9fa;
}

.time-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: bold;
    font-size: 0.9rem;
}

.live-clock {
    background: rgba(255,255,255,0.2);
    padding: 1rem;
    border-radius: 15px;
    backdrop-filter: blur(10px);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stats-card, .chart-card, .today-care-card {
    animation: fadeInUp 0.6s ease-out;
}
</style>

<?php
// Helper functions for status display
function getStatusColor($status) {
    switch($status) {
        case 'planifie': return 'warning';
        case 'en_cours': return 'info';
        case 'effectue': return 'success';
        case 'annule': return 'danger';
        default: return 'secondary';
    }
}

function getStatusLabel($status) {
    switch($status) {
        case 'planifie': return 'Planifié';
        case 'en_cours': return 'En cours';
        case 'effectue': return 'Effectué';
        case 'annule': return 'Annulé';
        default: return 'Inconnu';
    }
}
?>

<?php require_once dirname(__DIR__) . '/partials/dashboard_footer.php'; ?>