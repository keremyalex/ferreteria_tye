<template>
    <div>
        <Head :title="`Orden #${order.numero_orden}`" />
        <SidebarLayout>
            <div class="min-h-screen py-6 bg-gray-50 dark:bg-gray-900">
                <div class="max-w-4xl px-4 mx-auto sm:px-6 lg:px-8">
                    
                    <!-- Header -->
                    <div class="mb-8">
                        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <div class="flex items-center space-x-3">
                                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                                            Orden #{{ order.numero_orden }}
                                        </h1>
                                        <span 
                                            class="inline-flex px-3 py-1 text-sm font-semibold rounded-full"
                                            :class="getTipoBadgeClass(order.tipo)"
                                        >
                                            {{ getTipoText(order.tipo) }}
                                        </span>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        Creada el {{ formatDate(order.created_at) }}
                                    </p>
                                </div>
                                <div class="flex space-x-3">
                                    <Link 
                                        :href="route('orders.index')"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700"
                                    >
                                        <ArrowLeftIcon class="w-4 h-4 mr-2" />
                                        Volver
                                    </Link>
                                    
                                    <!-- Acciones según el estado -->
                                    <div class="flex space-x-2">
                                        <!-- Confirmar orden -->
                                        <button 
                                            v-if="order.estado === 'pendiente'"
                                            @click="updateEstado('confirmado')"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        >
                                            <CheckIcon class="w-4 h-4 mr-2" />
                                            Confirmar
                                        </button>

                                        <!-- Marcar como listo para retiro -->
                                        <button 
                                            v-if="order.estado === 'confirmado'"
                                            @click="updateEstado('listo_retiro')"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                                        >
                                            <ClockIcon class="w-4 h-4 mr-2" />
                                            Listo para Retiro
                                        </button>

                                        <!-- Marcar como pagado -->
                                        <button 
                                            v-if="order.estado_pago === 'pendiente' && order.tipo === 'presencial'"
                                            @click="marcarComoPagado"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                        >
                                            <CreditCardIcon class="w-4 h-4 mr-2" />
                                            Marcar como Pagado
                                        </button>

                                        <!-- Entregar orden -->
                                        <button 
                                            v-if="order.estado === 'listo_retiro' && order.estado_pago === 'pagado'"
                                            @click="updateEstado('completado')"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                        >
                                            <TruckIcon class="w-4 h-4 mr-2" />
                                            Entregar
                                        </button>

                                        <!-- Cancelar orden -->
                                        <button 
                                            v-if="order.estado !== 'completado' && order.estado !== 'cancelado' && !(order.tipo === 'presencial' && order.estado_pago === 'pagado')"
                                            @click="cancelarOrden"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                        >
                                            <XMarkIcon class="w-4 h-4 mr-2" />
                                            Cancelar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                        <!-- Información principal -->
                        <div class="space-y-6 lg:col-span-2">
                            
                            <!-- Detalles de la orden -->
                            <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        Detalles de la Orden
                                    </h3>
                                </div>
                                <div class="p-6">
                                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                        
                                        <!-- Estado actual -->
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado</dt>
                                            <dd class="mt-1">
                                                <span 
                                                    class="inline-flex px-2 py-1 text-sm font-semibold rounded-full"
                                                    :class="getEstadoBadgeClass(order.estado)"
                                                >
                                                    {{ getEstadoText(order.estado) }}
                                                </span>
                                            </dd>
                                        </div>

                                        <!-- Estado de pago -->
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado de Pago</dt>
                                            <dd class="mt-1">
                                                <span 
                                                    class="inline-flex px-2 py-1 text-sm font-semibold rounded-full"
                                                    :class="getPagoBadgeClass(order.estado_pago)"
                                                >
                                                    {{ getPagoText(order.estado_pago) }}
                                                </span>
                                            </dd>
                                        </div>

                                        <!-- Método de pago -->
                                        <div v-if="order.metodo_pago">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Método de Pago</dt>
                                            <dd class="mt-1 text-sm text-gray-900 capitalize dark:text-white">
                                                {{ order.metodo_pago }}
                                            </dd>
                                        </div>

                                        <!-- Vendedor -->
                                        <div v-if="order.vendedor_id">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Vendedor</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ order.seller?.name || 'No asignado' }}
                                            </dd>
                                        </div>

                                        <!-- Fecha de confirmación -->
                                        <div v-if="order.fecha_confirmacion">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de Confirmación</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ formatDate(order.fecha_confirmacion) }}
                                            </dd>
                                        </div>

                                        <!-- Fecha de entrega -->
                                        <div v-if="order.fecha_entrega">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de Entrega</dt>
                                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                                {{ formatDate(order.fecha_entrega) }}
                                            </dd>
                                        </div>
                                    </div>

                                    <!-- Notas -->
                                    <div v-if="order.notas" class="mt-6">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Notas</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ order.notas }}
                                        </dd>
                                    </div>
                                </div>
                            </div>

                            <!-- Productos -->
                            <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        Productos ({{ order.items?.length || 0 }})
                                    </h3>
                                </div>
                                <div class="overflow-hidden">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                                    Producto
                                                </th>
                                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                                    Cantidad
                                                </th>
                                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                                    Precio
                                                </th>
                                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                                    Total
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                            <tr v-for="item in order.items" :key="item.id">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ item.product?.nombre || 'Producto no encontrado' }}
                                                    </div>
                                                    <div v-if="item.product?.descripcion" class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ item.product.descripcion }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                                    {{ item.cantidad }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                                    ${{ parseFloat(item.precio).toLocaleString() }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                                    ${{ parseFloat(item.total).toLocaleString() }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar derecho -->
                        <div class="space-y-6">
                            
                            <!-- Cliente -->
                            <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        Cliente
                                    </h3>
                                </div>
                                <div class="p-6">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ getCustomerName() }}
                                        </div>
                                        
                                        <!-- Información del cliente presencial -->
                                        <div v-if="order.tipo === 'presencial' && order.client" class="mt-2 space-y-1 text-gray-600 dark:text-gray-400">
                                            <div v-if="order.client.telf">
                                                <strong>Teléfono:</strong> {{ order.client.telf }}
                                            </div>
                                            <div v-if="order.client.direccion">
                                                <strong>Dirección:</strong> {{ order.client.direccion }}
                                            </div>
                                        </div>

                                        <!-- Información del usuario online -->
                                        <div v-if="order.tipo === 'online'" class="mt-2 space-y-1 text-gray-600 dark:text-gray-400">
                                            <div v-if="order.user?.email">
                                                <strong>Email:</strong> {{ order.user.email }}
                                            </div>
                                            <div v-if="order.direccion_facturacion?.telefono">
                                                <strong>Teléfono:</strong> {{ order.direccion_facturacion.telefono }}
                                            </div>
                                            <div v-if="order.direccion_facturacion?.direccion">
                                                <strong>Dirección:</strong> {{ order.direccion_facturacion.direccion }}
                                            </div>
                                            <div v-if="order.direccion_facturacion?.ciudad">
                                                <strong>Ciudad:</strong> {{ order.direccion_facturacion.ciudad }}
                                            </div>
                                            <div v-if="order.direccion_facturacion?.envio !== undefined">
                                                <strong>Costo de envío:</strong> Bs {{ Number(order.direccion_facturacion.envio).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Resumen de precios -->
                            <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        Resumen
                                    </h3>
                                </div>
                                <div class="p-6">
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Subtotal:</span>
                                            <span class="text-sm text-gray-900 dark:text-white">
                                                Bs {{ parseFloat(order.total || 0).toLocaleString() }}
                                            </span>
                                        </div>
                                        <div class="pt-3 border-t border-gray-200 dark:border-gray-600">
                                            <div class="flex justify-between">
                                                <span class="text-base font-medium text-gray-900 dark:text-white">Total:</span>
                                                <span class="text-lg font-bold text-gray-900 dark:text-white">
                                                    Bs {{ parseFloat(order.total || 0).toLocaleString() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Historial de estados (si existe) -->
                            <div v-if="order.status_history && order.status_history.length > 0" class="bg-white rounded-lg shadow dark:bg-gray-800">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        Historial
                                    </h3>
                                </div>
                                <div class="p-6">
                                    <div class="space-y-3">
                                        <div v-for="status in order.status_history" :key="status.id" class="text-sm">
                                            <div class="flex items-center justify-between">
                                                <span class="font-medium text-gray-900 dark:text-white">
                                                    {{ getEstadoText(status.estado) }}
                                                </span>
                                                <span class="text-gray-500 dark:text-gray-400">
                                                    {{ formatDate(status.created_at) }}
                                                </span>
                                            </div>
                                            <div v-if="status.notas" class="mt-1 text-gray-600 dark:text-gray-400">
                                                {{ status.notas }}
                                            </div>
                                        </div>
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
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { 
    ArrowLeftIcon,
    CheckIcon,
    ClockIcon,
    CreditCardIcon,
    TruckIcon,
    XMarkIcon
} from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
    order: {
        type: Object,
        required: true
    }
})

// Methods
const getCustomerName = () => {
    if (props.order.tipo === 'online') {
        return props.order.user?.name || 'Usuario no identificado'
    }
    return props.order.client?.nombre || 'Cliente no identificado'
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
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

// Actions
const updateEstado = (nuevoEstado) => {
    if (confirm(`¿Está seguro que desea cambiar el estado a "${getEstadoText(nuevoEstado)}"?`)) {
        router.patch(route('orders.update', props.order.id), {
            estado: nuevoEstado
        }, {
            preserveScroll: true
        })
    }
}

const marcarComoPagado = () => {
    if (confirm('¿Confirmar que el pago ha sido recibido?')) {
        router.patch(route('orders.update', props.order.id), {
            estado_pago: 'pagado'
        }, {
            preserveScroll: true
        })
    }
}

const cancelarOrden = () => {
    const razon = prompt('¿Por qué motivo se cancela la orden?')
    if (razon !== null) {
        router.patch(route('orders.update', props.order.id), {
            estado: 'cancelado',
            notas_cancelacion: razon
        }, {
            preserveScroll: true
        })
    }
}
</script>