<template>
    <div>
        <Head title="Gestión de Inventario" />
        <SidebarLayout>
            <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <!-- Header y estadísticas -->
                    <div class="mb-8">
                        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                            <div class="flex justify-between items-center mb-6">
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                                        Gestión de Inventario
                                    </h1>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        Control de stock y movimientos de inventario
                                    </p>
                                </div>
                                <Link 
                                    :href="route('inventory.create')"
                                    v-if="$page.props.auth.user.permissions?.includes('create.inventory')"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Nuevo Inventario
                                </Link>
                            </div>

                            <!-- Tarjetas de estadísticas -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <dt class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Productos</dt>
                                            <dd class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ stats.total_products }}</dd>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-yellow-50 dark:bg-yellow-900 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-8 w-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <dt class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Stock Bajo</dt>
                                            <dd class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">{{ stats.low_stock_count }}</dd>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-red-50 dark:bg-red-900 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <dt class="text-sm font-medium text-red-600 dark:text-red-400">Sin Stock</dt>
                                            <dd class="text-2xl font-bold text-red-900 dark:text-red-100">{{ stats.no_stock_count }}</dd>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-green-50 dark:bg-green-900 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <dt class="text-sm font-medium text-green-600 dark:text-green-400">Valor Total</dt>
                                            <dd class="text-2xl font-bold text-green-900 dark:text-green-100">
                                                Bs {{ Number(stats.total_value || 0).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                                            </dd>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros y búsqueda -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <!-- Búsqueda -->
                            <div class="md:col-span-2 relative">
                                <input
                                    v-model="searchForm.search"
                                    @input="search"
                                    type="text"
                                    placeholder="Buscar productos por nombre o descripción..."
                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                />
                                <div v-if="searchForm.search && searchForm.search.length > 0" class="absolute right-3 top-2.5">
                                    <svg class="w-4 h-4 text-gray-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Filtro por categoría -->
                            <div>
                                <select
                                    v-model="searchForm.category"
                                    @change="search"
                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                >
                                    <option value="">Todas las categorías</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.nombre }}
                                    </option>
                                </select>
                            </div>

                            <!-- Filtros rápidos -->
                            <div>
                                <label class="inline-flex items-center">
                                    <input
                                        v-model="searchForm.low_stock"
                                        @change="search"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    />
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Stock Bajo</span>
                                </label>
                            </div>

                            <div>
                                <label class="inline-flex items-center">
                                    <input
                                        v-model="searchForm.no_stock"
                                        @change="search"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    />
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Sin Stock</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de inventario -->
                    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <button @click="sort('product.nombre')" class="flex items-center space-x-1 hover:text-gray-700 dark:hover:text-gray-100">
                                                <span>Producto</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                                </svg>
                                            </button>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Categoría
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <button @click="sort('cantidad')" class="flex items-center space-x-1 hover:text-gray-700 dark:hover:text-gray-100">
                                                <span>Stock Actual</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                                </svg>
                                            </button>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Stock Mínimo
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <button @click="sort('precio_venta')" class="flex items-center space-x-1 hover:text-gray-700 dark:hover:text-gray-100">
                                                <span>Precio Venta</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                                </svg>
                                            </button>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Estado
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="item in inventoryDetails.data" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 bg-indigo-100 dark:bg-indigo-800 rounded-lg flex items-center justify-center">
                                                        <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ item.product.nombre }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ item.product.measurement.simbolo }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ item.product.category.nombre }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium" :class="{
                                                'text-red-600 dark:text-red-400': item.cantidad === 0,
                                                'text-yellow-600 dark:text-yellow-400': item.cantidad > 0 && item.cantidad <= item.cantidad_minima,
                                                'text-green-600 dark:text-green-400': item.cantidad > item.cantidad_minima
                                            }">
                                                {{ item.cantidad }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ item.cantidad_minima }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-medium">
                                            Bs {{ Number(item.precio_venta).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="item.cantidad === 0" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                Sin Stock
                                            </span>
                                            <span v-else-if="item.cantidad <= item.cantidad_minima" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                Stock Bajo
                                            </span>
                                            <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Disponible
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button 
                                                @click="openUpdateModal(item)"
                                                v-if="$page.props.auth.user.permissions?.includes('edit.inventory')"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3"
                                            >
                                                Actualizar
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                        Mostrando {{ inventoryDetails.from }} a {{ inventoryDetails.to }} de {{ inventoryDetails.total }} resultados
                                    </span>
                                </div>
                                <div class="flex space-x-2">
                                    <template v-for="link in inventoryDetails.links" :key="link.label">
                                        <Link 
                                            v-if="link.url"
                                            :href="link.url"
                                            v-html="link.label"
                                            :class="[
                                                'px-3 py-2 text-sm font-medium rounded-md',
                                                link.active 
                                                    ? 'bg-indigo-600 text-white' 
                                                    : 'bg-white text-gray-500 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700'
                                            ]"
                                        />
                                        <span 
                                            v-else
                                            v-html="link.label"
                                            class="px-3 py-2 text-sm font-medium rounded-md bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-700 dark:text-gray-500"
                                        />
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de actualización de stock -->
            <div v-if="showUpdateModal" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeUpdateModal"></div>

                    <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                                    Actualizar Stock: {{ selectedItem?.product.nombre }}
                                </h3>
                                <form @submit.prevent="updateStock">
                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                                Cantidad Actual
                                            </label>
                                            <input
                                                v-model="updateForm.cantidad"
                                                type="number"
                                                min="0"
                                                class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                                Cantidad Mínima
                                            </label>
                                            <input
                                                v-model="updateForm.cantidad_minima"
                                                type="number"
                                                min="0"
                                                class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                                Precio de Venta
                                            </label>
                                            <input
                                                v-model="updateForm.precio_venta"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                                Motivo
                                            </label>
                                            <input
                                                v-model="updateForm.motivo"
                                                type="text"
                                                placeholder="Motivo de la actualización"
                                                class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                            />
                                        </div>
                                    </div>
                                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                        <button
                                            type="submit"
                                            :disabled="updateForm.processing"
                                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                                        >
                                            Actualizar
                                        </button>
                                        <button
                                            @click="closeUpdateModal"
                                            type="button"
                                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
                                        >
                                            Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </SidebarLayout>
    </div>
</template>

<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import { ref, reactive } from 'vue'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { debounce } from 'lodash'

const props = defineProps({
    inventoryDetails: Object,
    categories: Array,
    filters: Object,
    stats: Object
})

const searchForm = reactive({
    search: props.filters?.search || '',
    category: props.filters?.category || '',
    low_stock: Boolean(props.filters?.low_stock),
    no_stock: Boolean(props.filters?.no_stock),
    sort: props.filters?.sort || 'id'
})

const showUpdateModal = ref(false)
const selectedItem = ref(null)

const updateForm = useForm({
    cantidad: 0,
    cantidad_minima: 0,
    precio_venta: 0,
    motivo: ''
})

const search = debounce(() => {
    const params = {}
    
    // Limpiar y verificar parámetros de búsqueda
    if (searchForm.search && searchForm.search.trim().length > 0) {
        params.search = searchForm.search.trim()
    }
    
    if (searchForm.category && searchForm.category !== '') {
        params.category = searchForm.category
    }
    
    if (searchForm.low_stock) {
        params.low_stock = 1
    }
    
    if (searchForm.no_stock) {
        params.no_stock = 1
    }
    
    if (searchForm.sort && searchForm.sort !== 'id') {
        params.sort = searchForm.sort
    }
    
    // Realizar la búsqueda
    router.get(route('inventory.index'), params, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    })
}, 300)

const sort = (field) => {
    searchForm.sort = field
    search()
}

const openUpdateModal = (item) => {
    selectedItem.value = item
    updateForm.cantidad = item.cantidad
    updateForm.cantidad_minima = item.cantidad_minima
    updateForm.precio_venta = item.precio_venta
    updateForm.motivo = ''
    showUpdateModal.value = true
}

const closeUpdateModal = () => {
    showUpdateModal.value = false
    selectedItem.value = null
    updateForm.reset()
}

const updateStock = () => {
    updateForm.put(route('inventory.updateStock', selectedItem.value.id), {
        onSuccess: () => {
            closeUpdateModal()
        }
    })
}
</script>