<template>
    <div>
        <Head title="Nueva Venta - Órdenes" />
        <SidebarLayout>
            <div class="min-h-screen py-6 bg-gray-50 dark:bg-gray-900">
                <div class="px-4 mx-auto max-w-4xl sm:px-6 lg:px-8">
                    
                    <!-- Header -->
                    <div class="mb-8">
                        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                                        Nueva Venta
                                    </h1>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        Crear una nueva venta presencial
                                    </p>
                                </div>
                                <Link 
                                    :href="route('orders.index')"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700"
                                >
                                    <ArrowLeftIcon class="w-4 h-4 mr-2" />
                                    Volver
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <!-- Información del Cliente -->
                        <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    Información del Cliente
                                </h3>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <!-- Selector de cliente -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Cliente *
                                        </label>
                                        <select 
                                            v-model="form.cliente_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                            :class="{'border-red-300 focus:border-red-500 focus:ring-red-500': errors.cliente_id}"
                                            required
                                        >
                                            <option value="">Seleccionar cliente</option>
                                            <option v-for="client in clients" :key="client.id" :value="client.id">
                                                {{ client.nombre }} - {{ client.telf }}
                                            </option>
                                        </select>
                                        <p v-if="errors.cliente_id" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                            {{ errors.cliente_id }}
                                        </p>
                                    </div>

                                    <!-- Método de pago -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Método de Pago *
                                        </label>
                                        <select 
                                            v-model="form.metodo_pago"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                            :class="{'border-red-300 focus:border-red-500 focus:ring-red-500': errors.metodo_pago}"
                                            required
                                        >
                                            <option value="">Seleccionar método</option>
                                            <option value="efectivo">Efectivo</option>
                                            <option value="tarjeta">Tarjeta</option>
                                            <option value="transferencia">Transferencia</option>
                                        </select>
                                        <p v-if="errors.metodo_pago" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                            {{ errors.metodo_pago }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Notas -->
                                <div class="mt-6">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Notas
                                    </label>
                                    <textarea 
                                        v-model="form.notas"
                                        rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                        placeholder="Notas adicionales sobre la venta..."
                                    ></textarea>
                                    <p v-if="errors.notas" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        {{ errors.notas }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Productos -->
                        <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        Productos
                                    </h3>
                                    <button 
                                        type="button"
                                        @click="addProduct"
                                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    >
                                        <PlusIcon class="w-4 h-4 mr-2" />
                                        Agregar Producto
                                    </button>
                                </div>
                            </div>
                            <div class="p-6">
                                
                                <!-- Lista de productos -->
                                <div v-if="form.items.length > 0" class="space-y-4">
                                    <div 
                                        v-for="(item, index) in form.items" 
                                        :key="index"
                                        class="p-4 border border-gray-200 rounded-lg dark:border-gray-600"
                                    >
                                        <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                                            <!-- Producto -->
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Producto *
                                                </label>
                                                <select 
                                                    v-model="item.producto_id"
                                                    @change="updateProductPrice(index)"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                    required
                                                >
                                                    <option value="">Seleccionar producto</option>
                                                    <option 
                                                        v-for="product in availableProducts" 
                                                        :key="product.id" 
                                                        :value="product.id"
                                                    >
                                                        {{ product.nombre }} ({{ product.stock }} disponibles)
                                                    </option>
                                                </select>
                                            </div>

                                            <!-- Cantidad -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Cantidad *
                                                </label>
                                                <input 
                                                    type="number" 
                                                    v-model="item.cantidad"
                                                    @input="calculateSubtotal(index)"
                                                    min="1"
                                                    :max="getMaxStock(item.producto_id)"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                    required
                                                />
                                            </div>

                                            <!-- Precio -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Precio *
                                                </label>
                                                <input 
                                                    type="number" 
                                                    v-model="item.precio"
                                                    @input="calculateSubtotal(index)"
                                                    step="0.01"
                                                    min="0"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                    required
                                                />
                                            </div>

                                            <!-- Subtotal y acciones -->
                                            <div class="flex items-end justify-between">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        Total
                                                    </label>
                                                    <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                                                        ${{ item.total?.toLocaleString() || '0' }}
                                                    </div>
                                                </div>
                                                <button 
                                                    type="button"
                                                    @click="removeProduct(index)"
                                                    class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-md dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-900"
                                                    title="Eliminar producto"
                                                >
                                                    <TrashIcon class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mensaje cuando no hay productos -->
                                <div v-else class="text-center py-12">
                                    <ShoppingCartIcon class="w-12 h-12 mx-auto mb-4 text-gray-400" />
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        No hay productos agregados. Haz clic en "Agregar Producto" para comenzar.
                                    </p>
                                </div>

                                <!-- Error de items -->
                                <p v-if="errors.items" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {{ errors.items }}
                                </p>
                            </div>
                        </div>

                        <!-- Resumen -->
                        <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    Resumen
                                </h3>
                            </div>
                            <div class="p-6">
                                <div class="flex justify-between items-center text-lg font-semibold">
                                    <span class="text-gray-900 dark:text-white">Total:</span>
                                    <span class="text-2xl text-blue-600 dark:text-blue-400">
                                        ${{ totalVenta.toLocaleString() }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="flex justify-end space-x-3">
                            <Link 
                                :href="route('orders.index')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700"
                            >
                                Cancelar
                            </Link>
                            <button 
                                type="submit"
                                :disabled="processing || form.items.length === 0"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="processing">Guardando...</span>
                                <span v-else>Crear Venta</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </SidebarLayout>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { 
    ArrowLeftIcon, 
    PlusIcon, 
    TrashIcon,
    ShoppingCartIcon
} from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
    clients: {
        type: Array,
        required: true
    },
    products: {
        type: Array,
        required: true
    },
    errors: {
        type: Object,
        default: () => ({})
    }
})

// Form
const form = useForm({
    tipo: 'presencial',
    cliente_id: '',
    metodo_pago: '',
    estado_pago: 'pendiente', // Agregar campo requerido
    notas: '',
    subtotal: 0, // Agregar campo requerido
    total: 0,
    items: []
})

// State
const processing = ref(false)

// Computed
const availableProducts = computed(() => {
    return props.products.filter(product => product.stock > 0)
})

const totalVenta = computed(() => {
    return form.items.reduce((total, item) => {
        return total + (parseFloat(item.total) || 0)
    }, 0)
})

// Methods
const addProduct = () => {
    form.items.push({
        producto_id: '',
        cantidad: 1,
        precio: 0,
        total: 0
    })
}

const removeProduct = (index) => {
    form.items.splice(index, 1)
}

const getMaxStock = (productId) => {
    const product = props.products.find(p => p.id == productId)
    return product ? product.stock : 0
}

const updateProductPrice = (index) => {
    const item = form.items[index]
    const product = props.products.find(p => p.id == item.producto_id)
    
    if (product) {
        item.precio = product.precio_venta
        calculateSubtotal(index)
    }
}

const calculateSubtotal = (index) => {
    const item = form.items[index]
    const cantidad = parseInt(item.cantidad) || 0
    const precio = parseFloat(item.precio) || 0
    item.total = cantidad * precio
}

const submit = () => {
    processing.value = true
    
    // Calcular subtotal y total
    const calculatedTotal = totalVenta.value
    form.subtotal = calculatedTotal // Subtotal es igual al total ya que no hay impuestos
    form.total = calculatedTotal
    
    form.post(route('orders.store'), {
        onFinish: () => {
            processing.value = false
        }
    })
}

// Watch para recalcular totales
watch(() => form.items, () => {
    // Recalcular subtotales si es necesario
}, { deep: true })
</script>