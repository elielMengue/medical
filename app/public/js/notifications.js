/**
 * Système de notifications en temps réel
 */
class NotificationSystem {
    constructor() {
        this.notifications = [];
        this.unreadCount = 0;
        this.updateInterval = 30000; // 30 secondes
        this.init();
    }

    init() {
        this.createNotificationBell();
        this.loadNotifications();
        this.startAutoUpdate();
        this.bindEvents();
    }

    /**
     * Créer la cloche de notification dans la navbar
     */
    createNotificationBell() {
        const navbar = document.querySelector('.navbar-nav.ms-auto');
        if (!navbar) return;

        const bellHTML = `
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationBadge" style="display: none;">
                        <span id="unreadCount">0</span>
                        <span class="visually-hidden">notifications non lues</span>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3" style="width: 350px; max-height: 500px; overflow-y: auto;" aria-labelledby="notificationDropdown">
                    <div class="dropdown-header bg-primary text-white rounded-top-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Notifications</h6>
                            <button class="btn btn-sm btn-outline-light border-0" id="markAllRead" title="Tout marquer comme lu">
                                <i class="bi bi-check2-all"></i>
                            </button>
                        </div>
                    </div>
                    <div id="notificationList">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-footer text-center p-2 border-top">
                        <a href="#" class="text-decoration-none text-muted small" id="viewAllNotifications">
                            Voir toutes les notifications
                        </a>
                    </div>
                </div>
            </li>
        `;

        // Insérer avant le dernier élément (profil utilisateur)
        const lastItem = navbar.lastElementChild;
        lastItem.insertAdjacentHTML('beforebegin', bellHTML);
    }

    /**
     * Charger les notifications depuis le serveur
     */
    async loadNotifications() {
        try {
            const response = await fetch('index.php?controller=notification&action=getNotifications', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Erreur lors du chargement des notifications');
            }

            const data = await response.json();
            this.notifications = data.notifications || [];
            this.unreadCount = data.unread_count || 0;
            this.renderNotifications();
            this.updateBadge();

        } catch (error) {
            console.error('Erreur:', error);
            this.showError('Impossible de charger les notifications');
        }
    }

