<?php
// Démarrer la session pour les messages
if(session_id() == '') {
    session_start();
}

// Vérifier que l'utilisateur est admin
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php?controller=patient&action=index');
    exit();
}
?>
<!-- Form Card -->
<div class="bg-white rounded-2xl shadow-lg p-6 max-w-3xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Créer un utilisateur</h2>
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

    <form method="POST" action="index.php?controller=auth&action=register" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                <input type="text" id="nom" name="nom" required 
                       placeholder="Entrez le nom"
                       value="<?php echo isset($_SESSION['old_input']['nom']) ? htmlspecialchars(strtoupper($_SESSION['old_input']['nom'])) : ''; ?>"
                       oninput="this.value = this.value.toUpperCase()"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase">
            </div>
            
            <div>
                <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">Prénom</label>
                <input type="text" id="prenom" name="prenom" required 
                       placeholder="Entrez le prénom"
                       value="<?php echo isset($_SESSION['old_input']['prenom']) ? htmlspecialchars(ucfirst(strtolower($_SESSION['old_input']['prenom']))) : ''; ?>"
                       oninput="this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase()"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input type="email" id="email" name="email" required 
                   placeholder="exemple@medical.com"
                   value="<?php echo isset($_SESSION['old_input']['email']) ? htmlspecialchars($_SESSION['old_input']['email']) : ''; ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="matricule" class="block text-sm font-medium text-gray-700 mb-2">Matricule</label>
                <input type="text" id="matricule" name="matricule" required 
                       placeholder="Ex: EMP001"
                       value="<?php echo isset($_SESSION['old_input']['matricule']) ? htmlspecialchars($_SESSION['old_input']['matricule']) : ''; ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Rôle</label>
                <select id="role" name="role" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Choisir un rôle</option>
                    <option value="admin" <?php echo (isset($_SESSION['old_input']['role']) && $_SESSION['old_input']['role'] == 'admin') ? 'selected' : ''; ?>>Administrateur</option>
                    <option value="medecin" <?php echo (isset($_SESSION['old_input']['role']) && $_SESSION['old_input']['role'] == 'medecin') ? 'selected' : ''; ?>>Médecin</option>
                    <option value="major" <?php echo (isset($_SESSION['old_input']['role']) && $_SESSION['old_input']['role'] == 'major') ? 'selected' : ''; ?>>Major</option>
                    <option value="infirmier" <?php echo (isset($_SESSION['old_input']['role']) && $_SESSION['old_input']['role'] == 'infirmier') ? 'selected' : ''; ?>>Infirmier</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="service" class="block text-sm font-medium text-gray-700 mb-2">Service</label>
                <select id="service" name="service"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Sélectionner un service</option>
                    <option value="urgence medicale" <?php echo (isset($_SESSION['old_input']['service']) && $_SESSION['old_input']['service'] == 'urgence medicale') ? 'selected' : ''; ?>>Urgence médicale</option>
                    <option value="urgence chirurgicale" <?php echo (isset($_SESSION['old_input']['service']) && $_SESSION['old_input']['service'] == 'urgence chirurgicale') ? 'selected' : ''; ?>>Urgence chirurgicale</option>
                </select>
                <p class="mt-1 text-sm text-gray-500">Optionnel</p>
            </div>
            
            <div>
                <label for="centre" class="block text-sm font-medium text-gray-700 mb-2">Centre</label>
                <select id="centre" name="centre"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Sélectionner un centre</option>
                    <option value="amitié" <?php echo (isset($_SESSION['old_input']['centre']) && $_SESSION['old_input']['centre'] == 'amitié') ? 'selected' : ''; ?>>Amitié</option>
                    <option value="communautaire" <?php echo (isset($_SESSION['old_input']['centre']) && $_SESSION['old_input']['centre'] == 'communautaire') ? 'selected' : ''; ?>>Communautaire</option>
                </select>
                <p class="mt-1 text-sm text-gray-500">Optionnel</p>
            </div>
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
            <p class="mt-1 text-sm text-gray-500">Optionnel</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Mot de passe</label>
                <input type="password" id="password" name="password" required 
                       placeholder="••••••••"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirmer</label>
                <input type="password" id="confirm_password" name="confirm_password" required 
                       placeholder="••••••••"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>

        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
            <a href="index.php?controller=admin&action=users" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium flex items-center space-x-2">
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

    const form = document.querySelector('form');
    if(form) {
        form.addEventListener('submit', function(e) {
            var password = document.getElementById('password').value;
            var confirm = document.getElementById('confirm_password').value;
            
            if(password !== confirm) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas !');
            }
        });
    }
});
</script>

<?php 
if(isset($_SESSION['old_input'])) {
    unset($_SESSION['old_input']);
}
?>
