<template>
  <AppLayout title="Detalles de Categoría">
    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <Link
            :href="route('categories.index')"
            class="text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
          >
            <ArrowLeftIcon class="w-5 h-5" />
          </Link>
          <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
              <img
                v-if="category.imagen"
                :src="category.imagen"
                :alt="category.nombre_int"
                class="object-cover w-12 h-12 border border-gray-200 rounded-lg dark:border-gray-600"
              />
              <div
                v-else
                class="flex items-center justify-center w-12 h-12 bg-gray-300 rounded-lg dark:bg-gray-600"
              >
                <FolderIcon class="w-6 h-6 text-gray-500 dark:text-gray-400" />
              </div>
            </div>
            <div>
              <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ category.nombre_int }}
              </h2>
              <div class="flex items-center mt-1 space-x-3">
                <span
                  :class="[
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                    category.estado === 'activo'
                      ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                      : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                  ]"
                >
                  <CheckCircleIcon v-if="category.estado === 'activo'" class="w-3 h-3 mr-1" />
                  <XCircleIcon v-else class="w-3 h-3 mr-1" />
                  {{ category.estado === 'activo' ? $t('Activo') : $t('Inactivo') }}
                </span>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                  {{ $t('ID') }}: {{ category.id }}
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="flex items-center space-x-2">
          <Link
            v-if="$page.props.auth.user.permissions?.includes('edit categories')"
            :href="route('categories.edit', category.id)"
            class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <PencilIcon class="w-4 h-4 mr-2" />
            {{ $t('Editar') }}
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
        
        <!-- Información general -->
        <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
          <div class="p-6 lg:p-8">
            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ $t('Información General') }}
            </h3>
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
              <div>
                <h4 class="text-sm font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                  {{ $t('Descripción') }}
                </h4>
                <p class="mt-2 text-sm text-gray-900 dark:text-gray-100">
                  {{ category.descripcion || $t('Sin descripción proporcionada') }}
                </p>
              </div>
              
              <div>
                <h4 class="text-sm font-medium tracking-wider text-gray-500 uppercase dark:text-gray-400">
                  {{ $t('Estadísticas') }}
                </h4>
                <div class="mt-2 space-y-2">
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">{{ $t('Productos asociados') }}:</span>
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ category.products_count || 0 }}</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">{{ $t('Fecha de creación') }}:</span>
                    <span class="text-gray-900 dark:text-gray-100">{{ formatDate(category.created_at) }}</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">{{ $t('Última actualización') }}:</span>
                    <span class="text-gray-900 dark:text-gray-100">{{ formatDate(category.updated_at) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Productos asociados -->
        <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
          <div class="p-6 lg:p-8">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ $t('Productos Asociados') }}
                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                  ({{ category.products_count || 0 }})
                </span>
              </h3>
              <Link
                v-if="$page.props.auth.user.permissions?.includes('create.products')"
                :href="route('products.create', { category_id: category.id })"
                class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-indigo-700 bg-indigo-100 border border-transparent rounded-md hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-900 dark:text-indigo-200 dark:hover:bg-indigo-800"
              >
                <PlusIcon class="w-4 h-4 mr-2" />
                {{ $t('Agregar Producto') }}
              </Link>
            </div>

            <!-- Lista de productos -->
            <div v-if="category.products && category.products.length > 0" class="space-y-4">
              <div
                v-for="product in category.products"
                :key="product.id"
                class="p-4 transition-colors border border-gray-200 rounded-lg dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700"
              >
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                      <img
                        v-if="product.imagen"
                        :src="product.imagen"
                        :alt="product.nombre_int"
                        class="object-cover w-12 h-12 rounded-lg"
                      />
                      <div
                        v-else
                        class="flex items-center justify-center w-12 h-12 bg-gray-200 rounded-lg dark:bg-gray-600"
                      >
                        <CubeIcon class="w-6 h-6 text-gray-400" />
                      </div>
                    </div>
                    <div class="flex-1 min-w-0">
                      <h4 class="text-sm font-medium text-gray-900 truncate dark:text-gray-100">
                        {{ product.nombre_int }}
                      </h4>
                      <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                        {{ product.descripcion || $t('Sin descripción') }}
                      </p>
                      <div class="flex items-center mt-1 space-x-4">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                          ${{ product.precio }}
                        </span>
                        <span v-if="product.supplier" class="text-xs text-gray-500 dark:text-gray-400">
                          {{ product.supplier.nombre_int }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2">
                    <Link
                      :href="route('products.show', product.id)"
                      class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                    >
                      <EyeIcon class="w-4 h-4" />
                    </Link>
                    <Link
                      v-if="$page.props.auth.user.permissions?.includes('edit.products')"
                      :href="route('products.edit', product.id)"
                      class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300"
                    >
                      <PencilIcon class="w-4 h-4" />
                    </Link>
                  </div>
                </div>
              </div>
            </div>

            <!-- Estado vacío -->
            <div
              v-else
              class="py-12 text-center border-2 border-gray-300 border-dashed rounded-lg dark:border-gray-600"
            >
              <CubeIcon class="w-12 h-12 mx-auto text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                {{ $t('No hay productos') }}
              </h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $t('Esta categoría aún no tiene productos asociados.') }}
              </p>
              <div v-if="$page.props.auth.user.permissions?.includes('create.products')" class="mt-6">
                <Link
                  :href="route('products.create', { category_id: category.id })"
                  class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <PlusIcon class="w-4 h-4 mr-2" />
                  {{ $t('Agregar Primer Producto') }}
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Acciones adicionales -->
        <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
          <div class="p-6 lg:p-8">
            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">
              {{ $t('Acciones') }}
            </h3>
            
            <div class="flex flex-wrap gap-3">
              <Link
                :href="route('products.index', { category: category.id })"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:border-gray-500 dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <CubeIcon class="w-4 h-4 mr-2" />
                {{ $t('Ver Todos los Productos') }}
              </Link>

              <Link
                v-if="$page.props.auth.user.permissions?.includes('edit.categories')"
                :href="route('categories.edit', category.id)"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <PencilIcon class="w-4 h-4 mr-2" />
                {{ $t('Editar Categoría') }}
              </Link>

              <button
                v-if="$page.props.auth.user.permissions?.includes('delete categories') && (!category.products_count || category.products_count === 0)"
                @click="confirmDelete"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              >
                <TrashIcon class="w-4 h-4 mr-2" />
                {{ $t('Eliminar Categoría') }}
              </button>
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
        {{ $t('¿Estás seguro de que deseas eliminar la categoría') }}
        <span class="font-medium">"{{ category.nombre_int }}"</span>?
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
          @click="deleteCategory"
        >
          {{ $t('Eliminar') }}
        </DangerButton>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/SidebarLayout.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'
import DangerButton from '@/Components/DangerButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import {
  ArrowLeftIcon,
  FolderIcon,
  CheckCircleIcon,
  XCircleIcon,
  PencilIcon,
  EyeIcon,
  PlusIcon,
  CubeIcon,
  TrashIcon
} from '@heroicons/vue/24/outline'
import { useTranslations } from '@/composables/useTranslations'

const { t: $t } = useTranslations()

defineProps({
  category: {
    type: Object,
    required: true
  }
})

const showDeleteModal = ref(false)
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

const confirmDelete = () => {
  showDeleteModal.value = true
}

const deleteCategory = () => {
  deleteForm.delete(route('categories.destroy', props.category.id), {
    onSuccess: () => {
      router.visit(route('categories.index'))
    }
  })
}
</script>