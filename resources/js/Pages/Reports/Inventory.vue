<template>
    <SidebarLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Reporte de Inventario
                </h2>
                <Link :href="route('reports.index')" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                    ← Volver a Reportes
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                
                <!-- Estadísticas Generales -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                    <!-- Valor Total -->
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
                                        Bs {{ formatNumber(stats.total_inventory_value) }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Valor Total</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Productos -->
                    <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-2 mr-4 bg-blue-100 rounded-lg dark:bg-blue-900">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ stats.total_products }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Productos</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Productos Críticos -->
                    <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-2 mr-4 bg-yellow-100 rounded-lg dark:bg-yellow-900">
                                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ stats.critical_products }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Stock Crítico</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Productos Agotados -->
                    <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-2 mr-4 bg-red-100 rounded-lg dark:bg-red-900">
                                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ stats.out_of_stock_products }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Agotados</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid de Análisis -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    
                    <!-- Productos con Stock Crítico -->
                    <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">⚠️ Stock Crítico</h3>
                        </div>
                        <div class="p-6 overflow-y-auto max-h-96">
                            <div v-if="criticalStock.length > 0" class="space-y-3">
                                <div v-for="item in criticalStock" :key="item.product_id" class="flex items-center justify-between p-3 border-l-4 border-yellow-400 rounded bg-yellow-50 dark:bg-yellow-900/20">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ item.product_name }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ item.category }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-yellow-600 dark:text-yellow-400">
                                            {{ item.current_stock }} / {{ item.min_stock }}
                                        </div>
                                        <div class="text-xs text-red-500">
                                            Faltan: {{ item.difference }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="py-8 text-center">
                                <svg class="w-12 h-12 mx-auto mb-2 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                                <p class="font-medium text-green-500">¡Todo el stock está en niveles normales!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Productos Agotados -->
                    <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">❌ Productos Agotados</h3>
                        </div>
                        <div class="p-6 overflow-y-auto max-h-96">
                            <div v-if="outOfStock.length > 0" class="space-y-3">
                                <div v-for="item in outOfStock" :key="item.product_id" class="flex items-center p-3 border-l-4 border-red-400 rounded bg-red-50 dark:bg-red-900/20">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ item.product_name }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ item.category }} - ID: {{ item.product_id }}
                                        </div>
                                    </div>
                                    <div class="text-red-600 dark:text-red-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="py-8 text-center">
                                <svg class="w-12 h-12 mx-auto mb-2 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                                <p class="font-medium text-green-500">¡No hay productos agotados!</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Actual (Top 20) -->
                <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">📦 Estado del Stock (Top 20)</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table v-if="currentStock.length > 0" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                        Producto
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                        Categoría
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                        Stock Actual
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                        Stock Mínimo
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                        Valor
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase dark:text-gray-400">
                                        Estado
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                <tr v-for="item in currentStock.slice(0, 20)" :key="item.product_id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ item.product_name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                ID: {{ item.product_id }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ item.category }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-right text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ item.current_stock }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-right text-gray-500 whitespace-nowrap dark:text-gray-400">
                                        {{ item.min_stock }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-right text-green-600 whitespace-nowrap dark:text-green-400">
                                        Bs {{ formatNumber(item.value) }}
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <span :class="getStatusColor(item.status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                            {{ item.status === 'low' ? 'Bajo' : 'Normal' }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-else class="p-6 text-center">
                            <p class="text-gray-500 dark:text-gray-400">No hay productos en inventario</p>
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

const props = defineProps({
    currentStock: {
        type: Array,
        default: () => []
    },
    criticalStock: {
        type: Array,
        default: () => []
    },
    outOfStock: {
        type: Array,
        default: () => []
    },
    stats: {
        type: Object,
        default: () => ({})
    }
});

const formatNumber = (value) => {
    if (!value) return '0.00';
    return new Intl.NumberFormat('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
};

const getStatusColor = (status) => {
    return status === 'low' 
        ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
        : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
};
</script>