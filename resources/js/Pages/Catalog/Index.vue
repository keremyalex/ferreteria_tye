<template>
    <Head title="Catálogo de Productos" />
    <ShopLayout :categories="categories">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Filtros y ordenamiento -->
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-8 space-y-4 lg:space-y-0">
                <div class="flex items-center space-x-4">
                    <select v-model="sortOption" @change="updateSort" class="border border-gray-300 rounded-lg px-4 py-2">
                        <option value="nombre-asc">Nombre A-Z</option>
                        <option value="nombre-desc">Nombre Z-A</option>
                        <option value="precio_venta-asc">Precio: Menor a Mayor</option>
                        <option value="precio_venta-desc">Precio: Mayor a Menor</option>
                        <option value="created_at-desc">Más Recientes</option>
                    </select>
                </div>
                
                <div class="text-sm text-gray-600">
                    Mostrando {{ products.from }} - {{ products.to }} de {{ products.total }} productos
                </div>
            </div>

            <!-- Grid de productos -->
            <div v-if="products.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8">
                <div v-for="product in products.data" :key="product.id" 
                    class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    
                    <Link :href="route('catalog.show', product.id)" class="block">
                        <div class="aspect-w-1 aspect-h-1 w-full">
                            <img v-if="product.imagen_url && !failedImages[product.id]" 
                                :src="product.imagen_url" 
                                :alt="product.nombre"
                                class="w-full h-48 object-cover"
                                @error="handleImageError(product.id)"
                            />
                            <div v-else class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <CubeIcon class="h-12 w-12 text-gray-400" />
                                <span class="ml-2 text-gray-500 text-sm">{{ product.nombre.split(' ')[0] }}</span>
                            </div>
                        </div>
                    </Link>

                    <div class="p-4">
                        <Link :href="route('catalog.show', product.id)">
                            <h3 class="text-lg font-semibold text-gray-900 hover:text-blue-600 transition-colors mb-2">
                                {{ product.nombre }}
                            </h3>
                        </Link>
                        
                        <p class="text-sm text-gray-600 mb-2 line-clamp-2">
                            {{ product.descripcion }}
                        </p>

                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs text-gray-500">{{ product.category.nombre }}</span>
                            <span class="text-xs text-gray-500">Stock: {{ product.stock_disponible }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="text-2xl font-bold text-blue-600">
                                Bs {{ Number(product.precio_final).toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                            </div>
                            
                            <button @click="addToCart(product)" 
                                :disabled="product.stock_disponible <= 0"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                                <span v-if="product.stock_disponible > 0">Agregar</span>
                                <span v-else>Sin Stock</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estado vacío -->
            <div v-else class="text-center py-12">
                <CubeIcon class="mx-auto h-16 w-16 text-gray-400 mb-4" />
                <h3 class="text-lg font-medium text-gray-900 mb-2">No hay productos disponibles</h3>
                <p class="text-gray-600">
                    <span v-if="filters.search">No se encontraron productos con el término "{{ filters.search }}"</span>
                    <span v-else>No hay productos en esta categoría en este momento</span>
                </p>
            </div>

            <!-- Paginación -->
            <div v-if="products.data.length > 0 && products.links" class="flex justify-center">
                <nav class="flex space-x-2">
                    <template v-for="link in products.links" :key="link.label">
                        <Link v-if="link && link.url"
                            :href="link.url"
                            v-html="link.label"
                            :class="[
                                'px-3 py-2 text-sm font-medium rounded-md',
                                link.active 
                                    ? 'bg-blue-600 text-white' 
                                    : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
                            ]"
                        />
                        <span v-else-if="link"
                            v-html="link.label"
                            class="px-3 py-2 text-sm font-medium rounded-md bg-gray-100 text-gray-400 cursor-not-allowed"
                        />
                    </template>
                </nav>
            </div>
        </div>

        <!-- Modal de confirmación -->
        <div v-if="showAddedModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showAddedModal = false"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full sm:p-6">
                    <div class="text-center">
                        <CheckCircleIcon class="mx-auto h-12 w-12 text-green-500 mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">¡Producto agregado!</h3>
                        <p class="text-gray-600 mb-4">{{ lastAddedProduct?.nombre }} se agregó a tu carrito</p>
                        
                        <div class="space-y-3">
                            <button @click="showAddedModal = false" 
                                class="w-full bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">
                                Seguir Comprando
                            </button>
                            <Link :href="route('cart.index')" 
                                class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 inline-block text-center">
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
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import ShopLayout from '@/Layouts/ShopLayout.vue'
import { CubeIcon, CheckCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object
})

const sortOption = ref((() => {
    const sort = props.filters?.sort || 'nombre'
    const direction = props.filters?.direction || 'asc'
    return `${sort}-${direction}`
})())

const showAddedModal = ref(false)
const lastAddedProduct = ref(null)
const failedImages = ref({})

const updateSort = () => {
    const [field, direction] = sortOption.value.split('-')
    router.get(route('catalog.index'), {
        ...props.filters,
        sort: field,
        direction: direction
    }, {
        preserveState: true,
        preserveScroll: true
    })
}

const addToCart = (product) => {
    const cart = JSON.parse(localStorage.getItem('cart') || '{"items": [], "total": 0}')
    
    // Buscar si el producto ya está en el carrito
    const existingItem = cart.items.find(item => item.product_id === product.id)
    
    if (existingItem) {
        // Si ya existe, incrementar cantidad (pero no exceder el stock)
        if (existingItem.cantidad < product.stock_disponible) {
            existingItem.cantidad += 1
            existingItem.subtotal = existingItem.cantidad * existingItem.precio
        } else {
            alert('No puedes agregar más unidades. Stock insuficiente.')
            return
        }
    } else {
        // Si no existe, agregarlo
        cart.items.push({
            product_id: product.id,
            nombre: product.nombre,
            precio: product.precio_final,
            cantidad: 1,
            imagen: product.imagen_url,
            stock_disponible: product.stock_disponible,
            subtotal: product.precio_final
        })
    }
    
    // Recalcular total
    cart.total = cart.items.reduce((sum, item) => sum + item.subtotal, 0)
    
    // Guardar en localStorage
    localStorage.setItem('cart', JSON.stringify(cart))
    
    // Disparar evento para actualizar el contador del carrito
    window.dispatchEvent(new CustomEvent('cart-updated'))
    
    // Mostrar modal de confirmación
    lastAddedProduct.value = product
    showAddedModal.value = true
    
    // Auto cerrar el modal después de 3 segundos
    setTimeout(() => {
        showAddedModal.value = false
    }, 3000)
}

const handleImageError = (productId) => {
    failedImages.value[productId] = true
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>