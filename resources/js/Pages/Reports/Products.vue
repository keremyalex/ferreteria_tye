<template>
    <SidebarLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Reporte de Productos
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

                <!-- Productos Más Vendidos -->
                <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">🏆 Top 20 Productos Más Vendidos</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table v-if="topProducts.length > 0" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                        Posición
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                        Producto
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                        Cantidad Vendida
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                        Ingresos Totales
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                        Precio Promedio
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                <tr v-for="(product, index) in topProducts" :key="product.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex items-center justify-center w-8 h-8 text-sm font-bold rounded-full" 
                                                 :class="getRankColor(index)">
                                                {{ index + 1 }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ product.nombre }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                ID: {{ product.id }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-right text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ product.total_sold }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-right text-green-600 whitespace-nowrap dark:text-green-400">
                                        Bs {{ formatNumber(product.total_revenue) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-right text-gray-900 whitespace-nowrap dark:text-white">
                                        Bs {{ formatNumber(product.avg_price) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-else class="p-6 text-center">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">No hay datos de productos vendidos</p>
                        </div>
                    </div>
                </div>

                <!-- Grid de Análisis -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    
                    <!-- Ventas por Categoría -->
                    <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">📊 Ventas por Categoría</h3>
                        </div>
                        <div class="p-6">
                            <div class="h-64 mb-4">
                                <canvas ref="categoryChart"></canvas>
                            </div>
                            <div v-if="productsByCategory.length > 0" class="space-y-2">
                                <div v-for="category in productsByCategory" :key="category.category_name" class="flex items-center justify-between p-2 rounded bg-gray-50 dark:bg-gray-700">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ category.category_name }}</span>
                                    <span class="text-sm font-bold text-green-600 dark:text-green-400">Bs {{ formatNumber(category.total_revenue) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Productos de Bajo Movimiento -->
                    <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">⚠️ Productos de Bajo Movimiento</h3>
                        </div>
                        <div class="p-6">
                            <div v-if="lowMovementProducts.length > 0" class="space-y-3">
                                <div v-for="product in lowMovementProducts.slice(0, 10)" :key="product.id" class="flex items-center justify-between p-3 border-l-4 border-yellow-400 rounded bg-yellow-50 dark:bg-yellow-900/20">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ product.nombre }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ product.codigo }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-yellow-600 dark:text-yellow-400">
                                            {{ product.total_sold }} vendidos
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Bs {{ formatNumber(product.precio_venta) }}
                                        </div>
                                    </div>
                                </div>
                                <div v-if="lowMovementProducts.length > 10" class="text-center">
                                    <button @click="showAllLowMovement = !showAllLowMovement" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ showAllLowMovement ? 'Ver menos' : `Ver ${lowMovementProducts.length - 10} más...` }}
                                    </button>
                                </div>
                                <div v-show="showAllLowMovement" class="space-y-3">
                                    <div v-for="product in lowMovementProducts.slice(10)" :key="product.id" class="flex items-center justify-between p-3 border-l-4 border-yellow-400 rounded bg-yellow-50 dark:bg-yellow-900/20">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ product.nombre }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                ID: {{ product.id }}
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-bold text-yellow-600 dark:text-yellow-400">
                                                {{ product.total_sold }} vendidos
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                Bs {{ formatNumber(product.precio_venta) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="py-8 text-center">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400">Todos los productos tienen buen movimiento</p>
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
    topProducts: {
        type: Array,
        default: () => []
    },
    productsByCategory: {
        type: Array,
        default: () => []
    },
    lowMovementProducts: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const categoryChart = ref(null);
const showAllLowMovement = ref(false);
const form = ref({
    start_date: props.filters.start_date || '',
    end_date: props.filters.end_date || ''
});

const formatNumber = (value) => {
    if (!value) return '0.00';
    return new Intl.NumberFormat('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
};

const getRankColor = (index) => {
    if (index === 0) return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'; // Oro
    if (index === 1) return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'; // Plata
    if (index === 2) return 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200'; // Bronce
    return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
};

const applyFilters = () => {
    router.get(route('reports.products'), form.value);
};

const exportToPDF = () => {
    alert('Funcionalidad de exportación a PDF próximamente');
};

const exportToExcel = () => {
    alert('Funcionalidad de exportación a Excel próximamente');
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
    // Gráfico por categorías
    if (categoryChart.value && props.productsByCategory.length > 0) {
        const ctx = categoryChart.value.getContext('2d');
        const colors = ['#ef4444', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316'];
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: props.productsByCategory.map(item => item.category_name),
                datasets: [{
                    data: props.productsByCategory.map(item => item.total_revenue),
                    backgroundColor: colors.slice(0, props.productsByCategory.length),
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
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = formatNumber(context.parsed);
                                return `${label}: Bs ${value}`;
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