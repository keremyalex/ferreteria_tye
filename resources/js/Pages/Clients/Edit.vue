<template>
    <div>
        <Head title="Editar Cliente" />
        <SidebarLayout>
            <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        Editar Cliente
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Actualiza la información del cliente
                                    </p>
                                </div>
                                <Link 
                                    :href="route('clients.index')"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                    </svg>
                                    Volver
                                </Link>
                            </div>
                        </div>

                        <form @submit.prevent="submit" class="px-4 py-5 sm:p-6">
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <!-- Nombre -->
                                <div>
                                    <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                        Nombre <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="nombre"
                                        v-model="form.nombre"
                                        type="text"
                                        class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        :class="{ 'border-red-500 dark:border-red-500': form.errors.nombre }"
                                        placeholder="Ingresa el nombre del cliente"
                                    />
                                    <div v-if="form.errors.nombre" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors.nombre }}
                                    </div>
                                </div>

                                <!-- NIT -->
                                <div>
                                    <label for="nit" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                        NIT
                                    </label>
                                    <input
                                        id="nit"
                                        v-model="form.nit"
                                        type="text"
                                        class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        :class="{ 'border-red-500 dark:border-red-500': form.errors.nit }"
                                        placeholder="Ingresa el NIT del cliente"
                                    />
                                    <div v-if="form.errors.nit" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors.nit }}
                                    </div>
                                </div>

                                <!-- Cédula de Identidad -->
                                <div>
                                    <label for="ci" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                        Cédula de Identidad
                                    </label>
                                    <input
                                        id="ci"
                                        v-model="form.ci"
                                        type="text"
                                        class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        :class="{ 'border-red-500 dark:border-red-500': form.errors.ci }"
                                        placeholder="Ingresa la cédula de identidad"
                                    />
                                    <div v-if="form.errors.ci" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors.ci }}
                                    </div>
                                </div>

                                <!-- Teléfono -->
                                <div>
                                    <label for="telf" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                                        Teléfono
                                    </label>
                                    <input
                                        id="telf"
                                        v-model="form.telf"
                                        type="tel"
                                        class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        :class="{ 'border-red-500 dark:border-red-500': form.errors.telf }"
                                        placeholder="Ingresa el teléfono del cliente"
                                    />
                                    <div v-if="form.errors.telf" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                        {{ form.errors.telf }}
                                    </div>
                                </div>
                            </div>

                            <!-- Historial de Cambios -->
                            <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6" v-if="client.updated_at">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    Información de Registro
                                </h3>
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Fecha de Creación
                                        </dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">
                                            {{ formatDate(client.created_at) }}
                                        </dd>
                                    </div>
                                    <div v-if="client.updated_at !== client.created_at">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Última Modificación
                                        </dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">
                                            {{ formatDate(client.updated_at) }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Botones -->
                            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex justify-end space-x-4">
                                    <Link 
                                        :href="route('clients.index')"
                                        class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-base font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        Cancelar
                                    </Link>
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Actualizar Cliente
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </SidebarLayout>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'

const props = defineProps({
    client: Object,
})

const form = useForm({
    nombre: props.client.nombre || '',
    nit: props.client.nit || '',
    ci: props.client.ci || '',
    telf: props.client.telf || '',
})

const submit = () => {
    form.put(route('clients.update', props.client.id), {
        onSuccess: () => {
            // Mantener los datos actualizados
        }
    })
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>