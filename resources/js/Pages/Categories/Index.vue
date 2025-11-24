<template>
  <AppLayout title="Categorías">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ $t('Gestión de Categorías') }}
        </h2>
        <Link
          :href="route('categories.create')"
          class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
        >
          <PlusIcon class="w-4 h-4 mr-2" />
          {{ $t('Nueva Categoría') }}
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 lg:p-8">
            <!-- Filtros y búsqueda -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  {{ $t('Buscar') }}
                </label>
                <div class="relative">
                  <input
                    id="search"
                    v-model="filters.search"
                    type="text"
                    :placeholder="$t('Buscar categorías...')"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                    @input="debouncedSearch"
                  />
                  <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" />
                </div>
              </div>
              
              <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  {{ $t('Estado') }}
                </label>
                <select
                  id="status"
                  v-model="filters.status"
                  class="w-full py-2 px-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                  @change="applyFilters"
                >
                  <option value="">{{ $t('Todos') }}</option>
                  <option value="active">{{ $t('Activo') }}</option>
                  <option value="inactive">{{ $t('Inactivo') }}</option>
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
                  <option value="created_at">{{ $t('Fecha de creación') }}</option>
                  <option value="products_count">{{ $t('Cantidad de productos') }}</option>
                </select>
              </div>
            </div>

            <!-- Tabla de categorías -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                      {{ $t('Nombre') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                      {{ $t('Descripción') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                      {{ $t('Productos') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                      {{ $t('Estado') }}
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
                    v-for="category in categories.data"
                    :key="category.id"
                    class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
                  >
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                          <img
                            v-if="category.imagen"
                            :src="category.imagen"
                            :alt="category.nombre"
                            class="h-10 w-10 rounded-full object-cover"
                          />
                          <div
                            v-else
                            class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center"
                          >
                            <FolderIcon class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                          </div>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ category.nombre }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm text-gray-900 dark:text-gray-100">
                        {{ category.descripcion || $t('Sin descripción') }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        {{ category.products_count || 0 }} {{ $t('productos') }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="[
                          'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                          category.estado === 'activo'
                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                        ]"
                      >
                        <CheckCircleIcon v-if="category.estado === 'activo'" class="w-4 h-4 mr-1" />
                        <XCircleIcon v-else class="w-4 h-4 mr-1" />
                        {{ category.estado === 'activo' ? $t('Activo') : $t('Inactivo') }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                      {{ formatDate(category.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="flex items-center justify-end space-x-2">
                        <Link
                          :href="route('categories.show', category.id)"
                          class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                          :title="$t('Ver detalles')"
                        >
                          <EyeIcon class="h-4 w-4" />
                        </Link>
                        <Link
                          v-if="$page.props.auth.user.permissions?.includes('edit categories')"
                          :href="route('categories.edit', category.id)"
                          class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors"
                          :title="$t('Editar')"
                        >
                          <PencilIcon class="h-4 w-4" />
                        </Link>
                        <button
                          v-if="$page.props.auth.user.permissions?.includes('delete categories') && category.products_count === 0"
                          @click="deleteCategory(category)"
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
              v-if="categories.data.length === 0"
              class="text-center py-12"
            >
              <FolderIcon class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                {{ $t('No hay categorías') }}
              </h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $t('Comienza creando tu primera categoría.') }}
              </p>
              <div class="mt-6">
                <Link
                  :href="route('categories.create')"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <PlusIcon class="w-4 h-4 mr-2" />
                  {{ $t('Nueva Categoría') }}
                </Link>
              </div>
            </div>

            <!-- Paginación -->
            <div v-if="categories.data.length > 0" class="mt-6">
              <Pagination :links="categories.links" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmación para eliminar -->
    <ConfirmationModal :show="showDeleteModal" @close="showDeleteModal = false">
      <template #title>
        {{ $t('Eliminar Categoría') }}
      </template>

      <template #content>
        {{ $t('¿Estás seguro de que deseas eliminar esta categoría?') }}
        <span class="font-medium">{{ categoryToDelete?.nombre }}</span>
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
import { ref, reactive, computed } from 'vue'
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
  FolderIcon,
  CheckCircleIcon,
  XCircleIcon
} from '@heroicons/vue/24/outline'
import { useTranslations } from '@/composables/useTranslations'

const { t: $t } = useTranslations()

defineProps({
  categories: {
    type: Object,
    required: true
  }
})

// Estado reactivo
const filters = reactive({
  search: '',
  status: '',
  sort: 'nombre'
})

const showDeleteModal = ref(false)
const categoryToDelete = ref(null)

const deleteForm = useForm({})

// Búsqueda con debounce
const debouncedSearch = debounce(() => {
  applyFilters()
}, 300)

// Aplicar filtros
const applyFilters = () => {
  const params = {}
  
  if (filters.search) params.search = filters.search
  if (filters.status) params.status = filters.status
  if (filters.sort) params.sort = filters.sort

  router.get(route('categories.index'), params, {
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

// Eliminar categoría
const deleteCategory = (category) => {
  categoryToDelete.value = category
  showDeleteModal.value = true
}

const confirmDelete = () => {
  deleteForm.delete(route('categories.destroy', categoryToDelete.value.id), {
    onSuccess: () => {
      showDeleteModal.value = false
      categoryToDelete.value = null
    }
  })
}
</script>