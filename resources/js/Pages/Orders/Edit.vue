<template>
    <div>
        <Head :title="`Editar Orden #${order.numero_orden}`" />
        <SidebarLayout>
            <div class="min-h-screen py-6 bg-gray-50 dark:bg-gray-900">
                <div class="max-w-4xl px-4 mx-auto sm:px-6 lg:px-8">
                    
                    <!-- Header -->
                    <div class="mb-8">
                        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                                        Editar Orden #{{ order.numero_orden }}
                                    </h1>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        Modificar los detalles de la venta
                                    </p>
                                </div>
                                <div class="flex space-x-3">
                                    <Link 
                                        :href="route('orders.show', order.id)"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700"
                                    >
                                        <ArrowLeftIcon class="w-4 h-4 mr-2" />
                                        Volver
                                    </Link>
                                </div>
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
                                    
                                    <!-- Cliente (solo para ventas presenciales) -->
                                    <div v-if="order.tipo === 'presencial'">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Cliente *
                                        </label>
                                        <select 
                                            v-model="form.cliente_id"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
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

                                    <!-- Usuario (solo para ventas online) -->
                                    <div v-else>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Usuario
                                        </label>
                                        <input 
                                            type="text" 
                                            :value="order.user?.name || 'Usuario no identificado'"
                                            readonly
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white sm:text-sm"
                                        />
                                    </div>

                                    <!-- Método de pago -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Método de Pago *
                                        </label>
                                        <select 
                                            v-model="form.metodo_pago"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
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

                                    <!-- Estado -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Estado *
                                        </label>
                                        <select 
                                            v-model="form.estado"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                            :class="{'border-red-300 focus:border-red-500 focus:ring-red-500': errors.estado}"
                                            required
                                        >
                                            <option value="pendiente">Pendiente</option>
                                            <option value="confirmado">Confirmado</option>
                                            <option value="en_proceso">En Proceso</option>
                                            <option value="listo_retiro">Listo para Retiro</option>
                                            <option value="completado">Completado</option>
                                            <option value="cancelado">Cancelado</option>
                                        </select>
                                        <p v-if="errors.estado" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                            {{ errors.estado }}
                                        </p>
                                    </div>

                                    <!-- Estado de Pago -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Estado de Pago *
                                        </label>
                                        <select 
                                            v-model="form.estado_pago"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                            :class="{'border-red-300 focus:border-red-500 focus:ring-red-500': errors.estado_pago}"
                                            required
                                        >
                                            <option value="pendiente">Pendiente</option>
                                            <option value="pagado">Pagado</option>
                                            <option value="fallido">Fallido</option>
                                        </select>
                                        <p v-if="errors.estado_pago" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                            {{ errors.estado_pago }}
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
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                        placeholder="Notas adicionales sobre la venta..."
                                    ></textarea>
                                    <p v-if="errors.notas" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        {{ errors.notas }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Productos (Solo editable en estado pendiente) -->
                        <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        Productos
                                    </h3>
                                    <button 
                                        v-if="form.estado === 'pendiente'"
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
                                
                                <!-- Productos editables -->
                                <div v-if="form.estado === 'pendiente'" class="space-y-4">
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
                                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
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
                                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
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
                                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                    required
                                                />
                                            </div>

                                            <!-- Subtotal y acciones -->
                                            <div class="flex items-end justify-between">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        Subtotal
                                                    </label>
                                                    <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                                                        ${{ (parseFloat(item.subtotal) || 0).toLocaleString() }}
                                                    </div>
                                                </div>
                                                <button 
                                                    type="button"
                                                    @click="removeProduct(index)"
                                                    class="p-2 text-red-600 rounded-md hover:text-red-900 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-900"
                                                    title="Eliminar producto"
                                                >
                                                    <TrashIcon class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Productos no editables -->
                                <div v-else>
                                    <div class="p-4 mb-4 border border-yellow-200 rounded-lg bg-yellow-50 dark:bg-yellow-900 dark:border-yellow-700">
                                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                            <strong>Nota:</strong> Los productos no pueden editarse una vez que la orden ha sido confirmada. 
                                            Para modificar productos, cambie el estado a "Pendiente" primero.
                                        </p>
                                    </div>
                                    
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                                        Producto
                                                    </th>
                                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                                        Cantidad
                                                    </th>
                                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                                        Precio
                                                    </th>
                                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                                        Subtotal
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                                <tr v-for="item in order.items" :key="item.id">
                                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                                        {{ item.product?.nombre || 'Producto no encontrado' }}
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                                        {{ item.cantidad }}
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                                        ${{ parseFloat(item.precio).toLocaleString() }}
                                                    </td>
                                                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                                        ${{ (parseFloat(item.subtotal) || 0).toLocaleString() }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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
                                <div class="flex items-center justify-between text-lg font-semibold">
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
                                :href="route('orders.show', order.id)"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700"
                            >
                                Cancelar
                            </Link>
                            <button 
                                type="submit"
                                :disabled="processing"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="processing">Guardando...</span>
                                <span v-else>Guardar Cambios</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </SidebarLayout>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { 
    ArrowLeftIcon, 
    PlusIcon, 
    TrashIcon
} from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
    order: {
        type: Object,
        required: true
    },
    clients: {
        type: Array,
        default: () => []
    },
    products: {
        type: Array,
        default: () => []
    },
    errors: {
        type: Object,
        default: () => ({})
    }
})

