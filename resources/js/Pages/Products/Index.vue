<template>
    <SidebarLayout title="Productos">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ $t('Gestión de Productos') }}
                </h2>
                <Link :href="route('products.create')"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-blue-600 border border-transparent rounded-md dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                <PlusIcon class="w-4 h-4 mr-2" />
                {{ $t('Nuevo Producto') }}
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 lg:p-8">
                        <!-- Filtros y búsqueda -->
                        <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-4">
                            <div>
                                <label for="search"
                                    class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $t('Buscar') }}
                                </label>
                                <div class="relative">
                                    <input id="search" v-model="filters.search" type="text"
                                        :placeholder="$t('Buscar productos...')"
                                        class="w-full py-2 pl-10 pr-4 border border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                                        @input="debouncedSearch" />
                                    <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" />
                                </div>
                            </div>

                            <div>
                                <label for="category"
                                    class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $t('Categoría') }}
                                </label>
                                <select id="category" v-model="filters.category"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    @change="applyFilters">
                                    <option value="">{{ $t('Todas las categorías') }}</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.nombre }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="supplier"
                                    class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $t('Proveedor') }}
                                </label>
                                <select id="supplier" v-model="filters.supplier"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    @change="applyFilters">
                                    <option value="">{{ $t('Todos los proveedores') }}</option>
                                    <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                        {{ supplier.nombre_empresa }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="sort"
                                    class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $t('Ordenar por') }}
                                </label>
                                <select id="sort" v-model="filters.sort"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    @change="applyFilters">
                                    <option value="nombre">{{ $t('Nombre') }}</option>
                                    <option value="precio_venta">{{ $t('Precio de Venta') }}</option>
                                    <option value="created_at">{{ $t('Fecha de creación') }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Tabla de productos -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            {{ $t('Producto') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            {{ $t('Categoría') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            {{ $t('Precio') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            {{ $t('Proveedor') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                            {{ $t('Fecha') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                            {{ $t('Acciones') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="product in products?.data || []" :key="product.id"
                                        class="transition-colors duration-150 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 w-12 h-12">
                                                    <img v-if="product.imagen" :src="product.imagen"
                                                        :alt="product.nombre"
                                                        class="object-cover w-12 h-12 rounded-lg" />
                                                    <div v-else
                                                        class="flex items-center justify-center w-12 h-12 bg-gray-300 rounded-lg dark:bg-gray-600">
                                                        <CubeIcon class="w-6 h-6 text-gray-500 dark:text-gray-400" />
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ product.nombre }}
                                                    </div>
                                                    <div
                                                        class="max-w-xs text-sm text-gray-500 truncate dark:text-gray-400">
                                                        {{ product.descripcion || $t('Sin descripción') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                {{ product.category?.nombre || $t('Sin categoría') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                ${{ product.precio_venta || '0.00' }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ product.measurement?.simbolo || '' }}
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400">
                                            {{ product.supplier?.nombre_empresa || $t('Sin proveedor') }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400">
                                            {{ formatDate(product.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end space-x-2">
                                                <Link :href="route('products.show', product.id)"
                                                    class="text-blue-600 transition-colors hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                                    :title="$t('Ver detalles')">
                                                <EyeIcon class="w-4 h-4" />
                                                </Link>
                                                <Link
                                                    v-if="$page.props.auth.user.permissions?.includes('edit.products')"
                                                    :href="route('products.edit', product.id)"
                                                    class="text-indigo-600 transition-colors hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                                    :title="$t('Editar')">
                                                <PencilIcon class="w-4 h-4" />
                                                </Link>
                                                <button
                                                    v-if="$page.props.auth.user.permissions?.includes('delete.products')"
                                                    @click="deleteProduct(product)"
                                                    class="text-red-600 transition-colors hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                    :title="$t('Eliminar')">
                                                    <TrashIcon class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Estado vacío -->
                        <div v-if="!products?.data?.length" class="py-12 text-center">
                            <CubeIcon class="w-12 h-12 mx-auto text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $t('No hay productos') }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ $t('Comienza creando tu primer producto.') }}
                            </p>
                            <div class="mt-6">
                                <Link :href="route('products.create')"
                                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <PlusIcon class="w-4 h-4 mr-2" />
                                {{ $t('Nuevo Producto') }}
                                </Link>
                            </div>
                        </div>

                        <!-- Paginación -->
                        <div v-if="products?.data?.length > 0" class="mt-6">
                            <Pagination :links="products.links" />
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
                <span class="font-medium">{{ productToDelete?.nombre }}</span>
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
import { ref, reactive } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'
import DangerButton from '@/Components/DangerButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import {
    PlusIcon,
    MagnifyingGlassIcon,
    EyeIcon,
    PencilIcon,
    TrashIcon,
    CubeIcon
} from '@heroicons/vue/24/outline'
import { useTranslations } from '@/composables/useTranslations'

const { t: $t } = useTranslations()

defineProps({
    products: {
        type: Object,
        required: true
    },
    categories: {
        type: Array,
        default: () => []
    },
    suppliers: {
        type: Array,
        default: () => []
    }
})

// Estado reactivo
const filters = reactive({
    search: '',
    category: '',
    supplier: '',
    sort: 'nombre'
})

const showDeleteModal = ref(false)
const productToDelete = ref(null)

const deleteForm = useForm({})

// Búsqueda con debounce
const debouncedSearch = debounce(() => {
    applyFilters()
}, 300)

// Aplicar filtros
const applyFilters = () => {
    const params = {}

    if (filters.search) params.search = filters.search
    if (filters.category) params.category = filters.category
    if (filters.supplier) params.supplier = filters.supplier
    if (filters.sort) params.sort = filters.sort

    router.get(route('products.index'), params, {
        preserveState: true,
        preserveScroll: true
    })
}

// Formatear fecha
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

// Eliminar producto
const deleteProduct = (product) => {
    productToDelete.value = product
    showDeleteModal.value = true
}

const confirmDelete = () => {
    deleteForm.delete(route('products.destroy', productToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false
            productToDelete.value = null
        }
    })
}
</script>