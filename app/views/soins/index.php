<?php
// démarrage de session et préparation des variables
if(session_id() == '') { session_start(); }
$userRole = $_SESSION['user_role'] ?? '';
$dateAffichage = $date ?? date('Y-m-d');
$dateFormatee = date('d/m/Y', strtotime($dateAffichage));

// Préparer les données des soins
$soinsArray = array();
if($soins) {
    while($row = $soins->fetch(\PDO::FETCH_ASSOC)) {
        $soinsArray[] = $row;
    }
}

// Calculer les statistiques pour les cartes
$todayStats = $statsToday ?? array('total' => 0, 'planifies' => 0, 'en_cours' => 0, 'effectues' => 0, 'annules' => 0);
$allStats = $statsAll ?? array('total' => 0, 'planifies' => 0, 'en_cours' => 0, 'effectues' => 0, 'annules' => 0);
$upcoming = $upcomingCount ?? 0;

// Compter les soins du jour
$todayTotal = $todayStats['total'] ?? 0;
$todayUpcoming = ($todayStats['planifies'] ?? 0) + ($todayStats['en_cours'] ?? 0);
$todayCompleted = $todayStats['effectues'] ?? 0;
$todayCancelled = $todayStats['annules'] ?? 0;
?>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Today's Appointments -->
    <div class="card-gradient-purple rounded-2xl p-6 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-white bg-opacity-50 rounded-full p-3">
                <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v6m0 0l-3-3m3 3l3-3"/>
                </svg>
            </div>
        </div>
        <div class="text-sm font-medium text-gray-700 mb-1">Today's Appointments</div>
        <div class="text-3xl font-bold text-gray-900"><?php echo $todayTotal; ?></div>
    </div>

    <!-- Upcoming Appointments -->
    <div class="card-gradient-green rounded-2xl p-6 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-white bg-opacity-50 rounded-full p-3">
                <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <div class="text-sm font-medium text-gray-700 mb-1">Upcoming Appointments</div>
        <div class="text-3xl font-bold text-gray-900"><?php echo $upcoming; ?></div>
    </div>

    <!-- Completed Appointments -->
    <div class="card-gradient-blue-green rounded-2xl p-6 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-white bg-opacity-50 rounded-full p-3">
                <svg class="w-6 h-6 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <div class="text-sm font-medium text-gray-700 mb-1">Completed Appointments</div>
        <div class="text-3xl font-bold text-gray-900"><?php echo $allStats['effectues'] ?? 0; ?></div>
    </div>

    <!-- Cancelled Appointments -->
    <div class="card-gradient-pink rounded-2xl p-6 shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <div class="bg-white bg-opacity-50 rounded-full p-3">
                <svg class="w-6 h-6 text-pink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
        </div>
        <div class="text-sm font-medium text-gray-700 mb-1">Cancelled Appointments</div>
        <div class="text-3xl font-bold text-gray-900"><?php echo $allStats['annules'] ?? 0; ?></div>
    </div>
</div>

<!-- Appointments List Section -->
<div class="bg-white rounded-2xl shadow-lg p-6">
    <!-- Section Header with Filters -->
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Appointments List</h2>
        <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
            <!-- Date Filter -->
            <form method="get" action="index.php" class="flex items-center space-x-2">
                <input type="hidden" name="controller" value="soin">
                <input type="hidden" name="action" value="index">
                <input type="date" name="date" value="<?= htmlspecialchars($dateAffichage); ?>" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">Voir</button>
            </form>
            <!-- Action Button -->
            <?php if($userRole == 'admin' || $userRole == 'major'): ?>
            <a href="index.php?controller=soin&action=create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                Planifier un soin
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">#</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                        <div class="flex items-center">
                            Patient Name
                            <svg class="w-4 h-4 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                            </svg>
                        </div>
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Infirmier</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Type de soin</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Heure</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if(!empty($soinsArray)): ?>
                    <?php foreach($soinsArray as $i => $soin): ?>
                    <tr class="hover:bg-gray-50 transition-colors <?php echo ($i % 2 == 1) ? 'bg-gray-50' : ''; ?>">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $i + 1; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <a href="index.php?controller=patient&action=show&id=<?= $soin['patient_id']; ?>" class="text-blue-600 hover:text-blue-800 hover:underline">
                                <?= htmlspecialchars(($soin['patient_prenom'] ?? '') . ' ' . ($soin['patient_nom'] ?? '')); ?>
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <?= htmlspecialchars(($soin['infirmier_prenom'] ?? '') . ' ' . ($soin['infirmier_nom'] ?? '')); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($soin['type_soin'] ?? ''); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= date('d/m/Y', strtotime($soin['date_soin'])); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($soin['heure_soin'] ?? ''); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                                $status = $soin['statut'] ?? 'planifie';
                                $badgeClass = 'bg-yellow-100 text-yellow-800';
                                $statusText = 'UPCOMING';
                                if($status == 'effectue') { 
                                    $badgeClass = 'bg-green-100 text-green-800';
                                    $statusText = 'COMPLETED';
                                } elseif($status == 'annule') { 
                                    $badgeClass = 'bg-red-100 text-red-800';
                                    $statusText = 'CANCELLED';
                                } elseif($status == 'en_cours') {
                                    $badgeClass = 'bg-blue-100 text-blue-800';
                                    $statusText = 'IN PROGRESS';
                                }
                            ?>
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full <?= $badgeClass; ?>"><?= $statusText; ?></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center space-x-2">
                                <a href="index.php?controller=soin&action=show&id=<?= $soin['id']; ?>" class="text-blue-600 hover:text-blue-800 font-medium">View</a>
                                <?php if($userRole == 'admin'): ?>
                                <button onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce soin?')) window.location.href='index.php?controller=soin&action=delete&id=<?= $soin['id']; ?>'" class="p-1 hover:bg-gray-100 rounded text-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p>Aucun soin trouvé pour cette date.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination (si nécessaire) -->
    <?php if(!empty($soinsArray) && count($soinsArray) > 10): ?>
    <div class="mt-6 flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Showing <span class="font-medium">1</span> to <span class="font-medium"><?= min(10, count($soinsArray)); ?></span> of <span class="font-medium"><?= count($soinsArray); ?></span> entries
        </div>
        <div class="flex items-center space-x-2">
            <button class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Previous</button>
            <button class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">1</button>
            <button class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Next</button>
        </div>
    </div>
    <?php endif; ?>
</div>