    /**
     * Afficher les notifications dans le dropdown
     */
    renderNotifications() {
        const notificationList = document.getElementById('notificationList');
        
        if (this.notifications.length === 0) {
            notificationList.innerHTML = `
                <div class="text-center py-4">
                    <i class="bi bi-bell-slash display-4 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">Aucune notification</p>
                </div>
            `;
            return;
        }

        const notificationsHTML = this.notifications.map(notification => `
            <div class="notification-item border-bottom p-3 ${!notification.is_read ? 'bg-light' : ''}" 
                 data-notification-id="${notification.id}">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0">
                        <div class="notification-icon bg-${notification.color}-subtle text-${notification.color} rounded-circle p-2">
                            <i class="bi ${notification.icon}"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="notification-title mb-1 ${notification.priority === 'high' ? 'text-danger' : ''}">
                                    ${notification.title}
                                    ${!notification.is_read ? '<span class="badge bg-danger ms-1">Nouveau</span>' : ''}
                                </h6>
                                <p class="notification-message text-muted mb-1 small">${notification.message}</p>
                                <small class="text-muted">${this.formatTime(notification.created_at)}</small>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-muted p-1" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="notificationSystem.markAsRead('${notification.id}')">
                                            <i class="bi bi-check2 me-2"></i>Marquer comme lu
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="#" onclick="notificationSystem.deleteNotification('${notification.id}')">
                                            <i class="bi bi-trash me-2"></i>Supprimer
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        notificationList.innerHTML = notificationsHTML;
    }

    /**
     * Mettre à jour le badge de notification
     */
    updateBadge() {
        const badge = document.getElementById('notificationBadge');
        const unreadCountElement = document.getElementById('unreadCount');
        
        if (this.unreadCount > 0) {
            badge.style.display = 'block';
            unreadCountElement.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
            
            // Animation de la cloche
            this.animateBell();
        } else {
            badge.style.display = 'none';
        }
    }

    /**
     * Animer la cloche lors de nouvelles notifications
     */
    animateBell() {
        const bell = document.querySelector('#notificationDropdown i.bi-bell');
        if (bell) {
            bell.style.animation = 'bell-ring 0.5s ease-in-out';
            setTimeout(() => {
                bell.style.animation = '';
            }, 500);
        }
    }

    /**
     * Marquer une notification comme lue
     */
    async markAsRead(notificationId) {
        try {
            const response = await fetch('index.php?controller=notification&action=markAsRead', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ notification_id: notificationId })
            });

            if (response.ok) {
                // Mettre à jour localement
                const notification = this.notifications.find(n => n.id === notificationId);
                if (notification) {
                    notification.is_read = true;
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                    this.renderNotifications();
                    this.updateBadge();
                }
            }
        } catch (error) {
            console.error('Erreur:', error);
        }
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    async markAllAsRead() {
        // Marquer toutes les notifications non lues comme lues
        const unreadNotifications = this.notifications.filter(n => !n.is_read);
        
        for (const notification of unreadNotifications) {
            await this.markAsRead(notification.id);
        }
    }

    /**
     * Supprimer une notification
     */
    async deleteNotification(notificationId) {
        try {
            const response = await fetch('index.php?controller=notification&action=deleteNotification', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ notification_id: notificationId })
            });

            if (response.ok) {
                // Supprimer localement
                this.notifications = this.notifications.filter(n => n.id !== notificationId);
                this.renderNotifications();
                this.updateBadge();
            }
        } catch (error) {
            console.error('Erreur:', error);
        }
    }

    /**
     * Démarrer la mise à jour automatique
     */
    startAutoUpdate() {
        setInterval(() => {
            this.loadNotifications();
        }, this.updateInterval);
    }

    /**
     * Lier les événements
     */
    bindEvents() {
        // Marquer tout comme lu
        document.addEventListener('click', (e) => {
            if (e.target.closest('#markAllRead')) {
                e.preventDefault();
                this.markAllAsRead();
            }
        });

        // Afficher toutes les notifications
        document.addEventListener('click', (e) => {
            if (e.target.closest('#viewAllNotifications')) {
                e.preventDefault();
                // Ici on pourrait rediriger vers une page de notifications complète
                this.showInfo('Page des notifications - À implémenter');
            }
        });

        // Marquer comme lu au clic sur une notification
        document.addEventListener('click', (e) => {
            const notificationItem = e.target.closest('.notification-item');
            if (notificationItem && !e.target.closest('.dropdown')) {
                const notificationId = notificationItem.dataset.notificationId;
                const notification = this.notifications.find(n => n.id === notificationId);
                if (notification && !notification.is_read) {
                    this.markAsRead(notificationId);
                }
            }
        });
    }

    /**
     * Formater le temps écoulé
     */
    formatTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        const minutes = Math.floor(diff / 60000);
        const hours = Math.floor(diff / 3600000);
        const days = Math.floor(diff / 86400000);

        if (minutes < 1) return 'À l\'instant';
        if (minutes < 60) return `Il y a ${minutes} min`;
        if (hours < 24) return `Il y a ${hours}h`;
        if (days < 7) return `Il y a ${days} jours`;
        return date.toLocaleDateString('fr-FR');
    }

    /**
     * Afficher une erreur
     */
    showError(message) {
        this.showNotification(message, 'danger');
    }

    /**
     * Afficher une information
     */
    showInfo(message) {
        this.showNotification(message, 'info');
    }

    /**
     * Afficher une notification toast
     */
    showNotification(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        const toastContainer = document.getElementById('toastContainer') || this.createToastContainer();
        toastContainer.appendChild(toast);

        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        // Supprimer après affichage
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    /**
     * Créer le conteneur pour les toasts
     */
    createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
        return container;
    }
}

// CSS pour l'animation de la cloche
const style = document.createElement('style');
style.textContent = `
    @keyframes bell-ring {
        0% { transform: rotate(0deg); }
        25% { transform: rotate(-10deg); }
        50% { transform: rotate(10deg); }
        75% { transform: rotate(-5deg); }
        100% { transform: rotate(0deg); }
    }

    .notification-item {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .notification-item:hover {
        background-color: rgba(0, 123, 255, 0.1) !important;
    }

    .notification-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dropdown-header {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .dropdown-footer {
        position: sticky;
        bottom: 0;
        background: white;
    }
`;
document.head.appendChild(style);

// Initialiser le système de notifications
let notificationSystem;
document.addEventListener('DOMContentLoaded', function() {
    notificationSystem = new NotificationSystem();
});