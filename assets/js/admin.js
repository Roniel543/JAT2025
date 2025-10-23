/**
 * JAT2025 - JavaScript para panel de administración
 * Funcionalidades del dashboard y gestión de participantes
 */

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', () => {
    initializeAdminFeatures();
    initializeConfirmations();
    initializeTooltips();
});

/**
 * Inicializar características del admin
 */
function initializeAdminFeatures() {
    // Auto-hide mensajes de éxito/error
    const alerts = document.querySelectorAll('.alert, .message');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // Inicializar filtros de búsqueda
    initializeSearchFilters();

    // Inicializar animaciones de las tarjetas
    initializeCardAnimations();
}

/**
 * Inicializar confirmaciones de eliminación
 */
function initializeConfirmations() {
    const deleteButtons = document.querySelectorAll('[data-confirm-delete]');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const participantName = this.getAttribute('data-participant-name');
            const confirmMessage = `¿Estás seguro de que quieres eliminar a ${participantName}?`;

            if (confirm(confirmMessage)) {
                window.location.href = this.href;
            }
        });
    });
}

/**
 * Inicializar tooltips
 */
function initializeTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');

    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

/**
 * Mostrar tooltip
 */
function showTooltip(e) {
    const tooltipText = e.target.getAttribute('data-tooltip');
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = tooltipText;
    tooltip.style.cssText = `
    position: absolute;
    background: #1f2937;
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 12px;
    z-index: 1000;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.2s;
  `;

    document.body.appendChild(tooltip);

    const rect = e.target.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';

    setTimeout(() => {
        tooltip.style.opacity = '1';
    }, 10);

    e.target._tooltip = tooltip;
}

/**
 * Ocultar tooltip
 */
function hideTooltip(e) {
    if (e.target._tooltip) {
        e.target._tooltip.remove();
        delete e.target._tooltip;
    }
}

/**
 * Inicializar filtros de búsqueda
 */
function initializeSearchFilters() {
    const searchInput = document.getElementById('searchInput');
    const filterSelect = document.getElementById('filterSelect');

    if (searchInput) {
        searchInput.addEventListener('input', debounce(filterTable, 300));
    }

    if (filterSelect) {
        filterSelect.addEventListener('change', filterTable);
    }
}

/**
 * Filtrar tabla de participantes
 */
function filterTable() {
    const searchInput = document.getElementById('searchInput');
    const filterSelect = document.getElementById('filterSelect');
    const tableRows = document.querySelectorAll('tbody tr');

    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    const filterValue = filterSelect ? filterSelect.value : '';

    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const matchesSearch = text.includes(searchTerm);
        const matchesFilter = filterValue === '' || row.querySelector(`[data-area="${filterValue}"]`);

        if (matchesSearch && matchesFilter) {
            row.style.display = '';
            row.classList.add('fade-in-up');
        } else {
            row.style.display = 'none';
        }
    });
}

/**
 * Inicializar animaciones de las tarjetas
 */
function initializeCardAnimations() {
    const cards = document.querySelectorAll('.stat-card, .card');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('fade-in-up');
                }, index * 100);
            }
        });
    });

    cards.forEach(card => {
        observer.observe(card);
    });
}

/**
 * Función debounce para optimizar búsquedas
 */
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

/**
 * Exportar datos a CSV
 */
function exportToCSV() {
    const table = document.querySelector('table');
    if (!table) return;

    const rows = Array.from(table.querySelectorAll('tr'));
    const csvContent = rows.map(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        return cells.map(cell => `"${cell.textContent.trim()}"`).join(',');
    }).join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `participantes_jat2025_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
}

/**
 * Mostrar modal de confirmación personalizado
 */
function showConfirmModal(title, message, onConfirm) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">${title}</h3>
      <p class="text-gray-600 mb-6">${message}</p>
      <div class="flex justify-end space-x-3">
        <button class="px-4 py-2 text-gray-600 bg-gray-200 rounded hover:bg-gray-300" onclick="this.closest('.fixed').remove()">
          Cancelar
        </button>
        <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700" onclick="this.closest('.fixed').remove(); ${onConfirm}">
          Confirmar
        </button>
      </div>
    </div>
  `;

    document.body.appendChild(modal);
}

/**
 * Actualizar estadísticas en tiempo real
 */
function updateStats() {
    // Esta función se puede usar para actualizar estadísticas via AJAX
    fetch('api/stats.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalInscritos').textContent = data.total;
            document.getElementById('informaticaCount').textContent = data.informatica;
            document.getElementById('metalmecanicaCount').textContent = data.metalmecanica;
        })
        .catch(error => console.error('Error actualizando estadísticas:', error));
}

/**
 * Inicializar auto-refresh de estadísticas
 */
function initializeAutoRefresh() {
    // Actualizar estadísticas cada 30 segundos
    setInterval(updateStats, 30000);
}
