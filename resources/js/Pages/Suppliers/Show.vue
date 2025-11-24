<template>
  <AppLayout title="Detalles del Proveedor">
    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <Link
            :href="route('suppliers.index')"
            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
          >
            <ArrowLeftIcon class="h-5 w-5" />
          </Link>
          <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
              {{ supplier.nombre_empresa }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
              NIT: {{ supplier.nit }}
            </p>
          </div>
        </div>
        <div class="flex items-center space-x-3">
          <Link
            v-if="$page.props.auth.user.permissions?.includes('edit.suppliers')"
            :href="route('suppliers.edit', supplier.id)"
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150"
          >
            <PencilIcon class="h-4 w-4 mr-1" />
            {{ $t('Editar') }}
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Información principal -->
          <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
              <div class="p-6 lg:p-8">
                <div class="flex items-start space-x-4">
                  <div class="flex-shrink-0">
                    <div class="h-16 w-16 rounded-xl bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                      <BuildingOfficeIcon class="h-8 w-8 text-purple-600 dark:text-purple-300" />
                    </div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                      {{ supplier.nombre_empresa }}
                    </h3>
                    <div class="mt-2 flex flex-wrap gap-2">
                      <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                        NIT: {{ supplier.nit }}
                      </span>
                      <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200">
                        <CheckCircleIcon class="w-4 h-4 mr-1" />
                        {{ $t('Activo') }}
                      </span>
                    </div>
                  </div>
                </div>

                <div class="mt-8">
                  <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">
                    {{ $t('Información de contacto') }}
                  </h4>
                  <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Persona de contacto -->
                    <div v-if="supplier.nombre_persona">
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                        <UserIcon class="w-4 h-4 mr-2" />
                        {{ $t('Persona de contacto') }}
                      </dt>
                      <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ supplier.nombre_persona }}
                      </dd>
                    </div>

                    <!-- Teléfono -->
                    <div v-if="supplier.telefono">
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                        <PhoneIcon class="w-4 h-4 mr-2" />
                        {{ $t('Teléfono') }}
                      </dt>
                      <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        <a 
                          :href="`tel:${supplier.telefono}`"
                          class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors"
                        >
                          {{ supplier.telefono }}
                        </a>
                      </dd>
                    </div>

                    <!-- Correo electrónico -->
                    <div v-if="supplier.correo">
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                        <EnvelopeIcon class="w-4 h-4 mr-2" />
                        {{ $t('Correo electrónico') }}
                      </dt>
                      <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        <a 
                          :href="`mailto:${supplier.correo}`"
                          class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors"
                        >
                          {{ supplier.correo }}
                        </a>
                      </dd>
                    </div>

                    <!-- Dirección -->
                    <div v-if="supplier.direccion" class="sm:col-span-2">
                      <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                        <MapPinIcon class="w-4 h-4 mr-2" />
                        {{ $t('Dirección') }}
                      </dt>
                      <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ supplier.direccion }}
                      </dd>
                    </div>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- Panel lateral -->
          <div class="lg:col-span-1 space-y-6">
            <!-- Estadísticas rápidas -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
              <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ $t('Estadísticas') }}
              </h4>
              <div class="space-y-4">
                <div class="flex items-center justify-between">
                  <span class="text-sm text-gray-500 dark:text-gray-400">{{ $t('Productos suministrados') }}</span>
                  <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ supplier.productos_count || 0 }}
                  </span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-sm text-gray-500 dark:text-gray-400">{{ $t('Compras realizadas') }}</span>
                  <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ supplier.compras_count || 0 }}
                  </span>
                </div>
                <div class="flex items-center justify-between">
                  <span class="text-sm text-gray-500 dark:text-gray-400">{{ $t('Total comprado') }}</span>
                  <span class="text-sm font-medium text-green-600 dark:text-green-400">
                    Bs. {{ formatCurrency(supplier.total_compras || 0) }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Información adicional -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
              <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ $t('Información del registro') }}
              </h4>
              <div class="space-y-3">
                <div>
                  <span class="text-sm text-gray-500 dark:text-gray-400">{{ $t('Registrado el') }}</span>
                  <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ formatDate(supplier.created_at) }}
                  </p>
                </div>
                <div v-if="supplier.updated_at !== supplier.created_at">
                  <span class="text-sm text-gray-500 dark:text-gray-400">{{ $t('Última actualización') }}</span>
                  <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ formatDate(supplier.updated_at) }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Acciones rápidas -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
              <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ $t('Acciones rápidas') }}
              </h4>
              <div class="space-y-3">
                <button
                  v-if="$page.props.auth.user.permissions?.includes('create.purchases')"
                  type="button"
                  class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150"
                >
                  <ShoppingCartIcon class="w-4 h-4 mr-2" />
                  {{ $t('Nueva compra') }}
                </button>
                
                <button
                  v-if="$page.props.auth.user.permissions?.includes('view.products')"
                  type="button"
                  class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-500 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150"
                >
                  <CubeIcon class="w-4 h-4 mr-2" />
                  {{ $t('Ver productos') }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Productos del proveedor -->
        <div v-if="supplier.productos && supplier.productos.length > 0" class="mt-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 lg:p-8">
              <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">
                {{ $t('Productos suministrados') }}
              </h4>
              
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ $t('Producto') }}
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ $t('Categoría') }}
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ $t('Precio actual') }}
                      </th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ $t('Stock') }}
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="producto in supplier.productos" :key="producto.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                          {{ producto.nombre }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                          {{ producto.codigo }}
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        {{ producto.categoria?.nombre || $t('Sin categoría') }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                        Bs. {{ formatCurrency(producto.precio_venta) }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="[
                          'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                          producto.stock_actual > producto.stock_minimo 
                            ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200'
                            : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200'
                        ]">
                          {{ producto.stock_actual }} {{ producto.unidad_medida?.simbolo || '' }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/SidebarLayout.vue'
import {
  ArrowLeftIcon,
  BuildingOfficeIcon,
  CheckCircleIcon,
  CubeIcon,
  EnvelopeIcon,
  MapPinIcon,
  PencilIcon,
  PhoneIcon,
  ShoppingCartIcon,
  UserIcon
} from '@heroicons/vue/24/outline'
import { useTranslations } from '@/composables/useTranslations'

const { t: $t } = useTranslations()

defineProps({
  supplier: Object
})

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('es-BO', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatCurrency = (amount) => {
  if (!amount) return '0.00'
  return Number(amount).toLocaleString('es-BO', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}
</script>