<?php
if(session_id() == '') {
    session_start();
}

// Vérifier que l'utilisateur a le droit de créer (Major ou Admin)
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
if($userRole != 'major' && $userRole != 'admin') {
    $_SESSION['error'] = "Vous n'avez pas les droits pour planifier des soins";
    header('Location: index.php?controller=soin&action=index');
    exit();
}
?>
<!-- Form Card -->
<div class="bg-white rounded-2xl shadow-lg p-6 max-w-3xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Planifier un soin</h2>
        <p class="text-gray-600">Remplissez les informations ci-dessous</p>
    </div>
    
    <?php if(isset($_SESSION['errors']) && is_array($_SESSION['errors'])): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <h5 class="text-red-800 font-semibold mb-2 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Erreurs :
            </h5>
            <ul class="list-disc list-inside text-red-700 space-y-1">
                <?php foreach($_SESSION['errors'] as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <form method="POST" action="index.php?controller=soin&action=store" class="space-y-6">
        <div>
            <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-2">
                Patient <span class="text-red-500">*</span>
            </label>
            <select id="patient_id" name="patient_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Sélectionner un patient</option>
                <?php if(isset($patients) && is_array($patients)): ?>
                    <?php foreach($patients as $patient): ?>
                        <option value="<?php echo $patient['id']; ?>" 
                            <?php echo (isset($_SESSION['old_input']['patient_id']) && $_SESSION['old_input']['patient_id'] == $patient['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($patient['nom'] . ' ' . $patient['prenom']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div>
            <label for="infirmier_id" class="block text-sm font-medium text-gray-700 mb-2">
                Infirmier(ère) <span class="text-red-500">*</span>
            </label>
            <select id="infirmier_id" name="infirmier_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Sélectionner un infirmier</option>
                <?php if(isset($infirmiers) && is_array($infirmiers)): ?>
                    <?php foreach($infirmiers as $infirmier): ?>
                        <option value="<?php echo $infirmier['id']; ?>"
                            <?php echo (isset($_SESSION['old_input']['infirmier_id']) && $_SESSION['old_input']['infirmier_id'] == $infirmier['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($infirmier['prenom'] . ' ' . $infirmier['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div>
            <label for="type_soin" class="block text-sm font-medium text-gray-700 mb-2">
                Type de soin <span class="text-red-500">*</span>
            </label>
            <select id="type_soin" name="type_soin" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Choisir un type...</option>
                <option value="Prise de sang">Prise de sang</option>
                <option value="Pansement">Pansement</option>
                <option value="Injection">Injection</option>
                <option value="Perfusion">Perfusion</option>
                <option value="Constantes">Prise des constantes</option>
                <option value="Toilette">Toilette</option>
                <option value="Levé">Aide au levé</option>
                <option value="Repas">Aide à la prise de repas</option>
                <option value="Médicament">Administration médicament</option>
                <option value="Rééducation">Rééducation</option>
                <option value="Autre">Autre</option>
            </select>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description / Instructions</label>
            <textarea id="description" name="description" rows="3" 
                      placeholder="Indications particulières..."
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?php echo isset($_SESSION['old_input']['description']) ? htmlspecialchars($_SESSION['old_input']['description']) : ''; ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="date_soin" class="block text-sm font-medium text-gray-700 mb-2">
                    Date <span class="text-red-500">*</span>
                </label>
                <input type="date" id="date_soin" name="date_soin" 
                       value="<?php echo isset($_SESSION['old_input']['date_soin']) ? $_SESSION['old_input']['date_soin'] : date('Y-m-d'); ?>" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label for="heure_soin" class="block text-sm font-medium text-gray-700 mb-2">
                    Heure <span class="text-red-500">*</span>
                </label>
                <input type="time" id="heure_soin" name="heure_soin" 
                       value="<?php echo isset($_SESSION['old_input']['heure_soin']) ? $_SESSION['old_input']['heure_soin'] : date('H:i'); ?>" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>

        <div>
            <label for="numero_lit" class="block text-sm font-medium text-gray-700 mb-2">Numéro de lit</label>
            <input type="text" id="numero_lit" name="numero_lit" 
                   placeholder="Ex: A12, B7, ..."
                   value="<?php echo isset($_SESSION['old_input']['numero_lit']) ? htmlspecialchars($_SESSION['old_input']['numero_lit']) : ''; ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <p class="mt-1 text-sm text-gray-500">Optionnel</p>
        </div>

        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Les champs marqués d'un <span class="text-red-500 font-semibold">*</span> sont obligatoires.
            </p>
        </div>

        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
            <a href="index.php?controller=soin&action=index" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Annuler</span>
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Planifier</span>
            </button>
        </div>
    </form>
</div>

<?php 
if(isset($_SESSION['old_input'])) {
    unset($_SESSION['old_input']);
}
?>
