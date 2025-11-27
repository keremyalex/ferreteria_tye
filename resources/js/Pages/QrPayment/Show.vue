<template>
    <ShopLayout :categories="[]">
        <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="mb-8">
                <Link :href="route('client.credits')" class="flex items-center text-blue-600 hover:text-blue-800">
                    <ArrowLeftIcon class="w-4 h-4 mr-2" />
                    Volver a Mis Créditos
                </Link>
            </div>

            <div class="max-w-md mx-auto">
                <div class="mb-6 text-center">
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ getPaymentTitle() }}
                    </h1>
                    <p class="text-gray-600">Orden #{{ order.numero_orden }}</p>
                </div>

                <QrPayment 
                    :order-id="order.id"
                    :amount="amount"
                    :payment-type="'cuota'"
                    :payment-type-backend="paymentType"
                    :payment-description="description"
                    :client-document-id="'75664056'"
                    @payment-completed="onPaymentCompleted"
                    @payment-failed="onPaymentFailed"
                    @cancel="onCancel"
                />
            </div>
        </div>
    </ShopLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import ShopLayout from '@/Layouts/ShopLayout.vue'
import QrPayment from '@/Components/QrPayment.vue'
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    order: Object,
    paymentType: String,
    amount: [String, Number],
    description: String
})

const getPaymentTitle = () => {
    if (props.paymentType === 'primera_cuota') {
        return 'Pagar Primera Cuota'
    } else if (props.paymentType === 'segunda_cuota') {
        return 'Pagar Segunda Cuota'
    }
    return 'Realizar Pago'
}

const getQrPaymentType = () => {
    // Convertir payment_type del backend al formato esperado por QrPayment
    if (props.paymentType === 'primera_cuota' || props.paymentType === 'segunda_cuota') {
        return 'credito'
    }
    return 'contado'
}

const onPaymentCompleted = () => {
    // Redirigir a mis créditos después del pago exitoso
    window.location.href = '/mis-creditos'
}

const onPaymentFailed = () => {
    // Mostrar mensaje de error o redirigir
    console.error('Pago fallido')
}

const onCancel = () => {
    // Volver a mis créditos
    window.location.href = '/mis-creditos'
}
</script>