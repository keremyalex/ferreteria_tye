<template>
    <div>
        <Head title="Detalles del Cliente" />
        <SidebarLayout>
            <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        <!-- Header -->
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <div class="h-16 w-16 bg-indigo-600 rounded-full flex items-center justify-center">
                                        <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                            {{ client.nombre }}
                                        </h2>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Cliente registrado el {{ formatDate(client.created_at) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex space-x-3">
                                    <Link 
                                        :href="route('clients.edit', client.id)"
                                        v-if="$page.props.auth.user.permissions?.includes('edit.clients')"
                                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        Editar
                                    </Link>
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
                        </div>

                        <!-- Información del Cliente -->
                        <div class="px-4 py-5 sm:p-6">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2 lg:grid-cols-3">
                                <!-- Nombre -->
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path>
                                            </svg>
                                            Nombre Completo
                                        </div>
                                    </dt>
                                    <dd class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ client.nombre }}
                                    </dd>
                                </div>

                                <!-- NIT -->
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"></path>
                                            </svg>
                                            NIT
                                        </div>
                                    </dt>
                                    <dd class="text-lg font-semibold text-gray-900 dark:text-white">
                                        <span v-if="client.nit">{{ client.nit }}</span>
                                        <span v-else class="text-gray-400 dark:text-gray-500 italic">No registrado</span>
                                    </dd>
                                </div>

                                <!-- Cédula de Identidad -->
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                            </svg>
                                            Cédula de Identidad
                                        </div>
                                    </dt>
                                    <dd class="text-lg font-semibold text-gray-900 dark:text-white">
                                        <span v-if="client.ci">{{ client.ci }}</span>
                                        <span v-else class="text-gray-400 dark:text-gray-500 italic">No registrada</span>
                                    </dd>
                                </div>

                                <!-- Teléfono -->
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                            </svg>
                                            Teléfono
                                        </div>
                                    </dt>
                                    <dd class="text-lg font-semibold text-gray-900 dark:text-white">
                                        <span v-if="client.telf">{{ client.telf }}</span>
                                        <span v-else class="text-gray-400 dark:text-gray-500 italic">No registrado</span>
                                    </dd>
                                </div>

                                <!-- Fecha de Registro -->
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                            </svg>
                                            Fecha de Registro
                                        </div>
                                    </dt>
                                    <dd class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ formatDate(client.created_at) }}
                                    </dd>
                                </div>

                                <!-- Última Actualización -->
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4" v-if="client.updated_at !== client.created_at">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                                            </svg>
                                            Última Actualización
                                        </div>
                                    </dt>
                                    <dd class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ formatDate(client.updated_at) }}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Acciones de Eliminar -->
                        <div class="bg-red-50 dark:bg-red-900 px-4 py-5 sm:p-6" v-if="$page.props.auth.user.permissions?.includes('delete.clients')">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-lg font-medium text-red-800 dark:text-red-200">
                                        Eliminar Cliente
                                    </h3>
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-300">
                                        Una vez eliminado, todos los datos de este cliente se perderán permanentemente. Esta acción no se puede deshacer.
                                    </p>
                                </div>
                                <button
                                    @click="confirmDelete"
                                    class="ml-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                >
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Eliminar Cliente
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal de Confirmación -->
                    <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <!-- Overlay -->
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDeleteModal = false"></div>

                            <!-- Modal -->
                            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                            Confirmar Eliminación
                                        </h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                ¿Estás seguro de que deseas eliminar al cliente <strong>{{ client.nombre }}</strong>? Esta acción no se puede deshacer.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                    <button
                                        @click="deleteClient"
                                        type="button"
                                        :disabled="deleteForm.processing"
                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                                    >
                                        <svg v-if="deleteForm.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Eliminar
                                    </button>
                                    <button
                                        @click="showDeleteModal = false"
                                        type="button"
                                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
                                    >
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </SidebarLayout>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'

const props = defineProps({
    client: Object,
})

const showDeleteModal = ref(false)

const deleteForm = useForm({})

const confirmDelete = () => {
    showDeleteModal.value = true
}

const deleteClient = () => {
    deleteForm.delete(route('clients.destroy', props.client.id), {
        onSuccess: () => {
            showDeleteModal.value = false
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