<template>
    <Head title="Finalizar Compra" />
    <ShopLayout :categories="categories">
        <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Título y breadcrumb -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Finalizar Compra</h1>
                <nav class="flex mt-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <Link :href="route('home')" class="text-gray-600 hover:text-blue-600">
                                <HomeIcon class="w-4 h-4 mr-1" />
                                Inicio
                            </Link>
                        </li>
                        <li>
                            <ChevronRightIcon class="w-4 h-4 text-gray-400" />
                        </li>
                        <li>
                            <Link :href="route('cart.index')" class="text-gray-600 hover:text-blue-600">
                                Carrito
                            </Link>
                        </li>
                        <li>
                            <ChevronRightIcon class="w-4 h-4 text-gray-400" />
                        </li>
                        <li class="text-gray-900" aria-current="page">
                            Checkout
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Indicador de progreso -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 text-white bg-green-600 rounded-full">
                            <CheckIcon class="w-5 h-5" />
                        </div>
                        <span class="ml-2 text-sm font-medium text-green-600">Carrito</span>
                    </div>
                    <div class="flex-1 mx-4 h-0.5 bg-blue-600"></div>
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 text-white bg-blue-600 rounded-full">
                            <span class="text-sm font-medium">2</span>
                        </div>
                        <span class="ml-2 text-sm font-medium text-blue-600">Información</span>
                    </div>
                    <div class="flex-1 mx-4 h-0.5 bg-gray-300"></div>
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 text-gray-500 bg-gray-300 rounded-full">
                            <span class="text-sm font-medium">3</span>
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-500">Confirmación</span>
                    </div>
                </div>
            </div>

            <div v-if="cartItems.length > 0">
                <!-- Modal/Overlay para pago QR -->
                <div v-if="showQrPayment" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                    <div class="w-full max-w-lg mx-4">
                        <QrPayment 
                            v-if="currentOrder"
                            :order-id="currentOrder.id"
                            :amount="currentOrder.total"
                            :payment-type="form.tipo_pago"
                            @payment-completed="onQrPaymentCompleted"
                            @payment-failed="onQrPaymentFailed"
                            @cancel="onQrPaymentCancelled"
                        />
                    </div>
                </div>

                <!-- Formulario principal (oculto durante pago QR) -->
                <div v-show="!showQrPayment" class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                    <!-- Formulario de información -->
                    <div class="space-y-6">
                        <!-- Información de contacto -->
                        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                            <h2 class="mb-4 text-lg font-semibold text-gray-900">Información de Contacto</h2>
                            <div class="space-y-4">
                                <div>
                                    <label for="email" class="block mb-1 text-sm font-medium text-gray-700">
                                        Correo electrónico
                                    </label>
                                    <input 
                                        id="email" 
                                        v-model="form.email" 
                                        type="email" 
                                        readonly
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Método de pago -->
                        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                            <h2 class="mb-4 text-lg font-semibold text-gray-900">Método de Pago</h2>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input 
                                        v-model="form.metodo_pago" 
                                        type="radio" 
                                        value="qr" 
                                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                    />
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Código QR</div>
                                        <div class="text-sm text-gray-500">Pago rápido con QR bancario</div>
                                    </div>
                                </label>
                                
                                <label class="flex items-center">
                                    <input 
                                        v-model="form.metodo_pago" 
                                        type="radio" 
                                        value="efectivo" 
                                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                    />
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Efectivo (Contado)</div>
                                        <div class="text-sm text-gray-500">Pago completo al momento de la entrega</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Tipo de pago -->
                        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                            <h2 class="mb-4 text-lg font-semibold text-gray-900">Modalidad de Pago</h2>
                            <div class="space-y-3">
                                <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                                       :class="{ 'border-blue-500 bg-blue-50': form.tipo_pago === 'contado' }">
                                    <input 
                                        v-model="form.tipo_pago" 
                                        type="radio" 
                                        value="contado" 
                                        class="w-4 h-4 mt-1 text-blue-600 border-gray-300 focus:ring-blue-500"
                                    />
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Pago al contado</div>
                                        <div class="text-sm text-gray-500">Pago completo del total</div>
                                        <div class="mt-1 text-lg font-semibold text-green-600">
                                            Bs {{ Number(total).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                                        </div>
                                    </div>
                                </label>
                                
                                <label class="flex items-start p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                                       :class="{ 'border-blue-500 bg-blue-50': form.tipo_pago === 'credito' }">
                                    <input 
                                        v-model="form.tipo_pago" 
                                        type="radio" 
                                        value="credito" 
                                        class="w-4 h-4 mt-1 text-blue-600 border-gray-300 focus:ring-blue-500"
                                    />
                                    <div class="flex-1 ml-3">
                                        <div class="text-sm font-medium text-gray-900">Pago a crédito (2 cuotas)</div>
                                        <div class="mb-2 text-sm text-gray-500">Paga 50% ahora y 50% en 30 días</div>
                                        
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="p-2 rounded bg-blue-50">
                                                <div class="text-xs text-gray-600">Primera cuota</div>
                                                <div class="text-sm font-semibold text-blue-600">
                                                    Bs {{ Number(total/2).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                                                </div>
                                                <div class="text-xs text-gray-500">Ahora</div>
                                            </div>
                                            <div class="p-2 rounded bg-orange-50">
                                                <div class="text-xs text-gray-600">Segunda cuota</div>
                                                <div class="text-sm font-semibold text-orange-600">
                                                    Bs {{ Number(total/2).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                                                </div>
                                                <div class="text-xs text-gray-500">En 30 días</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen del pedido -->
                    <div>
                        <div class="sticky bg-white border border-gray-200 rounded-lg shadow-sm top-8">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-semibold text-gray-900">Resumen del pedido</h2>
                            </div>
                            
                            <!-- Lista de productos -->
                            <div class="p-6">
                                <div class="mb-6 space-y-4">
                                    <div v-for="item in cartItems" :key="item.product_id" class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <img v-if="item.imagen_url" 
                                                :src="item.imagen_url" 
                                                :alt="item.nombre"
                                                class="object-cover w-12 h-12 rounded-lg"
                                            />
                                            <div v-else class="flex items-center justify-center w-12 h-12 bg-gray-200 rounded-lg">
                                                <CubeIcon class="w-6 h-6 text-gray-400" />
                                            </div>
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-sm font-medium text-gray-900">{{ item.nombre }}</h3>
                                            <p class="text-xs text-gray-500">Cantidad: {{ item.cantidad }}</p>
                                        </div>
                                        
                                        <div class="text-sm font-medium text-gray-900">
                                            Bs {{ Number(item.subtotal).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Cálculos -->
                                <div class="pt-4 space-y-3 border-t border-gray-200">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Subtotal ({{ totalItems }} productos)</span>
                                        <span class="text-gray-900">Bs {{ Number(subtotal).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}</span>
                                    </div>
                                    
                                    <hr class="border-gray-200">
                                    
                                    <div class="flex justify-between text-lg font-semibold">
                                        <span class="text-gray-900">
                                            <span v-if="tipoPago === 'credito'">Total (Primera cuota)</span>
                                            <span v-else>Total</span>
                                        </span>
                                        <span class="text-blue-600">Bs {{ Number(totalAPagar).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}</span>
                                    </div>
                                </div>
                                
                                <!-- Botón de procesar pedido -->
                                <button 
                                    @click="procesarPedido" 
                                    :disabled="!isFormValid || isProcessing"
                                    class="w-full px-4 py-3 mt-6 font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
                                >
                                    <span v-if="isProcessing">
                                        <svg class="inline w-5 h-5 mr-3 -ml-1 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Procesando pedido...
                                    </span>
                                    <span v-else>
                                        Confirmar y Realizar Pedido - Bs {{ Number(totalAPagar).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                                    </span>
                                </button>
                                
                                <p class="mt-4 text-xs text-center text-gray-500">
                                    Al confirmar el pedido aceptas nuestros términos y condiciones de venta.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estado vacío -->
            <div v-else class="py-16 text-center">
                <ShoppingCartIcon class="w-16 h-16 mx-auto mb-6 text-gray-400" />
                <h2 class="mb-4 text-2xl font-semibold text-gray-900">No hay productos en el carrito</h2>
                <p class="mb-8 text-gray-600">Agrega algunos productos para proceder con el checkout</p>
                <Link :href="route('home')" 
                    class="inline-flex items-center px-6 py-3 text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                    <ArrowLeftIcon class="w-4 h-4 mr-2" />
                    Explorar productos
                </Link>
            </div>
        </div>
    </ShopLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import ShopLayout from '@/Layouts/ShopLayout.vue'
import QrPayment from '@/Components/QrPayment.vue'
import { 
    CubeIcon,
    ShoppingCartIcon, 
    HomeIcon, 
    ChevronRightIcon,
    CheckIcon,
    ArrowLeftIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    categories: Array,
    orderData: {
        type: Object,
        default: null
    },
    message: {
        type: String,
        default: null
    }
})

const page = usePage()
const cartItems = ref([])
const isProcessing = ref(false)
const showQrPayment = ref(false)
const currentOrder = ref(null)

const form = ref({
    email: '',
    metodo_pago: 'qr',
    tipo_pago: 'contado'
})

const totalItems = computed(() => {
    return cartItems.value.reduce((total, item) => total + item.cantidad, 0)
})

const subtotal = computed(() => {
    return cartItems.value.reduce((total, item) => total + item.subtotal, 0)
})

const total = computed(() => {
    return subtotal.value
})

// Total a pagar según el tipo de pago
const totalAPagar = computed(() => {
    if (form.value.tipo_pago === 'credito') {
        return total.value / 2 // Primera cuota
    }
    return total.value // Pago completo
})

const isFormValid = computed(() => {
    return form.value.metodo_pago &&
           form.value.tipo_pago &&
           cartItems.value.length > 0
})

const loadCart = () => {
    const cart = JSON.parse(localStorage.getItem('cart') || '{"items": [], "total": 0}')
    cartItems.value = cart.items
}

const procesarPedido = async () => {
    if (!isFormValid.value) {
        alert('Por favor, completa todos los campos requeridos.')
        return
    }

    isProcessing.value = true

    try {
        // Preparar datos del pedido
        const user = page.props.auth?.user
        const orderData = {
            items: cartItems.value.map(item => ({
                product_id: item.product_id,
                cantidad: item.cantidad,
                precio_unitario: item.precio
            })),
            cliente: {
                nombre: user?.name || 'Cliente',
                email: user?.email || form.value.email,
                telefono: user?.telefono || '70000000'
            },
            metodo_pago: form.value.metodo_pago,
            tipo_pago: form.value.tipo_pago,
            subtotal: subtotal.value,
            total: subtotal.value
        }

        // Usar router de Inertia
        router.post(route('cart.process'), orderData, {
            onSuccess: (page) => {
                console.log('Respuesta completa:', page)
                console.log('orderData en props:', page.props?.orderData)
                
                // Los datos ahora llegan como props en la nueva página
                const orderDataFromProps = page.props?.orderData
                
                if (orderDataFromProps && orderDataFromProps.id) {
                    currentOrder.value = orderDataFromProps
                    console.log('Order data asignada:', currentOrder.value)

                    // Si es pago QR, mostrar la interfaz QR
                    if (form.value.metodo_pago === 'qr') {
                        showQrPayment.value = true
                    } else {
                        // Para pagos en efectivo, completar directamente
                        completarPedido()
                    }
                } else {
                    console.error('No se recibieron datos de orden válidos:', orderDataFromProps)
                    alert('Error: No se pudo crear la orden. Intenta nuevamente.')
                }
            },
            onError: (errors) => {
                console.error('Errores de validación:', errors)
                alert('Error al procesar el pedido: ' + (Object.values(errors)[0] || 'Error de validación'))
            },
            onFinish: () => {
                isProcessing.value = false
            }
        })

    } catch (error) {
        console.error('Error al procesar pedido:', error)
        alert('Error al procesar el pedido: ' + (error.message || 'Error desconocido'))
        isProcessing.value = false
    }
}

const completarPedido = () => {
    // Limpiar carrito
    localStorage.removeItem('cart')
    window.dispatchEvent(new CustomEvent('cart-updated'))

    // Redirigir a página de confirmación
    router.visit(route('home'), {
        onSuccess: () => {
            alert(`¡Pedido ${currentOrder.value?.numero_orden} realizado con éxito! Te contactaremos pronto para confirmar los detalles.`)
        }
    })
}

const onQrPaymentCompleted = () => {
    // Limpiar carrito cuando el pago QR es exitoso
    localStorage.removeItem('cart')
    window.dispatchEvent(new CustomEvent('cart-updated'))
    // El componente QrPayment se encarga de la redirección
}

const onQrPaymentFailed = () => {
    showQrPayment.value = false
    alert('El pago QR falló. Puedes intentar nuevamente o elegir otro método de pago.')
}

const onQrPaymentCancelled = () => {
    showQrPayment.value = false
}

const initializeForm = () => {
    // Inicializar con datos del usuario autenticado
    const user = page.props.auth?.user
    if (user) {
        form.value.email = user.email || ''
    }
}

onMounted(() => {
    loadCart()
    initializeForm()
    
    // Verificar si ya hay orderData al cargar
    if (props.orderData && props.orderData.id) {
        console.log('orderData detectada al montar:', props.orderData)
        currentOrder.value = props.orderData
        
        // Si es pago QR, mostrar la interfaz QR automaticamente
        if (props.orderData.metodo_pago === 'qr') {
            showQrPayment.value = true
        }
    }
})
</script>