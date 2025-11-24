<template>
  <AppLayout title="Clientes">
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $t('Gestión de Clientes') }}
          </h2>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ $t('Administra la información de tus clientes') }}
          </p>
        </div>
        <div class="flex items-center space-x-3">
          <Link
            v-if="$page.props.auth.user.permissions?.includes('create.clients')"
            :href="route('clients.create')"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
          >
            <PlusIcon class="w-4 h-4 mr-2" />
            {{ $t('Nuevo Cliente') }}
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filtros de búsqueda -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6">
          <div class="p-6">
            <form @submit.prevent="performSearch" class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Búsqueda general -->
                <div class="lg:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ $t('Búsqueda general') }}
                  </label>
                  <div class="relative">
                    <input
                      v-model="filters.search"
                      type="text"
                      :placeholder="$t('Buscar por nombre, CI, NIT o teléfono...')"
                      class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white dark:bg-gray-700 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 dark:text-gray-300 sm:text-sm"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                    </div>
                  </div>
                </div>

                <!-- Filtro por CI -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ $t('CI') }}
                  </label>
                  <input
                    v-model="filters.ci"
                    type="text"
                    :placeholder="$t('Filtrar por CI')"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md leading-5 bg-white dark:bg-gray-700 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 dark:text-gray-300 sm:text-sm"
                  />
                </div>

                <!-- Filtro por NIT -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ $t('NIT') }}
                  </label>
                  <input
                    v-model="filters.nit"
                    type="text"
                    :placeholder="$t('Filtrar por NIT')"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md leading-5 bg-white dark:bg-gray-700 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 dark:text-gray-300 sm:text-sm"
                  />
                </div>

                <!-- Filtro por teléfono -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ $t('Teléfono') }}
                  </label>
                  <input
                    v-model="filters.telefono"
                    type="text"
                    :placeholder="$t('Filtrar por teléfono')"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md leading-5 bg-white dark:bg-gray-700 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 dark:text-gray-300 sm:text-sm"
                  />
                </div>
              </div>

              <div class="flex items-center space-x-3">
                <button
                  type="submit"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <MagnifyingGlassIcon class="w-4 h-4 mr-2" />
                  {{ $t('Buscar') }}
                </button>
                <button
                  type="button"
                  @click="clearFilters"
                  class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                  <XMarkIcon class="w-4 h-4 mr-2" />
                  {{ $t('Limpiar') }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Tabla de clientes -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ $t('Cliente') }}
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ $t('Documento') }}
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ $t('Teléfono') }}
                  </th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ $t('Registro') }}
                  </th>
                  <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ $t('Acciones') }}
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="client in clients.data" :key="client.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                  <!-- Información del cliente -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                          <UserIcon class="h-5 w-5 text-indigo-600 dark:text-indigo-300" />
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                          {{ client.nombre }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                          ID: {{ client.id }}
                        </div>
                      </div>
                    </div>
                  </td>

                  <!-- Documento -->
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div v-if="client.ci" class="text-sm text-gray-900 dark:text-gray-100">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200">
                        CI: {{ client.ci }}
                      </span>
                    </div>
                    <div v-if="client.nit" class="text-sm text-gray-900 dark:text-gray-100 mt-1">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200">
                        NIT: {{ client.nit }}
                      </span>
                    </div>
                    <div v-if="!client.ci && !client.nit" class="text-sm text-gray-400 dark:text-gray-500">
                      {{ $t('Sin documento') }}
                    </div>
                  </td>

                  <!-- Teléfono -->
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    <span v-if="client.telf">{{ client.telf }}</span>
                    <span v-else class="text-gray-400 dark:text-gray-500">{{ $t('Sin teléfono') }}</span>
                  </td>

                  <!-- Fecha de registro -->
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ formatDate(client.created_at) }}
                  </td>

                  <!-- Acciones -->
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    <Link
                      :href="route('clients.show', client.id)"
                      class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                    >
                      <EyeIcon class="w-4 h-4" />
                    </Link>
                    <Link
                      v-if="$page.props.auth.user.permissions?.includes('edit.clients')"
                      :href="route('clients.edit', client.id)"
                      class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                    >
                      <PencilIcon class="w-4 h-4" />
                    </Link>
                    <button
                      v-if="$page.props.auth.user.permissions?.includes('delete.clients')"
                      @click="confirmDelete(client)"
                      class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                    >
                      <TrashIcon class="w-4 h-4" />
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Paginación -->
          <div v-if="clients.links && clients.links.length > 3" class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
            <Pagination :links="clients.links" />
          </div>

          <!-- Estado vacío -->
          <div v-if="clients.data.length === 0" class="text-center py-12">
            <UserIcon class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" />
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
              {{ $t('No hay clientes') }}
            </h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              {{ hasFilters ? $t('No se encontraron clientes con los filtros aplicados') : $t('Comienza agregando tu primer cliente') }}
            </p>
            <div v-if="!hasFilters" class="mt-6">
              <Link
                v-if="$page.props.auth.user.permissions?.includes('create.clients')"
                :href="route('clients.create')"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <PlusIcon class="w-4 h-4 mr-2" />
                {{ $t('Agregar cliente') }}
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmación para eliminar -->
    <ConfirmationModal :show="showDeleteModal" @close="showDeleteModal = false">
      <template #title>
        {{ $t('Eliminar Cliente') }}
      </template>

      <template #content>
        {{ $t('¿Estás seguro de que quieres eliminar este cliente?') }} <strong>{{ clientToDelete?.nombre }}</strong>.
        <br><br>
        {{ $t('Esta acción no se puede deshacer') }}.
      </template>

      <template #footer>
        <SecondaryButton @click="showDeleteModal = false">
          {{ $t('Cancelar') }}
        </SecondaryButton>

        <DangerButton
          class="ml-3"
          :class="{ 'opacity-25': deleteForm.processing }"
          :disabled="deleteForm.processing"
          @click="deleteClient"
        >
          <TrashIcon class="w-4 h-4 mr-2" />
          {{ deleteForm.processing ? $t('Eliminando...') : $t('Eliminar') }}
        </DangerButton>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>

