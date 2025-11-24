<template>
    <div>
        <Head title="Crear Inventario" />
        <SidebarLayout>
            <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        Crear Nuevo Inventario
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Registra un nuevo inventario con los productos y sus stocks
                                    </p>
                                </div>
                                <Link 
                                    :href="route('inventory.index')"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Volver al Inventario
                                </Link>
                            </div>
                        </div>

                        <form @submit.prevent="submit" class="px-4 py-5 sm:p-6">
                            <!-- Fecha del Inventario -->
                            <div class="mb-6">
                                <label for="fecha" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                    Fecha del Inventario <span class="text-red-500">*</span>
                                </label>
                                <input
                                    id="fecha"
                                    v-model="form.fecha"
                                    type="date"
                                    class="block w-full max-w-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                />
                            </div>

                            <!-- Productos -->
                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        Productos del Inventario
                                    </h3>
                                    <button
                                        type="button"
                                        @click="addProduct"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        Agregar Producto
                                    </button>
                                </div>

                                <div v-if="form.productos.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    <p class="mt-2">No hay productos agregados</p>
                                </div>

                                <!-- Lista de productos -->
                                <div v-else class="space-y-4">
                                    <div 
                                        v-for="(producto, index) in form.productos" 
                                        :key="index"
                                        class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600"
                                    >
                                        <div class="flex justify-between items-start mb-4">
                                            <h4 class="font-medium text-gray-900 dark:text-white">
                                                Producto {{ index + 1 }}
                                            </h4>
                                            <button
                                                type="button"
                                                @click="removeProduct(index)"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                            >
                                                Eliminar
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                            <!-- Producto -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                                    Producto <span class="text-red-500">*</span>
                                                </label>
                                                <select
                                                    v-model="producto.product_id"
                                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                >
                                                    <option value="">Seleccionar producto</option>
                                                    <option 
                                                        v-for="product in products" 
                                                        :key="product.id" 
                                                        :value="product.id"
                                                    >
                                                        {{ product.nombre }}
                                                    </option>
                                                </select>
                                            </div>

                                            <!-- Cantidad -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                                    Cantidad <span class="text-red-500">*</span>
                                                </label>
                                                <input
                                                    v-model.number="producto.cantidad"
                                                    type="number"
                                                    min="0"
                                                    placeholder="0"
                                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                />
                                            </div>

                                            <!-- Cantidad Mínima -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                                    Cantidad Mínima <span class="text-red-500">*</span>
                                                </label>
                                                <input
                                                    v-model.number="producto.cantidad_minima"
                                                    type="number"
                                                    min="0"
                                                    placeholder="0"
                                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                />
                                            </div>

                                            <!-- Precio de Venta -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                                    Precio de Venta <span class="text-red-500">*</span>
                                                </label>
                                                <input
                                                    v-model.number="producto.precio_venta"
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    placeholder="0.00"
                                                    class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <Link 
                                    :href="route('inventory.index')"
                                    class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-base font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                                >
                                    Cancelar
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing || form.productos.length === 0"
                                    class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    Crear Inventario
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </SidebarLayout>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'

const props = defineProps({
    products: Array,
})

const form = useForm({
    fecha: new Date().toISOString().split('T')[0],
    productos: []
})

const addProduct = () => {
    form.productos.push({
        product_id: '',
        cantidad: 0,
        cantidad_minima: 0,
        precio_venta: 0
    })
}

const removeProduct = (index) => {
    form.productos.splice(index, 1)
}

const submit = () => {
    form.post(route('inventory.store'))
}
</script>