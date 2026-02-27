<?php
if(session_id() == '') {
    session_start();
}

// FORCER L'ADMIN À AVOIR LES DROITS
if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') {
    // Admin peut passer outre toutes les vérifications
} else {
    // Vérification normale pour les autres rôles
    $userRole = $_SESSION['user_role'];
    if($userRole != 'medecin' && $userRole != 'major') {
        $_SESSION['error'] = "Vous n'avez pas les droits pour créer un patient";
        header('Location: index.php?controller=patient&action=index');
        exit();
    }
}
?>

<!-- Form Card -->
<div class="bg-white rounded-2xl shadow-lg p-6 max-w-3xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Nouveau patient</h2>
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

    <form method="POST" action="index.php?controller=patient&action=store" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                    Nom <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nom" name="nom" required 
                       placeholder="Entrez le nom"
                       value="<?php echo isset($_SESSION['old_input']['nom']) ? htmlspecialchars(strtoupper($_SESSION['old_input']['nom'])) : ''; ?>"
                       oninput="this.value = this.value.toUpperCase()"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase">
            </div>
            
            <div>
                <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">
                    Prénom <span class="text-red-500">*</span>
                </label>
                <input type="text" id="prenom" name="prenom" required 
                       placeholder="Entrez le prénom"
                       value="<?php echo isset($_SESSION['old_input']['prenom']) ? htmlspecialchars(ucfirst(strtolower($_SESSION['old_input']['prenom']))) : ''; ?>"
                       oninput="this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase()"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>

        <div>
            <label for="sexe" class="block text-sm font-medium text-gray-700 mb-2">
                Sexe <span class="text-red-500">*</span>
            </label>
            <select id="sexe" name="sexe" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Sélectionner le sexe</option>
                <option value="M" <?php echo (isset($_SESSION['old_input']['sexe']) && $_SESSION['old_input']['sexe'] == 'M') ? 'selected' : ''; ?>>Masculin</option>
                <option value="F" <?php echo (isset($_SESSION['old_input']['sexe']) && $_SESSION['old_input']['sexe'] == 'F') ? 'selected' : ''; ?>>Féminin</option>
            </select>
        </div>

        <div>
            <label for="date_naissance" class="block text-sm font-medium text-gray-700 mb-2">
                Date de naissance <span class="text-red-500">*</span>
            </label>
            <input type="date" id="date_naissance" name="date_naissance" required
                   value="<?php echo isset($_SESSION['old_input']['date_naissance']) ? htmlspecialchars($_SESSION['old_input']['date_naissance']) : ''; ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div>
            <label for="adresse" class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
            <textarea id="adresse" name="adresse" rows="2" 
                      placeholder="Numéro, rue, code postal, ville..."
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?php echo isset($_SESSION['old_input']['adresse']) ? htmlspecialchars($_SESSION['old_input']['adresse']) : ''; ?></textarea>
            <p class="mt-1 text-sm text-gray-500">Optionnel</p>
        </div>

        <div>
            <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
            <input type="tel" id="telephone" name="telephone" 
                   placeholder="12 34 56 78"
                   pattern="[0-9]{8}" 
                   title="Le numéro doit contenir 8 chiffres"
                   maxlength="8"
                   value="<?php echo isset($_SESSION['old_input']['telephone']) ? htmlspecialchars($_SESSION['old_input']['telephone']) : ''; ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <p class="mt-1 text-sm text-gray-500">Optionnel - 8 chiffres</p>
        </div>

        <div>
            <label for="groupe_sanguin" class="block text-sm font-medium text-gray-700 mb-2">Groupe sanguin</label>
            <select id="groupe_sanguin" name="groupe_sanguin"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Sélectionner un groupe sanguin</option>
                <option value="A+" <?php echo (isset($_SESSION['old_input']['groupe_sanguin']) && $_SESSION['old_input']['groupe_sanguin'] == 'A+') ? 'selected' : ''; ?>>A+</option>
                <option value="A-" <?php echo (isset($_SESSION['old_input']['groupe_sanguin']) && $_SESSION['old_input']['groupe_sanguin'] == 'A-') ? 'selected' : ''; ?>>A-</option>
                <option value="B+" <?php echo (isset($_SESSION['old_input']['groupe_sanguin']) && $_SESSION['old_input']['groupe_sanguin'] == 'B+') ? 'selected' : ''; ?>>B+</option>
                <option value="B-" <?php echo (isset($_SESSION['old_input']['groupe_sanguin']) && $_SESSION['old_input']['groupe_sanguin'] == 'B-') ? 'selected' : ''; ?>>B-</option>
                <option value="AB+" <?php echo (isset($_SESSION['old_input']['groupe_sanguin']) && $_SESSION['old_input']['groupe_sanguin'] == 'AB+') ? 'selected' : ''; ?>>AB+</option>
                <option value="AB-" <?php echo (isset($_SESSION['old_input']['groupe_sanguin']) && $_SESSION['old_input']['groupe_sanguin'] == 'AB-') ? 'selected' : ''; ?>>AB-</option>
                <option value="O+" <?php echo (isset($_SESSION['old_input']['groupe_sanguin']) && $_SESSION['old_input']['groupe_sanguin'] == 'O+') ? 'selected' : ''; ?>>O+</option>
                <option value="O-" <?php echo (isset($_SESSION['old_input']['groupe_sanguin']) && $_SESSION['old_input']['groupe_sanguin'] == 'O-') ? 'selected' : ''; ?>>O-</option>
            </select>
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
            <a href="index.php?controller=patient&action=search" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Annuler</span>
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Enregistrer</span>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const telephoneInput = document.getElementById('telephone');
    if(telephoneInput) {
        telephoneInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }
});
</script>

<?php 
if(isset($_SESSION['old_input'])) {
    unset($_SESSION['old_input']);
}
?>
