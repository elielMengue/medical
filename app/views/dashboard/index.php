<?php
// Helper functions for status display
function getStatusColor($status) {
    switch($status) {
        case 'planifie': return 'bg-yellow-100 text-yellow-800';
        case 'en_cours': return 'bg-blue-100 text-blue-800';
        case 'effectue': return 'bg-green-100 text-green-800';
        case 'annule': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
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

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Patients Card -->
    <div class="card-gradient-purple rounded-2xl p-6 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-white bg-opacity-50 rounded-full p-3">
                <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
        <div class="text-sm font-medium text-gray-700 mb-1">Total Patients</div>
        <div class="text-3xl font-bold text-gray-900"><?php echo isset($stats['total_patients']) ? $stats['total_patients'] : 0; ?></div>
        <div class="text-xs text-gray-600 mt-1">Total enregistrés</div>
    </div>

    <!-- Soins Total Card -->
    <div class="card-gradient-green rounded-2xl p-6 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-white bg-opacity-50 rounded-full p-3">
                <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
        </div>
        <div class="text-sm font-medium text-gray-700 mb-1">Soins Total</div>
        <div class="text-3xl font-bold text-gray-900"><?php echo isset($stats['total_soins']) ? $stats['total_soins'] : 0; ?></div>
        <div class="text-xs text-gray-600 mt-1">Depuis le début</div>
    </div>

    <!-- Today's Appointments Card -->
    <div class="card-gradient-blue-green rounded-2xl p-6 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-white bg-opacity-50 rounded-full p-3">
                <svg class="w-6 h-6 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
        <div class="text-sm font-medium text-gray-700 mb-1">Aujourd'hui</div>
        <div class="text-3xl font-bold text-gray-900"><?php echo isset($stats['soins_aujourdhui']) ? $stats['soins_aujourdhui'] : 0; ?></div>
        <div class="text-xs text-gray-600 mt-1">Soins planifiés</div>
    </div>

    <!-- En Cours Card -->
    <div class="card-gradient-pink rounded-2xl p-6 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-white bg-opacity-50 rounded-full p-3">
                <svg class="w-6 h-6 text-pink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <div class="text-sm font-medium text-gray-700 mb-1">En Cours</div>
        <div class="text-3xl font-bold text-gray-900"><?php echo isset($stats['soins_en_cours']) ? $stats['soins_en_cours'] : 0; ?></div>
        <div class="text-xs text-gray-600 mt-1">Actuellement</div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Graphique des soins par mois -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Évolution des Soins</h3>
            <div class="flex space-x-2">
                <button class="px-3 py-1 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg">Mois</button>
                <button class="px-3 py-1 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg">Semaine</button>
            </div>
        </div>
        <div class="h-64">
            <canvas id="soinsChart"></canvas>
        </div>
    </div>

    <!-- Répartition par statut -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Statut des Soins</h3>
        <div class="h-64">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
</div>

<!-- Planning du Jour -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-lg font-bold text-gray-900">Planning du Jour</h3>
        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
            <?php echo isset($soinsAujourdhui) ? count($soinsAujourdhui) : 0; ?> soins
        </span>
    </div>
    <div class="max-h-96 overflow-y-auto">
        <?php if(!empty($soinsAujourdhui)): ?>
            <?php foreach($soinsAujourdhui as $soin): ?>
            <div class="px-6 py-4 border-b border-gray-100 hover:bg-gray-50 transition-colors flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="bg-gradient-to-br from-blue-500 to-purple-600 text-white px-4 py-2 rounded-lg font-semibold text-sm">
                        <?php echo date('H:i', strtotime($soin['heure_soin'])); ?>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-semibold text-gray-900 mb-1"><?php echo htmlspecialchars($soin['type_soin']); ?></h4>
                    <p class="text-sm text-gray-600">
                        <span class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <?php echo htmlspecialchars($soin['patient_nom'] . ' ' . $soin['patient_prenom']); ?>
                        </span>
                        <span class="mx-2">•</span>
                        <span class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Lit <?php echo htmlspecialchars($soin['numero_lit']); ?>
                        </span>
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full <?php echo getStatusColor($soin['statut']); ?>">
                        <?php echo getStatusLabel($soin['statut']); ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <p class="text-gray-500 mt-3">Aucun soin planifié pour aujourd'hui</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Scripts pour les graphiques -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique des soins par mois
    const soinsCtx = document.getElementById('soinsChart');
    if(soinsCtx) {
        const soinsChart = new Chart(soinsCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($soinsParMois ?? [], 'mois')); ?>,
                datasets: [{
                    label: 'Soins par mois',
                    data: <?php echo json_encode(array_column($soinsParMois ?? [], 'nombre')); ?>,
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
    }

    // Graphique en camembert des statuts
    const statusCtx = document.getElementById('statusChart');
    if(statusCtx) {
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($soinsParStatut ?? [], 'statut')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($soinsParStatut ?? [], 'nombre')); ?>,
                    backgroundColor: [
                        '#fbbf24', // planifie - yellow
                        '#3b82f6', // en_cours - blue
                        '#10b981', // effectue - green
                        '#ef4444'  // annule - red
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
    }
});
</script>
