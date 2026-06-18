const ctx = document.getElementById('graficaEstados');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Operativo', 'Inactivo', 'De baja'],
        datasets: [{
            label: 'Cantidad de equipos',
            data: [12, 4, 2]
        }]
    }
});


const ctx2 = document.getElementById('graficaEstados1')
new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: [
            'EQ-2026-001',
            'EQ-2026-002',
            'EQ-2026-003'
        ],
        datasets: [{
            label: 'Incidencias',
            data: [3, 2, 1]
        }]
    }
});