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
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
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

                        <!-- Información de envío -->
                        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                            <h2 class="mb-4 text-lg font-semibold text-gray-900">Información de Envío</h2>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div>
                                    <label for="nombre" class="block mb-1 text-sm font-medium text-gray-700">
                                        Nombre completo *
                                    </label>
                                    <input 
                                        id="nombre" 
                                        v-model="form.nombre" 
                                        type="text" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>
                                
                                <div>
                                    <label for="telefono" class="block mb-1 text-sm font-medium text-gray-700">
                                        Teléfono *
                                    </label>
                                    <input 
                                        id="telefono" 
                                        v-model="form.telefono" 
                                        type="tel" 
                                        required
                                        placeholder="+591 70123456"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>
                            </div>

                            <div class="mt-4">
                                <label for="direccion" class="block mb-1 text-sm font-medium text-gray-700">
                                    Dirección completa *
                                </label>
                                <textarea 
                                    id="direccion" 
                                    v-model="form.direccion" 
                                    rows="3" 
                                    required
                                    placeholder="Calle, número, zona, ciudad..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                ></textarea>
                            </div>

                            <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-2">
                                <div>
                                    <label for="ciudad" class="block mb-1 text-sm font-medium text-gray-700">
                                        Ciudad *
                                    </label>
                                    <select 
                                        id="ciudad" 
                                        v-model="form.ciudad" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                        <option value="">Seleccionar ciudad</option>
                                        <option value="La Paz">La Paz</option>
                                        <option value="El Alto">El Alto</option>
                                        <option value="Santa Cruz">Santa Cruz</option>
                                        <option value="Cochabamba">Cochabamba</option>
                                        <option value="Sucre">Sucre</option>
                                        <option value="Oruro">Oruro</option>
                                        <option value="Potosí">Potosí</option>
                                        <option value="Tarija">Tarija</option>
                                        <option value="Trinidad">Trinidad</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="codigo_postal" class="block mb-1 text-sm font-medium text-gray-700">
                                        Código postal
                                    </label>
                                    <input 
                                        id="codigo_postal" 
                                        v-model="form.codigo_postal" 
                                        type="text" 
                                        placeholder="Opcional"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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
                                        value="contraentrega" 
                                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                    />
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Pago contra entrega</div>
                                        <div class="text-sm text-gray-500">Paga en efectivo cuando recibas tu pedido</div>
                                    </div>
                                </label>
                                
                                <label class="flex items-center">
                                    <input 
                                        v-model="form.metodo_pago" 
                                        type="radio" 
                                        value="transferencia" 
                                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                    />
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">Transferencia bancaria</div>
                                        <div class="text-sm text-gray-500">Te proporcionaremos los datos bancarios</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Notas adicionales -->
                        <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                            <h2 class="mb-4 text-lg font-semibold text-gray-900">Notas adicionales</h2>
                            <textarea 
                                v-model="form.notas" 
                                rows="3" 
                                placeholder="Instrucciones especiales para la entrega, horarios preferidos, etc."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            ></textarea>
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
                                            <img v-if="item.imagen" 
                                                :src="item.imagen" 
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
                                    
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Envío</span>
                                        <span class="text-gray-900" :class="{ 'text-green-600 font-medium': envio === 0 }">
                                            <span v-if="envio === 0">Gratis</span>
                                            <span v-else>Bs {{ Number(envio).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}</span>
                                        </span>
                                    </div>
                                    
                                    <hr class="border-gray-200">
                                    
                                    <div class="flex justify-between text-lg font-semibold">
                                        <span class="text-gray-900">Total</span>
                                        <span class="text-blue-600">Bs {{ Number(total).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}</span>
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
                                        Confirmar y Realizar Pedido
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
import { 
    CubeIcon,
    ShoppingCartIcon, 
    HomeIcon, 
    ChevronRightIcon,
    CheckIcon,
    ArrowLeftIcon
} from '@heroicons/vue/24/outline'

defineProps({
    categories: Array
})

const page = usePage()
const cartItems = ref([])
const isProcessing = ref(false)

const form = ref({
    email: '',
    nombre: '',
    telefono: '',
    direccion: '',
    ciudad: '',
    codigo_postal: '',
    metodo_pago: 'contraentrega',
    notas: ''
})

const totalItems = computed(() => {
    return cartItems.value.reduce((total, item) => total + item.cantidad, 0)
})

const subtotal = computed(() => {
    return cartItems.value.reduce((total, item) => total + item.subtotal, 0)
})

const envio = computed(() => {
    // Envío gratis para pedidos mayores a Bs 200
    return subtotal.value >= 200 ? 0 : 30
})

const total = computed(() => {
    return subtotal.value + envio.value
})

const isFormValid = computed(() => {
    return form.value.nombre && 
           form.value.telefono && 
           form.value.direccion && 
           form.value.ciudad && 
           form.value.metodo_pago &&
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
        const orderData = {
            items: cartItems.value.map(item => ({
                product_id: item.product_id,
                cantidad: item.cantidad,
                precio_unitario: item.precio
            })),
            cliente: {
                nombre: form.value.nombre,
                email: form.value.email,
                telefono: form.value.telefono,
                direccion: form.value.direccion,
                ciudad: form.value.ciudad,
                codigo_postal: form.value.codigo_postal
            },
            metodo_pago: form.value.metodo_pago,
            notas: form.value.notas,
            subtotal: subtotal.value,
            envio: envio.value,
            total: total.value
        }

        // Llamar a la API para procesar el pedido
        const response = await fetch(route('cart.process'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(orderData)
        })

        const data = await response.json()

        if (response.ok && data.success) {
            // Limpiar carrito
            localStorage.removeItem('cart')
            window.dispatchEvent(new CustomEvent('cart-updated'))

            // Redirigir a página de confirmación (por ahora al home con mensaje)
            router.visit(route('home'), {
                onSuccess: () => {
                    alert(`¡Pedido ${data.order.order_number} realizado con éxito! Te contactaremos pronto para confirmar los detalles.`)
                }
            })
        } else {
            throw new Error(data.message || 'Error al procesar el pedido')
        }

    } catch (error) {
        console.error('Error al procesar pedido:', error)
        alert('Error al procesar el pedido: ' + (error.message || 'Error desconocido'))
    } finally {
        isProcessing.value = false
    }
}

const initializeForm = () => {
    // Inicializar con datos del usuario autenticado
    const user = page.props.auth?.user
    if (user) {
        form.value.email = user.email || ''
        form.value.nombre = user.name || ''
    }
}

onMounted(() => {
    loadCart()
    initializeForm()
})
</script>