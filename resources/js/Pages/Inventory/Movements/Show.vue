<template>
    <div>
        <Head :title="`Movimiento #${movement.id} - Inventario`" />
        <SidebarLayout>
            <div class="min-h-screen py-6 bg-gray-50 dark:bg-gray-900">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    
                    <!-- Header -->
                    <div class="mb-8">
                        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <div class="flex items-center space-x-4">
                                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                                            Movimiento #{{ movement.id }}
                                        </h1>
                                        <span 
                                            class="inline-flex px-3 py-1 text-sm font-semibold rounded-full"
                                            :class="getTipoBadgeClass(movement.tipo)"
                                        >
                                            {{ getTipoText(movement.tipo) }}
                                        </span>
                                        <span 
                                            class="inline-flex px-3 py-1 text-sm font-semibold rounded-full"
                                            :class="getEstadoBadgeClass(movement.estado)"
                                        >
                                            {{ getEstadoText(movement.estado) }}
                                        </span>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        {{ formatDate(movement.fecha) }} - {{ movement.user?.name || 'Sistema' }}
                                    </p>
                                </div>
                                <div class="flex space-x-3">
                                    <Link 
                                        :href="route('inventory.movements.index')"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-600 dark:text-white dark:border-gray-500"
                                    >
                                        <ArrowLeftIcon class="w-4 h-4 mr-2" />
                                        Volver
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                        <!-- Información Principal -->
                        <div class="space-y-8 lg:col-span-2">
                            <!-- Detalles del Movimiento -->
                            <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        Información del Movimiento
                                    </h3>
                                </div>
                                
                                <div class="p-6">
                                    <dl class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                Tipo de Movimiento
                                            </dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ getTipoText(movement.tipo) }}
                                            </dd>
                                        </div>

                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                Estado
                                            </dt>
                                            <dd class="mt-1">
                                                <span 
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                                    :class="getEstadoBadgeClass(movement.estado)"
                                                >
                                                    {{ getEstadoText(movement.estado) }}
                                                </span>
                                            </dd>
                                        </div>

                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                Fecha
                                            </dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ formatDate(movement.fecha) }}
                                            </dd>
                                        </div>

                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                Usuario
                                            </dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ movement.user?.name || 'Sistema' }}
                                            </dd>
                                        </div>

                                        <div v-if="movement.referencia">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                Referencia
                                            </dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ movement.referencia }}
                                            </dd>
                                        </div>

                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                Valor Total
                                            </dt>
                                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                                                ${{ valorTotal.toLocaleString() }}
                                            </dd>
                                        </div>
                                    </dl>

                                    <div v-if="movement.observaciones" class="mt-6">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Observaciones
                                        </dt>
                                        <dd class="p-3 mt-1 text-sm text-gray-900 rounded-md bg-gray-50 dark:text-white dark:bg-gray-700">
                                            {{ movement.observaciones }}
                                        </dd>
                                    </div>
                                </div>
                            </div>

                            <!-- Productos -->
                            <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        Productos ({{ movement.details.length }})
                                    </h3>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                                    Producto
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase dark:text-gray-300">
                                                    Cantidad
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-300">
                                                    Precio Unit.
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-300">
                                                    Subtotal
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                            <tr v-for="detail in movement.details" :key="detail.id">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 w-10 h-10">
                                                            <img 
                                                                v-if="detail.product.imagen_url"
                                                                :src="detail.product.imagen_url" 
                                                                :alt="detail.product.nombre"
                                                                class="object-cover w-10 h-10 rounded-full"
                                                            />
                                                            <div v-else class="flex items-center justify-center w-10 h-10 bg-gray-300 rounded-full">
                                                                <span class="text-xs text-gray-500">N/A</span>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ detail.product.nombre }}
                                                            </div>
                                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                                {{ detail.product.category?.nombre }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-center text-gray-900 whitespace-nowrap dark:text-white">
                                                    <span 
                                                        class="font-medium"
                                                        :class="getCantidadClass(detail.cantidad, movement.tipo)"
                                                    >
                                                        {{ formatCantidad(detail.cantidad, movement.tipo) }}
                                                    </span>
                                                    <span class="ml-1 text-gray-500">
                                                        {{ detail.product.measurement?.simbolo }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-right text-gray-900 whitespace-nowrap dark:text-white">
                                                    ${{ parseFloat(detail.precio_unitario || 0).toLocaleString() }}
                                                </td>
                                                <td class="px-6 py-4 text-sm font-medium text-right text-gray-900 whitespace-nowrap dark:text-white">
                                                    ${{ (detail.cantidad * (detail.precio_unitario || 0)).toLocaleString() }}
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <td colspan="3" class="px-6 py-3 text-sm font-medium text-right text-gray-900 dark:text-white">
                                                    Total:
                                                </td>
                                                <td class="px-6 py-3 text-sm font-bold text-right text-gray-900 dark:text-white">
                                                    ${{ valorTotal.toLocaleString() }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Panel Lateral -->
                        <div class="space-y-6">
                            <!-- Acciones -->
                            <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        Acciones
                                    </h3>
                                </div>
                                
                                <div class="p-6 space-y-4">
                                    <!-- Aplicar Movimiento -->
                                    <button
                                        v-if="movement.estado === 'pendiente' && $page.props.auth.user.permissions?.includes('edit.inventory')"
                                        @click="applyMovement"
                                        class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                    >
                                        <CheckIcon class="w-4 h-4 mr-2" />
                                        Aplicar al Stock
                                    </button>

                                    <!-- Revertir Movimiento -->
                                    <button
                                        v-if="movement.estado === 'aplicado' && $page.props.auth.user.permissions?.includes('edit.inventory')"
                                        @click="revertMovement"
                                        class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-yellow-600 border border-transparent rounded-md shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                                    >
                                        <ArrowUturnLeftIcon class="w-4 h-4 mr-2" />
                                        Revertir Movimiento
                                    </button>

                                    <!-- Eliminar -->
                                    <button
                                        v-if="movement.estado !== 'aplicado' && $page.props.auth.user.permissions?.includes('delete.inventory')"
                                        @click="deleteMovement"
                                        class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                    >
                                        <TrashIcon class="w-4 h-4 mr-2" />
                                        Eliminar Movimiento
                                    </button>

                                    <!-- Imprimir -->
                                    <button
                                        @click="printMovement"
                                        class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-600 dark:text-white dark:border-gray-500"
                                    >
                                        <PrinterIcon class="w-4 h-4 mr-2" />
                                        Imprimir
                                    </button>
                                </div>
                            </div>

                            <!-- Estadísticas -->
                            <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        Resumen
                                    </h3>
                                </div>
                                
                                <div class="p-6 space-y-4">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Total Productos:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ movement.details.length }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Total Unidades:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ totalUnidades }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-600">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">Valor Total:</span>
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">${{ valorTotal.toLocaleString() }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Historial -->
                            <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        Historial
                                    </h3>
                                </div>
                                
                                <div class="p-6">
                                    <div class="flow-root">
                                        <ul class="-mb-8">
                                            <li>
                                                <div class="relative pb-8">
                                                    <div class="relative flex space-x-3">
                                                        <div>
                                                            <span class="flex items-center justify-center w-8 h-8 bg-blue-500 rounded-full ring-8 ring-white dark:ring-gray-800">
                                                                <DocumentTextIcon class="w-4 h-4 text-white" />
                                                            </span>
                                                        </div>
                                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                            <div>
                                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                    Movimiento creado por <span class="font-medium">{{ movement.user?.name || 'Sistema' }}</span>
                                                                </p>
                                                            </div>
                                                            <div class="text-sm text-right text-gray-500 whitespace-nowrap dark:text-gray-400">
                                                                {{ formatDate(movement.created_at) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            
                                            <li v-if="movement.estado === 'aplicado'">
                                                <div class="relative">
                                                    <div class="relative flex space-x-3">
                                                        <div>
                                                            <span class="flex items-center justify-center w-8 h-8 bg-green-500 rounded-full ring-8 ring-white dark:ring-gray-800">
                                                                <CheckIcon class="w-4 h-4 text-white" />
                                                            </span>
                                                        </div>
                                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                            <div>
                                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                    Movimiento aplicado al stock
                                                                </p>
                                                            </div>
                                                            <div class="text-sm text-right text-gray-500 whitespace-nowrap dark:text-gray-400">
                                                                {{ formatDate(movement.updated_at) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </SidebarLayout>
    </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import {
    ArrowLeftIcon,
    CheckIcon,
    ArrowUturnLeftIcon,
    TrashIcon,
    PrinterIcon,
    DocumentTextIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    movement: {
        type: Object,
        required: true
    }
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const getTipoText = (tipo) => {
    const tipos = {
        entrada: 'Entrada',
        salida: 'Salida',
        ajuste: 'Ajuste'
    }
    return tipos[tipo] || tipo
}

const getTipoBadgeClass = (tipo) => {
    const classes = {
        entrada: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        salida: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        ajuste: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
    }
    return classes[tipo] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
}

const getEstadoText = (estado) => {
    const estados = {
        aplicado: 'Aplicado',
        pendiente: 'Pendiente',
        cancelado: 'Cancelado'
    }
    return estados[estado] || estado
}

const getEstadoBadgeClass = (estado) => {
    const classes = {
        aplicado: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        pendiente: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        cancelado: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    }
    return classes[estado] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
}

const formatCantidad = (cantidad, tipo) => {
    if (tipo === 'ajuste') {
        // Para ajustes, la cantidad ya tiene el signo correcto
        return cantidad >= 0 ? `+${cantidad}` : `${cantidad}`
    }
    
    // Para entrada y salida, aplicar el signo según el tipo
    const value = Math.abs(cantidad)
    return tipo === 'salida' ? `-${value}` : `+${value}`
}

const getCantidadClass = (cantidad, tipo) => {
    if (tipo === 'ajuste') {
        return cantidad >= 0 ? 'text-green-600' : 'text-red-600'
    }
    
    return tipo === 'entrada' ? 'text-green-600' : 'text-red-600'
}

const valorTotal = computed(() => {
    return props.movement.details.reduce((total, detail) => {
        return total + (detail.cantidad * (detail.precio_unitario || 0))
    }, 0)
})

const totalUnidades = computed(() => {
    return props.movement.details.reduce((total, detail) => {
        return total + detail.cantidad
    }, 0)
})

const applyMovement = () => {
    if (confirm('¿Está seguro que desea aplicar este movimiento? Esta acción afectará el stock de los productos.')) {
        router.post(route('inventory.movements.apply', props.movement.id))
    }
}

const revertMovement = () => {
    if (confirm('¿Está seguro que desea revertir este movimiento? Esto revertirá los cambios de stock realizados.')) {
        router.post(route('inventory.movements.revert', props.movement.id))
    }
}

const deleteMovement = () => {
    if (confirm('¿Está seguro que desea eliminar este movimiento? Esta acción no se puede deshacer.')) {
        router.delete(route('inventory.movements.destroy', props.movement.id))
    }
}

const printMovement = () => {
    window.print()
}
</script>