<script setup>
import { Link, router, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import AppLayout from '@/Layouts/SidebarLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'
import DangerButton from '@/Components/DangerButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import {
  EyeIcon,
  MagnifyingGlassIcon,
  PencilIcon,
  PlusIcon,
  TrashIcon,
  UserIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline'
import { useTranslations } from '@/composables/useTranslations'

const { t: $t } = useTranslations()

const props = defineProps({
  clients: Object,
  filters: Object
})

// Filtros reactivos
const filters = ref({
  search: props.filters?.search || '',
  ci: props.filters?.ci || '',
  nit: props.filters?.nit || '',
  telefono: props.filters?.telefono || ''
})

// Estado de eliminación
const showDeleteModal = ref(false)
const clientToDelete = ref(null)

const deleteForm = useForm({})

// Computed
const hasFilters = computed(() => {
  return filters.value.search || filters.value.ci || filters.value.nit || filters.value.telefono
})

// Métodos
const performSearch = () => {
  const query = { ...filters.value }
  // Limpiar valores vacíos
  Object.keys(query).forEach(key => {
    if (!query[key]) {
      delete query[key]
    }
  })
  
  router.get(route('clients.index'), query, {
    preserveState: true,
    preserveScroll: true
  })
}

const clearFilters = () => {
  filters.value = {
    search: '',
    ci: '',
    nit: '',
    telefono: ''
  }
  router.get(route('clients.index'))
}

const confirmDelete = (client) => {
  clientToDelete.value = client
  showDeleteModal.value = true
}

const deleteClient = () => {
  if (clientToDelete.value) {
    deleteForm.delete(route('clients.destroy', clientToDelete.value.id), {
      onSuccess: () => {
        showDeleteModal.value = false
        clientToDelete.value = null
      }
    })
  }
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('es-BO', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}
</script>