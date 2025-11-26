<template>
    <ShopLayout>
        <div class="max-w-4xl p-6 mx-auto">
            <!-- Header -->
            <div class="p-6 mb-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Mi Perfil</h1>
                        <p class="text-gray-600">Gestiona tu información personal y configuración de cuenta</p>
                    </div>
                </div>
            </div>

            <!-- Mensajes de éxito/error -->
            <div v-if="$page.props.flash.success" class="relative px-4 py-3 mb-6 text-green-700 bg-green-100 border border-green-400 rounded">
                {{ $page.props.flash.success }}
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Información Personal -->
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Información Personal</h2>
                        <p class="text-sm text-gray-600">Actualiza tu información de contacto</p>
                    </div>
                    
                    <form @submit.prevent="updateProfile" class="p-6 space-y-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Nombre Completo
                            </label>
                            <input
                                type="text"
                                v-model="profileForm.name"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': profileErrors.name }"
                                required
                            />
                            <p v-if="profileErrors.name" class="mt-1 text-sm text-red-600">{{ profileErrors.name[0] }}</p>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Correo Electrónico
                            </label>
                            <input
                                type="email"
                                v-model="profileForm.email"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': profileErrors.email }"
                                required
                            />
                            <p v-if="profileErrors.email" class="mt-1 text-sm text-red-600">{{ profileErrors.email[0] }}</p>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Teléfono
                            </label>
                            <input
                                type="tel"
                                v-model="profileForm.telefono"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': profileErrors.telefono }"
                                placeholder="Ej: +51 987 654 321"
                            />
                            <p v-if="profileErrors.telefono" class="mt-1 text-sm text-red-600">{{ profileErrors.telefono[0] }}</p>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Dirección
                            </label>
                            <textarea
                                v-model="profileForm.direccion"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': profileErrors.direccion }"
                                placeholder="Ingresa tu dirección completa"
                            ></textarea>
                            <p v-if="profileErrors.direccion" class="mt-1 text-sm text-red-600">{{ profileErrors.direccion[0] }}</p>
                        </div>

                        <div class="pt-4">
                            <button
                                type="submit"
                                :disabled="profileProcessing"
                                class="w-full px-4 py-2 text-white transition duration-200 bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="profileProcessing">Actualizando...</span>
                                <span v-else>Actualizar Información</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Cambiar Contraseña -->
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Cambiar Contraseña</h2>
                        <p class="text-sm text-gray-600">Asegura tu cuenta con una contraseña fuerte</p>
                    </div>
                    
                    <form @submit.prevent="updatePassword" class="p-6 space-y-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Contraseña Actual
                            </label>
                            <input
                                type="password"
                                v-model="passwordForm.current_password"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': passwordErrors.current_password }"
                                required
                            />
                            <p v-if="passwordErrors.current_password" class="mt-1 text-sm text-red-600">{{ passwordErrors.current_password[0] }}</p>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Nueva Contraseña
                            </label>
                            <input
                                type="password"
                                v-model="passwordForm.password"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': passwordErrors.password }"
                                required
                            />
                            <p v-if="passwordErrors.password" class="mt-1 text-sm text-red-600">{{ passwordErrors.password[0] }}</p>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">
                                Confirmar Nueva Contraseña
                            </label>
                            <input
                                type="password"
                                v-model="passwordForm.password_confirmation"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            />
                        </div>

                        <div class="pt-4">
                            <button
                                type="submit"
                                :disabled="passwordProcessing"
                                class="w-full px-4 py-2 text-white transition duration-200 bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="passwordProcessing">Actualizando...</span>
                                <span v-else>Cambiar Contraseña</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </ShopLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import ShopLayout from '@/Layouts/ShopLayout.vue'

const props = defineProps({
    user: Object
})

// Estados de los formularios
const profileProcessing = ref(false)
const passwordProcessing = ref(false)
const profileErrors = ref({})
const passwordErrors = ref({})

// Formulario de perfil
const profileForm = reactive({
    name: props.user.name,
    email: props.user.email,
    telefono: props.user.telefono || '',
    direccion: props.user.direccion || ''
})

// Formulario de contraseña
const passwordForm = reactive({
    current_password: '',
    password: '',
    password_confirmation: ''
})

// Actualizar perfil
const updateProfile = () => {
    profileProcessing.value = true
    profileErrors.value = {}

    router.put(route('client.profile.update'), profileForm, {
        preserveScroll: true,
        onError: (errors) => {
            profileErrors.value = errors
        },
        onFinish: () => {
            profileProcessing.value = false
        }
    })
}

// Actualizar contraseña
const updatePassword = () => {
    passwordProcessing.value = true
    passwordErrors.value = {}

    router.put(route('client.profile.password'), passwordForm, {
        preserveScroll: true,
        onSuccess: () => {
            // Limpiar formulario
            Object.assign(passwordForm, {
                current_password: '',
                password: '',
                password_confirmation: ''
            })
        },
        onError: (errors) => {
            passwordErrors.value = errors
        },
        onFinish: () => {
            passwordProcessing.value = false
        }
    })
}
</script>