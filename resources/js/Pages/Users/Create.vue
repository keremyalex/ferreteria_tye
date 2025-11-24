<template>
    <SidebarLayout title="Crear Usuario">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-white">
                Crear Nuevo Usuario
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <form @submit.prevent="submit" autocomplete="off">
                            <!-- Nombre -->
                            <div class="mb-4">
                                <label for="create_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Nombre
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    id="create_name"
                                    name="create_name"
                                    autocomplete="off"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.name }"
                                    required
                                />
                                <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label for="create_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Email
                                </label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    id="create_email"
                                    name="create_email"
                                    autocomplete="off"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.email }"
                                    required
                                />
                                <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <!-- Contraseña -->
                            <div class="mb-4">
                                <label for="create_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Contraseña
                                </label>
                                <input
                                    v-model="form.password"
                                    type="password"
                                    id="create_password"
                                    name="create_password"
                                    autocomplete="new-password"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.password }"
                                    required
                                />
                                <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.password }}
                                </div>
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div class="mb-4">
                                <label for="create_password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Confirmar Contraseña
                                </label>
                                <input
                                    v-model="form.password_confirmation"
                                    type="password"
                                    id="create_password_confirmation"
                                    name="create_password_confirmation"
                                    autocomplete="new-password"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    required
                                />
                            </div>

                            <!-- Rol -->
                            <div class="mb-6">
                                <label for="role" class="block text-sm font-medium text-gray-700">
                                    Rol
                                </label>
                                <select
                                    v-model="form.role"
                                    id="role"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': form.errors.role }"
                                    required
                                >
                                    <option value="">Selecciona un rol</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.name">
                                        {{ role.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.role" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.role }}
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="flex justify-end space-x-4">
                                <Link 
                                    :href="route('users.index')"
                                    class="px-4 py-2 font-bold text-white bg-gray-500 rounded hover:bg-gray-700"
                                >
                                    Cancelar
                                </Link>
                                <button 
                                    type="submit"
                                    class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700"
                                    :disabled="form.processing"
                                >
                                    <span v-if="form.processing">Guardando...</span>
                                    <span v-else>Crear Usuario</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'

const props = defineProps({
    roles: Array,
    errors: Object,
})

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
})

// Asegurar que los campos estén vacíos al montar el componente
onMounted(() => {
    form.reset()
    form.name = ''
    form.email = ''
    form.password = ''
    form.password_confirmation = ''
    form.role = ''
})

const submit = () => {
    form.post(route('users.store'))
}
</script>