<template>
    <SidebarLayout title="Crear Producto">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ $t('Crear Producto') }}
                </h2>
                <Link :href="route('products.index')"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    <ArrowLeftIcon class="w-4 h-4 mr-2" />
                    {{ $t('Volver') }}
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 lg:p-8">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                                <!-- Información básica -->
                                <div class="space-y-6">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                            {{ $t('Información Básica') }}
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $t('Información general del producto') }}
                                        </p>
                                    </div>

                                    <!-- Nombre -->
                                    <div>
                                        <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ $t('Nombre') }} <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            id="nombre"
                                            v-model="form.nombre"
                                            type="text"
                                            required
                                            autocomplete="off"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-600 dark:focus:ring-indigo-600 sm:text-sm"
                                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.nombre }"
                                        />
                                        <div v-if="form.errors.nombre" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                            {{ form.errors.nombre }}
                                        </div>
                                    </div>

                                    <!-- Descripción -->
                                    <div>
                                        <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ $t('Descripción') }}
                                        </label>
                                        <textarea
                                            id="descripcion"
                                            v-model="form.descripcion"
                                            rows="4"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-600 dark:focus:ring-indigo-600 sm:text-sm"
                                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.descripcion }"
                                        ></textarea>
                                        <div v-if="form.errors.descripcion" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                            {{ form.errors.descripcion }}
                                        </div>
                                    </div>

                                    <!-- Precio de Venta -->
                                    <div>
                                        <label for="precio_venta" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ $t('Precio de Venta') }} <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative mt-1">
                                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                            </div>
                                            <input
                                                id="precio_venta"
                                                v-model="form.precio_venta"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                placeholder="0.00"
                                                required
                                                class="block w-full pr-12 border-gray-300 rounded-md shadow-sm pl-7 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-600 dark:focus:ring-indigo-600 sm:text-sm"
                                                :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.precio_venta }"
                                            />
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $t('Precio de venta al público') }}
                                        </p>
                                        <div v-if="form.errors.precio_venta" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                            {{ form.errors.precio_venta }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Relaciones -->
                                <div class="space-y-6">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                            {{ $t('Clasificación') }}
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $t('Categoría, unidad de medida y proveedor') }}
                                        </p>
                                    </div>

                                    <!-- Categoría -->
                                    <div>
                                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ $t('Categoría') }} <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            id="category_id"
                                            v-model="form.category_id"
                                            required
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-600 dark:focus:ring-indigo-600 sm:text-sm"
                                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.category_id }"
                                        >
                                            <option value="">{{ $t('Seleccionar categoría') }}</option>
                                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                                {{ category.nombre }}
                                            </option>
                                        </select>
                                        <div v-if="form.errors.category_id" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                            {{ form.errors.category_id }}
                                        </div>
                                    </div>

                                    <!-- Unidad de medida -->
                                    <div>
                                        <label for="measurement_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ $t('Unidad de Medida') }} <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            id="measurement_id"
                                            v-model="form.measurement_id"
                                            required
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-600 dark:focus:ring-indigo-600 sm:text-sm"
                                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.measurement_id }"
                                        >
                                            <option value="">{{ $t('Seleccionar unidad') }}</option>
                                            <option v-for="measurement in measurements" :key="measurement.id" :value="measurement.id">
                                                {{ measurement.nombre }} ({{ measurement.simbolo }})
                                            </option>
                                        </select>
                                        <div v-if="form.errors.measurement_id" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                            {{ form.errors.measurement_id }}
                                        </div>
                                    </div>

                                    <!-- Proveedor -->
                                    <div>
                                        <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ $t('Proveedor') }} <span class="text-red-500">*</span>
                                        </label>
                                        <select
                                            id="supplier_id"
                                            v-model="form.supplier_id"
                                            required
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-600 dark:focus:ring-indigo-600 sm:text-sm"
                                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.supplier_id }"
                                        >
                                            <option value="">{{ $t('Seleccionar proveedor') }}</option>
                                            <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                                {{ supplier.nombre_empresa }}
                                            </option>
                                        </select>
                                        <div v-if="form.errors.supplier_id" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                            {{ form.errors.supplier_id }}
                                        </div>
                                    </div>

                                    <!-- Imagen -->
                                    <div>
                                        <label for="imagen" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ $t('Imagen') }}
                                        </label>
                                        <input
                                            id="imagen"
                                            ref="imageInput"
                                            type="file"
                                            accept="image/*"
                                            @change="handleImageChange"
                                            class="block w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300 dark:hover:file:bg-blue-800"
                                        />
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $t('Selecciona una imagen para el producto (JPG, PNG, GIF, WEBP - máx. 2MB)') }}
                                        </p>
                                        <div v-if="form.errors.imagen" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                            {{ form.errors.imagen }}
                                        </div>
                                    </div>

                                    <!-- Vista previa de imagen -->
                                    <div v-if="imagePreview" class="mt-4">
                                        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ $t('Vista previa') }}
                                        </label>
                                        <div class="relative">
                                            <img 
                                                :src="imagePreview" 
                                                :alt="form.nombre || 'Vista previa'"
                                                class="object-cover w-32 h-32 border border-gray-300 rounded-lg dark:border-gray-600"
                                            />
                                            <button
                                                type="button"
                                                @click="removeImage"
                                                class="absolute flex items-center justify-center w-6 h-6 text-xs text-white bg-red-500 rounded-full -top-2 -right-2 hover:bg-red-600"
                                            >
                                                ×
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="flex items-center justify-end pt-6 mt-6 space-x-3 border-t border-gray-200 dark:border-gray-700">
                                <Link :href="route('products.index')"
                                    class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800">
                                    {{ $t('Cancelar') }}
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed dark:focus:ring-offset-gray-800"
                                    :class="{ 'opacity-25': form.processing }"
                                >
                                    <span v-if="form.processing" class="inline-flex items-center">
                                        <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ $t('Creando...') }}
                                    </span>
                                    <span v-else>
                                        {{ $t('Crear Producto') }}
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'
import { useTranslations } from '@/composables/useTranslations'

const { t: $t } = useTranslations()

const props = defineProps({
    categories: {
        type: Array,
        required: true
    },
    measurements: {
        type: Array,
        required: true
    },
    suppliers: {
        type: Array,
        required: true
    }
})

const imageError = ref(false)
const imagePreview = ref(null)
const imageInput = ref(null)

const form = useForm({
    nombre: '',
    descripcion: '',
    precio_venta: '',
    category_id: '',
    measurement_id: '',
    supplier_id: '',
    imagen: null
})

const handleImageChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.imagen = file
        
        // Crear preview
        const reader = new FileReader()
        reader.onload = (e) => {
            imagePreview.value = e.target.result
        }
        reader.readAsDataURL(file)
    }
}

const removeImage = () => {
    form.imagen = null
    imagePreview.value = null
    if (imageInput.value) {
        imageInput.value.value = ''
    }
}

const submit = () => {
    imageError.value = false
    form.post(route('products.store'), {
        forceFormData: true,
        onError: () => {
            // El formulario manejará automáticamente los errores
        }
    })
}
</script>