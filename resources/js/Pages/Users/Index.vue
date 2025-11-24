<template>
    <SidebarLayout title="Gestión de Usuarios">
        <div class="mx-auto max-w-7xl">
            <div class="overflow-hidden bg-white rounded-lg shadow-lg dark:bg-gray-800">
                <div class="p-6">
                    <!-- Header con botón de crear -->
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            Lista de Usuarios
                        </h3>
                        <Link 
                            :href="route('users.create')"
                            class="px-4 py-2 font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                            v-if="$page.props.auth?.user?.permissions?.includes('create.users')"
                        >
                            Crear Usuario
                        </Link>
                    </div>

                    <!-- Mensaje de éxito -->
                    <div 
                        v-if="$page.props.flash?.success"
                        class="px-4 py-3 mb-4 text-green-700 bg-green-100 border border-green-400 rounded-lg dark:bg-green-800 dark:text-green-200 dark:border-green-600"
                    >
                        {{ $page.props.flash.success }}
                    </div>

                    <!-- Tabla de usuarios -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                        Nombre
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                        Email
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                        Roles
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                        Fecha de Registro
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                <tr v-for="user in users?.data || []" :key="user.id">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ user.name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-300">
                                        {{ user.email }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-300">
                                        <span 
                                            v-for="role in user.roles" 
                                            :key="role"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1 dark:bg-blue-900 dark:text-blue-300"
                                        >
                                            {{ role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-300">
                                        {{ user.created_at }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                        <div class="flex space-x-2">
                                            <Link 
                                                :href="route('users.show', user.id)"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                                v-if="$page.props.auth?.user?.permissions?.includes('view.users')"
                                            >
                                                Ver
                                            </Link>
                                            <Link 
                                                :href="route('users.edit', user.id)"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                                v-if="$page.props.auth?.user?.permissions?.includes('edit.users')"
                                            >
                                                Editar
                                            </Link>
                                            <button 
                                                @click="deleteUser(user.id)"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                v-if="$page.props.auth?.user?.permissions?.includes('delete.users')"
                                            >
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-6" v-if="users?.links && users.links.length > 3">
                        <nav class="flex justify-center">
                            <div class="flex space-x-1">
                                <Link 
                                    v-for="link in users.links" 
                                    :key="link.label"
                                    :href="link.url"
                                    :class="[
                                        'px-3 py-2 text-sm rounded-lg border',
                                        link.active 
                                            ? 'bg-blue-600 text-white border-blue-600 dark:bg-blue-600 dark:border-blue-600' 
                                            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700'
                                    ]"
                                    v-html="link.label"
                                />
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'

defineProps({
    users: Object,
})

const deleteUser = (userId) => {
    if (confirm('¿Estás seguro de que quieres eliminar este usuario?')) {
        router.delete(route('users.destroy', userId))
    }
}
</script>