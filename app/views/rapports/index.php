<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-file-bar-graph me-2"></i>
                        Tableau de bord des rapports
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card text-white bg-info">
                                <div class="card-body">
                                    <h5 class="card-title">Total patients</h5>
                                    <h2><?php echo $total_patients; ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-white bg-success">
                                <div class="card-body">
                                    <h5 class="card-title">Total soins</h5>
                                    <h2><?php echo $total_soins; ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-white bg-warning">
                                <div class="card-body">
                                    <h5 class="card-title">Soins aujourd'hui</h5>
                                    <h2><?php echo $soins_aujourdhui; ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-white bg-danger">
                                <div class="card-body">
                                    <h5 class="card-title">Sans antécédents</h5>
                                    <h2><?php echo $patients_sans_antecedents; ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Rapports patients</h5>
                                </div>
                                <div class="card-body">
                                    <p>Consultez la liste complète des patients</p>
                                    <a href="index.php?controller=rapport&action=patients" class="btn btn-primary">
                                        Voir le rapport
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Rapports soins</h5>
                                </div>
                                <div class="card-body">
                                    <p>Analyse des soins par période</p>
                                    <a href="index.php?controller=rapport&action=soins" class="btn btn-primary">
                                        Voir le rapport
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>