<template>
    <div class="max-w-md mx-auto bg-white border border-gray-200 rounded-lg shadow-sm">
        <!-- Estados del pago QR -->
        
        <!-- Estado: Generando QR -->
        <div v-if="status === 'generating'" class="p-6">
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 mb-4 border-4 border-blue-200 rounded-full border-t-blue-600 animate-spin"></div>
                <h3 class="mb-2 text-lg font-semibold text-gray-900">Generando código QR</h3>
                <p class="text-sm text-center text-gray-600">
                    Estamos preparando tu código QR para el pago...
                </p>
            </div>
        </div>

        <!-- Estado: Mostrando QR -->
        <div v-else-if="status === 'showing_qr'" class="p-6">
            <div class="flex flex-col items-center">
                <h3 class="mb-2 text-lg font-semibold text-gray-900">Escanea el código QR</h3>
                <p class="mb-4 text-sm text-center text-gray-600">
                    Abre tu app bancaria y escanea el código para pagar
                </p>
                
                <!-- QR Code -->
                <div class="relative mb-4">
                    <div v-if="qrData" class="p-4 bg-white border rounded-lg">
                        <img :src="'data:image/png;base64,' + qrData" 
                             alt="Código QR" 
                             class="w-48 h-48 mx-auto" />
                    </div>
                    <div v-else class="flex items-center justify-center w-48 h-48 bg-gray-200 rounded-lg">
                        <span class="text-gray-500">QR no disponible</span>
                    </div>
                </div>

                <!-- Información de pago -->
                <div class="w-full p-4 mb-4 rounded-lg bg-gray-50">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600">Monto a pagar:</span>
                        <span class="text-lg font-bold text-blue-600">
                            Bs {{ Number(amount).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600">Orden:</span>
                        <span class="text-sm font-medium text-gray-900">#{{ paymentNumber }}</span>
                    </div>
                    <div v-if="expiresAt" class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Expira en:</span>
                        <span class="text-sm font-medium" :class="timeRemaining < 300 ? 'text-red-600' : 'text-gray-900'">
                            {{ formatTimeRemaining() }}
                        </span>
                    </div>
                </div>

                <!-- Estado de verificación -->
                <div class="flex items-center justify-center w-full mb-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 mr-2 border-2 border-blue-200 rounded-full border-t-blue-600 animate-spin"></div>
                        <span class="text-sm text-blue-600">Esperando confirmación del pago...</span>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex w-full gap-3">
                    <button @click="refreshQR" 
                            :disabled="isLoading"
                            class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 disabled:opacity-50">
                        Actualizar QR
                    </button>
                    <button @click="$emit('cancel')" 
                            class="flex-1 px-4 py-2 text-sm font-medium text-red-600 border border-red-200 rounded-lg bg-red-50 hover:bg-red-100">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>

        <!-- Estado: Pago exitoso -->
        <div v-else-if="status === 'paid'" class="p-6">
            <div class="flex flex-col items-center">
                <div class="flex items-center justify-center w-16 h-16 mb-4 bg-green-100 rounded-full">
                    <CheckIcon class="w-8 h-8 text-green-600" />
                </div>
                <h3 class="mb-2 text-lg font-semibold text-green-800">¡Pago confirmado!</h3>
                <p class="mb-4 text-sm text-center text-gray-600">
                    Tu pago ha sido procesado exitosamente
                </p>
                <div class="w-full p-4 mb-4 rounded-lg bg-green-50">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600">Monto pagado:</span>
                        <span class="text-lg font-bold text-green-600">
                            Bs {{ Number(amount).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Confirmado el:</span>
                        <span class="text-sm font-medium text-gray-900">{{ formatPaidDate() }}</span>
                    </div>
                </div>
                <button @click="$emit('payment-completed')" 
                        class="w-full px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700">
                    Continuar
                </button>
            </div>
        </div>

        <!-- Estado: Expirado -->
        <div v-else-if="status === 'expired'" class="p-6">
            <div class="flex flex-col items-center">
                <div class="flex items-center justify-center w-16 h-16 mb-4 bg-yellow-100 rounded-full">
                    <ExclamationTriangleIcon class="w-8 h-8 text-yellow-600" />
                </div>
                <h3 class="mb-2 text-lg font-semibold text-yellow-800">Código QR expirado</h3>
                <p class="mb-4 text-sm text-center text-gray-600">
                    El tiempo límite para el pago ha vencido
                </p>
                <div class="flex w-full gap-3">
                    <button @click="generateNewQR" 
                            :disabled="isLoading"
                            class="flex-1 px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50">
                        Generar nuevo QR
                    </button>
                    <button @click="$emit('cancel')" 
                            class="flex-1 px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>

        <!-- Estado: Error -->
        <div v-else-if="status === 'error'" class="p-6">
            <div class="flex flex-col items-center">
                <div class="flex items-center justify-center w-16 h-16 mb-4 bg-red-100 rounded-full">
                    <XMarkIcon class="w-8 h-8 text-red-600" />
                </div>
                <h3 class="mb-2 text-lg font-semibold text-red-800">Error en el pago</h3>
                <p class="mb-4 text-sm text-center text-gray-600">
                    {{ errorMessage || 'Ocurrió un error al procesar el pago' }}
                </p>
                <div class="flex w-full gap-3">
                    <button @click="retryPayment" 
                            :disabled="isLoading"
                            class="flex-1 px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50">
                        Reintentar
                    </button>
                    <button @click="$emit('cancel')" 
                            class="flex-1 px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { CheckIcon, ExclamationTriangleIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    orderId: {
        type: Number,
        required: true
    },
    amount: {
        type: [String, Number],
        required: true,
        validator: (value) => {
            return value !== undefined && value !== null && value !== '' && !isNaN(Number(value))
        }
    },
    clientDocumentId: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['payment-completed', 'payment-failed', 'cancel'])

// Estado reactivo
const status = ref('generating') // generating, showing_qr, paid, expired, error
const qrData = ref(null)
const paymentNumber = ref('')
const expiresAt = ref(null)
const paidAt = ref(null)
const errorMessage = ref('')
const isLoading = ref(false)
const timeRemaining = ref(0)

// Intervalos
let verificationInterval = null
let timeInterval = null

// Métodos
const generateQR = async () => {
    status.value = 'generating'
    isLoading.value = true
    errorMessage.value = ''

    // Debug: verificar los datos que se envían
    const requestData = {
        order_id: props.orderId,
        client_document_id: props.clientDocumentId
    }
    
    console.log('Datos enviados a QR generate:', requestData)
    console.log('Props orderId:', props.orderId, 'Type:', typeof props.orderId)

    try {
        // Obtener CSRF token de manera segura (múltiples métodos)
        const page = usePage()
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                         || page?.props?.csrf_token 
                         || window?.Laravel?.csrfToken
        
        if (!csrfToken) {
            throw new Error('CSRF token no encontrado')
        }

        console.log('CSRF token obtenido:', csrfToken.substring(0, 10) + '...')

        // Usar fetch directo para evitar problemas con router.post
        const response = await fetch('/qr/generar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(requestData)
        })

        const data = await response.json()
        console.log('Respuesta QR:', data)

        if (data.success && data.qr_data) {
            qrData.value = data.qr_data
            paymentNumber.value = data.payment_number
            expiresAt.value = new Date(data.expires_at)
            status.value = 'showing_qr'
            
            startVerificationPolling()
            startTimeCountdown()
        } else {
            errorMessage.value = data.error || 'Error al generar QR'
            status.value = 'error'
        }
    } catch (error) {
        console.error('Error en generateQR:', error)
        errorMessage.value = 'Error de conexión'
        status.value = 'error'
    } finally {
        isLoading.value = false
    }
}

const verifyPayment = async () => {
    try {
        const response = await fetch('/qr/verificar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                payment_number: paymentNumber.value
            })
        })

        const data = await response.json()

        if (response.ok && data.success) {
            if (data.status === 'paid') {
                paidAt.value = new Date(data.paid_at)
                status.value = 'paid'
                stopIntervals()
                emit('payment-completed')
            } else if (data.status === 'expired') {
                status.value = 'expired'
                stopIntervals()
            }
        }

    } catch (error) {
        console.error('Error al verificar pago:', error)
    }
}

