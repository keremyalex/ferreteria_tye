<script setup>
import { Link } from '@inertiajs/vue3';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';

defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalProducts: 0,
            lowStockProducts: 0,
            totalUsers: 0,
            totalOrders: 0,
            monthlyRevenue: 0,
        })
    }
});
</script>

<template>
    <SidebarLayout title="Dashboard - Ferretería TYE">
        <template #header>
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                    Dashboard - Ferretería TYE
                </h1>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Bienvenido, {{ $page.props.auth.user.name }}
                </div>
            </div>
        </template>

        <div class="mb-4 col-span-full xl:mb-2">
            <!-- Cards de estadísticas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Total Productos -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg dark:border-gray-600 h-32 md:h-64">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 sm:p-6 xl:p-8 h-full">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <span class="text-2xl sm:text-3xl leading-none font-bold text-gray-900 dark:text-white">{{ stats.totalProducts.toLocaleString() }}</span>
                                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Total Productos</h3>
                            </div>
                            <div class="ml-5 w-0 flex-1 flex items-center justify-end text-green-500 text-base font-bold">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Bajo -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg dark:border-gray-600 h-32 md:h-64">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 sm:p-6 xl:p-8 h-full">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <span class="text-2xl sm:text-3xl leading-none font-bold text-red-600 dark:text-red-400">{{ stats.lowStockProducts }}</span>
                                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Stock Bajo</h3>
                            </div>
                            <div class="ml-5 w-0 flex-1 flex items-center justify-end text-red-500 text-base font-bold">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Usuarios -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg dark:border-gray-600 h-32 md:h-64" v-if="$page.props.auth.user.permissions?.includes('view.users')">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 sm:p-6 xl:p-8 h-full">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <span class="text-2xl sm:text-3xl leading-none font-bold text-gray-900 dark:text-white">{{ stats.totalUsers }}</span>
                                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Total Usuarios</h3>
                            </div>
                            <div class="ml-5 w-0 flex-1 flex items-center justify-end text-green-500 text-base font-bold">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Órdenes del mes -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg dark:border-gray-600 h-32 md:h-64">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 sm:p-6 xl:p-8 h-full">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <span class="text-2xl sm:text-3xl leading-none font-bold text-gray-900 dark:text-white">{{ stats.totalOrders }}</span>
                                <h3 class="text-base font-normal text-gray-500 dark:text-gray-400">Órdenes (Mes)</h3>
                            </div>
                            <div class="ml-5 w-0 flex-1 flex items-center justify-end text-purple-500 text-base font-bold">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 2L3 7v11a1 1 0 001 1h12a1 1 0 001-1V7l-7-5zM9 18v-6h2v6H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accesos rápidos -->
            <div class="bg-white shadow rounded-lg dark:bg-gray-800 p-4 sm:p-6 xl:p-8 mb-6">
                <h3 class="text-xl leading-none font-bold text-gray-900 dark:text-white mb-10">
                    Accesos Rápidos
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <!-- Crear Usuario -->
                    <Link 
                        v-if="$page.props.auth.user.permissions?.includes('create.users')"
                        :href="route('users.create')" 
                        class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white rounded-lg p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
                    >
                        <div class="flex flex-col items-center">
                            <svg class="w-8 h-8 mb-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"></path>
                            </svg>
                            <span class="text-sm font-medium text-center">Crear Usuario</span>
                        </div>
                    </Link>

                    <!-- Crear Producto -->
                    <div class="bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white rounded-lg p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                        <div class="flex flex-col items-center">
                            <svg class="w-8 h-8 mb-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium text-center">Crear Producto</span>
                        </div>
                    </div>

                    <!-- Entrada de Stock -->
                    <div class="bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white rounded-lg p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                        <div class="flex flex-col items-center">
                            <svg class="w-8 h-8 mb-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium text-center">Entrada Stock</span>
                        </div>
                    </div>

                    <!-- Nueva Venta -->
                    <div class="bg-gradient-to-r from-purple-500 to-indigo-500 hover:from-purple-600 hover:to-indigo-600 text-white rounded-lg p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                        <div class="flex flex-col items-center">
                            <svg class="w-8 h-8 mb-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                            </svg>
                            <span class="text-sm font-medium text-center">Nueva Venta</span>
                        </div>
                    </div>

                    <!-- Ver Categorías -->
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white rounded-lg p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                        <div class="flex flex-col items-center">
                            <svg class="w-8 h-8 mb-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                            </svg>
                            <span class="text-sm font-medium text-center">Categorías</span>
                        </div>
                    </div>

                    <!-- Reportes -->
                    <div class="bg-gradient-to-r from-gray-600 to-gray-800 hover:from-gray-700 hover:to-gray-900 text-white rounded-lg p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer">
                        <div class="flex flex-col items-center">
                            <svg class="w-8 h-8 mb-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                            </svg>
                            <span class="text-sm font-medium text-center">Reportes</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actividad reciente -->
            <div class="bg-white shadow rounded-lg dark:bg-gray-800 p-4 sm:p-6 xl:p-8">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-xl font-bold leading-none text-gray-900 dark:text-white">
                        Actividad Reciente
                    </h3>
                    <a href="#" class="text-sm font-medium text-primary-600 hover:bg-gray-100 rounded-lg p-2 dark:text-primary-500 dark:hover:bg-gray-700">
                        Ver todo
                    </a>
                </div>
                <div class="flow-root">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
