<?php
if(session_id() == '') {
    session_start();
}

// Rôle de l'utilisateur connecté
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
?>

<!-- Messages de notification -->
<?php if(isset($_SESSION['success'])): ?>
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between animate-fade-in">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-green-800"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center justify-between animate-fade-in">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span class="text-red-800"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
<?php endif; ?>

<!-- Informations personnelles du patient -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
    <!-- En-tête avec nom du patient -->
    <div class="bg-gradient-to-r from-blue-400 to-blue-600 px-6 py-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-white mb-2 flex items-center">
                    <svg class="w-8 h-8 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="mr-2">Dossier médical de</span>
                    <?php 
                    $civilite = '';
                    if(isset($patient['sexe'])) {
                        $civilite = ($patient['sexe'] == 'M') ? 'M. ' : 'Mme ';
                    }
                    echo '<span class="text-white opacity-95">' . htmlspecialchars($civilite . $patient['prenom'] . ' ' . $patient['nom']) . '</span>'; 
                    ?>
                </h2>
                <p class="text-white/80 flex items-center mt-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Inscrit le <?php echo isset($patient['created_at']) ? date('d/m/Y', strtotime($patient['created_at'])) : date('d/m/Y'); ?>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Informations détaillées -->
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="border-b md:border-b-0 md:border-r border-gray-200 pb-4 md:pb-0 md:pr-4">
                <span class="text-sm font-semibold text-gray-700 block mb-1">Né le :</span>
                <span class="text-gray-900 font-medium"><?php echo date('d/m/Y', strtotime($patient['date_naissance'])); ?></span>
            </div>
            <div class="border-b md:border-b-0 md:border-r border-gray-200 pb-4 md:pb-0 md:pr-4">
                <span class="text-sm font-semibold text-gray-700 block mb-1">Âge :</span>
                <span class="text-gray-900 font-medium">
                    <?php 
                        $birthDate = new DateTime($patient['date_naissance']);
                        $today = new DateTime();
                        $age = $birthDate->diff($today)->y;
                        echo $age . ' ans';
                    ?>
                </span>
            </div>
            <div class="border-b md:border-b-0 md:border-r border-gray-200 pb-4 md:pb-0 md:pr-4">
                <span class="text-sm font-semibold text-gray-700 block mb-1">Téléphone :</span>
                <span class="text-gray-900 font-medium">
                    <?php if(!empty($patient['telephone'])): ?>
                        <a href="tel:<?php echo $patient['telephone']; ?>" class="text-blue-600 hover:text-blue-800 hover:underline">
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
                        <span class="text-gray-500">Non renseigné</span>
                    <?php endif; ?>
                </span>
            </div>
            <div class="pb-4 md:pb-0">
                <span class="text-sm font-semibold text-gray-700 block mb-1">Adresse :</span>
                <span class="text-gray-900 font-medium">
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
                        <span class="text-gray-500">Non renseignée</span>
                    <?php endif; ?>
                </span>
            </div>
        </div>
        
        <!-- Groupe sanguin -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <span class="text-sm font-semibold text-gray-700 block mb-2">Groupe sanguin :</span>
            <?php if(!empty($patient['groupe_sanguin'])): ?>
                <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                    <?php echo htmlspecialchars($patient['groupe_sanguin']); ?>
                </span>
            <?php else: ?>
                <span class="text-gray-500">Non renseigné</span>
            <?php endif; ?>
        </div>
        
        <!-- Boutons d'action -->
        <div class="flex justify-end gap-3 mt-6">
            <?php if($userRole === 'medecin' || $userRole === 'major' || $userRole === 'admin'): ?>
            <a href="index.php?controller=patient&action=edit&id=<?php echo $patient['id']; ?>" 
               class="w-10 h-10 bg-gradient-to-r from-blue-400 to-blue-600 text-white rounded-lg flex items-center justify-center hover:from-blue-500 hover:to-blue-700 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
               title="Modifier le patient">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
            <?php endif; ?>
            
            <?php if($userRole === 'admin'): ?>
            <a href="index.php?controller=patient&action=delete&id=<?php echo $patient['id']; ?>" 
               class="w-10 h-10 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg flex items-center justify-center hover:from-red-600 hover:to-red-700 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce patient ?\nCette action est irréversible et supprimera tous ses antécédents.')"
               title="Supprimer le patient">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Antécédents médicaux -->
