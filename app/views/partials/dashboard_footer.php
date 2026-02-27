    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h6>Gestion Médicale</h6>
                    <p class="small text-muted">
                        Système de gestion complète pour établissements de santé
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="small text-muted mb-0">
                        © <?php echo date('Y'); ?> - Tous droits réservés
                    </p>
                    <p class="small text-muted">
                        Version 2.0 - Dashboard Intelligence
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Système de notifications -->
    <script src="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/app/public/js/notifications.js"></script>
    
    <!-- Scripts personnalisés -->
    <script>
        // Notifications en direct (simulation)
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
        
        // Confirmation pour les actions importantes
        function confirmAction(message) {
            return confirm(message);
        }
        
        // Chargement asynchrone des données
        async function fetchData(url) {
            try {
                const response = await fetch(url);
                return await response.json();
            } catch (error) {
                console.error('Erreur lors du chargement des données:', error);
                showNotification('Erreur de chargement des données', 'danger');
            }
        }
    </script>
</body>
</html>