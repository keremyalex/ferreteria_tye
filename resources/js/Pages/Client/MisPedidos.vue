<template>
    <Head title="Mis Pedidos" />
    <ShopLayout :categories="[]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Título -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Mis Pedidos</h1>
                <p class="mt-2 text-gray-600">Aquí puedes ver el historial de tus compras y el estado de cada pedido.</p>
            </div>

            <!-- Lista de pedidos -->
            <div v-if="orders.data.length > 0" class="space-y-6">
                <div v-for="order in orders.data" :key="order.id" 
                    class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    
                    <!-- Header del pedido -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        Pedido #{{ order.numero_orden }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        Realizado el {{ order.created_at }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <!-- Estado del pedido -->
                                <span :class="getEstadoBadgeClass(order.estado)"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                    {{ getEstadoText(order.estado) }}
                                </span>
                                <!-- Estado de pago -->
                                <span :class="getPagoBadgeClass(order.estado_pago)"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                    {{ getPagoText(order.estado_pago) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido del pedido -->
                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Productos -->
                            <div class="lg:col-span-2">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Productos</h4>
                                <div class="space-y-3">
                                    <div v-for="item in order.items" :key="item.producto_nombre"
                                        class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <img v-if="item.imagen_url" 
                                                :src="item.imagen_url" 
                                                :alt="item.producto_nombre"
                                                class="w-12 h-12 object-cover rounded-lg"
                                            />
                                            <div v-else class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <CubeIcon class="w-6 h-6 text-gray-400" />
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <h5 class="text-sm font-medium text-gray-900">{{ item.producto_nombre }}</h5>
                                            <p class="text-sm text-gray-600">
                                                {{ item.cantidad }} × Bs {{ Number(item.precio).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                                            </p>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">
                                            Bs {{ Number(item.subtotal).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de envío y total -->
                            <div class="space-y-4">
                                <!-- Dirección de envío -->
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Dirección de Entrega</h4>
                                    <div class="text-sm text-gray-600">
                                        <p>{{ order.direccion_facturacion?.nombre }}</p>
                                        <p>{{ order.direccion_facturacion?.direccion }}</p>
                                        <p>{{ order.direccion_facturacion?.ciudad }}</p>
                                        <p>{{ order.direccion_facturacion?.telefono }}</p>
                                    </div>
                                </div>

                                <!-- Resumen de costos -->
                                <div class="border-t pt-4">
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Subtotal</span>
                                            <span class="text-gray-900">Bs {{ Number(order.subtotal).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Envío</span>
                                            <span class="text-gray-900">
                                                <span v-if="order.direccion_facturacion?.envio === 0" class="text-green-600 font-medium">Gratis</span>
                                                <span v-else>Bs {{ Number(order.direccion_facturacion?.envio || 0).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}</span>
                                            </span>
                                        </div>
                                        <div class="flex justify-between text-lg font-semibold pt-2 border-t">
                                            <span class="text-gray-900">Total</span>
                                            <span class="text-blue-600">Bs {{ Number(order.total).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Método de pago -->
                                <div class="border-t pt-4">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Método de Pago</h4>
                                    <p class="text-sm text-gray-600">{{ getMetodoPagoText(order.metodo_pago) }}</p>
                                </div>

                                <!-- Botón ver detalles -->
                                <div class="border-t pt-4">
                                    <Link :href="route('client.orders.show', order.id)"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Ver Detalles Completos
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estado vacío -->
            <div v-else class="text-center py-16">
                <ClipboardDocumentListIcon class="mx-auto h-16 w-16 text-gray-400 mb-6" />
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">No tienes pedidos aún</h2>
                <p class="text-gray-600 mb-8">¡Explora nuestro catálogo y realiza tu primera compra!</p>
                <Link :href="route('home')" 
                    class="inline-flex items-center px-6 py-3 text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                    <CubeIcon class="w-4 h-4 mr-2" />
                    Explorar Catálogo
                </Link>
            </div>

            <!-- Paginación -->
            <div v-if="orders.data.length > 0" class="mt-8">
                <Pagination :links="orders.links" />
            </div>
        </div>
    </ShopLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import ShopLayout from '@/Layouts/ShopLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { 
    CubeIcon,
    ClipboardDocumentListIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    orders: Object
})

// Helper functions
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
        pendiente: 'bg-yellow-100 text-yellow-800',
        confirmado: 'bg-blue-100 text-blue-800',
        en_proceso: 'bg-purple-100 text-purple-800',
        listo_retiro: 'bg-orange-100 text-orange-800',
        entregado: 'bg-green-100 text-green-800',
        completado: 'bg-green-100 text-green-800',
        cancelado: 'bg-red-100 text-red-800'
    }
    return classes[estado] || 'bg-gray-100 text-gray-800'
}

const getPagoText = (estadoPago) => {
    const estados = {
        pendiente: 'Pago Pendiente',
        pagado: 'Pagado',
        fallido: 'Pago Fallido'
    }
    return estados[estadoPago] || estadoPago
}

const getPagoBadgeClass = (estadoPago) => {
    const classes = {
        pendiente: 'bg-yellow-100 text-yellow-800',
        pagado: 'bg-green-100 text-green-800',
        fallido: 'bg-red-100 text-red-800'
    }
    return classes[estadoPago] || 'bg-gray-100 text-gray-800'
}

const getMetodoPagoText = (metodo) => {
    const metodos = {
        contraentrega: 'Pago contra entrega',
        transferencia: 'Transferencia bancaria',
        tarjeta: 'Tarjeta de crédito/débito',
        efectivo: 'Efectivo'
    }
    return metodos[metodo] || metodo
}
</script>