<div class="bg-white rounded-2xl shadow-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <div class="bg-gradient-to-r from-blue-400 to-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <span class="font-semibold uppercase text-sm tracking-wide">Antécédents médicaux</span>
        </div>
        
        <?php if($userRole === 'medecin' || $userRole === 'major' || $userRole === 'admin'): ?>
        <a href="index.php?controller=antecedent&action=create&patient_id=<?php echo $patient['id']; ?>" 
           class="w-10 h-10 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg flex items-center justify-center hover:from-green-600 hover:to-green-700 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
           title="Ajouter un antécédent">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
        </a>
        <?php endif; ?>
    </div>

    <?php if(isset($antecedents) && $antecedents->rowCount() > 0): ?>
        <div class="space-y-4">
            <?php while($ant = $antecedents->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-blue-500 shadow-sm hover:shadow-md transition-shadow">
                <!-- En-tête de l'antécédent -->
                <div class="flex justify-between items-start mb-4 pb-4 border-b border-gray-200">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="text-blue-600 font-bold flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <?php echo date('d/m/Y', strtotime($ant['date_consultation'])); ?>
                        </span>
                        <span class="text-gray-700 font-semibold flex items-center">
                            <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <?php echo htmlspecialchars($ant['motif_consultation']); ?>
                        </span>
                    </div>
                    
                    <?php if($userRole === 'medecin' || $userRole === 'major' || $userRole === 'admin'): ?>
                    <a href="index.php?controller=antecedent&action=resultat&id=<?php echo $ant['id']; ?>&patient_id=<?php echo $patient['id']; ?>" 
                       class="bg-gradient-to-r from-cyan-500 to-cyan-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:from-cyan-600 hover:to-cyan-700 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Résultats
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Historique de la maladie -->
                <?php if(!empty($ant['historique_maladie'])): ?>
                <div class="mb-4 p-3 bg-white rounded-lg border border-gray-200">
                    <div class="text-sm font-bold text-gray-700 uppercase mb-2 flex items-center pb-2 border-b-2 border-blue-500">
                        <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Historique de la maladie
                    </div>
                    <div class="text-gray-800 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($ant['historique_maladie'])); ?></div>
                </div>
                <?php endif; ?>

                <!-- Antécédents personnels -->
                <?php if(!empty($ant['antecedents_medicaux']) || !empty($ant['antecedents_chirurgicaux']) || !empty($ant['antecedents_familiaux']) || !empty($ant['allergies'])): ?>
                <div class="mb-4 p-3 bg-white rounded-lg border border-gray-200">
                    <div class="text-sm font-bold text-gray-700 uppercase mb-3 flex items-center pb-2 border-b-2 border-blue-500">
                        <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Antécédents
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <?php if(!empty($ant['antecedents_medicaux'])): ?>
                        <div>
                            <span class="text-xs font-semibold text-gray-600 uppercase block mb-1">Médicaux :</span>
                            <span class="text-gray-800 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($ant['antecedents_medicaux'])); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($ant['antecedents_chirurgicaux'])): ?>
                        <div>
                            <span class="text-xs font-semibold text-gray-600 uppercase block mb-1">Chirurgicaux :</span>
                            <span class="text-gray-800 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($ant['antecedents_chirurgicaux'])); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($ant['antecedents_familiaux'])): ?>
                        <div>
                            <span class="text-xs font-semibold text-gray-600 uppercase block mb-1">Familiaux :</span>
                            <span class="text-gray-800 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($ant['antecedents_familiaux'])); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($ant['allergies'])): ?>
                        <div>
                            <span class="text-xs font-semibold text-gray-600 uppercase block mb-1">Allergies :</span>
                            <span class="text-gray-800 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($ant['allergies'])); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Examen clinique du jour -->
                <?php if(!empty($ant['ta']) || !empty($ant['fc']) || !empty($ant['temperature']) || !empty($ant['fr']) || !empty($ant['saturation']) || !empty($ant['poids'])): ?>
                <div class="mb-4 p-3 bg-white rounded-lg border border-gray-200">
                    <div class="text-sm font-bold text-gray-700 uppercase mb-3 flex items-center pb-2 border-b-2 border-blue-500">
                        <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Examen clinique du jour
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <?php if(!empty($ant['ta'])): ?>
                        <div>
                            <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-semibold mr-2">TA</span>
                            <span class="text-gray-800"><?php echo htmlspecialchars($ant['ta']); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($ant['fc'])): ?>
                        <div>
                            <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-semibold mr-2">FC</span>
                            <span class="text-gray-800"><?php echo htmlspecialchars($ant['fc']); ?> bpm</span>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($ant['temperature'])): ?>
                        <div>
                            <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-semibold mr-2">T°</span>
                            <span class="text-gray-800"><?php echo htmlspecialchars($ant['temperature']); ?> °C</span>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($ant['fr'])): ?>
                        <div>
                            <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-semibold mr-2">FR</span>
                            <span class="text-gray-800"><?php echo htmlspecialchars($ant['fr']); ?> /min</span>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($ant['saturation'])): ?>
                        <div>
                            <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-semibold mr-2">SpO2</span>
                            <span class="text-gray-800"><?php echo htmlspecialchars($ant['saturation']); ?>%</span>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($ant['poids'])): ?>
                        <div>
                            <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-semibold mr-2">Poids</span>
                            <span class="text-gray-800"><?php echo htmlspecialchars($ant['poids']); ?> kg</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Examen par appareil -->
                <?php if(!empty($ant['appareil_pleuro_pulmonaire']) || !empty($ant['appareil_cardio_vasculaire']) || !empty($ant['appareil_digestif']) || !empty($ant['appareil_locomoteur']) || !empty($ant['appareil_uro_genital']) || !empty($ant['autre_organe'])): ?>
                <div class="mb-4 p-3 bg-white rounded-lg border border-gray-200">
                    <div class="text-sm font-bold text-gray-700 uppercase mb-3 flex items-center pb-2 border-b-2 border-blue-500">
                        <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                        </svg>
                        Examen par appareil
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <?php if(!empty($ant['appareil_pleuro_pulmonaire'])): ?>
                        <div>
                            <span class="text-xs font-semibold text-gray-600 uppercase block mb-1">Pleuro-pulmonaire :</span>
                            <span class="text-gray-800 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($ant['appareil_pleuro_pulmonaire'])); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($ant['appareil_cardio_vasculaire'])): ?>
                        <div>
                            <span class="text-xs font-semibold text-gray-600 uppercase block mb-1">Cardio-vasculaire :</span>
                            <span class="text-gray-800 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($ant['appareil_cardio_vasculaire'])); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($ant['appareil_digestif'])): ?>
                        <div>
                            <span class="text-xs font-semibold text-gray-600 uppercase block mb-1">Digestif :</span>
                            <span class="text-gray-800 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($ant['appareil_digestif'])); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($ant['appareil_locomoteur'])): ?>
                        <div>
                            <span class="text-xs font-semibold text-gray-600 uppercase block mb-1">Locomoteur :</span>
                            <span class="text-gray-800 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($ant['appareil_locomoteur'])); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($ant['appareil_uro_genital'])): ?>
                        <div>
                            <span class="text-xs font-semibold text-gray-600 uppercase block mb-1">Uro-génital :</span>
                            <span class="text-gray-800 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($ant['appareil_uro_genital'])); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($ant['autre_organe'])): ?>
                        <div>
                            <span class="text-xs font-semibold text-gray-600 uppercase block mb-1">Autre :</span>
                            <span class="text-gray-800 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($ant['autre_organe'])); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Résumé syndromique et Diagnostic -->
                <?php if(!empty($ant['resume_syndromique']) || !empty($ant['diagnostic_presomption'])): ?>
                <div class="mb-4 p-3 bg-white rounded-lg border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php if(!empty($ant['resume_syndromique'])): ?>
                        <div>
                            <div class="text-sm font-bold text-gray-700 uppercase mb-2 flex items-center pb-2 border-b-2 border-blue-500">
                                <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Résumé syndromique
                            </div>
                            <div class="text-gray-800 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($ant['resume_syndromique'])); ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($ant['diagnostic_presomption'])): ?>
                        <div>
                            <div class="text-sm font-bold text-gray-700 uppercase mb-2 flex items-center pb-2 border-b-2 border-blue-500">
                                <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                Diagnostic présomption
                            </div>
                            <div class="text-gray-800 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($ant['diagnostic_presomption'])); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Examens complémentaires -->
                <?php if(!empty($ant['examen_complementaire'])): ?>
                <div class="mb-4 p-3 bg-white rounded-lg border border-gray-200">
                    <div class="text-sm font-bold text-gray-700 uppercase mb-2 flex items-center pb-2 border-b-2 border-blue-500">
                        <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Examens complémentaires
                    </div>
                    <div class="text-gray-800 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($ant['examen_complementaire'])); ?></div>
                </div>
                <?php endif; ?>

                <!-- Traitement symptomatique -->
                <?php if(!empty($ant['traitement_symptomatique'])): ?>
                <div class="mb-4 p-3 bg-white rounded-lg border border-gray-200">
                    <div class="text-sm font-bold text-gray-700 uppercase mb-2 flex items-center pb-2 border-b-2 border-blue-500">
                        <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                        Traitement symptomatique
                    </div>
                    <div class="text-gray-800 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($ant['traitement_symptomatique'])); ?></div>
                </div>
                <?php endif; ?>

                <!-- Résultats et traitements spécifiques -->
                <?php if(!empty($ant['resultat']) || !empty($ant['traitement_specifique'])): ?>
                <div class="mb-4 p-3 bg-white rounded-lg border border-gray-200">
                    <div class="text-sm font-bold text-gray-700 uppercase mb-3 flex items-center pb-2 border-b-2 border-blue-500">
                        <svg class="w-4 h-4 mr-1 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Résultats et traitements spécifiques
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php if(!empty($ant['resultat'])): ?>
                        <div>
                            <span class="text-xs font-semibold text-gray-600 uppercase block mb-2">Résultats d'examens :</span>
                            <div class="text-gray-800 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($ant['resultat'])); ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($ant['traitement_specifique'])): ?>
                        <div>
                            <span class="text-xs font-semibold text-gray-600 uppercase block mb-2">Traitements spécifiques :</span>
                            <div class="text-gray-800 whitespace-pre-line"><?php echo nl2br(htmlspecialchars($ant['traitement_specifique'])); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-xl font-light text-gray-600 mb-2">Aucun antécédent médical</h3>
            <?php if($userRole === 'medecin' || $userRole === 'major' || $userRole === 'admin'): ?>
                <p class="text-gray-500">Cliquez sur le bouton <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> pour en créer un.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    // Auto-fermeture des alertes après 5 secondes
    setTimeout(function() {
        document.querySelectorAll('.bg-green-50, .bg-red-50').forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        });
    }, 5000);
</script>
