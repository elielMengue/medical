<?php require_once dirname(__DIR__) . '/partials/dashboard_header.php'; ?>

<div class="container-fluid">
    <!-- En-tête du calendrier -->
    <div class="calendar-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="display-5 fw-bold gradient-text">
                    <i class="bi bi-calendar3 me-3"></i>
                    Calendrier Médical
                </h1>
                <p class="lead text-muted mb-0">
                    Gérez les rendez-vous et les soins planifiés
                </p>
            </div>
            <div class="col-auto">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary" id="prevMonth">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button type="button" class="btn btn-primary" id="currentMonth">
                        <i class="bi bi-calendar-event me-2"></i>
                        Aujourd'hui
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="nextMonth">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Vue du calendrier -->
    <div class="calendar-container">
        <div class="row">
            <!-- Calendrier -->
            <div class="col-lg-8">
                <div class="calendar-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-calendar-week me-2"></i>
                            <span id="calendarMonth"></span>
                        </h5>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary active" id="monthView">
                                Mois
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="weekView">
                                Semaine
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="dayView">
                                Jour
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>

            <!-- Panneau latéral -->
            <div class="col-lg-4">
                <!-- Statistiques rapides -->
                <div class="stats-card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-graph-up me-2"></i>
                            Statistiques du mois
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="stat-item">
                                    <div class="stat-number text-primary"><?php echo $stats['total_rendezvous'] ?? 0; ?></div>
                                    <div class="stat-label">Rendez-vous</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <div class="stat-number text-success"><?php echo $stats['rendezvous_confirmes'] ?? 0; ?></div>
                                    <div class="stat-label">Confirmés</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <div class="stat-number text-warning"><?php echo $stats['rendezvous_en_attente'] ?? 0; ?></div>
                                    <div class="stat-label">En attente</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <div class="stat-number text-info"><?php echo $stats['soins_planifies'] ?? 0; ?></div>
                                    <div class="stat-label">Soins</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Prochains rendez-vous -->
                <div class="upcoming-card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-clock me-2"></i>
                            Prochains rendez-vous
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <?php if(!empty($upcomingAppointments)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach($upcomingAppointments as $appointment): ?>
                            <div class="list-group-item list-group-item-action border-0 border-bottom">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($appointment['patient_nom'] . ' ' . $appointment['patient_prenom']); ?></h6>
                                    <small class="text-muted"><?php echo date('H:i', strtotime($appointment['date_heure'])); ?></small>
                                </div>
                                <p class="mb-1 small text-muted"><?php echo htmlspecialchars($appointment['motif']); ?></p>
                                <small class="badge bg-<?php echo $appointment['statut'] === 'confirmé' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($appointment['statut']); ?>
                                </small>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x display-4 text-muted"></i>
                            <p class="text-muted mt-2">Aucun rendez-vous prévu</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="quick-actions-card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-lightning me-2"></i>
                            Actions rapides
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary hover-lift" data-bs-toggle="modal" data-bs-target="#newAppointmentModal">
                                <i class="bi bi-plus-circle me-2"></i>
                                Nouveau rendez-vous
                            </button>
                            <button type="button" class="btn btn-outline-primary hover-lift" onclick="location.href='/projets/projet_medical/app/public/index.php?controller=soin&action=create'">
                                <i class="bi bi-heart-pulse me-2"></i>
                                Planifier un soin
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal nouveau rendez-vous -->
<div class="modal fade" id="newAppointmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-calendar-plus me-2"></i>
                    Nouveau rendez-vous
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="newAppointmentForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="patient_id" class="form-label">Patient</label>
                        <select class="form-select" id="patient_id" required>
                            <option value="">Sélectionner un patient</option>
                            <?php foreach($patients as $patient): ?>
                            <option value="<?php echo $patient['id']; ?>">
                                <?php echo htmlspecialchars($patient['prenom'] . ' ' . $patient['nom']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="date_heure" class="form-label">Date et heure</label>
                        <input type="datetime-local" class="form-control" id="date_heure" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="motif" class="form-label">Motif</label>
                        <textarea class="form-control" id="motif" rows="3" placeholder="Raison du rendez-vous..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="duree" class="form-label">Durée (minutes)</label>
                        <select class="form-select" id="duree">
                            <option value="30">30 minutes</option>
                            <option value="60">1 heure</option>
                            <option value="90">1h30</option>
                            <option value="120">2 heures</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>
                        Créer le rendez-vous
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
// Données du calendrier
const appointments = <?php echo json_encode($appointments ?? []); ?>;
const soins = <?php echo json_encode($soins ?? []); ?>;

// Configuration du calendrier
let calendar;

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: false,
        locale: 'fr',
        firstDay: 1,
        height: 'auto',
        events: function(fetchInfo, successCallback, failureCallback) {
            // Charger les événements via AJAX
            fetch('/projets/projet_medical/app/public/index.php?controller=calendar&action=getEvents')
                .then(response => response.json())
                .then(events => {
                    successCallback(events);
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des événements:', error);
                    failureCallback(error);
                });
        },
        eventColor: '#3788d8',
        eventTextColor: '#ffffff',
        eventClick: function(info) {
            showEventDetails(info.event);
        },
        dateClick: function(info) {
            createAppointment(info.date);
        }
    });
    
    calendar.render();
    updateCalendarTitle();
});

