<template>
    <Head title="Detalle del Pedido" />
    <ShopLayout :categories="[]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumb -->
            <nav class="mb-8">
                <ol class="flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <Link :href="route('client.orders')" class="text-gray-600 hover:text-blue-600">
                            Mis Pedidos
                        </Link>
                    </li>
                    <li>
                        <span class="text-gray-400">/</span>
                    </li>
                    <li class="text-gray-900" aria-current="page">
                        Pedido #{{ order.numero_orden }}
                    </li>
                </ol>
            </nav>

            <!-- Header del pedido -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Pedido #{{ order.numero_orden }}</h1>
                            <p class="text-sm text-gray-600">
                                Realizado el {{ order.created_at }} • Última actualización: {{ order.updated_at }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <!-- Estado del pedido -->
                            <span :class="getEstadoBadgeClass(order.estado)"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium">
                                {{ getEstadoText(order.estado) }}
                            </span>
                            <!-- Estado de pago -->
                            <span :class="getPagoBadgeClass(order.estado_pago)"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium">
                                {{ getPagoText(order.estado_pago) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Productos -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Productos</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div v-for="item in order.items" :key="item.producto_nombre"
                                    class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <img v-if="item.imagen_url" 
                                            :src="item.imagen_url" 
                                            :alt="item.producto_nombre"
                                            class="w-16 h-16 object-cover rounded-lg"
                                        />
                                        <div v-else class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <CubeIcon class="w-8 h-8 text-gray-400" />
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-medium text-gray-900">{{ item.producto_nombre }}</h3>
                                        <div class="mt-1 flex items-center space-x-4 text-sm text-gray-600">
                                            <span>Cantidad: {{ item.cantidad }}</span>
                                            <span>Precio unitario: Bs {{ Number(item.precio).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-medium text-gray-900">
                                            Bs {{ Number(item.subtotal).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información lateral -->
                <div class="space-y-6">
                    <!-- Resumen de costos -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Resumen</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="text-gray-900">Bs {{ Number(order.subtotal).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Envío</span>
                                    <span class="text-gray-900">
                                        <span v-if="order.direccion_facturacion?.envio === 0" class="text-green-600 font-medium">Gratis</span>
                                        <span v-else>Bs {{ Number(order.direccion_facturacion?.envio || 0).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}</span>
                                    </span>
                                </div>
                                <div class="flex justify-between pt-3 border-t text-lg font-semibold">
                                    <span class="text-gray-900">Total</span>
                                    <span class="text-blue-600">Bs {{ Number(order.total).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dirección de entrega -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Dirección de Entrega</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-2 text-sm">
                                <div>
                                    <span class="font-medium text-gray-900">{{ order.direccion_facturacion?.nombre }}</span>
                                </div>
                                <div class="text-gray-600">
                                    {{ order.direccion_facturacion?.direccion }}
                                </div>
                                <div class="text-gray-600">
                                    {{ order.direccion_facturacion?.ciudad }}
                                </div>
                                <div v-if="order.direccion_facturacion?.codigo_postal" class="text-gray-600">
                                    C.P. {{ order.direccion_facturacion.codigo_postal }}
                                </div>
                                <div class="text-gray-600">
                                    Tel: {{ order.direccion_facturacion?.telefono }}
                                </div>
                                <div v-if="order.direccion_facturacion?.email" class="text-gray-600">
                                    Email: {{ order.direccion_facturacion.email }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Método de pago -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Método de Pago</h3>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-600">{{ getMetodoPagoText(order.metodo_pago) }}</p>
                            
                            <!-- Mostrar instrucciones específicas según el método de pago -->
                            <div v-if="order.metodo_pago === 'transferencia' && order.estado_pago === 'pendiente'" 
                                class="mt-4 p-4 bg-blue-50 rounded-lg">
                                <h4 class="text-sm font-medium text-blue-900 mb-2">Datos para Transferencia</h4>
                                <div class="text-sm text-blue-800 space-y-1">
                                    <p><strong>Banco:</strong> Banco Nacional de Bolivia</p>
                                    <p><strong>Cuenta:</strong> 1234567890</p>
                                    <p><strong>Titular:</strong> Ferretería TYE</p>
                                    <p><strong>Concepto:</strong> Pedido #{{ order.numero_orden }}</p>
                                </div>
                                <p class="mt-2 text-xs text-blue-700">
                                    Una vez realizada la transferencia, tu pedido será procesado en 24-48 horas.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Notas adicionales -->
                    <div v-if="order.observaciones" class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Notas</h3>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-600">{{ order.observaciones }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón volver -->
            <div class="mt-8">
                <Link :href="route('client.orders')" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    ← Volver a Mis Pedidos
                </Link>
            </div>
        </div>
    </ShopLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import ShopLayout from '@/Layouts/ShopLayout.vue'
import { CubeIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    order: Object
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