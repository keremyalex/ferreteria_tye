<template>
    <div class="fixed inset-0 z-50 overflow-y-auto" v-if="order">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="$emit('cerrar')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-blue-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <CreditCardIcon class="w-6 h-6 text-blue-600" />
                        </div>
                        <div class="w-full mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">
                                Pagar {{ cuota === 'primera' ? 'Primera' : 'Segunda' }} Cuota
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Pedido #{{ order.id }}</p>
                                <p class="mt-2 text-2xl font-bold text-gray-900">Bs. {{ Number(amount).toFixed(2) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6">
                        <h4 class="mb-3 text-sm font-medium text-gray-900">Selecciona el método de pago:</h4>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input v-model="metodoPago" type="radio" value="qr" class="w-4 h-4 text-blue-600">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Pago QR</span>
                                    <p class="text-xs text-gray-500">Pago instantáneo con código QR</p>
                                </div>
                            </label>
                            <label class="flex items-center">
                                <input v-model="metodoPago" type="radio" value="efectivo" class="w-4 h-4 text-blue-600">
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-700">Efectivo</span>
                                    <p class="text-xs text-gray-500">Pago en efectivo en la tienda</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button
                        @click="procesarPago"
                        :disabled="!metodoPago || procesando"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <span v-if="procesando">Procesando...</span>
                        <span v-else>Confirmar Pago</span>
                    </button>
                    <button
                        @click="$emit('cerrar')"
                        :disabled="procesando"
                        class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { CreditCardIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    order: Object,
    cuota: String,
    amount: Number
})

const emit = defineEmits(['cerrar', 'pago-exitoso'])

const metodoPago = ref('')
const procesando = ref(false)

const procesarPago = () => {
    if (!metodoPago.value) return
    procesando.value = true

    router.post(route('client.credits.pagar', props.order.id), {
        cuota: props.cuota,
        metodo_pago: metodoPago.value
    }, {
        onSuccess: () => {
            emit('pago-exitoso')
        },
        onError: () => {
            procesando.value = false
        },
        onFinish: () => {
            procesando.value = false
        }
    })
}
</script>