<template>
    <Head :title="product.nombre" />
    <ShopLayout :categories="categories">
        <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex mb-8" aria-label="Breadcrumb">
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
                    <li class="inline-flex items-center">
                        <Link :href="route('catalog.index', { category: product.category.id })" 
                            class="text-gray-600 hover:text-blue-600">
                            {{ product.category.nombre }}
                        </Link>
                    </li>
                    <li>
                        <ChevronRightIcon class="w-4 h-4 text-gray-400" />
                    </li>
                    <li class="text-gray-900" aria-current="page">
                        {{ product.nombre }}
                    </li>
                </ol>
            </nav>

            <div class="grid grid-cols-1 gap-8 mb-12 lg:grid-cols-2">
                <!-- Imagen del producto -->
                <div class="space-y-4">
                    <div class="w-full aspect-w-1 aspect-h-1">
                        <img v-if="product.imagen_url && !failedMainImage" 
                            :src="product.imagen_url" 
                            :alt="product.nombre"
                            class="w-full h-96 object-cover rounded-lg shadow-lg"
                            @error="handleMainImageError"
                        />
                        <div v-else class="flex items-center justify-center w-full bg-gray-200 rounded-lg shadow-lg h-96">
                            <div class="text-center">
                                <CubeIcon class="w-24 h-24 text-gray-400 mx-auto" />
                                <p class="mt-2 text-gray-500 text-lg font-medium">{{ product.nombre }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información del producto -->
                <div class="space-y-6">
                    <div>
                        <h1 class="mb-2 text-3xl font-bold text-gray-900">{{ product.nombre }}</h1>
                        <p class="text-lg text-gray-600">{{ product.descripcion }}</p>
                    </div>

                    <!-- Precio -->
                    <div class="p-4 rounded-lg bg-gray-50">
                        <div class="text-4xl font-bold text-blue-600">
                            Bs {{ Number(product.precio_final).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Categoría:</span>
                            <Link :href="route('catalog.index', { category: product.category.id })" 
                                class="ml-1 text-blue-600 hover:underline">
                                {{ product.category.nombre }}
                            </Link>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Unidad:</span>
                            <span class="ml-1 text-gray-600">{{ product.measurement.nombre }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Proveedor:</span>
                            <span class="ml-1 text-gray-600">{{ product.supplier.nombre }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Stock disponible:</span>
                            <span class="ml-1 text-gray-600" :class="{ 'text-red-600 font-semibold': product.stock_disponible <= 5 }">
                                {{ product.stock_disponible }} unidades
                            </span>
                        </div>
                    </div>

                    <!-- Agregar al carrito -->
                    <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                        <div class="space-y-4">
                            <div class="flex items-center space-x-4">
                                <label for="quantity" class="text-sm font-medium text-gray-700">Cantidad:</label>
                                <div class="flex items-center border border-gray-300 rounded-lg">
                                    <button @click="decrementQuantity" 
                                        :disabled="quantity <= 1"
                                        class="px-3 py-1 text-gray-600 hover:text-gray-800 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <MinusIcon class="w-4 h-4" />
                                    </button>
                                    <input v-model.number="quantity" 
                                        type="number" 
                                        min="1" 
                                        :max="product.stock_disponible"
                                        class="w-16 text-center border-0 focus:ring-0 focus:outline-none"
                                        @blur="validateQuantity"
                                    />
                                    <button @click="incrementQuantity" 
                                        :disabled="quantity >= product.stock_disponible"
                                        class="px-3 py-1 text-gray-600 hover:text-gray-800 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <PlusIcon class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>

                            <div class="text-lg font-semibold text-gray-900">
                                Subtotal: Bs {{ Number(quantity * product.precio_final).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                            </div>

                            <button @click="addToCart" 
                                :disabled="product.stock_disponible <= 0"
                                class="w-full px-6 py-3 text-lg font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
                                <span v-if="product.stock_disponible > 0">
                                    <ShoppingCartIcon class="inline w-5 h-5 mr-2" />
                                    Agregar al Carrito
                                </span>
                                <span v-else>Sin Stock Disponible</span>
                            </button>

                            <p class="text-xs text-center text-gray-500">
                                * Stock limitado. Precio sujeto a cambios sin previo aviso.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos relacionados -->
            <div v-if="relatedProducts.length > 0" class="mt-16">
                <h2 class="mb-8 text-2xl font-bold text-gray-900">Productos Relacionados</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <div v-for="relatedProduct in relatedProducts" :key="relatedProduct.id" 
                        class="overflow-hidden transition-shadow bg-white rounded-lg shadow-md hover:shadow-lg">
                        
                        <Link :href="route('catalog.show', relatedProduct.id)" class="block">
                            <div class="w-full aspect-w-1 aspect-h-1">
                                <img v-if="relatedProduct.imagen_url && !failedImages[relatedProduct.id]" 
                                    :src="relatedProduct.imagen_url" 
                                    :alt="relatedProduct.nombre"
                                    class="object-cover w-full h-48"
                                    @error="handleImageError(relatedProduct.id)"
                                />
                                <div v-else class="flex items-center justify-center w-full h-48 bg-gray-200">
                                    <CubeIcon class="w-12 h-12 text-gray-400" />
                                </div>
                            </div>
                        </Link>

                        <div class="p-4">
                            <Link :href="route('catalog.show', relatedProduct.id)">
                                <h3 class="mb-2 text-lg font-semibold text-gray-900 transition-colors hover:text-blue-600">
                                    {{ relatedProduct.nombre }}
                                </h3>
                            </Link>
                            
                            <div class="flex items-center justify-between">
                                <div class="text-xl font-bold text-blue-600">
                                    Bs {{ Number(relatedProduct.precio_final).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                                </div>
                                
                                <button @click="addRelatedToCart(relatedProduct)" 
                                    :disabled="relatedProduct.stock_disponible <= 0"
                                    class="px-3 py-1 text-sm text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
                                    <span v-if="relatedProduct.stock_disponible > 0">Agregar</span>
                                    <span v-else>Sin Stock</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de confirmación -->
        <div v-if="showAddedModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showAddedModal = false"></div>
                
                <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-sm sm:w-full sm:p-6">
                    <div class="text-center">
                        <CheckCircleIcon class="w-12 h-12 mx-auto mb-4 text-green-500" />
                        <h3 class="mb-2 text-lg font-medium text-gray-900">¡Producto agregado!</h3>
                        <p class="mb-4 text-gray-600">{{ lastAddedQuantity }} unidad(es) de {{ product.nombre }} agregado(s) a tu carrito</p>
                        
                        <div class="space-y-3">
                            <button @click="showAddedModal = false" 
                                class="w-full px-4 py-2 text-gray-800 bg-gray-200 rounded-lg hover:bg-gray-300">
                                Seguir Comprando
                            </button>
                            <Link :href="route('cart.index')" 
                                class="inline-block w-full px-4 py-2 text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                Ver Carrito
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import ShopLayout from '@/Layouts/ShopLayout.vue'
import { 
    CubeIcon, 
    CheckCircleIcon, 
    HomeIcon, 
    ChevronRightIcon,
    ShoppingCartIcon,
    MinusIcon,
    PlusIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    product: Object,
    relatedProducts: Array,
    categories: Array
})

const quantity = ref(1)
const showAddedModal = ref(false)
const lastAddedQuantity = ref(0)
const failedMainImage = ref(false)
const failedImages = ref({})

const incrementQuantity = () => {
    if (quantity.value < props.product.stock_disponible) {
        quantity.value++
    }
}

const decrementQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--
    }
}

const validateQuantity = () => {
    if (quantity.value < 1) {
        quantity.value = 1
    } else if (quantity.value > props.product.stock_disponible) {
        quantity.value = props.product.stock_disponible
    }
}

const addToCart = () => {
    const cart = JSON.parse(localStorage.getItem('cart') || '{"items": [], "total": 0}')
    
    // Buscar si el producto ya está en el carrito
    const existingItem = cart.items.find(item => item.product_id === props.product.id)
    
    if (existingItem) {
        // Si ya existe, incrementar cantidad (pero no exceder el stock)
        const newQuantity = existingItem.cantidad + quantity.value
        if (newQuantity <= props.product.stock_disponible) {
            existingItem.cantidad = newQuantity
            existingItem.subtotal = existingItem.cantidad * existingItem.precio
        } else {
            alert('No puedes agregar más unidades. Stock insuficiente.')
            return
        }
    } else {
        // Si no existe, agregarlo
        cart.items.push({
            product_id: props.product.id,
            nombre: props.product.nombre,
            precio: props.product.precio_final,
            cantidad: quantity.value,
            imagen: props.product.imagen_url,
            stock_disponible: props.product.stock_disponible,
            subtotal: quantity.value * props.product.precio_final
        })
    }
    
    // Recalcular total
    cart.total = cart.items.reduce((sum, item) => sum + item.subtotal, 0)
    
    // Guardar en localStorage
    localStorage.setItem('cart', JSON.stringify(cart))
    
    // Disparar evento para actualizar el contador del carrito
    window.dispatchEvent(new CustomEvent('cart-updated'))
    
    // Mostrar modal de confirmación
    lastAddedQuantity.value = quantity.value
    showAddedModal.value = true
    
    // Resetear cantidad a 1
    quantity.value = 1
    
    // Auto cerrar el modal después de 3 segundos
    setTimeout(() => {
        showAddedModal.value = false
    }, 3000)
}

const addRelatedToCart = (relatedProduct) => {
    const cart = JSON.parse(localStorage.getItem('cart') || '{"items": [], "total": 0}')
    
    // Buscar si el producto ya está en el carrito
    const existingItem = cart.items.find(item => item.product_id === relatedProduct.id)
    
    if (existingItem) {
        // Si ya existe, incrementar cantidad (pero no exceder el stock)
        if (existingItem.cantidad < relatedProduct.stock_disponible) {
            existingItem.cantidad += 1
            existingItem.subtotal = existingItem.cantidad * existingItem.precio
        } else {
            alert('No puedes agregar más unidades. Stock insuficiente.')
            return
        }
    } else {
        // Si no existe, agregarlo
        cart.items.push({
            product_id: relatedProduct.id,
            nombre: relatedProduct.nombre,
            precio: relatedProduct.precio_final,
            cantidad: 1,
            imagen: relatedProduct.imagen_url,
            stock_disponible: relatedProduct.stock_disponible,
            subtotal: relatedProduct.precio_final
        })
    }
    
    // Recalcular total
    cart.total = cart.items.reduce((sum, item) => sum + item.subtotal, 0)
    
    // Guardar en localStorage
    localStorage.setItem('cart', JSON.stringify(cart))
    
    // Disparar evento para actualizar el contador del carrito
    window.dispatchEvent(new CustomEvent('cart-updated'))
    
    // Mostrar notificación simple
    alert(`${relatedProduct.nombre} agregado al carrito`)
}

const handleMainImageError = () => {
    failedMainImage.value = true
}

const handleImageError = (productId) => {
    failedImages.value[productId] = true
}
</script>