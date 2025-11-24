<template>
  <AppLayout title="Nueva Unidad de Medida">
    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <Link
            :href="route('measurements.index')"
            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
          >
            <ArrowLeftIcon class="h-5 w-5" />
          </Link>
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $t('Nueva Unidad de Medida') }}
          </h2>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6 lg:p-8">
            <!-- Nombre -->
            <div class="mb-6">
              <InputLabel for="nombre" :value="$t('Nombre de la Unidad')" />
              <TextInput
                id="nombre"
                v-model="form.nombre"
                type="text"
                class="mt-1 block w-full"
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
                class="mt-1 block w-full"
                :placeholder="$t('Ejemplo: m, kg, L')"
                maxlength="10"
                required
              />
              <InputError class="mt-2" :message="form.errors.simbolo" />
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $t('Máximo 10 caracteres. Use abreviaciones estándar.') }}
              </p>
            </div>

            <!-- Vista previa -->
            <div v-if="form.nombre || form.simbolo" class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ $t('Vista previa') }}
              </h3>
              <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                <div class="flex items-center space-x-3">
                  <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                      <ScaleIcon class="h-5 w-5 text-blue-600 dark:text-blue-300" />
                    </div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                      {{ form.nombre || $t('Nombre de la unidad') }}
                    </h4>
                    <div class="mt-1">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                        {{ form.simbolo || $t('Símbolo') }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-8 flex items-center justify-end space-x-3">
              <Link
                :href="route('measurements.index')"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
              >
                {{ $t('Cancelar') }}
              </Link>

              <PrimaryButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.nombre || !form.simbolo"
              >
                <template v-if="form.processing">
                  <svg
                    class="animate-spin -ml-1 mr-3 h-4 w-4 text-white"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ $t('Creando...') }}
                </template>
                <template v-else>
                  <PlusIcon class="w-4 h-4 mr-2" />
                  {{ $t('Crear Unidad') }}
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
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/SidebarLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import {
  ArrowLeftIcon,
  ScaleIcon,
  PlusIcon
} from '@heroicons/vue/24/outline'
import { useTranslations } from '@/composables/useTranslations'

const { t: $t } = useTranslations()

const form = useForm({
  nombre: '',
  simbolo: ''
})

const submit = () => {
  form.post(route('measurements.store'), {
    onSuccess: () => {
      // La redirección será manejada por el controlador
    }
  })
}
</script>