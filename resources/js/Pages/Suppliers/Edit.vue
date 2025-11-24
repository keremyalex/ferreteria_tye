<template>
  <AppLayout title="Editar Proveedor">
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
              {{ $t('Editar Proveedor') }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
              {{ supplier.nombre_empresa }} • NIT: {{ supplier.nit }}
            </p>
          </div>
        </div>
        <div class="flex items-center space-x-3">
          <Link
            :href="route('suppliers.show', supplier.id)"
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
          >
            <EyeIcon class="h-4 w-4 mr-1" />
            {{ $t('Ver') }}
          </Link>
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
                <p v-if="hasChanged('nit')" class="mt-1 text-sm text-blue-600 dark:text-blue-400">
                  {{ $t('Valor anterior') }}: {{ supplier.nit }}
                </p>
                <p v-else class="mt-1 text-sm text-gray-500 dark:text-gray-400">
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
                <p v-if="hasChanged('nombre_empresa')" class="mt-1 text-sm text-blue-600 dark:text-blue-400">
                  {{ $t('Valor anterior') }}: {{ supplier.nombre_empresa }}
                </p>
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
                <p v-if="hasChanged('nombre_persona')" class="mt-1 text-sm text-blue-600 dark:text-blue-400">
                  {{ $t('Valor anterior') }}: {{ supplier.nombre_persona || $t('Sin especificar') }}
                </p>
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
                <p v-if="hasChanged('telefono')" class="mt-1 text-sm text-blue-600 dark:text-blue-400">
                  {{ $t('Valor anterior') }}: {{ supplier.telefono || $t('Sin especificar') }}
                </p>
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
                <p v-if="hasChanged('correo')" class="mt-1 text-sm text-blue-600 dark:text-blue-400">
                  {{ $t('Valor anterior') }}: {{ supplier.correo || $t('Sin especificar') }}
                </p>
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
                <p v-if="hasChanged('direccion')" class="mt-1 text-sm text-blue-600 dark:text-blue-400">
                  {{ $t('Valor anterior') }}: {{ supplier.direccion || $t('Sin especificar') }}
                </p>
              </div>
            </div>

            <!-- Resumen de cambios -->
            <div v-if="hasAnyChanges" class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ $t('Resumen de cambios') }}
              </h3>
              <div class="bg-blue-50 dark:bg-blue-900/50 rounded-lg p-4">
                <div class="flex">
                  <InformationCircleIcon class="flex-shrink-0 h-5 w-5 text-blue-400" />
                  <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                      {{ $t('Se actualizarán los siguientes campos') }}:
                    </h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                      <ul class="list-disc list-inside space-y-1">
                        <li v-if="hasChanged('nit')">
                          {{ $t('NIT') }}: {{ supplier.nit }} → {{ form.nit }}
                        </li>
                        <li v-if="hasChanged('nombre_empresa')">
                          {{ $t('Nombre de la empresa') }}: {{ supplier.nombre_empresa }} → {{ form.nombre_empresa }}
                        </li>
                        <li v-if="hasChanged('nombre_persona')">
                          {{ $t('Persona de contacto') }}: {{ supplier.nombre_persona || $t('Sin especificar') }} → {{ form.nombre_persona || $t('Sin especificar') }}
                        </li>
                        <li v-if="hasChanged('telefono')">
                          {{ $t('Teléfono') }}: {{ supplier.telefono || $t('Sin especificar') }} → {{ form.telefono || $t('Sin especificar') }}
                        </li>
                        <li v-if="hasChanged('correo')">
                          {{ $t('Correo') }}: {{ supplier.correo || $t('Sin especificar') }} → {{ form.correo || $t('Sin especificar') }}
                        </li>
                        <li v-if="hasChanged('direccion')">
                          {{ $t('Dirección') }}: {{ supplier.direccion || $t('Sin especificar') }} → {{ form.direccion || $t('Sin especificar') }}
                        </li>
                      </ul>
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
                :class="{ 'opacity-25': form.processing || !hasAnyChanges }"
                :disabled="form.processing || !hasAnyChanges || !form.nombre_empresa || !form.nit"
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
                  {{ $t('Actualizando...') }}
                </template>
                <template v-else>
                  <PencilIcon class="w-4 h-4 mr-2" />
                  {{ $t('Actualizar Proveedor') }}
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
import { computed } from 'vue'
import AppLayout from '@/Layouts/SidebarLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import {
  ArrowLeftIcon,
  EyeIcon,
  InformationCircleIcon,
  PencilIcon
} from '@heroicons/vue/24/outline'
import { useTranslations } from '@/composables/useTranslations'

const { t: $t } = useTranslations()

const props = defineProps({
  supplier: Object
})

const form = useForm({
  nit: props.supplier.nit,
  nombre_empresa: props.supplier.nombre_empresa,
  nombre_persona: props.supplier.nombre_persona || '',
  direccion: props.supplier.direccion || '',
  telefono: props.supplier.telefono || '',
  correo: props.supplier.correo || ''
})

const hasChanged = (field) => {
  return form[field] !== (props.supplier[field] || '')
}

const hasAnyChanges = computed(() => {
  return hasChanged('nit') || 
         hasChanged('nombre_empresa') || 
         hasChanged('nombre_persona') || 
         hasChanged('direccion') || 
         hasChanged('telefono') || 
         hasChanged('correo')
})

const submit = () => {
  form.patch(route('suppliers.update', props.supplier.id), {
    onSuccess: () => {
      // La redirección será manejada por el controlador
    }
  })
}
</script>