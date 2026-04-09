/**
 * JavaScript principal para el sistema de facturación
 */

// Funciones utilitarias
const Utils = {
    formatCurrency: (amount) => {
        return new Intl.NumberFormat('es-AR', {
            style: 'currency',
            currency: 'ARS'
        }).format(amount);
    },

    formatDate: (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-AR', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    },

    confirm: (message) => {
        return window.confirm(message);
    }
};

// Búsqueda en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    // Buscar en tablas
    const searchInputs = document.querySelectorAll('.table-search');
    searchInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const table = e.target.closest('div').querySelector('table');
            
            if (!table) return;
            
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
    });

    // Confirmar eliminaciones
    const deleteLinks = document.querySelectorAll('.btn-danger[href*="delete"], .btn-danger[href*="anular"]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('¿Está seguro de realizar esta acción?')) {
                e.preventDefault();
            }
        });
    });
});

// Exportar a PDF (funcionalidad básica)
function exportToPDF(elementId, filename = 'factura.pdf') {
    const element = document.getElementById(elementId);
    if (!element) {
        console.error('Elemento no encontrado');
        return;
    }

    // Aquí se podría integrar html2pdf.js o similar
    alert('Para exportar a PDF, instale una librería como html2pdf.js');
}

// Imprimir elemento específico
function printElement(elementId) {
    const element = document.getElementById(elementId);
    if (!element) return;

    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Imprimir</title>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(element.innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}
