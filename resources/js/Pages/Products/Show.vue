<template>
    <SidebarLayout :title="product.nombre">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ $t('Detalles del Producto') }}
                </h2>
                <div class="flex items-center space-x-3">
                    <Link
                        v-if="$page.props.auth.user.permissions?.includes('edit.products')"
                        :href="route('products.edit', product.id)"
                        class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                        <PencilIcon class="w-4 h-4 mr-2" />
                        {{ $t('Editar') }}
                    </Link>
                    <Link :href="route('products.index')"
                        class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                        <ArrowLeftIcon class="w-4 h-4 mr-2" />
                        {{ $t('Volver') }}
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 lg:p-8">
                        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                            <!-- Imagen del producto -->
                            <div class="lg:col-span-1">
                                <div class="aspect-square w-full max-w-lg mx-auto">
                                    <img v-if="product.imagen" 
                                        :src="product.imagen" 
                                        :alt="product.nombre"
                                        class="object-cover w-full h-full rounded-lg shadow-lg"
                                        @error="imageError = true" />
                                    <div v-else
                                        class="flex items-center justify-center w-full h-full bg-gray-200 rounded-lg shadow-lg dark:bg-gray-700">
                                        <CubeIcon class="w-24 h-24 text-gray-400" />
                                    </div>
                                </div>
                                
                                <!-- Información rápida -->
                                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">
                                        {{ $t('Información Rápida') }}
                                    </h4>
                                    <dl class="space-y-2">
                                        <div>
                                            <dt class="text-xs text-gray-500 dark:text-gray-400">{{ $t('Código') }}</dt>
                                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">#{{ product.id.toString().padStart(6, '0') }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs text-gray-500 dark:text-gray-400">{{ $t('Fecha de creación') }}</dt>
                                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ formatDate(product.created_at) }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs text-gray-500 dark:text-gray-400">{{ $t('Última actualización') }}</dt>
                                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ formatDate(product.updated_at) }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>

                            <!-- Detalles del producto -->
                            <div class="lg:col-span-2">
                                <!-- Header con nombre y precio -->
                                <div class="flex items-start justify-between mb-6">
                                    <div>
                                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                            {{ product.nombre }}
                                        </h1>
                                        <div class="flex items-center mt-2 space-x-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ product.category?.nombre || $t('Sin categoría') }}
                                            </span>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">•</span>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $t('por') }} {{ product.measurement?.simbolo || '' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                                            ${{ formatPrice(product.precio_venta) }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ product.measurement?.nombre }}
                                        </div>
                                        <div v-if="product.margen_real" class="text-sm text-green-600 dark:text-green-400 font-medium">
                                            {{ product.margen_real }}% margen
                                        </div>
                                    </div>
                                </div>

                                <!-- Descripción -->
                                <div class="mb-8">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">
                                        {{ $t('Descripción') }}
                                    </h3>
                                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                        {{ product.descripcion || $t('Sin descripción disponible.') }}
                                    </p>
                                </div>

                                <!-- Detalles técnicos -->
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <!-- Información del producto -->
                                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                        <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                            <InformationCircleIcon class="w-5 h-5 mr-2 text-gray-500" />
                                            {{ $t('Información del Producto') }}
                                        </h4>
                                        <dl class="space-y-3">
                                            <div>
                                                <dt class="text-sm text-gray-500 dark:text-gray-400">{{ $t('Categoría') }}</dt>
                                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ product.category?.nombre || $t('Sin categoría') }}
                                                </dd>
                                            </div>
                                            <div>
                                                <dt class="text-sm text-gray-500 dark:text-gray-400">{{ $t('Unidad de medida') }}</dt>
                                                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ product.measurement?.nombre || $t('Sin unidad') }}
                                                    <span v-if="product.measurement?.simbolo" class="text-gray-500 dark:text-gray-400">
                                                        ({{ product.measurement.simbolo }})
                                                    </span>
                                                </dd>
                                            </div>
                                            <div>
                                                <dt class="text-sm text-gray-500 dark:text-gray-400">{{ $t('Precio de Venta') }}</dt>
                                                <dd class="text-lg font-semibold text-green-600 dark:text-green-400">
                                                    ${{ formatPrice(product.precio_venta) }}
                                                </dd>
                                            </div>
                                        </dl>
                                    </div>

                                    <!-- Información del proveedor -->
                                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                        <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                            <BuildingOfficeIcon class="w-5 h-5 mr-2 text-gray-500" />
                                            {{ $t('Proveedor') }}
                                        </h4>
                                        <div v-if="product.supplier">
                                            <dl class="space-y-3">
                                                <div>
                                                    <dt class="text-sm text-gray-500 dark:text-gray-400">{{ $t('Empresa') }}</dt>
                                                    <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ product.supplier.nombre_empresa }}
                                                    </dd>
                                                </div>
                                                <div v-if="product.supplier.contacto">
                                                    <dt class="text-sm text-gray-500 dark:text-gray-400">{{ $t('Contacto') }}</dt>
                                                    <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ product.supplier.contacto }}
                                                    </dd>
                                                </div>
                                                <div v-if="product.supplier.telefono">
                                                    <dt class="text-sm text-gray-500 dark:text-gray-400">{{ $t('Teléfono') }}</dt>
                                                    <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        <a :href="`tel:${product.supplier.telefono}`" 
                                                           class="text-blue-600 dark:text-blue-400 hover:underline">
                                                            {{ product.supplier.telefono }}
                                                        </a>
                                                    </dd>
                                                </div>
                                                <div v-if="product.supplier.email">
                                                    <dt class="text-sm text-gray-500 dark:text-gray-400">{{ $t('Email') }}</dt>
                                                    <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        <a :href="`mailto:${product.supplier.email}`" 
                                                           class="text-blue-600 dark:text-blue-400 hover:underline">
                                                            {{ product.supplier.email }}
                                                        </a>
                                                    </dd>
                                                </div>
                                            </dl>
                                        </div>
                                        <div v-else class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $t('Sin proveedor asignado') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Inventario relacionado -->
                                <div v-if="product.inventoryDetails && product.inventoryDetails.length > 0" class="mt-8">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                        <CubeTransparentIcon class="w-5 h-5 mr-2 text-gray-500" />
                                        {{ $t('Stock en Inventario') }}
                                    </h3>
                                    <div class="overflow-hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-gray-900">
                                                <tr>
                                                    <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                                        {{ $t('Ubicación') }}
                                                    </th>
                                                    <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                                        {{ $t('Stock Actual') }}
                                                    </th>
                                                    <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                                        {{ $t('Stock Mínimo') }}
                                                    </th>
                                                    <th class="px-6 py-3 text-xs font-medium text-left text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                                        {{ $t('Estado') }}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                                <tr v-for="inventory in product.inventoryDetails" :key="inventory.id">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ inventory.inventory?.fecha || $t('Inventario General') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                        {{ inventory.cantidad }} {{ product.measurement?.simbolo }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                        {{ inventory.cantidad_minima }} {{ product.measurement?.simbolo }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span v-if="inventory.cantidad <= inventory.cantidad_minima"
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                            {{ $t('Stock Bajo') }}
                                                        </span>
                                                        <span v-else
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                            {{ $t('Stock Normal') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Acciones -->
                                <div class="flex items-center justify-end pt-6 mt-8 border-t border-gray-200 dark:border-gray-700 space-x-3">
                                    <button
                                        v-if="$page.props.auth.user.permissions?.includes('delete.products')"
                                        @click="showDeleteModal = true"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-md hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-gray-800 dark:border-red-600 dark:text-red-400 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800">
                                        <TrashIcon class="w-4 h-4 mr-2" />
                                        {{ $t('Eliminar') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de confirmación para eliminar -->
        <ConfirmationModal :show="showDeleteModal" @close="showDeleteModal = false">
            <template #title>
                {{ $t('Eliminar Producto') }}
            </template>

            <template #content>
                {{ $t('¿Estás seguro de que deseas eliminar este producto?') }}
                <strong>{{ product.nombre }}</strong>
                {{ $t('Esta acción no se puede deshacer.') }}
            </template>

            <template #footer>
                <SecondaryButton @click="showDeleteModal = false">
                    {{ $t('Cancelar') }}
                </SecondaryButton>

                <DangerButton class="ml-3" :class="{ 'opacity-25': deleteForm.processing }"
                    :disabled="deleteForm.processing" @click="confirmDelete">
                    {{ $t('Eliminar') }}
                </DangerButton>
            </template>
        </ConfirmationModal>
    </SidebarLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'
import DangerButton from '@/Components/DangerButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import {
    ArrowLeftIcon,
    PencilIcon,
    CubeIcon,
    InformationCircleIcon,
    BuildingOfficeIcon,
    CubeTransparentIcon,
    TrashIcon
} from '@heroicons/vue/24/outline'
import { useTranslations } from '@/composables/useTranslations'

const { t: $t } = useTranslations()

const props = defineProps({
    product: {
        type: Object,
        required: true
    }
})

const showDeleteModal = ref(false)
const imageError = ref(false)

const deleteForm = useForm({})

// Formatear fecha
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

// Formatear precio
const formatPrice = (price) => {
    return parseFloat(price).toLocaleString('es-ES', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })
}

// Eliminar producto
const confirmDelete = () => {
    deleteForm.delete(route('products.destroy', props.product.id), {
        onSuccess: () => {
            router.visit(route('products.index'))
        }
    })
}
</script>