<template>
    <div>
        <Head title="Ajuste de Inventario" />
        <SidebarLayout>
            <div class="min-h-screen py-6 bg-gray-50 dark:bg-gray-900">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    
                    <!-- Header -->
                    <div class="mb-8">
                        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h1 class="text-3xl font-bold text-blue-900 dark:text-blue-100">
                                        Ajuste de Inventario
                                    </h1>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        Corregir diferencias entre stock físico y sistema
                                    </p>
                                </div>
                                <Link 
                                    :href="route('inventory.movements.index')"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-600 dark:text-white dark:border-gray-500"
                                >
                                    <ArrowLeftIcon class="w-4 h-4 mr-2" />
                                    Volver
                                </Link>
                            </div>

                            <div class="p-4 rounded-lg bg-blue-50 dark:bg-blue-900">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <InformationCircleIcon class="w-5 h-5 text-blue-400" />
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                            Ajuste de Inventario
                                        </h3>
                                        <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                                            Use esta función para corregir diferencias entre el conteo físico y el stock del sistema. 
                                            Especifique el stock real encontrado, el sistema calculará automáticamente si es entrada o salida.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-8">
                        <!-- Información del Movimiento -->
                        <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    Información General
                                </h3>
                            </div>
                            
                            <div class="p-6">
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                                    <!-- Fecha -->
                                    <div>
                                        <label for="fecha" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Fecha de Conteo *
                                        </label>
                                        <input
                                            v-model="form.fecha"
                                            type="datetime-local"
                                            id="fecha"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                            required
                                        />
                                        <div v-if="form.errors.fecha" class="mt-1 text-sm text-red-600">{{ form.errors.fecha }}</div>
                                    </div>

                                    <!-- Referencia -->
                                    <div>
                                        <label for="referencia" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Referencia / Motivo
                                        </label>
                                        <input
                                            v-model="form.referencia"
                                            type="text"
                                            id="referencia"
                                            placeholder="Inventario físico, merma, corrección..."
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                        />
                                        <div v-if="form.errors.referencia" class="mt-1 text-sm text-red-600">{{ form.errors.referencia }}</div>
                                    </div>

                                    <!-- Estado (hidden) -->
                                    <input type="hidden" v-model="form.estado" value="aplicado" />
                                </div>

                                <!-- Observaciones -->
                                <div class="mt-6">
                                    <label for="observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Observaciones
                                    </label>
                                    <textarea
                                        v-model="form.observaciones"
                                        id="observaciones"
                                        rows="3"
                                        placeholder="Detalles del conteo físico, motivos de las diferencias..."
                                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                    ></textarea>
                                    <div v-if="form.errors.observaciones" class="mt-1 text-sm text-red-600">{{ form.errors.observaciones }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Productos -->
                        <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        Productos a Ajustar
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
                                <div v-if="form.productos.length === 0" class="py-8 text-center">
                                    <ClipboardDocumentListIcon class="w-12 h-12 mx-auto text-gray-400" />
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay productos agregados</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comience agregando productos que necesitan ajuste de inventario.</p>
                                </div>

                                <div v-else class="space-y-4">
                                    <div 
                                        v-for="(item, index) in form.productos" 
                                        :key="index"
                                        class="p-4 border border-gray-200 rounded-lg dark:border-gray-600"
                                    >
                                        <div class="grid grid-cols-1 gap-4 md:grid-cols-6">
                                            <!-- Producto -->
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Producto *
                                                </label>
                                                <select
                                                    v-model="item.producto_id"
                                                    @change="updateProductInfo(index)"
                                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                    required
                                                >
                                                    <option value="">Seleccionar producto...</option>
                                                    <option 
                                                        v-for="product in products" 
                                                        :key="product.id" 
                                                        :value="product.id"
                                                    >
                                                        {{ product.nombre }}
                                                    </option>
                                                </select>
                                            </div>

                                            <!-- Stock Actual -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Stock Sistema
                                                </label>
                                                <input
                                                    :value="getStockActual(item.producto_id)"
                                                    type="number"
                                                    readonly
                                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm bg-gray-50 dark:border-gray-600 dark:bg-gray-600 dark:text-white sm:text-sm"
                                                />
                                            </div>

                                            <!-- Stock Real (Conteo) -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Stock Real *
                                                </label>
                                                <input
                                                    v-model.number="item.stock_real"
                                                    type="number"
                                                    min="0"
                                                    step="0.01"
                                                    placeholder="Conteo físico"
                                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                    required
                                                />
                                            </div>

                                            <!-- Diferencia -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Diferencia
                                                </label>
                                                <div class="flex items-center h-10 mt-1">
                                                    <span 
                                                        class="px-3 py-2 text-sm font-medium rounded"
                                                        :class="getDiferenciaClass(index)"
                                                    >
                                                        {{ getDiferencia(index) }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Precio -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Precio Unit.
                                                </label>
                                                <input
                                                    v-model.number="item.precio_unitario"
                                                    type="number"
                                                    min="0"
                                                    step="0.01"
                                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                />
                                            </div>

                                            <!-- Acciones -->
                                            <div class="flex items-end">
                                                <button
                                                    type="button"
                                                    @click="removeProduct(index)"
                                                    class="inline-flex items-center p-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                                >
                                                    <TrashIcon class="w-5 h-5" />
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Info del producto seleccionado -->
                                        <div v-if="getProductInfo(item.producto_id)" class="p-3 mt-4 rounded-md bg-gray-50 dark:bg-gray-700">
                                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                                <div class="flex items-center justify-between">
                                                    <span>
                                                        {{ getProductInfo(item.producto_id).category?.nombre }} - 
                                                        {{ getProductInfo(item.producto_id).measurement?.simbolo }}
                                                    </span>
                                                    <span class="font-medium">
                                                        {{ getDiferenciaTexto(index) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Errores de productos -->
                                <div v-if="form.errors.productos" class="mt-4 text-sm text-red-600">
                                    {{ form.errors.productos }}
                                </div>
                            </div>
                        </div>

                        <!-- Resumen -->
                        <div v-if="form.productos.length > 0" class="bg-white rounded-lg shadow dark:bg-gray-800">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    Resumen de Ajustes
                                </h3>
                            </div>
                            
                            <div class="p-6">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                                    <div class="p-4 rounded-lg bg-blue-50 dark:bg-blue-900">
                                        <dt class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Productos</dt>
                                        <dd class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ form.productos.length }}</dd>
                                    </div>
                                    
                                    <div class="p-4 rounded-lg bg-green-50 dark:bg-green-900">
                                        <dt class="text-sm font-medium text-green-600 dark:text-green-400">Incrementos</dt>
                                        <dd class="text-2xl font-bold text-green-900 dark:text-green-100">{{ incrementos }}</dd>
                                    </div>
                                    
                                    <div class="p-4 rounded-lg bg-red-50 dark:bg-red-900">
                                        <dt class="text-sm font-medium text-red-600 dark:text-red-400">Decrementos</dt>
                                        <dd class="text-2xl font-bold text-red-900 dark:text-red-100">{{ decrementos }}</dd>
                                    </div>
                                    
                                    <div class="p-4 rounded-lg bg-purple-50 dark:bg-purple-900">
                                        <dt class="text-sm font-medium text-purple-600 dark:text-purple-400">Valor Impacto</dt>
                                        <dd class="text-2xl font-bold text-purple-900 dark:text-purple-100">${{ valorImpacto.toLocaleString() }}</dd>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex justify-end space-x-3">
                            <Link 
                                :href="route('inventory.movements.index')"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-600 dark:text-white dark:border-gray-500"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing || form.productos.length === 0"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                            >
                                <span v-if="form.processing">Procesando...</span>
                                <span v-else>Crear Ajuste</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </SidebarLayout>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import {
    ArrowLeftIcon,
    InformationCircleIcon,
    PlusIcon,
    ClipboardDocumentListIcon,
    TrashIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    products: {
        type: Array,
        required: true
    }
})

const form = useForm({
    tipo: 'ajuste',
    fecha: new Date().toISOString().slice(0, 16),
    referencia: '',
    observaciones: '',
    estado: 'aplicado',
    productos: []
})

const addProduct = () => {
    form.productos.push({
        producto_id: '',
        stock_real: 0,
        precio_unitario: 0
    })
}

const removeProduct = (index) => {
    form.productos.splice(index, 1)
}

const updateProductInfo = (index) => {
    const product = props.products.find(p => p.id == form.productos[index].producto_id)
    if (product) {
        if (product.precio_venta) {
            form.productos[index].precio_unitario = product.precio_venta
        }
        // Auto-llenar con stock actual
        form.productos[index].stock_real = product.inventory?.cantidad_actual || 0
    }
}

const getProductInfo = (productId) => {
    if (!productId) return null
    return props.products.find(p => p.id == productId)
}

const getStockActual = (productId) => {
    const product = getProductInfo(productId)
    return product ? (product.inventory?.cantidad_actual || 0) : 0
}

const getDiferencia = (index) => {
    const item = form.productos[index]
    if (!item.producto_id || item.stock_real === null || item.stock_real === '') return '0'
    
    const stockActual = parseFloat(getStockActual(item.producto_id))
    const diferencia = parseFloat(item.stock_real) - stockActual
    
    return diferencia > 0 ? `+${diferencia}` : `${diferencia}`
}

const getDiferenciaClass = (index) => {
    const item = form.productos[index]
    if (!item.producto_id || item.stock_real === null || item.stock_real === '') return 'bg-gray-100 text-gray-700'
    
    const stockActual = parseFloat(getStockActual(item.producto_id))
    const diferencia = parseFloat(item.stock_real) - stockActual
    
    if (diferencia > 0) return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
    if (diferencia < 0) return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    return 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
}

const getDiferenciaTexto = (index) => {
    const item = form.productos[index]
    if (!item.producto_id || item.stock_real === null || item.stock_real === '') return ''
    
    const stockActual = parseFloat(getStockActual(item.producto_id))
    const diferencia = parseFloat(item.stock_real) - stockActual
    
    if (diferencia > 0) return `Se agregará ${diferencia} al stock`
    if (diferencia < 0) return `Se restará ${Math.abs(diferencia)} del stock`
    return 'Sin cambios'
}

const incrementos = computed(() => {
    return form.productos.filter(item => {
        const stockActual = getStockActual(item.producto_id)
        return item.stock_real > stockActual
    }).length
})

const decrementos = computed(() => {
    return form.productos.filter(item => {
        const stockActual = getStockActual(item.producto_id)
        return item.stock_real < stockActual
    }).length
})

const valorImpacto = computed(() => {
    return form.productos.reduce((total, item) => {
        const stockActual = getStockActual(item.producto_id)
        const diferencia = Math.abs(item.stock_real - stockActual)
        return total + (diferencia * (item.precio_unitario || 0))
    }, 0)
})

const submit = () => {
    // Calcular las cantidades basadas en la diferencia
    const productosConCantidad = form.productos.map(item => {
        const stockActual = parseFloat(getStockActual(item.producto_id))
        const cantidad = parseFloat(item.stock_real) - stockActual // Diferencia real (puede ser negativa)
        return {
            ...item,
            cantidad: cantidad
        }
    })

    form.transform((data) => ({
        ...data,
        productos: productosConCantidad
    })).post(route('inventory.movements.ajuste.store'), {
        onSuccess: () => {
            // Redirigir después de crear exitosamente
        }
    })
}
</script>