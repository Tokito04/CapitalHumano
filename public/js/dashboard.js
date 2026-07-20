// URL de nuestra API. Se usa la sesión del navegador (cookie) para
// autenticar la petición; no se expone ninguna clave en el cliente.
const apiUrl = document.body.dataset.basePath + '/api/dashboard/stats';

// Configuración común para todos los gráficos
const chartConfig = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                padding: 20,
                usePointStyle: true,
                font: {
                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
                    size: 12
                }
            }
        }
    }
};

// Paleta de colores consistente con el diseño
const colors = {
    primary: ['rgba(102, 126, 234, 0.8)', 'rgba(118, 75, 162, 0.8)'],
    secondary: ['rgba(40, 167, 69, 0.8)', 'rgba(32, 201, 151, 0.8)'],
    tertiary: ['rgba(255, 193, 7, 0.8)', 'rgba(253, 126, 20, 0.8)'],
    quaternary: ['rgba(220, 53, 69, 0.8)', 'rgba(200, 35, 51, 0.8)'],
    info: ['rgba(23, 162, 184, 0.8)', 'rgba(19, 132, 150, 0.8)']
};

// Función para generar colores automáticamente
function generateColors(count) {
    const baseColors = Object.values(colors).flat();
    const result = [];
    for (let i = 0; i < count; i++) {
        result.push(baseColors[i % baseColors.length]);
    }
    return result;
}

// Usamos fetch (con la cookie de sesión) para obtener los datos de la API
fetch(apiUrl, { credentials: 'same-origin' })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error("Error de API:", data.error);
            return;
        }

        // Actualizar estadísticas numéricas
        const totalColaboradores = Object.values(data.por_sexo || {}).reduce((a, b) => a + b, 0);
        document.getElementById('totalColaboradores').textContent = totalColaboradores;
        document.getElementById('totalDepartamentos').textContent = Object.values(data.por_departamento || {}) || 0;

        // --- 1. Gráfico de Colaboradores por Sexo (Gráfico de Dona) ---
        const ctxSexo = document.getElementById('graficoSexo').getContext('2d');
        new Chart(ctxSexo, {
            type: 'doughnut',
            data: {
                labels: Object.keys(data.por_sexo || {}),
                datasets: [{
                    label: 'Total',
                    data: Object.values(data.por_sexo || {}),
                    backgroundColor: colors.primary,
                    borderColor: ['rgba(102, 126, 234, 1)', 'rgba(118, 75, 162, 1)'],
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                ...chartConfig,
                cutout: '60%',
                plugins: {
                    ...chartConfig.plugins,
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: ${context.parsed} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // --- 2. Gráfico de Colaboradores por Rango de Edad (Gráfico de Barras) ---
        const ctxEdad = document.getElementById('graficoEdad').getContext('2d');
        new Chart(ctxEdad, {
            type: 'bar',
            data: {
                labels: data.por_edad?.labels || [],
                datasets: [{
                    label: 'Total de Colaboradores',
                    data: data.por_edad?.data || [],
                    backgroundColor: generateColors(data.por_edad?.data?.length || 0),
                    borderColor: colors.secondary[0],
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                ...chartConfig,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            font: {
                                family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                            }
                        }
                    }
                }
            }
        });

        // --- 3. Gráfico de Colaboradores por Dirección (Gráfico de Barras Horizontales) ---
        const ctxDireccion = document.getElementById('graficoDireccion').getContext('2d');
        new Chart(ctxDireccion, {
            type: 'bar',
            data: {
                labels: data.por_direccion?.labels || [],
                datasets: [{
                    label: 'Total de Colaboradores',
                    data: data.por_direccion?.data || [],
                    backgroundColor: generateColors(data.por_direccion?.data?.length || 0),
                    borderColor: colors.tertiary[0],
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                ...chartConfig,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            font: {
                                family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                            }
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                            }
                        }
                    }
                }
            }
        });

    })
    .catch(error => {
        console.error('Error al obtener los datos para los gráficos:', error);
        // Mostrar mensaje de error en las estadísticas
        document.getElementById('totalColaboradores').textContent = 'Error';
        document.getElementById('colaboradoresActivos').textContent = 'Error';
        document.getElementById('totalDepartamentos').textContent = 'Error';
    });
