<template>
  <AppLayout title="Nuevo Cliente">
    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <Link
            :href="route('clients.index')"
            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
          >
            <ArrowLeftIcon class="h-5 w-5" />
          </Link>
          <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $t('Nuevo Cliente') }}
          </h2>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6 lg:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Nombre del cliente -->
              <div class="md:col-span-2">
                <InputLabel for="nombre" :value="$t('Nombre completo')" />
                <TextInput
                  id="nombre"
                  v-model="form.nombre"
                  type="text"
                  class="mt-1 block w-full"
                  :placeholder="$t('Ingresa el nombre completo del cliente')"
                  required
                  autofocus
                />
                <InputError class="mt-2" :message="form.errors.nombre" />
              </div>

              <!-- CI -->
              <div class="md:col-span-1">
                <InputLabel for="ci" :value="$t('Carnet de Identidad')" />
                <TextInput
                  id="ci"
                  v-model="form.ci"
                  type="text"
                  class="mt-1 block w-full"
                  :placeholder="$t('Ej: 1234567 LP')"
                />
                <InputError class="mt-2" :message="form.errors.ci" />
              </div>

              <!-- NIT -->
              <div class="md:col-span-1">
                <InputLabel for="nit" :value="$t('NIT')" />
                <TextInput
                  id="nit"
                  v-model="form.nit"
                  type="text"
                  class="mt-1 block w-full"
                  :placeholder="$t('Ej: 1234567011')"
                />
                <InputError class="mt-2" :message="form.errors.nit" />
              </div>

              <!-- Teléfono -->
              <div class="md:col-span-2">
                <InputLabel for="telf" :value="$t('Teléfono')" />
                <TextInput
                  id="telf"
                  v-model="form.telf"
                  type="tel"
                  class="mt-1 block w-full"
                  :placeholder="$t('Ej: +591 XXXXXXXX')"
                />
                <InputError class="mt-2" :message="form.errors.telf" />
              </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-8 flex items-center justify-end space-x-3">
              <Link
                :href="route('clients.index')"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
              >
                {{ $t('Cancelar') }}
              </Link>

              <PrimaryButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.nombre"
              >
                <template v-if="form.processing">
                  {{ $t('Creando...') }}
                </template>
                <template v-else>
                  {{ $t('Crear Cliente') }}
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
import { ArrowLeftIcon } from '@heroicons/vue/24/outline'
import { useTranslations } from '@/composables/useTranslations'

const { t: $t } = useTranslations()

const form = useForm({
  nombre: '',
  ci: '',
  nit: '',
  telf: ''
})

const submit = () => {
  form.post(route('clients.store'))
}
</script>