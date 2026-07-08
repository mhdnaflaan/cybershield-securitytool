<!-- Network Status Alert -->
<div id="network-status" class="hidden fixed top-0 left-0 right-0 z-[9999] p-3 text-center text-sm font-medium transition-all duration-300 shadow-lg">
    <div class="container mx-auto flex items-center justify-center gap-3">
        <i id="status-icon" class="fas fa-wifi text-lg"></i>
        <span id="status-message">You are offline. Please check your internet connection.</span>
        <button onclick="hideNetworkAlert()" class="ml-4 text-white hover:text-gray-200 transition">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<script>
    // Get elements
    const networkStatus = document.getElementById('network-status');
    const statusMessage = document.getElementById('status-message');
    const statusIcon = document.getElementById('status-icon');

    // Show alert
    function showNetworkAlert(message, type) {
        networkStatus.classList.remove('hidden');
       
        if (type === 'offline') {
            networkStatus.className = 'fixed top-0 left-0 right-0 z-[9999] p-3 text-center text-sm font-medium transition-all duration-300 bg-red-600 text-white shadow-lg';
            statusMessage.textContent = message || '🔴 You are offline. Please check your internet connection.';
            statusIcon.className = 'fas fa-wifi text-lg';
        } else if (type === 'online') {
            networkStatus.className = 'fixed top-0 left-0 right-0 z-[9999] p-3 text-center text-sm font-medium transition-all duration-300 bg-green-600 text-white shadow-lg';
            statusMessage.textContent = message || '✅ Internet connection restored!';
            statusIcon.className = 'fas fa-check-circle text-lg';
           
            // Auto-hide after 3 seconds when back online
            setTimeout(() => {
                hideNetworkAlert();
            }, 3000);
        } else if (type === 'slow') {
            networkStatus.className = 'fixed top-0 left-0 right-0 z-[9999] p-3 text-center text-sm font-medium transition-all duration-300 bg-yellow-600 text-white shadow-lg';
            statusMessage.textContent = message || '⚠️ Your internet connection is unstable. Some features may be slow.';
            statusIcon.className = 'fas fa-exclamation-triangle text-lg';
        }
    }

    // Hide alert
    function hideNetworkAlert() {
        networkStatus.classList.add('hidden');
    }

    // Check connection status on load
    function checkConnection() {
        if (!navigator.onLine) {
            showNetworkAlert('🔴 You are offline. Please check your internet connection.', 'offline');
        }
    }

    // Monitor online/offline events
    window.addEventListener('online', function() {
        showNetworkAlert('✅ Internet connection restored!', 'online');
    });

    window.addEventListener('offline', function() {
        showNetworkAlert('🔴 You are offline. Please check your internet connection.', 'offline');
    });

    // Check on page load
    document.addEventListener('DOMContentLoaded', function() {
        checkConnection();
    });

    // Check every 10 seconds (for unstable connections)
    setInterval(function() {
        if (!navigator.onLine) {
            showNetworkAlert('🔴 You are offline. Please check your internet connection.', 'offline');
        }
    }, 10000);
</script>