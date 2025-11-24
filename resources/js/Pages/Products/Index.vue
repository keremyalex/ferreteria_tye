<template>
  <AppLayout title="Productos">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ $t('Gestión de Productos') }}
        </h2>
        <Link
          :href="route('products.create')"
          class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
        >
          <PlusIcon class="w-4 h-4 mr-2" />
          {{ $t('Nuevo Producto') }}
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 lg:p-8">
            <!-- Filtros y búsqueda -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  {{ $t('Buscar') }}
                </label>
                <div class="relative">
                  <input
                    id="search"
                    v-model="filters.search"
                    type="text"
                    :placeholder="$t('Buscar productos...')"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                    @input="debouncedSearch"
                  />
                  <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" />
                </div>
              </div>
              
              <div>
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  {{ $t('Categoría') }}
                </label>
                <select
                  id="category"
                  v-model="filters.category"
                  class="w-full py-2 px-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                  @change="applyFilters"
                >
                  <option value="">{{ $t('Todas las categorías') }}</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.nombre }}
                  </option>
                </select>
              </div>

              <div>
                <label for="supplier" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  {{ $t('Proveedor') }}
                </label>
                <select
                  id="supplier"
                  v-model="filters.supplier"
                  class="w-full py-2 px-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                  @change="applyFilters"
                >
                  <option value="">{{ $t('Todos los proveedores') }}</option>
                  <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                    {{ supplier.nombre_empresa }}
                  </option>
                </select>
              </div>
              
              <div>
                <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  {{ $t('Ordenar por') }}
                </label>
                <select
                  id="sort"
                  v-model="filters.sort"
                  class="w-full py-2 px-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                  @change="applyFilters"
                >
                  <option value="nombre">{{ $t('Nombre') }}</option>
                  <option value="precio">{{ $t('Precio') }}</option>
                  <option value="created_at">{{ $t('Fecha de creación') }}</option>
                </select>
              </div>
            </div>

            <!-- Tabla de productos -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                      {{ $t('Producto') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                      {{ $t('Categoría') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                      {{ $t('Precio') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                      {{ $t('Proveedor') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                      {{ $t('Fecha') }}
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                      {{ $t('Acciones') }}
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                  <tr
                    v-for="product in products.data"
                    :key="product.id"
                    class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
                  >
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12">
                          <img
                            v-if="product.imagen"
                            :src="product.imagen"
                            :alt="product.nombre"
                            class="h-12 w-12 rounded-lg object-cover"
                          />
                          <div
                            v-else
                            class="h-12 w-12 rounded-lg bg-gray-300 dark:bg-gray-600 flex items-center justify-center"
                          >
                            <CubeIcon class="h-6 w-6 text-gray-500 dark:text-gray-400" />
                          </div>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ product.nombre }}
                          </div>
                          <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">
                            {{ product.descripcion || $t('Sin descripción') }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                        {{ product.category?.nombre || $t('Sin categoría') }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        ${{ product.precio }}
                      </div>
                      <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ product.measurement?.simbolo || '' }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                      {{ product.supplier?.nombre_empresa || $t('Sin proveedor') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                      {{ formatDate(product.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="flex items-center justify-end space-x-2">
                        <Link
                          :href="route('products.show', product.id)"
                          class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                          :title="$t('Ver detalles')"
                        >
                          <EyeIcon class="h-4 w-4" />
                        </Link>
                        <Link
                          v-if="$page.props.auth.user.permissions?.includes('edit.products')"
                          :href="route('products.edit', product.id)"
                          class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors"
                          :title="$t('Editar')"
                        >
                          <PencilIcon class="h-4 w-4" />
                        </Link>
                        <button
                          v-if="$page.props.auth.user.permissions?.includes('delete.products')"
                          @click="deleteProduct(product)"
                          class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors"
                          :title="$t('Eliminar')"
                        >
                          <TrashIcon class="h-4 w-4" />
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Estado vacío -->
            <div
              v-if="products.data.length === 0"
              class="text-center py-12"
            >
              <CubeIcon class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                {{ $t('No hay productos') }}
              </h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $t('Comienza creando tu primer producto.') }}
              </p>
              <div class="mt-6">
                <Link
                  :href="route('products.create')"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <PlusIcon class="w-4 h-4 mr-2" />
                  {{ $t('Nuevo Producto') }}
                </Link>
              </div>
            </div>

            <!-- Paginación -->
            <div v-if="products.data.length > 0" class="mt-6">
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

        <DangerButton
          class="ml-3"
          :class="{ 'opacity-25': deleteForm.processing }"
          :disabled="deleteForm.processing"
          @click="confirmDelete"
        >
          {{ $t('Eliminar') }}
        </DangerButton>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import AppLayout from '@/Layouts/SidebarLayout.vue'
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