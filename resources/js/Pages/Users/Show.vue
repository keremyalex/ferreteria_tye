<template>
    <AppLayout title="Detalles del Usuario">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalles del Usuario
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <!-- Header con acciones -->
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium text-gray-900">
                                Información del Usuario
                            </h3>
                            <div class="flex space-x-2">
                                <Link 
                                    :href="route('users.edit', user.id)"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm"
                                    v-if="$page.props.auth.user.permissions.includes('edit users')"
                                >
                                    Editar
                                </Link>
                                <Link 
                                    :href="route('users.index')"
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm"
                                >
                                    Volver
                                </Link>
                            </div>
                        </div>

                        <!-- Información básica -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">
                                    Nombre
                                </label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ user.name }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">
                                    Email
                                </label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ user.email }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">
                                    Fecha de Registro
                                </label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ user.created_at }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-500">
                                    Última Actualización
                                </label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ user.updated_at }}
                                </p>
                            </div>
                        </div>

                        <!-- Roles -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-500 mb-2">
                                Roles
                            </label>
                            <div class="flex flex-wrap gap-2">
                                <span 
                                    v-for="role in user.roles" 
                                    :key="role"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"
                                >
                                    {{ role }}
                                </span>
                                <span v-if="user.roles.length === 0" class="text-gray-500 text-sm">
                                    Sin roles asignados
                                </span>
                            </div>
                        </div>

                        <!-- Permisos -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-500 mb-2">
                                Permisos
                            </label>
                            <div class="max-h-48 overflow-y-auto bg-gray-50 p-4 rounded">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                    <span 
                                        v-for="permission in user.permissions" 
                                        :key="permission"
                                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800"
                                    >
                                        {{ permission }}
                                    </span>
                                </div>
                                <p v-if="user.permissions.length === 0" class="text-gray-500 text-sm">
                                    Sin permisos asignados
                                </p>
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

defineProps({
    user: Object,
})
</script>