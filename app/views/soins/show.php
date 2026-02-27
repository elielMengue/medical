<?php
if(session_id() == '') {
    session_start();
}

// Rôle de l'utilisateur connecté
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';

if(!isset($soin) || !$soin) {
    header('Location: index.php?controller=soin&action=index');
    exit();
}

// Helper function pour le statut
function getStatusBadge($status) {
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
        default: return $status;
    }
}
?>

<!-- Messages de notification -->
<?php if(isset($_SESSION['success'])): ?>
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
        <div class="flex items-center text-green-800">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    </div>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center justify-between">
        <div class="flex items-center text-red-800">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    </div>
<?php endif; ?>

<!-- Details Card -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Détails du soin</h2>
        <span class="px-4 py-2 inline-flex text-sm font-semibold rounded-full <?php echo getStatusBadge($soin['statut']); ?>">
            <?php echo getStatusLabel($soin['statut']); ?>
        </span>
    </div>

    <div class="space-y-4">
        <div class="flex items-start border-b border-gray-200 pb-4">
            <div class="w-48 flex-shrink-0">
                <span class="text-sm font-semibold text-gray-700 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Patient
                </span>
            </div>
            <div class="flex-1">
                <a href="index.php?controller=patient&action=show&id=<?php echo $soin['patient_id']; ?>" class="text-blue-600 hover:text-blue-800 font-semibold">
                    <?php echo htmlspecialchars($soin['patient_prenom'] . ' ' . strtoupper($soin['patient_nom'])); ?>
                </a>
            </div>
        </div>

        <div class="flex items-start border-b border-gray-200 pb-4">
            <div class="w-48 flex-shrink-0">
                <span class="text-sm font-semibold text-gray-700 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Infirmier(ère)
                </span>
            </div>
            <div class="flex-1 text-gray-900">
                <?php echo htmlspecialchars($soin['infirmier_prenom'] . ' ' . $soin['infirmier_nom']); ?>
            </div>
        </div>

        <div class="flex items-start border-b border-gray-200 pb-4">
            <div class="w-48 flex-shrink-0">
                <span class="text-sm font-semibold text-gray-700 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    Type de soin
                </span>
            </div>
            <div class="flex-1 text-gray-900 font-semibold">
                <?php echo htmlspecialchars($soin['type_soin']); ?>
            </div>
        </div>

        <?php if(!empty($soin['description'])): ?>
        <div class="flex items-start border-b border-gray-200 pb-4">
            <div class="w-48 flex-shrink-0">
                <span class="text-sm font-semibold text-gray-700 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Description
                </span>
            </div>
            <div class="flex-1 text-gray-700 whitespace-pre-line">
                <?php echo nl2br(htmlspecialchars($soin['description'])); ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="flex items-start border-b border-gray-200 pb-4">
            <div class="w-48 flex-shrink-0">
                <span class="text-sm font-semibold text-gray-700 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Date
                </span>
            </div>
            <div class="flex-1 text-gray-900">
                <?php echo date('d/m/Y', strtotime($soin['date_soin'])); ?>
            </div>
        </div>

        <div class="flex items-start border-b border-gray-200 pb-4">
            <div class="w-48 flex-shrink-0">
                <span class="text-sm font-semibold text-gray-700 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Heure
                </span>
            </div>
            <div class="flex-1 text-gray-900">
                <?php echo htmlspecialchars($soin['heure_soin']); ?>
            </div>
        </div>

        <?php if(!empty($soin['numero_lit'])): ?>
        <div class="flex items-start">
            <div class="w-48 flex-shrink-0">
                <span class="text-sm font-semibold text-gray-700 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Numéro de lit
                </span>
            </div>
            <div class="flex-1 text-gray-900">
                <?php echo htmlspecialchars($soin['numero_lit']); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Actions -->
<div class="flex justify-between items-center">
    <a href="index.php?controller=soin&action=index" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium flex items-center space-x-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Retour</span>
    </a>
    
    <div class="flex items-center space-x-3">
        <?php if(($userRole === 'major' || $userRole === 'admin') && $soin['statut'] !== 'effectue'): ?>
            <a href="index.php?controller=soin&action=edit&id=<?php echo $soin['id']; ?>" 
               class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors font-medium flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Modifier</span>
            </a>
        <?php endif; ?>
        
        <?php if($userRole === 'infirmier' && $soin['statut'] !== 'effectue'): ?>
            <a href="index.php?controller=soin&action=updateStatut&id=<?php echo $soin['id']; ?>&statut=effectue" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium flex items-center space-x-2"
               onclick="return confirm('Confirmer que ce soin a été effectué ?')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Marquer effectué</span>
            </a>
        <?php endif; ?>
        
        <?php if($userRole === 'admin'): ?>
            <a href="index.php?controller=soin&action=delete&id=<?php echo $soin['id']; ?>" 
               class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium flex items-center space-x-2"
               onclick="return confirm('Supprimer ce soin ?')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <span>Supprimer</span>
            </a>
        <?php endif; ?>
    </div>
</div>
