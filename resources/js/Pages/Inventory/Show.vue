<template>
    <div>
        <Head :title="`Inventario - ${inventory.product.nombre}`" />
        <SidebarLayout>
            <div class="min-h-screen py-6 bg-gray-50 dark:bg-gray-900">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    
                    <!-- Header -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                                    Detalle de Inventario
                                </h1>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    {{ inventory.product.nombre }}
                                </p>
                            </div>
                            <div class="flex space-x-3">
                                <Link 
                                    :href="route('inventory.edit', inventory.id)"
                                    v-if="$page.props.auth.user.permissions?.includes('edit.inventory')"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    <PencilIcon class="w-4 h-4 mr-2" />
                                    Editar
                                </Link>
                                <Link 
                                    :href="route('inventory.index')"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    <ArrowLeftIcon class="w-4 h-4 mr-2" />
                                    Volver
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                        
                        <!-- Información Principal -->
                        <div class="lg:col-span-2">
                            
                            <!-- Información del Producto -->
                            <div class="p-6 mb-6 bg-white rounded-lg shadow dark:bg-gray-800">
                                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                                    Información del Producto
                                </h3>
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-20 h-20 bg-indigo-100 rounded-lg flex items-center justify-center">
                                            <CubeIcon class="w-10 h-10 text-indigo-600" />
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-xl font-semibold text-gray-900 dark:text-white">
                                            {{ inventory.product.nombre }}
                                        </h4>
                                        <p class="mt-1 text-gray-600 dark:text-gray-400">
                                            {{ inventory.product.descripcion }}
                                        </p>
                                        <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
                                            <div>
                                                <span class="font-medium text-gray-500">Categoría:</span>
                                                <span class="ml-2 text-gray-900 dark:text-white">
                                                    {{ inventory.product.category.nombre }}
                                                </span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-500">Unidad:</span>
                                                <span class="ml-2 text-gray-900 dark:text-white">
                                                    {{ inventory.product.measurement.nombre }}
                                                </span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-500">Proveedor:</span>
                                                <span class="ml-2 text-gray-900 dark:text-white">
                                                    {{ inventory.product.supplier?.nombre_empresa || 'Sin asignar' }}
                                                </span>
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-500">Código:</span>
                                                <span class="ml-2 text-gray-900 dark:text-white">
                                                    #{{ String(inventory.product.id).padStart(6, '0') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Stock y Precios -->
                            <div class="p-6 mb-6 bg-white rounded-lg shadow dark:bg-gray-800">
                                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                                    Stock y Precios
                                </h3>
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    
                                    <!-- Cantidad Actual -->
                                    <div class="p-4 rounded-lg" :class="stockBadgeClass">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium" :class="stockTextClass">Stock Actual</p>
                                                <p class="text-2xl font-bold" :class="stockTextClass">
                                                    {{ inventory.cantidad_actual }} {{ inventory.product.measurement.nombre }}
                                                </p>
                                            </div>
                                            <div class="p-3 rounded-full" :class="stockIconBg">
                                                <CubeIcon class="w-6 h-6" :class="stockIconClass" />
                                            </div>
                                        </div>
                                        <p class="mt-2 text-sm" :class="stockTextClass">{{ stockStatusText }}</p>
                                    </div>

                                    <!-- Precio Efectivo -->
                                    <div class="p-4 bg-green-50 rounded-lg dark:bg-green-900">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-green-800 dark:text-green-200">Precio de Venta</p>
                                                <p class="text-2xl font-bold text-green-900 dark:text-green-100">
                                                    Bs {{ precioEfectivo.toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                                                </p>
                                            </div>
                                            <div class="p-3 bg-green-100 rounded-full dark:bg-green-800">
                                                <svg class="w-6 h-6 text-green-600 dark:text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <p class="mt-2 text-sm text-green-700 dark:text-green-300">
                                            {{ inventory.precio_venta ? 'Precio específico' : 'Precio base del producto' }}
                                        </p>
                                    </div>

                                    <!-- Stock Mínimo -->
                                    <div class="p-4 bg-yellow-50 rounded-lg dark:bg-yellow-900">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Stock Mínimo</p>
                                                <p class="text-lg font-bold text-yellow-900 dark:text-yellow-100">
                                                    {{ inventory.cantidad_minima }} {{ inventory.product.measurement.nombre }}
                                                </p>
                                            </div>
                                            <div class="p-3 bg-yellow-100 rounded-full dark:bg-yellow-800">
                                                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <p class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                            Nivel de alerta de stock
                                        </p>
                                    </div>

                                    <!-- Stock Máximo -->
                                    <div v-if="inventory.cantidad_maxima" class="p-4 bg-blue-50 rounded-lg dark:bg-blue-900">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Stock Máximo</p>
                                                <p class="text-lg font-bold text-blue-900 dark:text-blue-100">
                                                    {{ inventory.cantidad_maxima }} {{ inventory.product.measurement.nombre }}
                                                </p>
                                            </div>
                                            <div class="p-3 bg-blue-100 rounded-full dark:bg-blue-800">
                                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <p class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                            Capacidad máxima
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="space-y-6">
                            
                            <!-- Acciones Rápidas -->
                            <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
                                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                                    Acciones Rápidas
                                </h3>
                                <div class="space-y-3">
                                    <Link 
                                        :href="route('inventory.movements.entrada.create', { producto_id: inventory.producto_id })"
                                        class="flex items-center w-full px-4 py-2 text-sm font-medium text-green-700 bg-green-100 border border-green-300 rounded-md hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Registrar Entrada
                                    </Link>
                                    <Link 
                                        v-if="inventory.cantidad_actual > 0"
                                        :href="route('inventory.movements.salida.create', { producto_id: inventory.producto_id })"
                                        class="flex items-center w-full px-4 py-2 text-sm font-medium text-red-700 bg-red-100 border border-red-300 rounded-md hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                        Registrar Salida
                                    </Link>
                                    <Link 
                                        :href="route('inventory.movements.ajuste.create', { producto_id: inventory.producto_id })"
                                        class="flex items-center w-full px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 border border-blue-300 rounded-md hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Ajustar Stock
                                    </Link>
                                </div>
                            </div>

                            <!-- Información Financiera -->
                            <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
                                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                                    Valor del Inventario
                                </h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Valor Total:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">
                                            Bs {{ valorTotal.toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Precio Unitario:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">
                                            Bs {{ precioEfectivo.toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                                        </span>
                                    </div>
                                    <div class="pt-3 border-t border-gray-200 dark:border-gray-600">
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Stock × Precio:</span>
                                            <span class="font-bold text-indigo-600 dark:text-indigo-400">
                                                {{ inventory.cantidad_actual }} × Bs {{ precioEfectivo.toLocaleString('es-BO', { minimumFractionDigits: 2 }) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Estado del Stock -->
                            <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
                                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                                    Estado del Stock
                                </h3>
                                <div class="flex items-center p-3 rounded-lg" :class="stockBadgeClass">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center" :class="stockIconBg">
                                            <div class="w-3 h-3 rounded-full" :class="stockDotClass"></div>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-medium" :class="stockTextClass">{{ stockStatusText }}</p>
                                        <p class="text-sm" :class="stockSubTextClass">{{ stockDescription }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </SidebarLayout>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { ArrowLeftIcon, PencilIcon, CubeIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    inventory: {
        type: Object,
        required: true
    }
})

const precioEfectivo = computed(() => {
    return props.inventory.precio_venta || props.inventory.product.precio_venta || 0
})

const valorTotal = computed(() => {
    return props.inventory.cantidad_actual * precioEfectivo.value
})

const stockStatus = computed(() => {
    const actual = parseFloat(props.inventory.cantidad_actual)
    const minimo = parseFloat(props.inventory.cantidad_minima)
    const maximo = parseFloat(props.inventory.cantidad_maxima)

    if (actual <= 0) return 'sin_stock'
    if (actual <= minimo) return 'stock_bajo'
    if (maximo && actual >= maximo) return 'stock_alto'
    return 'stock_normal'
})

const stockBadgeClass = computed(() => {
    const classes = {
        sin_stock: 'bg-red-50 dark:bg-red-900',
        stock_bajo: 'bg-yellow-50 dark:bg-yellow-900',
        stock_alto: 'bg-blue-50 dark:bg-blue-900',
        stock_normal: 'bg-green-50 dark:bg-green-900'
    }
    return classes[stockStatus.value]
})

const stockTextClass = computed(() => {
    const classes = {
        sin_stock: 'text-red-900 dark:text-red-100',
        stock_bajo: 'text-yellow-900 dark:text-yellow-100',
        stock_alto: 'text-blue-900 dark:text-blue-100',
        stock_normal: 'text-green-900 dark:text-green-100'
    }
    return classes[stockStatus.value]
})

const stockSubTextClass = computed(() => {
    const classes = {
        sin_stock: 'text-red-700 dark:text-red-300',
        stock_bajo: 'text-yellow-700 dark:text-yellow-300',
        stock_alto: 'text-blue-700 dark:text-blue-300',
        stock_normal: 'text-green-700 dark:text-green-300'
    }
    return classes[stockStatus.value]
})

const stockIconClass = computed(() => {
    const classes = {
        sin_stock: 'text-red-600 dark:text-red-400',
        stock_bajo: 'text-yellow-600 dark:text-yellow-400',
        stock_alto: 'text-blue-600 dark:text-blue-400',
        stock_normal: 'text-green-600 dark:text-green-400'
    }
    return classes[stockStatus.value]
})

const stockIconBg = computed(() => {
    const classes = {
        sin_stock: 'bg-red-100 dark:bg-red-800',
        stock_bajo: 'bg-yellow-100 dark:bg-yellow-800',
        stock_alto: 'bg-blue-100 dark:bg-blue-800',
        stock_normal: 'bg-green-100 dark:bg-green-800'
    }
    return classes[stockStatus.value]
})

const stockDotClass = computed(() => {
    const classes = {
        sin_stock: 'bg-red-400',
        stock_bajo: 'bg-yellow-400',
        stock_alto: 'bg-blue-400',
        stock_normal: 'bg-green-400'
    }
    return classes[stockStatus.value]
})

const stockStatusText = computed(() => {
    const texts = {
        sin_stock: 'Sin Stock',
        stock_bajo: 'Stock Bajo',
        stock_alto: 'Stock Alto',
        stock_normal: 'Stock Normal'
    }
    return texts[stockStatus.value]
})

const stockDescription = computed(() => {
    const descriptions = {
        sin_stock: 'Producto agotado - requiere reposición urgente',
        stock_bajo: `Nivel por debajo del mínimo (${props.inventory.cantidad_minima})`,
        stock_alto: `Nivel cerca del máximo (${props.inventory.cantidad_maxima})`,
        stock_normal: 'Nivel de inventario óptimo'
    }
    return descriptions[stockStatus.value]
})
</script>