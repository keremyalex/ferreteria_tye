<template>
    <div>
        <Head title="Ventas - Órdenes" />
        <SidebarLayout>
            <div class="min-h-screen py-6 bg-gray-50 dark:bg-gray-900">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    
                    <!-- Header -->
                    <div class="mb-8">
                        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                                        Gestión de Ventas
                                    </h1>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        Administra las órdenes y ventas del sistema
                                    </p>
                                </div>
                                <div class="flex space-x-3">
                                    <Link 
                                        :href="route('orders.create')"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    >
                                        <PlusIcon class="w-4 h-4 mr-2" />
                                        Nueva Venta
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="mb-6">
                        <div class="p-4 bg-white rounded-lg shadow dark:bg-gray-800">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Tipo
                                    </label>
                                    <select class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                                        <option value="">Todos</option>
                                        <option value="presencial">Presencial</option>
                                        <option value="online">Online</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Estado
                                    </label>
                                    <select class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                                        <option value="">Todos</option>
                                        <option value="pendiente">Pendiente</option>
                                        <option value="confirmado">Confirmado</option>
                                        <option value="listo_retiro">Listo para Retiro</option>
                                        <option value="completado">Completado</option>
                                        <option value="cancelado">Cancelado</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Estado de Pago
                                    </label>
                                    <select class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                                        <option value="">Todos</option>
                                        <option value="pendiente">Pendiente</option>
                                        <option value="pagado">Pagado</option>
                                        <option value="fallido">Fallido</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Buscar
                                    </label>
                                    <input 
                                        type="text" 
                                        placeholder="Número de orden, cliente..."
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de órdenes -->
                    <div class="overflow-hidden bg-white rounded-lg shadow-lg dark:bg-gray-800">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            Orden
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            Cliente
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            Tipo
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            Total
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            Estado
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            Pago
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            Fecha
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <!-- Número de orden -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ order.numero_orden }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        ID: #{{ order.id }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Cliente -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                {{ getCustomerName(order) }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ order.vendedor_id ? `Vendedor: ${order.seller?.name}` : 'Online' }}
                                            </div>
                                        </td>

                                        <!-- Tipo -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span 
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                                :class="getTipoBadgeClass(order.tipo)"
                                            >
                                                {{ getTipoText(order.tipo) }}
                                            </span>
                                        </td>

                                        <!-- Total -->
                                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                            Bs {{ parseFloat(order.total).toLocaleString() }}
                                        </td>

                                        <!-- Estado -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span 
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                                :class="getEstadoBadgeClass(order.estado)"
                                            >
                                                {{ getEstadoText(order.estado) }}
                                            </span>
                                        </td>

                                        <!-- Estado de pago -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span 
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                                :class="getPagoBadgeClass(order.estado_pago)"
                                            >
                                                {{ getPagoText(order.estado_pago) }}
                                            </span>
                                        </td>

                                        <!-- Fecha -->
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400">
                                            {{ formatDate(order.created_at) }}
                                        </td>

                                        <!-- Acciones -->
                                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end space-x-2">
                                                <Link 
                                                    :href="route('orders.show', order.id)"
                                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                                    title="Ver detalles"
                                                >
                                                    <EyeIcon class="w-4 h-4" />
                                                </Link>
                                                
                                                <Link 
                                                    v-if="order.estado === 'pendiente'"
                                                    :href="route('orders.edit', order.id)"
                                                    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                                    title="Editar"
                                                >
                                                    <PencilIcon class="w-4 h-4" />
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Estado vacío -->
                                    <tr v-if="orders.data.length === 0">
                                        <td colspan="8" class="px-6 py-12 text-center">
                                            <div class="text-gray-500 dark:text-gray-400">
                                                <ShoppingCartIcon class="w-12 h-12 mx-auto mb-4 opacity-50" />
                                                <p class="mb-2 text-lg font-medium">No hay órdenes registradas</p>
                                                <p class="text-sm">Comienza creando tu primera venta</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div v-if="orders.data.length > 0" class="px-6 py-4 bg-gray-50 dark:bg-gray-700">
                            <Pagination :links="orders.links" />
                        </div>
                    </div>
                </div>
            </div>
        </SidebarLayout>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { 
    PlusIcon, 
    EyeIcon, 
    PencilIcon, 
    ShoppingCartIcon 
} from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
    orders: {
        type: Object,
        required: true
    }
})

// Helper functions
const getCustomerName = (order) => {
    if (order.tipo === 'online') {
        return order.user?.name || 'Usuario no identificado'
    }
    return order.client?.nombre || 'Cliente no identificado'
}

const getTipoText = (tipo) => {
    const tipos = {
        online: 'Online',
        presencial: 'Presencial'
    }
    return tipos[tipo] || tipo
}

const getTipoBadgeClass = (tipo) => {
    const classes = {
        online: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        presencial: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
    }
    return classes[tipo] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
}

const getEstadoText = (estado) => {
    const estados = {
        pendiente: 'Pendiente',
        confirmado: 'Confirmado',
        en_proceso: 'En Proceso',
        listo_retiro: 'Listo para Retiro',
        entregado: 'Entregado',
        completado: 'Completado',
        cancelado: 'Cancelado'
    }
    return estados[estado] || estado
}

const getEstadoBadgeClass = (estado) => {
    const classes = {
        pendiente: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        confirmado: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        en_proceso: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
        listo_retiro: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
        entregado: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        completado: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        cancelado: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    }
    return classes[estado] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
}

const getPagoText = (estado_pago) => {
    const estados = {
        pendiente: 'Pendiente',
        pagado: 'Pagado',
        fallido: 'Fallido'
    }
    return estados[estado_pago] || estado_pago
}

const getPagoBadgeClass = (estado_pago) => {
    const classes = {
        pendiente: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        pagado: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        fallido: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    }
    return classes[estado_pago] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>