// Form
const form = useForm({
    tipo: props.order.tipo,
    cliente_id: props.order.cliente_id,
    metodo_pago: props.order.metodo_pago,
    estado: props.order.estado,
    estado_pago: props.order.estado_pago,
    notas: props.order.notas,
    total: props.order.total,
    items: []
})

// State
const processing = ref(false)

// Computed
const availableProducts = computed(() => {
    return props.products.filter(product => product.stock > 0)
})

const totalVenta = computed(() => {
    if (form.estado === 'pendiente') {
        return form.items.reduce((total, item) => {
            return total + (parseFloat(item.subtotal) || 0)
        }, 0)
    }
    return parseFloat(props.order.total || 0)
})

// Methods
const addProduct = () => {
    form.items.push({
        producto_id: '',
        cantidad: 1,
        precio: 0,
        subtotal: 0
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
    item.subtotal = cantidad * precio
}

const submit = () => {
    processing.value = true
    
    // Solo actualizar total si los productos son editables
    if (form.estado === 'pendiente') {
        form.total = totalVenta.value
    }
    
    form.patch(route('orders.update', props.order.id), {
        onFinish: () => {
            processing.value = false
        }
    })
}

// Initialize form items with existing order items for pending orders
onMounted(() => {
    if (form.estado === 'pendiente') {
        form.items = props.order.items.map(item => ({
            id: item.id,
            producto_id: item.producto_id,
            cantidad: item.cantidad,
            precio: parseFloat(item.precio) || 0,
            subtotal: parseFloat(item.subtotal) || 0
        }))
        
        // Recalcular subtotales después de cargar los datos
        setTimeout(() => {
            form.items.forEach((item, index) => {
                calculateSubtotal(index)
            })
        }, 100)
    }
})

// Watch para recalcular totales cuando cambien cantidad o precio
watch(() => form.items, (newItems) => {
    newItems.forEach((item, index) => {
        // Asegurar que cantidad y precio sean números válidos
        if (item.cantidad && item.precio) {
            calculateSubtotal(index)
        }
    })
}, { deep: true })

// Watch específico para cambios en cantidad
watch(() => form.items.map(item => item.cantidad), () => {
    form.items.forEach((item, index) => {
        if (item.cantidad && item.precio) {
            calculateSubtotal(index)
        }
    })
}, { deep: true })

// Watch específico para cambios en precio
watch(() => form.items.map(item => item.precio), () => {
    form.items.forEach((item, index) => {
        if (item.cantidad && item.precio) {
            calculateSubtotal(index)
        }
    })
}, { deep: true })
</script>