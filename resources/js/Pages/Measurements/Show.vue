<template>
  <AppLayout :title="`Unidad: ${measurement.nombre}`">
    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <Link
            :href="route('measurements.index')"
            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
          >
            <ArrowLeftIcon class="h-5 w-5" />
          </Link>
          <div class="flex items-center space-x-3">
            <div class="h-8 w-8 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
              <ScaleIcon class="h-4 w-4 text-blue-600 dark:text-blue-300" />
            </div>
            <div>
              <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ measurement.nombre }}
              </h2>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t('Símbolo') }}: {{ measurement.simbolo }}
              </p>
            </div>
          </div>
        </div>
        <div class="flex items-center space-x-2">
          <Link
            v-if="$page.props.auth.user.permissions?.includes('edit measurements')"
            :href="route('measurements.edit', measurement.id)"
            class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <PencilIcon class="w-4 h-4 mr-2" />
            {{ $t('Editar') }}
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Información general -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 lg:p-8">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ $t('Información General') }}
              </h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <ScaleIcon class="h-5 w-5 text-gray-400" />
                  </div>
                  <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $t('Nombre') }}</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ measurement.nombre }}</p>
                  </div>
                </div>
              </div>

              <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <TagIcon class="h-5 w-5 text-gray-400" />
                  </div>
                  <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $t('Símbolo') }}</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ measurement.simbolo }}</p>
                  </div>
                </div>
              </div>

              <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <CubeIcon class="h-5 w-5 text-gray-400" />
                  </div>
                  <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $t('Productos') }}</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ measurement.products_count || 0 }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Fechas -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500 dark:text-gray-400">
                <div>
                  <span class="font-medium">{{ $t('Creada') }}:</span>
                  {{ formatDate(measurement.created_at) }}
                </div>
                <div>
                  <span class="font-medium">{{ $t('Última actualización') }}:</span>
                  {{ formatDate(measurement.updated_at) }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Productos que usan esta unidad -->
        <div v-if="measurement.products && measurement.products.length > 0" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 lg:p-8">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ $t('Productos que usan esta unidad') }}
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                  {{ measurement.products.length }}
                </span>
              </h3>
              <Link
                v-if="$page.props.auth.user.permissions?.includes('create.products')"
                :href="route('products.create')"
                class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-100 border border-transparent rounded-md hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800"
              >
                <PlusIcon class="w-4 h-4 mr-2" />
                {{ $t('Nuevo Producto') }}
              </Link>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div v-for="product in measurement.products" :key="product.id" class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                  <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-lg bg-green-100 dark:bg-green-900 flex items-center justify-center">
                      <CubeIcon class="h-5 w-5 text-green-600 dark:text-green-300" />
                    </div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                      <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                        {{ product.nombre }}
                      </p>
                      <Link
                        v-if="$page.props.auth.user.permissions?.includes('edit.products')"
                        :href="route('products.edit', product.id)"
                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                      >
                        <PencilIcon class="h-4 w-4" />
                      </Link>
                    </div>
                    <div class="mt-1 flex items-center justify-between">
                      <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                        {{ product.category?.nombre || $t('Sin categoría') }}
                      </p>
                      <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        ${{ product.precio }}
                      </span>
                    </div>
                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                      {{ $t('Proveedor') }}: {{ product.supplier?.nombre_empresa || $t('Sin proveedor') }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Estado vacío -->
        <div v-else class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 lg:p-8">
            <div class="text-center py-12">
              <CubeIcon class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                {{ $t('No hay productos') }}
              </h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $t('Esta unidad de medida aún no se usa en ningún producto.') }}
              </p>
              <div class="mt-6">
                <Link
                  v-if="$page.props.auth.user.permissions?.includes('create.products')"
                  :href="route('products.create')"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  <PlusIcon class="w-4 h-4 mr-2" />
                  {{ $t('Crear Producto') }}
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Acciones -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 lg:p-8">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">
              {{ $t('Acciones') }}
            </h3>
            
            <div class="flex flex-col sm:flex-row gap-3">
              <Link
                :href="route('measurements.index')"
                class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <ArrowLeftIcon class="w-4 h-4 mr-2" />
                {{ $t('Volver al Listado') }}
              </Link>

              <Link
                v-if="$page.props.auth.user.permissions?.includes('edit.measurements')"
                :href="route('measurements.edit', measurement.id)"
                class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                <PencilIcon class="w-4 h-4 mr-2" />
                {{ $t('Editar Unidad') }}
              </Link>

              <button
                v-if="$page.props.auth.user.permissions?.includes('delete.measurements') && (!measurement.products_count || measurement.products_count === 0)"
                @click="confirmDelete"
                class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              >
                <TrashIcon class="w-4 h-4 mr-2" />
                {{ $t('Eliminar') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <ConfirmationModal :show="confirmingMeasurementDeletion" @close="confirmingMeasurementDeletion = false">
      <template #title>
        {{ $t('Eliminar Unidad de Medida') }}
      </template>

      <template #content>
        {{ $t('¿Estás seguro de que quieres eliminar la unidad de medida') }}
        <span class="font-medium">"{{ measurement.nombre }}"</span>?
        {{ $t('Esta acción no se puede deshacer.') }}
      </template>

      <template #footer>
        <SecondaryButton @click="confirmingMeasurementDeletion = false">
          {{ $t('Cancelar') }}
        </SecondaryButton>

        <DangerButton
          class="ml-3"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="deleteMeasurement"
        >
          {{ $t('Eliminar') }}
        </DangerButton>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/SidebarLayout.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'
import DangerButton from '@/Components/DangerButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import {
  ArrowLeftIcon,
  ScaleIcon,
  TagIcon,
  CubeIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon
} from '@heroicons/vue/24/outline'
import { useTranslations } from '@/composables/useTranslations'

const { t: $t } = useTranslations()

const props = defineProps({
  measurement: {
    type: Object,
    required: true
  }
})

const form = useForm({})
const confirmingMeasurementDeletion = ref(false)

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
  confirmingMeasurementDeletion.value = true
}

const deleteMeasurement = () => {
  form.delete(route('measurements.destroy', props.measurement.id), {
    onSuccess: () => {
      confirmingMeasurementDeletion.value = false
    }
  })
}
</script>