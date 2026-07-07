<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Capital Humano</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/main.css">
</head>
<body class="main-page">
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="navbar">
            <a href="<?php echo BASE_PATH; ?>/dashboard" class="active">Dashboard</a>
            <?php if ($_SESSION['user_rol'] == \App\Helpers\AuthHelper::ROL_ADMINISTRADOR): ?>
                <a href="<?php echo BASE_PATH; ?>/usuarios">Usuarios</a>
            <?php endif; ?>
            <a href="<?php echo BASE_PATH; ?>/colaboradores">Colaboradores</a>
            <a href="<?php echo BASE_PATH; ?>/reportes/colaboradores">Reportes</a>
            <a href="https://tokito04.github.io/CapitalHumano/" target="_blank" class="right">Documentación</a>
            <a href="<?php echo BASE_PATH; ?>/logout" class="right">Cerrar Sesión</a>
        </div>
    <?php endif; ?>

    <div class="dashboard-container">
        <h1>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <p>Dashboard de Estadísticas - Resumen visual de los datos de Capital Humano.</p>

        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-number" id="totalColaboradores">--</div>
                <div class="stat-label">Total Colaboradores</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="totalDepartamentos">--</div>
                <div class="stat-label">Departamentos</div>
            </div>
        </div>

        <div class="charts-container">
            <div class="chart-card">
                <h3>Colaboradores por Sexo</h3>
                <div class="chart-wrapper">
                    <canvas id="graficoSexo"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <h3>Colaboradores por Rango de Edad</h3>
                <div class="chart-wrapper">
                    <canvas id="graficoEdad"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <h3>Top 10 Colaboradores por Dirección</h3>
                <div class="chart-wrapper">
                    <canvas id="graficoDireccion"></canvas>
                </div>
            </div>
        </div>

        <div class="action-buttons text-center">
            <a href="<?php echo BASE_PATH; ?>/colaboradores" class="btn">Ver Colaboradores</a>
            <?php if ($_SESSION['user_rol'] == \App\Helpers\AuthHelper::ROL_ADMINISTRADOR): ?>
                <a href="<?php echo BASE_PATH; ?>/colaboradores/crear" class="btn btn-success ml-10">Añadir Colaborador</a>
            <?php endif; ?>
            <a href="<?php echo BASE_PATH; ?>/reportes/colaboradores" class="btn btn-primary ml-10">Ver Reportes</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // URL de nuestra API
        const apiUrl = '<?php echo BASE_PATH; ?>/api/dashboard/stats?apikey=CONT-123-XYZ';

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

        // Usamos fetch para obtener los datos de la API
        fetch(apiUrl)
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
    </script>
</body>
</html>