const startVerificationPolling = () => {
    // Verificar cada 5 segundos
    verificationInterval = setInterval(verifyPayment, 5000)
}

const startTimeCountdown = () => {
    const updateTime = () => {
        if (expiresAt.value) {
            const now = new Date()
            const expires = new Date(expiresAt.value)
            timeRemaining.value = Math.max(0, Math.floor((expires - now) / 1000))
            
            if (timeRemaining.value === 0 && status.value === 'showing_qr') {
                status.value = 'expired'
                stopIntervals()
            }
        }
    }
    
    updateTime()
    timeInterval = setInterval(updateTime, 1000)
}

const stopIntervals = () => {
    if (verificationInterval) {
        clearInterval(verificationInterval)
        verificationInterval = null
    }
    if (timeInterval) {
        clearInterval(timeInterval)
        timeInterval = null
    }
}

const refreshQR = () => {
    generateQR()
}

const generateNewQR = () => {
    generateQR()
}

const retryPayment = () => {
    generateQR()
}

const formatTimeRemaining = () => {
    const minutes = Math.floor(timeRemaining.value / 60)
    const seconds = timeRemaining.value % 60
    return `${minutes}:${seconds.toString().padStart(2, '0')}`
}

const formatPaidDate = () => {
    if (!paidAt.value) return ''
    return new Date(paidAt.value).toLocaleString('es-BO', {
        day: '2-digit',
        month: '2-digit', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

// Lifecycle
onMounted(() => {
    generateQR()
})

onUnmounted(() => {
    stopIntervals()
})
</script>