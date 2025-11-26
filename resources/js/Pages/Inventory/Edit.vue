<template>
    <div>
        <Head :title="`Editar Inventario - ${inventory.product.nombre}`" />
        <SidebarLayout>
            <div class="min-h-screen py-6 bg-gray-50 dark:bg-gray-900">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    
                    <!-- Header -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                                    Editar Inventario
                                </h1>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                    {{ inventory.product.nombre }}
                                </p>
                            </div>
                            <div class="flex space-x-3">
                                <Link 
                                    :href="route('inventory.show', inventory.id)"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    <EyeIcon class="w-4 h-4 mr-2" />
                                    Ver Detalle
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

                    <!-- Información del producto -->
                    <div class="p-6 mb-6 bg-white rounded-lg shadow dark:bg-gray-800">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <CubeIcon class="w-8 h-8 text-indigo-600" />
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ inventory.product.nombre }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ inventory.product.descripcion }}
                                </p>
                                <div class="flex items-center mt-2 space-x-4 text-sm">
                                    <span class="px-2 py-1 bg-gray-100 rounded-md dark:bg-gray-700">
                                        {{ inventory.product.category.nombre }}
                                    </span>
                                    <span class="text-gray-500">
                                        ID: {{ inventory.product.id }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                        <div class="p-6">
                            <form @submit.prevent="submit">
                                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                    
                                    <!-- Stock Actual -->
                                    <div>
                                        <label for="cantidad_actual" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Cantidad Actual <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            id="cantidad_actual"
                                            v-model="form.cantidad_actual"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                            :class="{ 'border-red-300': form.errors.cantidad_actual }"
                                        />
                                        <p class="mt-1 text-sm text-gray-500">
                                            Unidad: {{ inventory.product.measurement.nombre }}
                                        </p>
                                        <div v-if="form.errors.cantidad_actual" class="mt-2 text-sm text-red-600">
                                            {{ form.errors.cantidad_actual }}
                                        </div>
                                    </div>

                                    <!-- Stock Mínimo -->
                                    <div>
                                        <label for="cantidad_minima" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Cantidad Mínima <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            id="cantidad_minima"
                                            v-model="form.cantidad_minima"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                            :class="{ 'border-red-300': form.errors.cantidad_minima }"
                                        />
                                        <p class="mt-1 text-sm text-gray-500">
                                            Nivel de alerta de stock bajo
                                        </p>
                                        <div v-if="form.errors.cantidad_minima" class="mt-2 text-sm text-red-600">
                                            {{ form.errors.cantidad_minima }}
                                        </div>
                                    </div>

                                    <!-- Stock Máximo -->
                                    <div>
                                        <label for="cantidad_maxima" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Cantidad Máxima
                                        </label>
                                        <input
                                            id="cantidad_maxima"
                                            v-model="form.cantidad_maxima"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                            :class="{ 'border-red-300': form.errors.cantidad_maxima }"
                                        />
                                        <p class="mt-1 text-sm text-gray-500">
                                            Capacidad máxima de almacenamiento (opcional)
                                        </p>
                                        <div v-if="form.errors.cantidad_maxima" class="mt-2 text-sm text-red-600">
                                            {{ form.errors.cantidad_maxima }}
                                        </div>
                                    </div>

                                    <!-- Precio de Venta Específico -->
                                    <div>
                                        <label for="precio_venta" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Precio de Venta Específico
                                        </label>
                                        <div class="relative mt-1">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">Bs</span>
                                            </div>
                                            <input
                                                id="precio_venta"
                                                v-model="form.precio_venta"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                placeholder="0.00"
                                                class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                :class="{ 'border-red-300': form.errors.precio_venta }"
                                            />
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Deja vacío para usar el precio base del producto (Bs {{ inventory.product.precio_venta }})
                                        </p>
                                        <div v-if="form.errors.precio_venta" class="mt-2 text-sm text-red-600">
                                            {{ form.errors.precio_venta }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Alertas de Stock -->
                                <div class="mt-6 p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        Estado del Stock
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                        <div class="flex items-center">
                                            <div :class="stockStatus.actual.color + ' w-3 h-3 rounded-full mr-2'"></div>
                                            <span class="text-gray-700 dark:text-gray-300">
                                                Stock Actual: {{ stockStatus.actual.text }}
                                            </span>
                                        </div>
                                        <div class="flex items-center">
                                            <div :class="stockStatus.minimo.color + ' w-3 h-3 rounded-full mr-2'"></div>
                                            <span class="text-gray-700 dark:text-gray-300">
                                                Stock Mínimo: {{ stockStatus.minimo.text }}
                                            </span>
                                        </div>
                                        <div class="flex items-center" v-if="form.cantidad_maxima">
                                            <div :class="stockStatus.maximo.color + ' w-3 h-3 rounded-full mr-2'"></div>
                                            <span class="text-gray-700 dark:text-gray-300">
                                                Stock Máximo: {{ stockStatus.maximo.text }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones -->
                                <div class="flex justify-end pt-6 mt-6 space-x-3 border-t border-gray-200 dark:border-gray-700">
                                    <Link
                                        :href="route('inventory.index')"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        Cancelar
                                    </Link>
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                                        :class="{ 'opacity-25': form.processing }"
                                    >
                                        <span v-if="form.processing" class="inline-flex items-center">
                                            <svg class="w-4 h-4 mr-3 -ml-1 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Actualizando...
                                        </span>
                                        <span v-else>
                                            Actualizar Inventario
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </SidebarLayout>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { ArrowLeftIcon, EyeIcon, CubeIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    inventory: {
        type: Object,
        required: true
    }
})

const form = useForm({
    cantidad_actual: props.inventory.cantidad_actual || 0,
    cantidad_minima: props.inventory.cantidad_minima || 0,
    cantidad_maxima: props.inventory.cantidad_maxima || null,
    precio_venta: props.inventory.precio_venta || null,
})

const stockStatus = computed(() => {
    const actual = parseFloat(form.cantidad_actual) || 0
    const minimo = parseFloat(form.cantidad_minima) || 0
    const maximo = parseFloat(form.cantidad_maxima) || null

    return {
        actual: {
            color: actual <= 0 ? 'bg-red-500' : actual <= minimo ? 'bg-yellow-500' : 'bg-green-500',
            text: actual <= 0 ? 'Sin Stock' : actual <= minimo ? 'Stock Bajo' : 'Stock Normal'
        },
        minimo: {
            color: 'bg-yellow-500',
            text: `Alerta en ${minimo}`
        },
        maximo: {
            color: maximo && actual >= maximo ? 'bg-red-500' : 'bg-blue-500',
            text: maximo ? `Máximo ${maximo}` : 'No definido'
        }
    }
})

const submit = () => {
    form.put(route('inventory.update', props.inventory.id), {
        onSuccess: () => {
            // Redirigir después de actualizar
        }
    })
}
</script>