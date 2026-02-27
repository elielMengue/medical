<?php
// Inclusion des en-têtes
require_once dirname(__DIR__) . '/partials/dashboard_header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-calendar-plus me-2"></i>
                        Planifier un soin - Accès Public
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="/projets/projet_medical/app/public/index.php?controller=soin&action=storePublic" class="needs-validation" novalidate>
                        
                        <!-- Sélection du patient -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="patient_id" class="form-label fw-bold">
                                    <i class="bi bi-person-heart me-1"></i> Patient
                                </label>
                                <select class="form-select" id="patient_id" name="patient_id" required onchange="if(this.value) window.location.href='/projets/projet_medical/app/public/index.php?controller=soin&action=createPublic&patient_id='+this.value;">
                                    <option value="">-- Sélectionner un patient --</option>
                                    <?php foreach($patients as $patient): ?>
                                        <option value="<?php echo $patient['id']; ?>" 
                                                <?php echo (isset($_GET['patient_id']) && $_GET['patient_id'] == $patient['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($patient['prenom'] . ' ' . $patient['nom'] . ' - ' . $patient['telephone']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Veuillez sélectionner un patient.
                                </div>
                            </div>
                        </div>

                        <?php if(isset($patient) && $patient): ?>
                            <!-- Informations du patient sélectionné -->
                            <div class="alert alert-info mb-4">
                                <h6><i class="bi bi-info-circle me-1"></i> Patient sélectionné :</h6>
                                <p class="mb-1"><strong>Nom :</strong> <?php echo htmlspecialchars($patient['prenom'] . ' ' . $patient['nom']); ?></p>
                                <p class="mb-0"><strong>Téléphone :</strong> <?php echo htmlspecialchars($patient['telephone']); ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Type de soin -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="type_soin" class="form-label fw-bold">
                                    <i class="bi bi-heart-pulse me-1"></i> Type de soin
                                </label>
                                <select class="form-select" id="type_soin" name="type_soin" required>
                                    <option value="">-- Sélectionner un type --</option>
                                    <option value="injection">Injection</option>
                                    <option value="perfusion">Perfusion</option>
                                    <option value="pansement">Pansement</option>
                                    <option value="prelevement">Prélèvement</option>
                                    <option value="surveillance">Surveillance</option>
                                    <option value="medication">Médication</option>
                                    <option value="autre">Autre</option>
                                </select>
                                <div class="invalid-feedback">
                                    Veuillez sélectionner un type de soin.
                                </div>
                            </div>

                            <!-- Date et heure -->
                            <div class="col-md-6">
                                <label for="date_heure" class="form-label fw-bold">
                                    <i class="bi bi-calendar-event me-1"></i> Date et heure
                                </label>
                                <input type="datetime-local" class="form-control" id="date_heure" name="date_heure" 
                                       value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                                <div class="invalid-feedback">
                                    Veuillez sélectionner une date et une heure.
                                </div>
                            </div>
                        </div>

                        <!-- Infirmier -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="infirmier_id" class="form-label fw-bold">
                                    <i class="bi bi-person-badge me-1"></i> Infirmier(ère) assigné(e)
                                </label>
                                <select class="form-select" id="infirmier_id" name="infirmier_id" required>
                                    <option value="">-- Sélectionner un infirmier --</option>
                                    <?php foreach($infirmiers as $infirmier): ?>
                                        <option value="<?php echo $infirmier['id']; ?>">
                                            <?php echo htmlspecialchars($infirmier['prenom'] . ' ' . $infirmier['nom']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Veuillez sélectionner un infirmier.
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="description" class="form-label fw-bold">
                                    <i class="bi bi-clipboard-heart me-1"></i> Description détaillée
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="4" 
                                          placeholder="Décrivez le soin à administrer..." required></textarea>
                                <div class="invalid-feedback">
                                    Veuillez fournir une description détaillée.
                                </div>
                            </div>
                        </div>

                        <!-- Statut (caché - par défaut "planifié") -->
                        <input type="hidden" name="statut" value="planifié">

                        <!-- Boutons -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <a href="/projets/projet_medical/app/public/index.php?controller=soin&action=index" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-1"></i>
                                        Retour au planning
                                    </a>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-calendar-check me-1"></i>
                                        Planifier le soin
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validation Bootstrap
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>

<?php
// Inclusion des pieds de page
require_once dirname(__DIR__) . '/partials/dashboard_footer.php';
?>