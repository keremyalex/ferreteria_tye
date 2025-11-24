<template>
    <AppLayout title="Crear Usuario">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Crear Nuevo Usuario
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <!-- Nombre -->
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Nombre
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    id="name"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.name }"
                                    required
                                />
                                <div v-if="errors.name" class="mt-1 text-sm text-red-600">
                                    {{ errors.name }}
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    Email
                                </label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    id="email"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.email }"
                                    required
                                />
                                <div v-if="errors.email" class="mt-1 text-sm text-red-600">
                                    {{ errors.email }}
                                </div>
                            </div>

                            <!-- Contraseña -->
                            <div class="mb-4">
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    Contraseña
                                </label>
                                <input
                                    v-model="form.password"
                                    type="password"
                                    id="password"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500': errors.password }"
                                    required
                                />
                                <div v-if="errors.password" class="mt-1 text-sm text-red-600">
                                    {{ errors.password }}
                                </div>
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                    Confirmar Contraseña
                                </label>
                                <input
                                    v-model="form.password_confirmation"
                                    type="password"
                                    id="password_confirmation"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
                                    :class="{ 'border-red-500': errors.role }"
                                    required
                                >
                                    <option value="">Selecciona un rol</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.name">
                                        {{ role.name }}
                                    </option>
                                </select>
                                <div v-if="errors.role" class="mt-1 text-sm text-red-600">
                                    {{ errors.role }}
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
                                    :disabled="processing"
                                >
                                    <span v-if="processing">Guardando...</span>
                                    <span v-else>Crear Usuario</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/SidebarLayout.vue'

const props = defineProps({
    roles: Array,
    errors: Object,
})

const { data: form, post, processing, errors } = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
})

const submit = () => {
    post(route('users.store'))
}
</script>