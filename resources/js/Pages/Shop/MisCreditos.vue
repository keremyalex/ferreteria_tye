<template>
    <ShopLayout :categories="categories">
        <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Mis Pedidos a Crédito</h1>
                <p class="mt-2 text-gray-600">Gestiona los pagos de tus pedidos a crédito</p>
            </div>

            <div v-if="creditOrders.length === 0" class="py-16 text-center">
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-6 bg-gray-100 rounded-full">
                    <CreditCardIcon class="w-8 h-8 text-gray-400" />
                </div>
                <h2 class="mb-2 text-xl font-semibold text-gray-900">No tienes pedidos a crédito</h2>
                <p class="text-gray-500">Todos tus pedidos están pagados al contado</p>
            </div>

            <div v-else class="space-y-6">
                <div v-for="order in creditOrders" :key="order.id" 
                     class="overflow-hidden bg-white border border-gray-200 rounded-lg shadow-sm">
                    
                    <!-- Header del pedido -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ order.numero_orden }}</h3>
                                <p class="text-sm text-gray-500">{{ formatDate(order.created_at) }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-gray-900">
                                    Bs {{ Number(order.total).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                                </div>
                                <span :class="getOrderStatusClass(order)" 
                                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                    {{ getOrderStatusText(order) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Estado de pagos -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            
                            <!-- Primera cuota -->
                            <div class="p-4 border rounded-lg"
                                 :class="{ 'border-green-200 bg-green-50': order.primer_cuota_pagada, 'border-gray-200': !order.primer_cuota_pagada }">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-medium text-gray-900">Primera Cuota (50%)</h4>
                                    <div v-if="order.primer_cuota_pagada" class="flex items-center text-green-600">
                                        <CheckIcon class="w-5 h-5 mr-1" />
                                        <span class="text-sm">Pagado</span>
                                    </div>
                                </div>
                                
                                <div class="mb-2 text-2xl font-bold"
                                     :class="{ 'text-green-600': order.primer_cuota_pagada, 'text-blue-600': !order.primer_cuota_pagada }">
                                    Bs {{ Number(order.primer_cuota).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                                </div>
                                
                                <div v-if="order.primer_cuota_pagada" class="text-sm text-gray-500">
                                    Pagado el {{ formatDate(order.fecha_pago_primer_cuota) }}
                                </div>
                                
                                <div v-else class="mt-3">
                                    <PagoButton 
                                        :order="order" 
                                        cuota="primera"
                                        :amount="Number(order.primer_cuota)"
                                        @pagar="abrirModalPago" />
                                </div>
                            </div>

                            <!-- Segunda cuota -->
                            <div class="p-4 border rounded-lg"
                                 :class="{ 
                                     'border-green-200 bg-green-50': order.segunda_cuota_pagada,
                                     'border-red-200 bg-red-50': isSegundaCuotaVencida(order),
                                     'border-gray-200': !order.segunda_cuota_pagada && !isSegundaCuotaVencida(order)
                                 }">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-medium text-gray-900">Segunda Cuota (50%)</h4>
                                    <div v-if="order.segunda_cuota_pagada" class="flex items-center text-green-600">
                                        <CheckIcon class="w-5 h-5 mr-1" />
                                        <span class="text-sm">Pagado</span>
                                    </div>
                                    <div v-else-if="isSegundaCuotaVencida(order)" class="flex items-center text-red-600">
                                        <ExclamationTriangleIcon class="w-5 h-5 mr-1" />
                                        <span class="text-sm">Vencido</span>
                                    </div>
                                </div>
                                
                                <div class="mb-2 text-2xl font-bold"
                                     :class="{ 
                                         'text-green-600': order.segunda_cuota_pagada, 
                                         'text-red-600': isSegundaCuotaVencida(order),
                                         'text-orange-600': !order.segunda_cuota_pagada && !isSegundaCuotaVencida(order)
                                     }">
                                    Bs {{ Number(order.segunda_cuota).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                                </div>
                                
                                <div v-if="order.segunda_cuota_pagada" class="text-sm text-gray-500">
                                    Pagado el {{ formatDate(order.fecha_pago_segunda_cuota) }}
                                </div>
                                
                                <div v-else>
                                    <div class="mb-3 text-sm"
                                         :class="{ 'text-red-600': isSegundaCuotaVencida(order), 'text-gray-500': !isSegundaCuotaVencida(order) }">
                                        Vence: {{ formatDate(order.fecha_vencimiento_segunda_cuota) }}
                                        <span v-if="isSegundaCuotaVencida(order)" class="font-medium">(Vencido)</span>
                                    </div>
                                    
                                    <PagoButton 
                                        :order="order" 
                                        cuota="segunda"
                                        :amount="Number(order.segunda_cuota)"
                                        :disabled="!order.primer_cuota_pagada"
                                        @pagar="abrirModalPago" />
                                </div>
                            </div>
                        </div>

                        <!-- Resumen -->
                        <div class="pt-4 mt-6 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Saldo pendiente:</span>
                                <span class="text-lg font-semibold"
                                      :class="{ 'text-green-600': getSaldoPendiente(order) === 0, 'text-red-600': getSaldoPendiente(order) > 0 }">
                                    Bs {{ Number(getSaldoPendiente(order)).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Pago -->
        <PagoModal 
            v-if="mostrarModalPago"
            :order="ordenSeleccionada"
            :cuota="cuotaSeleccionada"
            :amount="montoSeleccionado"
            @cerrar="cerrarModalPago"
            @pago-exitoso="manejarPagoExitoso" />
    </ShopLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import ShopLayout from '@/Layouts/ShopLayout.vue'
import PagoButton from '@/Components/PagoButton.vue'
import PagoModal from '@/Components/PagoModal.vue'
import { 
    CreditCardIcon, 
    CheckIcon, 
    ExclamationTriangleIcon 
} from '@heroicons/vue/24/outline'

const props = defineProps({
    categories: Array,
    creditOrders: Array
})

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-BO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const isSegundaCuotaVencida = (order) => {
    if (order.segunda_cuota_pagada) return false
    return new Date(order.fecha_vencimiento_segunda_cuota) < new Date()
}

const getSaldoPendiente = (order) => {
    let pendiente = 0
    if (!order.primer_cuota_pagada) pendiente += order.primer_cuota
    if (!order.segunda_cuota_pagada) pendiente += order.segunda_cuota
    return pendiente
}

const getOrderStatusClass = (order) => {
    if (order.primer_cuota_pagada && order.segunda_cuota_pagada) {
        return 'bg-green-100 text-green-800'
    }
    if (isSegundaCuotaVencida(order)) {
        return 'bg-red-100 text-red-800'
    }
    return 'bg-yellow-100 text-yellow-800'
}

const getOrderStatusText = (order) => {
    if (order.primer_cuota_pagada && order.segunda_cuota_pagada) {
        return 'Completado'
    }
    if (isSegundaCuotaVencida(order)) {
        return 'Vencido'
    }
    return 'Pendiente'
}

const pagarCuota = (order, cuota) => {
    const amount = Number(cuota === 'primera' ? order.primer_cuota : order.segunda_cuota)
    abrirModalPago({ order, cuota, amount })
}

// Modal de pago
const mostrarModalPago = ref(false)
const ordenSeleccionada = ref(null)
const cuotaSeleccionada = ref(null)
const montoSeleccionado = ref(0)

// Funciones del modal de pago
const abrirModalPago = ({ order, cuota, amount }) => {
    ordenSeleccionada.value = order
    cuotaSeleccionada.value = cuota
    montoSeleccionado.value = amount
    mostrarModalPago.value = true
}

const cerrarModalPago = () => {
    mostrarModalPago.value = false
    ordenSeleccionada.value = null
    cuotaSeleccionada.value = null
    montoSeleccionado.value = 0
}

const manejarPagoExitoso = () => {
    cerrarModalPago()
    // Recargar la página para mostrar los cambios
    window.location.reload()
}
</script>