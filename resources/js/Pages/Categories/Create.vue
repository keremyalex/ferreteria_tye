<template>
  <AppLayout title="Nueva Categoría">
    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <Link
            :href="route('categories.index')"
            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
          >
            <ArrowLeftIcon class="h-5 w-5" />
          </Link>
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $t('Nueva Categoría') }}
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
              <InputLabel for="nombre" :value="$t('Nombre de la Categoría')" />
              <TextInput
                id="nombre"
                v-model="form.nombre"
                type="text"
                class="mt-1 block w-full"
                :placeholder="$t('Ingresa el nombre de la categoría')"
                required
                autofocus
              />
              <InputError class="mt-2" :message="form.errors.nombre" />
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $t('Ejemplo: Herramientas, Ferretería, Construcción, etc.') }}
              </p>
            </div>

            <!-- Vista previa de la categoría -->
            <div v-if="form.nombre" class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ $t('Vista previa') }}
              </h3>
              <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                <div class="flex items-center space-x-3">
                  <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-lg bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                      <FolderIcon class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                    </div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                      {{ form.nombre }}
                    </h4>
                  </div>
                </div>
              </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-8 flex items-center justify-end space-x-3">
              <Link
                :href="route('categories.index')"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
              >
                {{ $t('Cancelar') }}
              </Link>

              <PrimaryButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.nombre"
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
                  <FolderPlusIcon class="w-4 h-4 mr-2" />
                  {{ $t('Crear Categoría') }}
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
  FolderIcon,
  FolderPlusIcon
} from '@heroicons/vue/24/outline'
import { useTranslations } from '@/composables/useTranslations'

const { t: $t } = useTranslations()

const form = useForm({
  nombre: ''
})

const submit = () => {
  form.post(route('categories.store'), {
    onSuccess: () => {
      // La redirección será manejada por el controlador
    }
  })
}
</script>