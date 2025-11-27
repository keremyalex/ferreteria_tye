<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header de la tienda -->
        <header class="bg-white border-b border-gray-200 shadow-sm">
            <!-- Barra superior -->
            <div class="bg-gray-800">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between py-2">
                        <div class="flex items-center space-x-6 text-sm text-gray-300">
                            <span>📞 +591 70123456</span>
                            <span>📧 info@ferreteria-tye.com</span>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Para usuarios no logueados -->
                            <template v-if="!$page.props.auth.user">
                                <Link :href="route('login')" class="text-sm text-gray-300 hover:text-white">
                                    Iniciar Sesión
                                </Link>
                                <Link :href="route('register')" class="text-sm text-gray-300 hover:text-white">
                                    Registrarse
                                </Link>
                            </template>

                            <!-- Para usuarios con rol cliente -->
                            <template v-else-if="isClient">
                                <span class="text-sm text-gray-300">
                                    Hola, {{ $page.props.auth.user.name }}
                                </span>
                            </template>

                            <!-- Para usuarios administrativos -->
                            <template v-else>
                                <Link :href="route('dashboard')" class="text-sm text-gray-300 hover:text-white">
                                    Panel Admin
                                </Link>
                            </template>

                            <!-- Cerrar sesión (todos los usuarios logueados) -->
                            <form v-if="$page.props.auth.user" @submit.prevent="logout">
                                <button type="submit" class="text-sm text-gray-300 hover:text-white">
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navegación principal -->
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-4">
                    <!-- Logo -->
                    <Link :href="route('home')" class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-600 rounded-lg">
                            <span class="text-xl font-bold text-white">F</span>
                        </div>
                        <div class="ml-3">
                            <h1 class="text-xl font-bold text-gray-900">Ferretería TYE</h1>
                            <p class="text-sm text-gray-600">Todo para tu construcción</p>
                        </div>
                    </Link>

                    <!-- Barra de búsqueda -->
                    <div class="flex-1 max-w-lg mx-8">
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                @keyup.enter="search"
                                type="text"
                                placeholder="Buscar productos..."
                                class="w-full py-2 pl-10 pr-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <MagnifyingGlassIcon class="w-5 h-5 text-gray-400" />
                            </div>
                        </div>
                    </div>

                    <!-- Carrito -->
                    <Link :href="route('cart.index')" class="relative inline-flex items-center px-4 py-2 text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                        <ShoppingCartIcon class="w-6 h-6 mr-2" />
                        <span class="font-medium">Carrito</span>
                        <span v-if="cartItemsCount > 0" class="absolute flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 rounded-full -top-2 -right-2">
                            {{ cartItemsCount }}
                        </span>
                    </Link>
                </div>

                <!-- Navegación de categorías -->
                <nav class="py-4 border-t border-gray-200">
                    <div class="flex space-x-8">
                        <Link :href="route('home')" 
                            class="font-medium text-gray-700 transition-colors hover:text-blue-600"
                            :class="{ 'text-blue-600': $page.url === '/' || $page.url === '/catalogo' }">
                            Todos los Productos
                        </Link>
                        <Link v-for="category in categories" :key="category.id"
                            :href="route('catalog.index', { category: category.id })"
                            class="font-medium text-gray-700 transition-colors hover:text-blue-600"
                            :class="{ 'text-blue-600': $page.props.filters?.category == category.id }">
                            {{ category.nombre }}
                        </Link>
                    </div>
                </nav>

                <!-- Menú específico para clientes logueados -->
                <nav v-if="isClient" class="py-3 border-t border-gray-100 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex space-x-6">
                            <Link :href="route('home')" 
                                class="flex items-center text-sm font-medium text-gray-600 transition-colors hover:text-blue-600"
                                :class="{ 'text-blue-600': $page.url === '/' }">
                                <CubeIcon class="w-4 h-4 mr-1" />
                                Catálogo
                            </Link>
                            <Link :href="route('client.orders')" 
                                class="flex items-center text-sm font-medium text-gray-600 transition-colors hover:text-blue-600"
                                :class="{ 'text-blue-600': $page.url.startsWith('/mis-pedidos') }">
                                <ClipboardDocumentListIcon class="w-4 h-4 mr-1" />
                                Mis Pedidos
                            </Link>
                            <Link :href="route('client.profile.show')" 
                                class="flex items-center text-sm font-medium text-gray-600 transition-colors hover:text-blue-600"
                                :class="{ 'text-blue-600': $page.url.startsWith('/mi-perfil') }">
                                <UserIcon class="w-4 h-4 mr-1" />
                                Mi Perfil
                            </Link>
                        </div>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Contenido principal -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="text-white bg-gray-800">
            <div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                    <div>
                        <h3 class="mb-4 text-lg font-semibold">Ferretería TYE</h3>
                        <p class="text-gray-300">
                            Tu aliado en construcción. Calidad, variedad y los mejores precios.
                        </p>
                    </div>
                    <div>
                        <h3 class="mb-4 text-lg font-semibold">Categorías</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-300 hover:text-white">Material de Construcción</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">Herramientas</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">Eléctricos</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white">Cerrajería</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="mb-4 text-lg font-semibold">Contacto</h3>
                        <ul class="space-y-2 text-gray-300">
                            <li>📍 Av. Principal 123, La Paz</li>
                            <li>📞 +591 70123456</li>
                            <li>📧 info@ferreteria-tye.com</li>
                            <li>🕒 Lun-Sáb: 8:00-18:00</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="mb-4 text-lg font-semibold">Síguenos</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-300 hover:text-white">Facebook</a>
                            <a href="#" class="text-gray-300 hover:text-white">Instagram</a>
                            <a href="#" class="text-gray-300 hover:text-white">WhatsApp</a>
                        </div>
                    </div>
                </div>
                <div class="pt-8 mt-8 text-center text-gray-300 border-t border-gray-700">
                    <div class="flex items-center justify-center space-x-4">
                        <p>&copy; 2025 Ferretería TYE. Todos los derechos reservados.</p>
                        <span class="text-gray-500">|</span>
                        <PageVisitCounter />
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import PageVisitCounter from '@/Components/PageVisitCounter.vue'
import { 
    MagnifyingGlassIcon, 
    ShoppingCartIcon,
    CubeIcon,
    ClipboardDocumentListIcon,
    UserIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    }
})

const page = usePage()
const searchQuery = ref('')

// Verificar si el usuario es cliente
const isClient = computed(() => {
    return page.props.auth?.user?.roles?.includes('cliente')
})

// Gestión del carrito
const cartItemsCount = ref(0)

const updateCartCount = () => {
    if (typeof window !== 'undefined') {
        const cart = JSON.parse(localStorage.getItem('cart') || '{"items": []}')
        cartItemsCount.value = cart.items.reduce((total, item) => total + item.cantidad, 0)
    }
}

const search = () => {
    if (searchQuery.value.trim()) {
        router.get(route('catalog.index'), { search: searchQuery.value.trim() })
    }
}

const logout = () => {
    router.post(route('logout'))
}

// Formatear fecha
const formatDate = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleDateString('es-BO', { 
        year: 'numeric', 
        month: 'long' 
    })
}

// Escuchar cambios en el carrito
onMounted(() => {
    updateCartCount() // Cargar contador inicial
    if (typeof window !== 'undefined') {
        window.addEventListener('cart-updated', updateCartCount)
    }
})
</script>