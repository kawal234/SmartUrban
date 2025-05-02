// CSRF Token setup for AJAX requests
document.addEventListener('DOMContentLoaded', function() {
    // Add CSRF token to all AJAX requests
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

    // Sidebar Toggle
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');

    if (sidebarCollapse) {
        sidebarCollapse.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            content.classList.toggle('active');
        });
    }

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Utility Usage Chart
    const utilityChart = document.getElementById('utilityChart');
    if (utilityChart) {
        const ctx = utilityChart.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Electricity Usage',
                    data: [65, 59, 80, 81, 56, 55],
                    borderColor: '#4f46e5',
                    tension: 0.1
                }, {
                    label: 'Water Usage',
                    data: [28, 48, 40, 19, 86, 27],
                    borderColor: '#10b981',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Utility Usage Trends'
                    }
                }
            }
        });
    }

    // Traffic Zones Map
    const trafficMap = document.getElementById('trafficMap');
    if (trafficMap) {
        const map = L.map('trafficMap').setView([0, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Add traffic zones to map
        const trafficZones = JSON.parse(trafficMap.dataset.zones);
        trafficZones.forEach(function(zone) {
            const marker = L.marker([zone.latitude, zone.longitude])
                .bindPopup(`
                    <strong>${zone.name}</strong><br>
                    Congestion: ${zone.congestion_level}%<br>
                    Status: ${zone.status}
                `)
                .addTo(map);

            // Add color based on congestion level
            const color = zone.congestion_level > 70 ? '#ef4444' : 
                         zone.congestion_level > 30 ? '#f59e0b' : '#10b981';
            
            marker.setIcon(L.divIcon({
                className: 'custom-div-icon',
                html: `<div style="background-color: ${color}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white;"></div>`,
                iconSize: [20, 20]
            }));
    // Handle sidebar collapse on mobile
    const sidebarToggle = document.querySelector('.navbar-toggler');
    const sidebar = document.querySelector('.sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }
});

// Toast notifications
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', function () {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    document.body.appendChild(container);
    return container;
}

// Form validation
function validateForm(form) {
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// Loading state management
function setLoadingState(element, isLoading) {
    if (isLoading) {
        element.disabled = true;
        const originalText = element.innerHTML;
        element.setAttribute('data-original-text', originalText);
        element.innerHTML = '<span class="loading-spinner"></span> Loading...';
    } else {
        element.disabled = false;
        const originalText = element.getAttribute('data-original-text');
        if (originalText) {
            element.innerHTML = originalText;
        }
    }
}

// Date formatting
function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Number formatting
function formatNumber(number, decimals = 2) {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(number);
}

// Error handling
function handleError(error) {
    console.error('Error:', error);
    let message = 'An error occurred. Please try again.';
    
    if (error.response) {
        if (error.response.data && error.response.data.message) {
            message = error.response.data.message;
        } else if (error.response.status === 422) {
            message = 'Please check your input and try again.';
        } else if (error.response.status === 401) {
            message = 'Your session has expired. Please log in again.';
            window.location.href = '/login';
        }
    }
    
    showToast(message, 'danger');
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle function
function throttle(func, limit) {
    let inThrottle;
    return function executedFunction(...args) {
        if (!inThrottle) {
            func(...args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Export functions for use in other modules
window.app = {
    showToast,
    validateForm,
    setLoadingState,
    formatDate,
    formatNumber,
    handleError,
    debounce,
    throttle
}; 