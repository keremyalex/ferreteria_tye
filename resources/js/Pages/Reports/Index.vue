<template>
    <SidebarLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Reportes y Análisis
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                
                <!-- Header -->
                <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                                    Centro de Reportes
                                </h1>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    Analiza el rendimiento de tu ferretería con reportes detallados
                                </p>
                            </div>
                            <div class="hidden md:block">
                                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid de Reportes Principales -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    
                    <!-- Reporte de Ventas -->
                    <Link :href="route('reports.sales')" class="group">
                        <div class="overflow-hidden transition-shadow duration-300 bg-white border-l-4 border-green-500 shadow-xl dark:bg-gray-800 sm:rounded-lg hover:shadow-2xl">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="flex items-center">
                                            <div class="p-2 mr-4 bg-green-100 rounded-lg dark:bg-green-900">
                                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400">
                                                    Reporte de Ventas
                                                </h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    Análisis de ingresos y clientes
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                            <div class="flex items-center">
                                                <span class="w-2 h-2 mr-2 bg-green-400 rounded-full"></span>
                                                Ventas por período
                                            </div>
                                            <div class="flex items-center">
                                                <span class="w-2 h-2 mr-2 bg-green-400 rounded-full"></span>
                                                Top clientes
                                            </div>
                                            <div class="flex items-center">
                                                <span class="w-2 h-2 mr-2 bg-green-400 rounded-full"></span>
                                                Métodos de pago
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-green-500 transition-transform duration-300 group-hover:translate-x-1">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Link>

                    <!-- Reporte de Productos -->
                    <Link :href="route('reports.products')" class="group">
                        <div class="overflow-hidden transition-shadow duration-300 bg-white border-l-4 border-blue-500 shadow-xl dark:bg-gray-800 sm:rounded-lg hover:shadow-2xl">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="flex items-center">
                                            <div class="p-2 mr-4 bg-blue-100 rounded-lg dark:bg-blue-900">
                                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                                    Reporte de Productos
                                                </h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    Rendimiento y análisis de productos
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                            <div class="flex items-center">
                                                <span class="w-2 h-2 mr-2 bg-blue-400 rounded-full"></span>
                                                Productos más vendidos
                                            </div>
                                            <div class="flex items-center">
                                                <span class="w-2 h-2 mr-2 bg-blue-400 rounded-full"></span>
                                                Análisis por categoría
                                            </div>
                                            <div class="flex items-center">
                                                <span class="w-2 h-2 mr-2 bg-blue-400 rounded-full"></span>
                                                Productos de bajo movimiento
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-blue-500 transition-transform duration-300 group-hover:translate-x-1">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Link>

                    <!-- Reporte de Inventario -->
                    <Link :href="route('reports.inventory')" class="group">
                        <div class="overflow-hidden transition-shadow duration-300 bg-white border-l-4 border-orange-500 shadow-xl dark:bg-gray-800 sm:rounded-lg hover:shadow-2xl">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="flex items-center">
                                            <div class="p-2 mr-4 bg-orange-100 rounded-lg dark:bg-orange-900">
                                                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400">
                                                    Reporte de Inventario
                                                </h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    Control y estado del stock
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                            <div class="flex items-center">
                                                <span class="w-2 h-2 mr-2 bg-orange-400 rounded-full"></span>
                                                Estado actual del stock
                                            </div>
                                            <div class="flex items-center">
                                                <span class="w-2 h-2 mr-2 bg-orange-400 rounded-full"></span>
                                                Productos críticos
                                            </div>
                                            <div class="flex items-center">
                                                <span class="w-2 h-2 mr-2 bg-orange-400 rounded-full"></span>
                                                Valorización
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-orange-500 transition-transform duration-300 group-hover:translate-x-1">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Link>

                    <!-- Reporte de Compras -->
                    <Link :href="route('reports.purchases')" class="group">
                        <div class="overflow-hidden transition-shadow duration-300 bg-white border-l-4 border-purple-500 shadow-xl dark:bg-gray-800 sm:rounded-lg hover:shadow-2xl">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="flex items-center">
                                            <div class="p-2 mr-4 bg-purple-100 rounded-lg dark:bg-purple-900">
                                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13L5.4 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m6 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400">
                                                    Reporte de Compras
                                                </h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    Análisis de adquisiciones
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                            <div class="flex items-center">
                                                <span class="w-2 h-2 mr-2 bg-purple-400 rounded-full"></span>
                                                Compras por proveedor
                                            </div>
                                            <div class="flex items-center">
                                                <span class="w-2 h-2 mr-2 bg-purple-400 rounded-full"></span>
                                                Tendencias temporales
                                            </div>
                                            <div class="flex items-center">
                                                <span class="w-2 h-2 mr-2 bg-purple-400 rounded-full"></span>
                                                Productos más comprados
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-purple-500 transition-transform duration-300 group-hover:translate-x-1">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
</script>