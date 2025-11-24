<template>
  <AppLayout title="Proveedores">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ $t('Gestión de Proveedores') }}
        </h2>
        <Link
          :href="route('suppliers.create')"
          class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
        >
          <PlusIcon class="w-4 h-4 mr-2" />
          {{ $t('Nuevo Proveedor') }}
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 lg:p-8">
            <!-- Filtros y búsqueda -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  {{ $t('Buscar') }}
                </label>
                <div class="relative">
                  <input
                    id="search"
                    v-model="filters.search"
                    type="text"
                    :placeholder="$t('Buscar por NIT, empresa, contacto...')"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400"
                    @input="debouncedSearch"
                  />
                  <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" />
                </div>
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
                  <option value="nombre_empresa">{{ $t('Empresa') }}</option>
                  <option value="nit">{{ $t('NIT') }}</option>
                  <option value="created_at">{{ $t('Fecha de registro') }}</option>
                </select>
              </div>
            </div>

            <!-- Tabla de proveedores -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                      {{ $t('Proveedor') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                      {{ $t('NIT') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                      {{ $t('Contacto') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                      {{ $t('Productos') }}
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                      {{ $t('Acciones') }}
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                  <tr v-for="supplier in suppliers.data" :key="supplier.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                          <div class="h-10 w-10 rounded-lg bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                            <BuildingOfficeIcon class="h-5 w-5 text-purple-600 dark:text-purple-300" />
                          </div>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ supplier.nombre_empresa }}
                          </div>
                          <div v-if="supplier.direccion" class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">
                            {{ supplier.direccion }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                        {{ supplier.nit }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-gray-100">
                        {{ supplier.nombre_persona || $t('No especificado') }}
                      </div>
                      <div v-if="supplier.telefono" class="text-sm text-gray-500 dark:text-gray-400">
                        {{ supplier.telefono }}
                      </div>
                      <div v-if="supplier.correo" class="text-sm text-gray-500 dark:text-gray-400">
                        {{ supplier.correo }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                      {{ supplier.products_count || 0 }} {{ $t('productos') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="flex items-center justify-end space-x-2">
                        <Link
                          :href="route('suppliers.show', supplier.id)"
                          class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                          :title="$t('Ver detalles')"
                        >
                          <EyeIcon class="h-4 w-4" />
                        </Link>
                        <Link
                          v-if="$page.props.auth.user.permissions?.includes('edit.suppliers')"
                          :href="route('suppliers.edit', supplier.id)"
                          class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors"
                          :title="$t('Editar')"
                        >
                          <PencilIcon class="h-4 w-4" />
                        </Link>
                        <button
                          v-if="$page.props.auth.user.permissions?.includes('delete.suppliers') && supplier.products_count === 0"
                          @click="confirmDelete(supplier)"
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

              <!-- Empty state -->
              <div v-if="suppliers.data.length === 0" class="text-center py-12">
                <BuildingOfficeIcon class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $t('No hay proveedores') }}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $t('Comienza agregando un nuevo proveedor.') }}</p>
                <div class="mt-6">
                  <Link
                    :href="route('suppliers.create')"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    <PlusIcon class="w-4 h-4 mr-2" />
                    {{ $t('Nuevo Proveedor') }}
                  </Link>
                </div>
              </div>
            </div>

            <!-- Paginación -->
            <div v-if="suppliers.data.length > 0" class="mt-6">
              <Pagination :links="suppliers.links" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <ConfirmationModal :show="confirmingSupplierDeletion" @close="confirmingSupplierDeletion = false">
      <template #title>
        {{ $t('Eliminar Proveedor') }}
      </template>

      <template #content>
        {{ $t('¿Estás seguro de que quieres eliminar el proveedor') }}
        <span class="font-medium">{{ supplierToDelete?.nombre_empresa }}</span>?
        {{ $t('Esta acción no se puede deshacer.') }}
      </template>

      <template #footer>
        <SecondaryButton @click="confirmingSupplierDeletion = false">
          {{ $t('Cancelar') }}
        </SecondaryButton>

        <DangerButton
          class="ml-3"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="deleteSupplier"
        >
          {{ $t('Eliminar') }}
        </DangerButton>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link, useForm } from '@inertiajs/vue3'
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
  BuildingOfficeIcon
} from '@heroicons/vue/24/outline'
import { useTranslations } from '@/composables/useTranslations'

const { t: $t } = useTranslations()

const props = defineProps({
  suppliers: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({})
  }
})

const form = useForm({})
const confirmingSupplierDeletion = ref(false)
const supplierToDelete = ref(null)

const filters = ref({
  search: props.filters.search || '',
  sort: props.filters.sort || 'nombre_empresa'
})

// Debounced search
const debouncedSearch = debounce(() => {
  applyFilters()
}, 300)

const applyFilters = () => {
  router.get(route('suppliers.index'), filters.value, {
    preserveState: true,
    replace: true
  })
}

const confirmDelete = (supplier) => {
  supplierToDelete.value = supplier
  confirmingSupplierDeletion.value = true
}

const deleteSupplier = () => {
  form.delete(route('suppliers.destroy', supplierToDelete.value.id), {
    onSuccess: () => {
      confirmingSupplierDeletion.value = false
      supplierToDelete.value = null
    }
  })
}
</script>