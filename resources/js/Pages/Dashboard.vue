<script setup>
import { Link } from '@inertiajs/vue3';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import { onMounted, ref } from 'vue';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({})
    },
    charts: {
        type: Object,
        default: () => ({})
    },
    recentActivity: {
        type: Array,
        default: () => []
    }
});

const salesChart = ref(null);
const categoryChart = ref(null);

const formatNumber = (value) => {
    if (!value) return '0';
    return new Intl.NumberFormat('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
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
    // Gráfico de ventas
    if (salesChart.value && props.charts?.sales) {
        const ctx = salesChart.value.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: props.charts.sales.map(item => item.date),
                datasets: [{
                    label: 'Ingresos (Bs)',
                    data: props.charts.sales.map(item => item.revenue),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1,
                    yAxisID: 'y'
                }, {
                    label: 'Órdenes',
                    data: props.charts.sales.map(item => item.orders),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.1,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Ingresos (Bs)'
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
                            drawOnChartArea: false,
                        },
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    }

    // Gráfico de inventario por categoría
    if (categoryChart.value && props.charts?.inventoryByCategory) {
        const ctx = categoryChart.value.getContext('2d');
        const colors = [
            'rgba(59, 130, 246, 0.8)',
            'rgba(34, 197, 94, 0.8)',
            'rgba(251, 191, 36, 0.8)',
            'rgba(239, 68, 68, 0.8)',
            'rgba(139, 92, 246, 0.8)',
            'rgba(236, 72, 153, 0.8)'
        ];

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: props.charts.inventoryByCategory.map(item => item.category),
                datasets: [{
                    data: props.charts.inventoryByCategory.map(item => item.total_stock),
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'right'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = parseFloat(context.raw) || 0;
                                const validData = context.dataset.data.filter(d => !isNaN(parseFloat(d)) && parseFloat(d) > 0);
                                const total = validData.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                                
                                if (total === 0 || isNaN(total)) {
                                    return `${label}: ${value} unidades (0.0%)`;
                                }
                                
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} unidades (${percentage}%)`;
                            }
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

<template>
    <SidebarLayout title="Dashboard - Ferretería TYE">
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                    Dashboard - Ferretería TYE
                </h1>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Bienvenido, {{ $page.props.auth.user.name }}
                </div>
            </div>
        </template>

        <div class="mb-4 col-span-full xl:mb-2">
            <!-- Cards de estadísticas principales -->
            <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
                <!-- Total Productos -->
                <div class="p-6 overflow-hidden bg-white rounded-lg shadow-xl dark:bg-gray-800">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-indigo-100 rounded-full dark:bg-indigo-900">
                                <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">
                                    Total Productos
                                </dt>
                                <dd class="text-3xl font-bold text-gray-900 dark:text-white">
                                    {{ (stats.totalProducts || 0).toLocaleString() }}
                                </dd>
                                <dd class="text-xs text-gray-600 dark:text-gray-300">
                                    {{ stats.totalCategories || 0 }} categorías
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Stock Bajo -->
                <div class="p-6 overflow-hidden bg-white rounded-lg shadow-xl dark:bg-gray-800">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-red-100 rounded-full dark:bg-red-900">
                                <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">
                                    Stock Bajo
                                </dt>
                                <dd class="text-3xl font-bold text-gray-900 dark:text-white">
                                    {{ stats.lowStockProducts || 0 }}
                                </dd>
                                <dd class="text-xs text-gray-600 dark:text-gray-300">
                                    {{ stats.criticalStockProducts || 0 }} críticos
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Ingresos del Mes -->
                <div class="p-6 overflow-hidden bg-white rounded-lg shadow-xl dark:bg-gray-800">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-green-100 rounded-full dark:bg-green-900">
                                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">
                                    Ingresos del Mes
                                </dt>
                                <dd class="text-3xl font-bold text-gray-900 dark:text-white">
                                    Bs {{ formatNumber(stats.monthlyRevenue) }}
                                </dd>
                                <dd :class="[
                                    'text-xs font-medium',
                                    stats.revenueGrowth >= 0 ? 'text-green-600' : 'text-red-600'
                                ]">
                                    <span v-if="stats.revenueGrowth >= 0">↗</span>
                                    <span v-else>↘</span>
                                    {{ Math.abs(stats.revenueGrowth || 0) }}% vs mes pasado
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Órdenes del Mes -->
                <div class="p-6 overflow-hidden bg-white rounded-lg shadow-xl dark:bg-gray-800">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-blue-100 rounded-full dark:bg-blue-900">
                                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 w-0 ml-5">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">
                                    Órdenes del Mes
                                </dt>
                                <dd class="text-3xl font-bold text-gray-900 dark:text-white">
                                    {{ stats.ordersThisMonth || 0 }}
                                </dd>
                                <dd :class="[
                                    'text-xs font-medium',
                                    stats.ordersGrowth >= 0 ? 'text-green-600' : 'text-red-600'
                                ]">
                                    <span v-if="stats.ordersGrowth >= 0">↗</span>
                                    <span v-else>↘</span>
                                    {{ Math.abs(stats.ordersGrowth || 0) }}% crecimiento
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjetas de estadísticas secundarias -->
            <div class="grid grid-cols-1 gap-4 mb-8 md:grid-cols-3 lg:grid-cols-5">
                <div class="p-4 overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ stats.totalUsers || 0 }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Usuarios</div>
                    </div>
                </div>
                <div class="p-4 overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ stats.totalInventory || 0 }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Items Inventario</div>
                    </div>
                </div>
                <div class="p-4 overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">Bs {{ formatNumber(stats.totalStockValue) }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Valor Stock</div>
                    </div>
                </div>
                <div class="p-4 overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ stats.totalOrders || 0 }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total Órdenes</div>
                    </div>
                </div>
                <div class="p-4 overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ stats.pendingPurchases || 0 }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Compras Pendientes</div>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="grid grid-cols-1 gap-8 mb-8 lg:grid-cols-2">
                <!-- Gráfico de Ventas -->
                <div class="p-6 overflow-hidden bg-white rounded-lg shadow-xl dark:bg-gray-800">
                    <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                        Ventas de los Últimos 7 Días
                    </h3>
                    <div class="h-64">
                        <canvas ref="salesChart"></canvas>
                    </div>
                </div>

                <!-- Inventario por Categoría -->
                <div class="p-6 overflow-hidden bg-white rounded-lg shadow-xl dark:bg-gray-800">
                    <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                        Inventario por Categoría
                    </h3>
                    <div class="h-64">
                        <canvas ref="categoryChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Productos -->
            <div class="mb-8 overflow-hidden bg-white rounded-lg shadow-xl dark:bg-gray-800">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="mb-4 text-lg font-medium leading-6 text-gray-900 dark:text-white">
                        Productos Más Vendidos del Mes
                    </h3>
                    <div v-if="charts.topProducts && charts.topProducts.length > 0" class="space-y-4">
                        <div v-for="(product, index) in charts.topProducts" :key="index" 
                             class="flex items-center justify-between p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full dark:bg-blue-900">
                                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ index + 1 }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ product.nombre }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ product.total_sold }} unidades vendidas</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-green-600 dark:text-green-400">Bs {{ formatNumber(product.revenue) }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">ingresos</div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="py-8 text-center text-gray-500 dark:text-gray-400">
                        No hay datos de ventas este mes
                    </div>
                </div>
            </div>

            <!-- Actividad reciente -->
            <div class="p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-6 xl:p-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold leading-none text-gray-900 dark:text-white">
                        Actividad Reciente
                    </h3>
                </div>
                <div class="flow-root">
                    <div v-if="recentActivity && recentActivity.length > 0" class="space-y-4">
                        <div v-for="(activity, index) in recentActivity" :key="index" 
                             class="flex items-center p-4 transition duration-150 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                            <div class="flex-shrink-0">
                                <div :class="[
                                    'w-10 h-10 rounded-full flex items-center justify-center',
                                    activity.type === 'order' ? 'bg-blue-100 dark:bg-blue-900' : 
                                    activity.type === 'movement' ? 'bg-purple-100 dark:bg-purple-900' : 'bg-green-100 dark:bg-green-900'
                                ]">
                                    <svg v-if="activity.icon === 'shopping-cart'" class="w-5 h-5" :class="[
                                        activity.type === 'order' ? 'text-blue-600 dark:text-blue-400' : 
                                        activity.type === 'movement' ? 'text-purple-600 dark:text-purple-400' : 'text-green-600 dark:text-green-400'
                                    ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6.5-5h3"></path>
                                    </svg>
                                    <svg v-else-if="activity.icon === 'plus-circle'" class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <svg v-else-if="activity.icon === 'minus-circle'" class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                    <svg v-else-if="activity.icon === 'refresh'" class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <svg v-else class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 ml-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ activity.title }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ activity.description }}</div>
                            </div>
                            <div class="flex-shrink-0 text-right">
                                <div v-if="activity.amount" class="font-medium text-gray-900 dark:text-white">Bs {{ formatNumber(activity.amount) }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ activity.time }}</div>
                                <div v-if="activity.status" :class="[
                                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                    activity.status === 'completada' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                    activity.status === 'pendiente' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                                    activity.status === 'info' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                                ]">
                                    {{ activity.status }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="py-12 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay actividad reciente</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Los movimientos aparecerán aquí cuando empieces a usar el sistema</p>
                    </div>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>
