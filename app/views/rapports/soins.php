<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="bi bi-calendar-heart-fill me-2"></i>
                Rapport des soins
            </h4>
            <a href="index.php?controller=rapport&action=index" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <input type="hidden" name="controller" value="rapport">
                <input type="hidden" name="action" value="soins">
                
                <div class="col-md-4">
                    <label class="form-label">Date début</label>
                    <input type="date" class="form-control" name="date_debut" value="<?php echo $date_debut; ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date fin</label>
                    <input type="date" class="form-control" name="date_fin" value="<?php echo $date_fin; ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block">Filtrer</button>
                </div>
            </form>
            
            <?php if(!empty($stats)): ?>
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5>Total soins</h5>
                            <h3><?php echo $stats['total']; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h5>Planifiés</h5>
                            <h3><?php echo $stats['planifies']; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h5>En cours</h5>
                            <h3><?php echo $stats['en_cours']; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h5>Effectués</h5>
                            <h3><?php echo $stats['effectues']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Heure</th>
                            <th>Patient</th>
                            <th>Infirmier</th>
                            <th>Type</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($soins as $soin): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($soin['date_soin'])); ?></td>
                            <td><?php echo $soin['heure_soin']; ?></td>
                            <td><?php echo htmlspecialchars($soin['patient_prenom'] . ' ' . $soin['patient_nom']); ?></td>
                            <td><?php echo htmlspecialchars($soin['infirmier_prenom'] . ' ' . $soin['infirmier_nom']); ?></td>
                            <td><?php echo htmlspecialchars($soin['type_soin']); ?></td>
                            <td>
                                <?php 
                                $badge = '';
                                switch($soin['statut']) {
                                    case 'planifie': $badge = 'bg-warning'; break;
                                    case 'en_cours': $badge = 'bg-info'; break;
                                    case 'effectue': $badge = 'bg-success'; break;
                                    case 'annule': $badge = 'bg-danger'; break;
                                    default: $badge = 'bg-secondary';
                                }
                                ?>
                                <span class="badge <?php echo $badge; ?>"><?php echo $soin['statut']; ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>