// Navigation dans le calendrier
document.getElementById('prevMonth').addEventListener('click', function() {
    calendar.prev();
    updateCalendarTitle();
});

document.getElementById('nextMonth').addEventListener('click', function() {
    calendar.next();
    updateCalendarTitle();
});

document.getElementById('currentMonth').addEventListener('click', function() {
    calendar.today();
    updateCalendarTitle();
});

// Changement de vue
document.getElementById('monthView').addEventListener('click', function() {
    calendar.changeView('dayGridMonth');
    updateViewButtons('monthView');
});

document.getElementById('weekView').addEventListener('click', function() {
    calendar.changeView('timeGridWeek');
    updateViewButtons('weekView');
});

document.getElementById('dayView').addEventListener('click', function() {
    calendar.changeView('timeGridDay');
    updateViewButtons('dayView');
});

// Fonctions utilitaires
function updateCalendarTitle() {
    const title = calendar.currentData.viewTitle;
    document.getElementById('calendarMonth').textContent = title;
}

function updateViewButtons(activeId) {
    document.querySelectorAll('[id$="View"]').forEach(btn => {
        btn.classList.remove('active');
    });
    document.getElementById(activeId).classList.add('active');
}

function showEventDetails(event) {
    showNotification(`Détails: ${event.title}`, 'info');
}

function createAppointment(date) {
    const modal = new bootstrap.Modal(document.getElementById('newAppointmentModal'));
    document.getElementById('date_heure').value = date.toISOString().slice(0, 16);
    modal.show();
}

// Formulaire de nouveau rendez-vous
document.getElementById('newAppointmentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const data = {
        patient_id: document.getElementById('patient_id').value,
        date_heure: document.getElementById('date_heure').value,
        motif: document.getElementById('motif').value || 'Consultation',
        duree: document.getElementById('duree').value || '30'
    };
    
    // Appel AJAX pour créer le rendez-vous
    fetch('/projets/projet_medical/app/public/index.php?controller=calendar&action=createAppointment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if(result.success) {
            showNotification('Rendez-vous créé avec succès!', 'success');
            // Recharger le calendrier pour afficher le nouveau rendez-vous
            calendar.refetchEvents();
            // Recharger aussi la liste des prochains rendez-vous
            location.reload();
        } else {
            showNotification('Erreur: ' + (result.error || 'Impossible de créer le rendez-vous'), 'danger');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Erreur de connexion au serveur', 'danger');
    })
    .finally(() => {
        bootstrap.Modal.getInstance(document.getElementById('newAppointmentModal')).hide();
        this.reset();
    });
});

// Fonction pour afficher les notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>

<style>
/* Styles pour le calendrier */
.calendar-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 20px;
    margin-bottom: 2rem;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.calendar-card, .stats-card, .upcoming-card, .quick-actions-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    background: white;
    overflow: hidden;
}

.calendar-card .card-header, .stats-card .card-header, 
.upcoming-card .card-header, .quick-actions-card .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
    padding: 1rem 1.5rem;
}

#calendar {
    padding: 1rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    border-radius: 10px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transition: transform 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-2px);
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    font-weight: 500;
}

.hover-lift {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.calendar-container {
    animation: fadeIn 0.6s ease-out;
}

/* Style pour FullCalendar */
.fc-theme-standard td, .fc-theme-standard th {
    border-color: #e9ecef;
}

.fc-daygrid-day-number {
    color: #495057;
    font-weight: 500;
}

.fc-day-today {
    background-color: rgba(102, 126, 234, 0.1) !important;
}

.fc-event {
    border-radius: 4px;
    padding: 2px 4px;
    font-size: 0.875rem;
}
</style>

<?php require_once dirname(__DIR__) . '/partials/dashboard_footer.php'; ?>