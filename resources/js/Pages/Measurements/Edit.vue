<template>
  <AppLayout title="Editar Unidad de Medida">
    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <Link
            :href="route('measurements.index')"
            class="text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
          >
            <ArrowLeftIcon class="w-5 h-5" />
          </Link>
          <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
              {{ $t('Editar Unidad de Medida') }}
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              {{ measurement.nombre }} ({{ measurement.simbolo }})
            </p>
          </div>
        </div>
        <div class="flex items-center space-x-2">
          <Link
            :href="route('measurements.show', measurement.id)"
            class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm dark:border-gray-500 dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <EyeIcon class="w-4 h-4 mr-2" />
            {{ $t('Ver') }}
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6 lg:p-8">
            <!-- Información actual -->
            <div class="p-4 mb-6 border border-blue-200 rounded-lg bg-blue-50 dark:bg-blue-900/20 dark:border-blue-800">
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <InformationCircleIcon class="w-5 h-5 text-blue-400" />
                </div>
                <div class="ml-3">
                  <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                    {{ $t('Información actual') }}
                  </h3>
                  <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                    <div class="flex items-center space-x-4">
                      <span>{{ $t('Productos que usan esta unidad') }}: <strong>{{ measurement.products_count || 0 }}</strong></span>
                      <span>{{ $t('Creada') }}: {{ formatDate(measurement.created_at) }}</span>
                      <span>{{ $t('Actualizada') }}: {{ formatDate(measurement.updated_at) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Nombre -->
            <div class="mb-6">
              <InputLabel for="nombre" :value="$t('Nombre de la Unidad')" />
              <TextInput
                id="nombre"
                v-model="form.nombre"
                type="text"
                class="block w-full mt-1"
                :placeholder="$t('Ejemplo: Metro, Kilogramo, Litro')"
                required
                autofocus
              />
              <InputError class="mt-2" :message="form.errors.nombre" />
            </div>

            <!-- Símbolo -->
            <div class="mb-6">
              <InputLabel for="simbolo" :value="$t('Símbolo')" />
              <TextInput
                id="simbolo"
                v-model="form.simbolo"
                type="text"
                class="block w-full mt-1"
                :placeholder="$t('Ejemplo: m, kg, L')"
                maxlength="10"
                required
              />
              <InputError class="mt-2" :message="form.errors.simbolo" />
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $t('Máximo 10 caracteres. Use abreviaciones estándar.') }}
              </p>
            </div>

            <!-- Comparación de cambios -->
            <div v-if="hasChanges" class="pt-6 mt-8 border-t border-gray-200 dark:border-gray-700">
              <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ $t('Resumen de cambios') }}
              </h3>
              <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <!-- Antes -->
                <div class="p-4 rounded-lg bg-red-50 dark:bg-red-900/20">
                  <h4 class="mb-2 text-sm font-medium text-red-800 dark:text-red-200">{{ $t('Antes') }}</h4>
                  <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                      <div class="flex items-center justify-center w-8 h-8 bg-gray-300 rounded dark:bg-gray-600">
                        <ScaleIcon class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                      </div>
                    </div>
                    <div class="flex-1 min-w-0">
                      <div class="text-sm text-red-800 truncate dark:text-red-200">{{ measurement.nombre }}</div>
                      <div class="text-xs text-red-600 truncate dark:text-red-300">{{ measurement.simbolo }}</div>
                    </div>
                  </div>
                </div>

                <!-- Después -->
                <div class="p-4 rounded-lg bg-green-50 dark:bg-green-900/20">
                  <h4 class="mb-2 text-sm font-medium text-green-800 dark:text-green-200">{{ $t('Después') }}</h4>
                  <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                      <div class="flex items-center justify-center w-8 h-8 bg-gray-300 rounded dark:bg-gray-600">
                        <ScaleIcon class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                      </div>
                    </div>
                    <div class="flex-1 min-w-0">
                      <div class="text-sm text-green-800 truncate dark:text-green-200">{{ form.nombre }}</div>
                      <div class="text-xs text-green-600 truncate dark:text-green-300">{{ form.simbolo }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex items-center justify-end mt-8 space-x-3">
              <Link
                :href="route('measurements.index')"
                class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25"
              >
                {{ $t('Cancelar') }}
              </Link>

              <PrimaryButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.nombre || !form.simbolo || !hasChanges"
              >
                <template v-if="form.processing">
                  <svg
                    class="w-4 h-4 mr-3 -ml-1 text-white animate-spin"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ $t('Guardando...') }}
                </template>
                <template v-else>
                  <CheckIcon class="w-4 h-4 mr-2" />
                  {{ $t('Guardar Cambios') }}
                </template>
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/SidebarLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import {
  ArrowLeftIcon,
  ScaleIcon,
  CheckIcon,
  EyeIcon,
  InformationCircleIcon
} from '@heroicons/vue/24/outline'
import { useTranslations } from '@/composables/useTranslations'

const { t: $t } = useTranslations()

const props = defineProps({
  measurement: {
    type: Object,
    required: true
  }
})

const form = useForm({
  nombre: props.measurement.nombre,
  simbolo: props.measurement.simbolo
})

// Verificar si hay cambios
const hasChanges = computed(() => {
  return (
    form.nombre !== props.measurement.nombre ||
    form.simbolo !== props.measurement.simbolo
  )
})

// Formatear fecha
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const submit = () => {
  form.put(route('measurements.update', props.measurement.id), {
    onSuccess: () => {
      // La redirección será manejada por el controlador
    }
  })
}
</script>