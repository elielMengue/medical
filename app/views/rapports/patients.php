<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="bi bi-people-fill me-2"></i>
                Rapport des patients
            </h4>
            <a href="index.php?controller=rapport&action=index" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Sexe</th>
                            <th>Date naissance</th>
                            <th>Téléphone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($patients as $patient): ?>
                        <tr>
                            <td><?php echo $patient['id']; ?></td>
                            <td><?php echo htmlspecialchars($patient['nom']); ?></td>
                            <td><?php echo htmlspecialchars($patient['prenom']); ?></td>
                            <td><?php echo isset($patient['sexe']) ? ($patient['sexe'] == 'M' ? 'M' : 'F') : '-'; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($patient['date_naissance'])); ?></td>
                            <td><?php echo $patient['telephone'] ?? '-'; ?></td>
                            <td>
                                <a href="index.php?controller=rapport&action=antecedents&patient_id=<?php echo $patient['id']; ?>" 
                                   class="btn btn-sm btn-info">
                                    Antécédents
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>