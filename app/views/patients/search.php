<?php
// Démarrer la session pour les messages
if(session_id() == '') {
    session_start();
}

// Rôle de l'utilisateur connecté
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';

// Vérifier si des critères ont été soumis
$hasCriteria = isset($_GET['nom']) || isset($_GET['prenom']) || isset($_GET['date_naissance']);

// Vérifier si $patients est un objet valide
$hasResults = isset($patients) && is_object($patients);

// Préparer les résultats
$patientsArray = array();
$patientsCount = 0;
if($hasResults && $patients) {
    $patientsCount = $patients->rowCount();
    while($row = $patients->fetch(\PDO::FETCH_ASSOC)) {
        $patientsArray[] = $row;
    }
}
?>

<!-- Search Form Card -->
<div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Recherche de patients</h2>
    
    <form action="index.php" method="GET" class="space-y-4">
        <input type="hidden" name="controller" value="patient">
        <input type="hidden" name="action" value="search">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                <input type="text" name="nom" 
                       value="<?php echo isset($_GET['nom']) ? htmlspecialchars($_GET['nom']) : ''; ?>"
                       placeholder="Ex: DUPONT"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Prénom</label>
                <input type="text" name="prenom" 
                       value="<?php echo isset($_GET['prenom']) ? htmlspecialchars($_GET['prenom']) : ''; ?>"
                       placeholder="Ex: Jean"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date de naissance</label>
                <input type="date" name="date_naissance" 
                       value="<?php echo isset($_GET['date_naissance']) ? htmlspecialchars($_GET['date_naissance']) : ''; ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span>Rechercher</span>
            </button>
        </div>
    </form>
</div>

<!-- Results Card -->
<div class="bg-white rounded-2xl shadow-lg overflow-hidden">
    <?php if($hasCriteria): ?>
        <?php if($hasResults && $patientsCount > 0): ?>
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">
                    Résultats (<?php echo $patientsCount; ?> patient(s) trouvé(s))
                </h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">#</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">NOM</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">PRÉNOM</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">SEXE</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">NÉ LE</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ÂGE</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">TÉLÉPHONE</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php 
                        $i = 1;
                        foreach($patientsArray as $row): 
                            // Calcul de l'âge
                            $age = 'N/A';
                            if(!empty($row['date_naissance'])) {
                                try {
                                    $birthDate = new DateTime($row['date_naissance']);
                                    $today = new DateTime();
                                    $age = $birthDate->diff($today)->y;
                                } catch(Exception $e) {
                                    $age = 'Date invalide';
                                }
                            }
                            
                            // Formatage du téléphone
                            $tel = !empty($row['telephone']) ? $row['telephone'] : '';
                            $telFormate = '';
                            if(strlen($tel) == 8) {
                                $telFormate = substr($tel, 0, 2) . ' ' . 
                                             substr($tel, 2, 2) . ' ' . 
                                             substr($tel, 4, 2) . ' ' . 
                                             substr($tel, 6, 2);
                            } else {
                                $telFormate = $tel;
                            }
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $i++; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900"><?php echo htmlspecialchars(strtoupper($row['nom'])); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars(ucfirst(strtolower($row['prenom']))); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php 
                                if(!empty($row['sexe'])) {
                                    echo ($row['sexe'] == 'M') ? 'M' : 'F';
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo !empty($row['date_naissance']) ? date('d/m/Y', strtotime($row['date_naissance'])) : '-'; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?php echo $age; ?> ans
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?php if(!empty($row['telephone'])): ?>
                                    <?php echo $telFormate; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center space-x-2">
                                    <a href="index.php?controller=patient&action=show&id=<?php echo $row['id']; ?>" 
                                       class="text-blue-600 hover:text-blue-800 font-medium" 
                                       title="Voir dossier médical">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    
                                    <?php if($userRole === 'admin' || $userRole === 'medecin' || $userRole === 'major'): ?>
                                    <a href="index.php?controller=patient&action=edit&id=<?php echo $row['id']; ?>" 
                                       class="text-yellow-600 hover:text-yellow-800" 
                                       title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
        <?php elseif($hasResults && $patientsCount == 0): ?>
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Aucun résultat</h3>
                <p class="text-gray-500">Aucun patient ne correspond à vos critères de recherche.</p>
            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Recherchez des patients</h3>
            <p class="text-gray-500">Utilisez le formulaire ci-dessus pour rechercher des patients.</p>
        </div>
    <?php endif; ?>
</div>
