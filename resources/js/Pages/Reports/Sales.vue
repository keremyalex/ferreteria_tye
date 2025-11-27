<template>
    <SidebarLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Reporte de Ventas
                </h2>
                <Link :href="route('reports.index')" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                    ← Volver a Reportes
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                
                <!-- Filtros -->
                <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">🔍 Filtros de Búsqueda</h3>
                        <form @submit.prevent="applyFilters" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Fecha Inicio
                                </label>
                                <input 
                                    v-model="form.start_date" 
                                    type="date" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                                />
                            </div>
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Fecha Fin
                                </label>
                                <input 
                                    v-model="form.end_date" 
                                    type="date" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                                />
                            </div>
                            <div class="flex items-end">
                                <button 
                                    type="submit"
                                    class="w-full px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    Aplicar Filtros
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Estadísticas Generales -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <!-- Total Ventas -->
                    <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-2 mr-4 bg-green-100 rounded-lg dark:bg-green-900">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                        Bs {{ formatNumber(stats.total_sales) }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Ventas</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Órdenes -->
                    <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-2 mr-4 bg-blue-100 rounded-lg dark:bg-blue-900">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ stats.total_orders }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Órdenes</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Promedio por Orden -->
                    <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-2 mr-4 bg-purple-100 rounded-lg dark:bg-purple-900">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                        Bs {{ formatNumber(stats.average_order_value) }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Promedio por Orden</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Ventas por Período -->
                <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">📊 Ventas por Día</h3>
                    </div>
                    <div class="p-6">
                        <div class="h-96">
                            <canvas ref="salesChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Grid de Análisis -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    
                    <!-- Top Clientes -->
                    <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">👑 Top Clientes</h3>
                        </div>
                        <div class="p-6">
                            <div v-if="topClients.length > 0" class="space-y-4">
                                <div v-for="(client, index) in topClients" :key="index" class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700">
                                    <div class="flex items-center">
                                        <div class="flex items-center justify-center w-8 h-8 mr-3 text-sm font-medium text-blue-600 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-400">
                                            {{ index + 1 }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ client.client_name }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ client.orders_count }} órdenes
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-green-600 dark:text-green-400">
                                            Bs {{ formatNumber(client.total_spent) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="py-8 text-center">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400">No hay datos de clientes</p>
                            </div>
                        </div>
                    </div>

                    <!-- Ventas por Método de Pago -->
                    <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">💳 Métodos de Pago</h3>
                        </div>
                        <div class="p-6">
                            <div class="h-64">
                                <canvas ref="paymentMethodChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botón de Exportar -->
                <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 text-center">
                        <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">📄 Exportar Reporte</h3>
                        <div class="space-x-4">
                            <button 
                                @click="exportToPDF"
                                class="px-6 py-2 text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                            >
                                📄 Exportar PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    salesByPeriod: {
        type: Array,
        default: () => []
    },
    topClients: {
        type: Array,
        default: () => []
    },
    salesByPaymentMethod: {
        type: Array,
        default: () => []
    },
    stats: {
        type: Object,
        default: () => ({})
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const salesChart = ref(null);
const paymentMethodChart = ref(null);
const form = ref({
    start_date: props.filters.start_date || '',
    end_date: props.filters.end_date || ''
});

const formatNumber = (value) => {
    if (!value) return '0.00';
    return new Intl.NumberFormat('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
};

const applyFilters = () => {
    router.get(route('reports.sales'), form.value);
};

const exportToPDF = () => {
    // Construir URL con parámetros actuales
    const params = new URLSearchParams();
    
    if (form.value.start_date) {
        params.append('start_date', form.value.start_date);
    }
    if (form.value.end_date) {
        params.append('end_date', form.value.end_date);
    }
    
    // Si no hay fechas específicas, usar período por defecto (7 días)
    if (!form.value.start_date && !form.value.end_date) {
        params.append('period', '7');
    }
    
    // Abrir PDF en nueva ventana
    window.open(route('reports.sales.pdf') + '?' + params.toString(), '_blank');
};

const initCharts = () => {
    // Cargar Chart.js dinámicamente
    if (typeof Chart === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
        script.onload = () => createCharts();
        document.head.appendChild(script);
    } else {
        createCharts();
    }
};

const createCharts = () => {
    // Gráfico de ventas por día
    if (salesChart.value && props.salesByPeriod) {
        const ctx = salesChart.value.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: props.salesByPeriod.map(item => {
                    const date = new Date(item.date);
                    return date.toLocaleDateString('es-BO', { month: 'short', day: 'numeric' });
                }),
                datasets: [{
                    label: 'Ventas (Bs)',
                    data: props.salesByPeriod.map(item => item.total_sales),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Órdenes',
                    data: props.salesByPeriod.map(item => item.orders_count),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    x: {
                        display: true,
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Ventas (Bs)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Número de Órdenes'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
    }

    // Gráfico de métodos de pago
    if (paymentMethodChart.value && props.salesByPaymentMethod) {
        const ctx = paymentMethodChart.value.getContext('2d');
        const colors = ['#ef4444', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6'];
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: props.salesByPaymentMethod.map(item => {
                    const methods = {
                        'efectivo': 'Efectivo',
                        'qr': 'QR',
                        'tarjeta': 'Tarjeta',
                        'transferencia': 'Transferencia'
                    };
                    return methods[item.metodo_pago] || item.metodo_pago;
                }),
                datasets: [{
                    data: props.salesByPaymentMethod.map(item => item.total_amount),
                    backgroundColor: colors.slice(0, props.salesByPaymentMethod.length),
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    }
};

onMounted(() => {
    initCharts();
});
</script>