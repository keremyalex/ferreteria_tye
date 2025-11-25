<template>
    <Head title="Carrito de Compras" />
    <ShopLayout :categories="categories">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Título -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Carrito de Compras</h1>
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
                        <li class="text-gray-900" aria-current="page">
                            Carrito
                        </li>
                    </ol>
                </nav>
            </div>

            <div v-if="cartItems.length > 0" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Lista de productos -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Productos en tu carrito</h2>
                        </div>
                        
                        <div class="divide-y divide-gray-200">
                            <div v-for="(item, index) in cartItems" :key="item.product_id" class="p-6">
                                <div class="flex items-center space-x-4">
                                    <!-- Imagen del producto -->
                                    <div class="flex-shrink-0">
                                        <img v-if="item.imagen" 
                                            :src="item.imagen" 
                                            :alt="item.nombre"
                                            class="w-16 h-16 object-cover rounded-lg"
                                        />
                                        <div v-else class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <CubeIcon class="h-8 w-8 text-gray-400" />
                                        </div>
                                    </div>

                                    <!-- Información del producto -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-medium text-gray-900">{{ item.nombre }}</h3>
                                        <p class="text-sm text-gray-500">Precio unitario: Bs {{ Number(item.precio).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}</p>
                                        <p v-if="item.stock_insuficiente" class="text-sm text-red-600 font-medium">
                                            ⚠️ Stock insuficiente. Disponible: {{ item.stock_disponible }}
                                        </p>
                                    </div>

                                    <!-- Controles de cantidad -->
                                    <div class="flex items-center space-x-3">
                                        <div class="flex items-center border border-gray-300 rounded-lg">
                                            <button @click="decrementQuantity(index)" 
                                                :disabled="item.cantidad <= 1"
                                                class="px-2 py-1 text-gray-600 hover:text-gray-800 disabled:opacity-50 disabled:cursor-not-allowed">
                                                <MinusIcon class="w-4 h-4" />
                                            </button>
                                            <span class="px-3 py-1 text-center min-w-[3rem]">{{ item.cantidad }}</span>
                                            <button @click="incrementQuantity(index)" 
                                                :disabled="item.cantidad >= item.stock_disponible"
                                                class="px-2 py-1 text-gray-600 hover:text-gray-800 disabled:opacity-50 disabled:cursor-not-allowed">
                                                <PlusIcon class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Subtotal y eliminar -->
                                    <div class="text-right">
                                        <div class="text-lg font-semibold text-gray-900">
                                            Bs {{ Number(item.subtotal).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                                        </div>
                                        <button @click="removeItem(index)" 
                                            class="text-red-600 hover:text-red-800 text-sm mt-1">
                                            <TrashIcon class="w-4 h-4 inline mr-1" />
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botón limpiar carrito -->
                        <div class="px-6 py-4 border-t border-gray-200">
                            <button @click="clearCart" 
                                class="text-red-600 hover:text-red-800 text-sm font-medium">
                                <TrashIcon class="w-4 h-4 inline mr-1" />
                                Vaciar carrito completo
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Resumen del pedido -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-8">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Resumen del pedido</h2>
                        </div>
                        
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Productos ({{ totalItems }})</span>
                                <span class="text-gray-900">Bs {{ Number(subtotal).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}</span>
                            </div>
                            
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Envío</span>
                                <span class="text-gray-900">A calcular</span>
                            </div>
                            
                            <hr class="border-gray-200">
                            
                            <div class="flex justify-between text-lg font-semibold">
                                <span class="text-gray-900">Total</span>
                                <span class="text-blue-600">Bs {{ Number(subtotal).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}</span>
                            </div>

                            <!-- Alertas de stock -->
                            <div v-if="hasStockIssues" class="bg-red-50 border border-red-200 rounded-lg p-3">
                                <div class="flex">
                                    <ExclamationTriangleIcon class="w-5 h-5 text-red-500 mr-2 flex-shrink-0 mt-0.5" />
                                    <div class="text-sm">
                                        <p class="text-red-800 font-medium">Problemas de stock detectados</p>
                                        <p class="text-red-700 mt-1">Algunos productos tienen stock limitado. Se ajustará automáticamente al procesar.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="space-y-3">
                                <button @click="updateCart" 
                                    :disabled="isUpdating"
                                    class="w-full bg-gray-600 text-white px-4 py-3 rounded-lg hover:bg-gray-700 transition-colors disabled:opacity-50">
                                    <span v-if="isUpdating">Validando...</span>
                                    <span v-else>Actualizar Carrito</span>
                                </button>
                                
                                <Link :href="route('cart.checkout')" 
                                    class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-block text-center font-medium">
                                    Proceder al Checkout
                                </Link>
                            </div>

                            <div class="text-center">
                                <Link :href="route('home')" 
                                    class="text-blue-600 hover:underline text-sm">
                                    ← Continuar comprando
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estado vacío -->
            <div v-else class="text-center py-16">
                <ShoppingCartIcon class="mx-auto h-16 w-16 text-gray-400 mb-6" />
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Tu carrito está vacío</h2>
                <p class="text-gray-600 mb-8">Agrega algunos productos para comenzar a comprar</p>
                <Link :href="route('home')" 
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
                    <ArrowLeftIcon class="w-4 h-4 mr-2" />
                    Explorar productos
                </Link>
            </div>
        </div>

        <!-- Modal de confirmación para limpiar carrito -->
        <div v-if="showClearModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showClearModal = false"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full sm:p-6">
                    <div class="text-center">
                        <ExclamationTriangleIcon class="mx-auto h-12 w-12 text-red-500 mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">¿Vaciar carrito?</h3>
                        <p class="text-gray-600 mb-6">Esta acción eliminará todos los productos del carrito y no se puede deshacer.</p>
                        
                        <div class="space-y-3">
                            <button @click="showClearModal = false" 
                                class="w-full bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">
                                Cancelar
                            </button>
                            <button @click="confirmClearCart" 
                                class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                                Sí, vaciar carrito
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import ShopLayout from '@/Layouts/ShopLayout.vue'
import { 
    CubeIcon,
    ShoppingCartIcon, 
    HomeIcon, 
    ChevronRightIcon,
    MinusIcon,
    PlusIcon,
    TrashIcon,
    ExclamationTriangleIcon,
    ArrowLeftIcon
} from '@heroicons/vue/24/outline'

defineProps({
    categories: Array
})

const cartItems = ref([])
const showClearModal = ref(false)
const isUpdating = ref(false)

const totalItems = computed(() => {
    return cartItems.value.reduce((total, item) => total + item.cantidad, 0)
})

const subtotal = computed(() => {
    return cartItems.value.reduce((total, item) => total + item.subtotal, 0)
})

const hasStockIssues = computed(() => {
    return cartItems.value.some(item => item.stock_insuficiente)
})

const loadCart = () => {
    const cart = JSON.parse(localStorage.getItem('cart') || '{"items": [], "total": 0}')
    cartItems.value = cart.items
}

const saveCart = () => {
    const cart = {
        items: cartItems.value,
        total: subtotal.value
    }
    localStorage.setItem('cart', JSON.stringify(cart))
    window.dispatchEvent(new CustomEvent('cart-updated'))
}

const incrementQuantity = (index) => {
    const item = cartItems.value[index]
    if (item.cantidad < item.stock_disponible) {
        item.cantidad++
        item.subtotal = item.cantidad * item.precio
        saveCart()
    }
}

const decrementQuantity = (index) => {
    const item = cartItems.value[index]
    if (item.cantidad > 1) {
        item.cantidad--
        item.subtotal = item.cantidad * item.precio
        saveCart()
    }
}

const removeItem = (index) => {
    cartItems.value.splice(index, 1)
    saveCart()
}

const clearCart = () => {
    showClearModal.value = true
}

const confirmClearCart = () => {
    cartItems.value = []
    saveCart()
    showClearModal.value = false
}

const updateCart = async () => {
    if (cartItems.value.length === 0) return
    
    isUpdating.value = true
    
    try {
        // Preparar datos para validación
        const items = cartItems.value.map(item => ({
            product_id: item.product_id,
            cantidad: item.cantidad
        }))
        
        // Llamar a la API de validación
        const response = await fetch(route('cart.validate'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ items })
        })
        
        const data = await response.json()
        
        if (response.ok) {
            // Actualizar el carrito con los datos validados
            cartItems.value = data.items.map(item => ({
                ...item,
                stock_insuficiente: item.stock_insuficiente || false
            }))
            saveCart()
            
            if (data.items.some(item => item.stock_insuficiente)) {
                alert('Se actualizaron las cantidades debido a stock limitado.')
            }
        } else {
            throw new Error('Error al validar el carrito')
        }
    } catch (error) {
        console.error('Error:', error)
        alert('Error al actualizar el carrito. Por favor, inténtalo de nuevo.')
    } finally {
        isUpdating.value = false
    }
}

onMounted(() => {
    loadCart()
})
</script>