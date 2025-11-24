<template>
  <AppLayout title="Nuevo Proveedor">
    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <Link
            :href="route('suppliers.index')"
            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
          >
            <ArrowLeftIcon class="h-5 w-5" />
          </Link>
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $t('Nuevo Proveedor') }}
          </h2>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6 lg:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- NIT -->
              <div class="md:col-span-1">
                <InputLabel for="nit" :value="$t('NIT')" />
                <TextInput
                  id="nit"
                  v-model="form.nit"
                  type="text"
                  class="mt-1 block w-full"
                  :placeholder="$t('Número de Identificación Tributaria')"
                  required
                  autofocus
                />
                <InputError class="mt-2" :message="form.errors.nit" />
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                  {{ $t('Número único de identificación fiscal') }}
                </p>
              </div>

              <!-- Nombre de la empresa -->
              <div class="md:col-span-1">
                <InputLabel for="nombre_empresa" :value="$t('Nombre de la Empresa')" />
                <TextInput
                  id="nombre_empresa"
                  v-model="form.nombre_empresa"
                  type="text"
                  class="mt-1 block w-full"
                  :placeholder="$t('Razón social o nombre comercial')"
                  required
                />
                <InputError class="mt-2" :message="form.errors.nombre_empresa" />
              </div>

              <!-- Nombre de contacto -->
              <div class="md:col-span-1">
                <InputLabel for="nombre_persona" :value="$t('Persona de Contacto')" />
                <TextInput
                  id="nombre_persona"
                  v-model="form.nombre_persona"
                  type="text"
                  class="mt-1 block w-full"
                  :placeholder="$t('Nombre del representante o contacto')"
                />
                <InputError class="mt-2" :message="form.errors.nombre_persona" />
              </div>

              <!-- Teléfono -->
              <div class="md:col-span-1">
                <InputLabel for="telefono" :value="$t('Teléfono')" />
                <TextInput
                  id="telefono"
                  v-model="form.telefono"
                  type="tel"
                  class="mt-1 block w-full"
                  :placeholder="$t('+591 XXXXXXXX')"
                />
                <InputError class="mt-2" :message="form.errors.telefono" />
              </div>

              <!-- Correo electrónico -->
              <div class="md:col-span-1">
                <InputLabel for="correo" :value="$t('Correo Electrónico')" />
                <TextInput
                  id="correo"
                  v-model="form.correo"
                  type="email"
                  class="mt-1 block w-full"
                  :placeholder="$t('contacto@empresa.com')"
                />
                <InputError class="mt-2" :message="form.errors.correo" />
              </div>

              <!-- Dirección -->
              <div class="md:col-span-2">
                <InputLabel for="direccion" :value="$t('Dirección')" />
                <textarea
                  id="direccion"
                  v-model="form.direccion"
                  rows="3"
                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm resize-none"
                  :placeholder="$t('Dirección completa de la empresa')"
                ></textarea>
                <InputError class="mt-2" :message="form.errors.direccion" />
              </div>
            </div>

            <!-- Vista previa -->
            <div v-if="form.nombre_empresa || form.nit" class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ $t('Vista previa') }}
              </h3>
              <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                  <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-lg bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                      <BuildingOfficeIcon class="h-6 w-6 text-purple-600 dark:text-purple-300" />
                    </div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">
                      {{ form.nombre_empresa || $t('Nombre de la empresa') }}
                    </h4>
                    <div class="mt-1 flex flex-wrap gap-2">
                      <span v-if="form.nit" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                        NIT: {{ form.nit }}
                      </span>
                    </div>
                    <div class="mt-2 space-y-1">
                      <p v-if="form.nombre_persona" class="text-sm text-gray-600 dark:text-gray-400">
                        <strong>{{ $t('Contacto') }}:</strong> {{ form.nombre_persona }}
                      </p>
                      <p v-if="form.telefono" class="text-sm text-gray-600 dark:text-gray-400">
                        <strong>{{ $t('Teléfono') }}:</strong> {{ form.telefono }}
                      </p>
                      <p v-if="form.correo" class="text-sm text-gray-600 dark:text-gray-400">
                        <strong>{{ $t('Correo') }}:</strong> {{ form.correo }}
                      </p>
                      <p v-if="form.direccion" class="text-sm text-gray-600 dark:text-gray-400">
                        <strong>{{ $t('Dirección') }}:</strong> {{ form.direccion }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-8 flex items-center justify-end space-x-3">
              <Link
                :href="route('suppliers.index')"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
              >
                {{ $t('Cancelar') }}
              </Link>

              <PrimaryButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.nombre_empresa || !form.nit"
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
                  {{ $t('Crear Proveedor') }}
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
  BuildingOfficeIcon,
  PlusIcon
} from '@heroicons/vue/24/outline'
import { useTranslations } from '@/composables/useTranslations'

const { t: $t } = useTranslations()

const form = useForm({
  nit: '',
  nombre_empresa: '',
  nombre_persona: '',
  direccion: '',
  telefono: '',
  correo: ''
})

const submit = () => {
  form.post(route('suppliers.store'), {
    onSuccess: () => {
      // La redirección será manejada por el controlador
    }
  })
}
</script>