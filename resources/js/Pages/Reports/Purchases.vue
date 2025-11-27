<template>
    <SidebarLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    🛒 Reporte de Compras
                </h2>
                <Link :href="route('reports.index')" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                    ← Volver a Reportes
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Filtros -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">🔍 Filtros de Búsqueda</h3>
                        <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Fecha Inicio
                                </label>
                                <input 
                                    v-model="form.start_date" 
                                    type="date" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
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
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    Aplicar Filtros
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Estadísticas Generales -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Total Compras -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg mr-4">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                        Bs {{ formatNumber(stats.total_purchases) }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Compras</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Órdenes -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg mr-4">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ stats.total_purchase_orders }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Órdenes de Compra</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Compras por Período -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">📊 Compras por Día</h3>
                    </div>
                    <div class="p-6">
                        <div class="h-96">
                            <canvas ref="purchasesChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Grid de Análisis -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- Top Proveedores -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">🏭 Top Proveedores</h3>
                        </div>
                        <div class="p-6">
                            <div v-if="purchasesBySupplier.length > 0" class="space-y-4">
                                <div v-for="(supplier, index) in purchasesBySupplier" :key="index" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400 rounded-full flex items-center justify-center text-sm font-medium mr-3">
                                            {{ index + 1 }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ supplier.supplier_name }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ supplier.purchases_count }} compras
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-purple-600 dark:text-purple-400">
                                            Bs {{ formatNumber(supplier.total_amount) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400">No hay datos de proveedores</p>
                            </div>
                        </div>
                    </div>

                    <!-- Productos Más Comprados -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">📦 Productos Más Comprados</h3>
                        </div>
                        <div class="p-6 max-h-96 overflow-y-auto">
                            <div v-if="topPurchasedProducts.length > 0" class="space-y-3">
                                <div v-for="(product, index) in topPurchasedProducts.slice(0, 10)" :key="product.id" class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 bg-blue-100 dark:bg-blue-800 text-blue-600 dark:text-blue-300 rounded-full flex items-center justify-center text-xs font-bold mr-3">
                                            {{ index + 1 }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ product.nombre }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                ID: {{ product.id }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-blue-600 dark:text-blue-400">
                                            {{ product.total_purchased }} unidades
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Bs {{ formatNumber(product.total_cost) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400">No hay datos de productos</p>
                            </div>
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
    purchasesBySupplier: {
        type: Array,
        default: () => []
    },
    purchasesByPeriod: {
        type: Array,
        default: () => []
    },
    topPurchasedProducts: {
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

const purchasesChart = ref(null);
const form = ref({
    start_date: props.filters.start_date || '',
    end_date: props.filters.end_date || ''
});

const formatNumber = (value) => {
    if (!value) return '0.00';
    return new Intl.NumberFormat('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
};

const applyFilters = () => {
    router.get(route('reports.purchases'), form.value);
};

const initCharts = () => {
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
    // Gráfico de compras por día
    if (purchasesChart.value && props.purchasesByPeriod) {
        const ctx = purchasesChart.value.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: props.purchasesByPeriod.map(item => {
                    const date = new Date(item.date);
                    return date.toLocaleDateString('es-BO', { month: 'short', day: 'numeric' });
                }),
                datasets: [{
                    label: 'Compras (Bs)',
                    data: props.purchasesByPeriod.map(item => item.total_amount),
                    backgroundColor: 'rgba(147, 51, 234, 0.6)',
                    borderColor: 'rgb(147, 51, 234)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Fecha'
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Monto (Bs)'
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