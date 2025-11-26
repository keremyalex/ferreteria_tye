<template>
    <div>
        <Head title="Nueva Entrada - Inventario" />
        <SidebarLayout>
            <div class="min-h-screen py-6 bg-gray-50 dark:bg-gray-900">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    
                    <!-- Header -->
                    <div class="mb-8">
                        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h1 class="text-3xl font-bold text-green-900 dark:text-green-100">
                                        Nueva Entrada de Inventario
                                    </h1>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        Registrar mercancía recibida de proveedores
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

                            <div class="p-4 rounded-lg bg-green-50 dark:bg-green-900">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <InformationCircleIcon class="w-5 h-5 text-green-400" />
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-green-800 dark:text-green-200">
                                            Entrada de Inventario
                                        </h3>
                                        <p class="mt-1 text-sm text-green-700 dark:text-green-300">
                                            Las entradas aumentan el stock disponible. Agregue los productos recibidos con sus cantidades exactas.
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
                                            Fecha de Entrada *
                                        </label>
                                        <input
                                            v-model="form.fecha"
                                            type="datetime-local"
                                            id="fecha"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                            required
                                        />
                                        <div v-if="form.errors.fecha" class="mt-1 text-sm text-red-600">{{ form.errors.fecha }}</div>
                                    </div>

                                    <!-- Referencia -->
                                    <div>
                                        <label for="referencia" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Referencia / N° de Documento
                                        </label>
                                        <input
                                            v-model="form.referencia"
                                            type="text"
                                            id="referencia"
                                            placeholder="Factura #123, Orden #456..."
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
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
                                        placeholder="Detalles adicionales sobre la entrada..."
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
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
                                        Productos a Ingresar
                                    </h3>
                                    <button
                                        type="button"
                                        @click="addProduct"
                                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                    >
                                        <PlusIcon class="w-4 h-4 mr-2" />
                                        Agregar Producto
                                    </button>
                                </div>
                            </div>

                            <div class="p-6">
                                <div v-if="form.productos.length === 0" class="text-center py-8">
                                    <ClipboardDocumentListIcon class="mx-auto h-12 w-12 text-gray-400" />
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay productos agregados</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comience agregando productos a la entrada de inventario.</p>
                                </div>

                                <div v-else class="space-y-4">
                                    <div 
                                        v-for="(item, index) in form.productos" 
                                        :key="index"
                                        class="p-4 border border-gray-200 rounded-lg dark:border-gray-600"
                                    >
                                        <div class="grid grid-cols-1 gap-4 md:grid-cols-6">
                                            <!-- Producto -->
                                            <div class="md:col-span-3">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Producto *
                                                </label>
                                                <select
                                                    v-model="item.producto_id"
                                                    @change="updateProductInfo(index)"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                    required
                                                >
                                                    <option value="">Seleccionar producto...</option>
                                                    <option 
                                                        v-for="product in products" 
                                                        :key="product.id" 
                                                        :value="product.id"
                                                    >
                                                        {{ product.nombre }} - {{ product.category?.nombre }}
                                                    </option>
                                                </select>
                                            </div>

                                            <!-- Cantidad -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Cantidad *
                                                </label>
                                                <input
                                                    v-model.number="item.cantidad"
                                                    type="number"
                                                    min="0.01"
                                                    step="0.01"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                    required
                                                />
                                            </div>

                                            <!-- Precio Unitario -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Precio Unitario
                                                </label>
                                                <input
                                                    v-model.number="item.precio_unitario"
                                                    type="number"
                                                    min="0"
                                                    step="0.01"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
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
                                        <div v-if="getProductInfo(item.producto_id)" class="mt-4 p-3 bg-gray-50 rounded-md dark:bg-gray-700">
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-gray-600 dark:text-gray-400">
                                                    Stock actual: <strong>{{ getProductInfo(item.producto_id).inventory?.cantidad_actual || 0 }}</strong>
                                                    {{ getProductInfo(item.producto_id).measurement?.simbolo }}
                                                </span>
                                                <span class="text-green-600 dark:text-green-400">
                                                    Nuevo stock: <strong>{{ parseFloat(getProductInfo(item.producto_id).inventory?.cantidad_actual || 0) + parseFloat(item.cantidad || 0) }}</strong>
                                                    {{ getProductInfo(item.producto_id).measurement?.simbolo }}
                                                </span>
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
                                    Resumen
                                </h3>
                            </div>
                            
                            <div class="p-6">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                    <div class="p-4 rounded-lg bg-green-50 dark:bg-green-900">
                                        <dt class="text-sm font-medium text-green-600 dark:text-green-400">Total Productos</dt>
                                        <dd class="text-2xl font-bold text-green-900 dark:text-green-100">{{ form.productos.length }}</dd>
                                    </div>
                                    
                                    <div class="p-4 rounded-lg bg-blue-50 dark:bg-blue-900">
                                        <dt class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Unidades</dt>
                                        <dd class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ totalUnidades }}</dd>
                                    </div>
                                    
                                    <div class="p-4 rounded-lg bg-purple-50 dark:bg-purple-900">
                                        <dt class="text-sm font-medium text-purple-600 dark:text-purple-400">Valor Total</dt>
                                        <dd class="text-2xl font-bold text-purple-900 dark:text-purple-100">${{ valorTotal.toLocaleString() }}</dd>
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
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
                            >
                                <span v-if="form.processing">Procesando...</span>
                                <span v-else>Crear Entrada</span>
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
    tipo: 'entrada',
    fecha: new Date().toISOString().slice(0, 16),
    referencia: '',
    observaciones: '',
    estado: 'aplicado',
    productos: []
})

const addProduct = () => {
    form.productos.push({
        producto_id: '',
        cantidad: 1,
        precio_unitario: 0
    })
}

const removeProduct = (index) => {
    form.productos.splice(index, 1)
}

const updateProductInfo = (index) => {
    const product = props.products.find(p => p.id == form.productos[index].producto_id)
    if (product && product.precio_venta) {
        form.productos[index].precio_unitario = product.precio_venta
    }
}

const getProductInfo = (productId) => {
    if (!productId) return null
    return props.products.find(p => p.id == productId)
}

const totalUnidades = computed(() => {
    return form.productos.reduce((total, item) => total + (item.cantidad || 0), 0)
})

const valorTotal = computed(() => {
    return form.productos.reduce((total, item) => {
        return total + ((item.cantidad || 0) * (item.precio_unitario || 0))
    }, 0)
})

const submit = () => {
    form.post(route('inventory.movements.entrada.store'), {
        onSuccess: () => {
            // Redirigir después de crear exitosamente
        }
    })
}
</script>