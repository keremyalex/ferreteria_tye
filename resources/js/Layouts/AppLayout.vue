<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PageVisitCounter from '@/Components/PageVisitCounter.vue';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <div class="min-h-screen bg-gray-100 flex flex-col">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex items-center shrink-0">
                                <Link :href="route('dashboard')">
                                    <ApplicationMark class="block w-auto h-9" />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </NavLink>
                                
                                <!-- Gestión de Usuarios -->
                                <div v-if="$page.props.auth.user.permissions?.includes('view users')" class="relative">
                                    <Dropdown align="left" width="48">
                                        <template #trigger>
                                            <button class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 text-gray-500 transition duration-150 ease-in-out border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300">
                                                Usuarios
                                                <svg class="ms-1 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>
                                        </template>
                                        <template #content>
                                            <DropdownLink :href="route('users.index')">Lista de Usuarios</DropdownLink>
                                            <DropdownLink v-if="$page.props.auth.user.permissions?.includes('create users')" :href="route('users.create')">Crear Usuario</DropdownLink>
                                        </template>
                                    </Dropdown>
                                </div>

                                <!-- Gestión de Productos -->
                                <div v-if="$page.props.auth.user.permissions?.includes('view products') || $page.props.auth.user.permissions?.includes('view categories') || $page.props.auth.user.permissions?.includes('view suppliers') || $page.props.auth.user.permissions?.includes('view measurements')" class="relative">
                                    <Dropdown align="left" width="48">
                                        <template #trigger>
                                            <button class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 text-gray-500 transition duration-150 ease-in-out border-b-2 border-transparent hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300">
                                                Productos
                                                <svg class="ms-1 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>
                                        </template>
                                        <template #content>
                                            <DropdownLink v-if="$page.props.auth.user.permissions?.includes('view products')" :href="route('products.index')">Productos</DropdownLink>
                                            <DropdownLink v-if="$page.props.auth.user.permissions?.includes('view categories')" :href="route('categories.index')">Categorías</DropdownLink>
                                            <DropdownLink v-if="$page.props.auth.user.permissions?.includes('view suppliers')" :href="route('suppliers.index')">Proveedores</DropdownLink>
                                            <DropdownLink v-if="$page.props.auth.user.permissions?.includes('view measurements')" :href="route('measurements.index')">Unidades de Medida</DropdownLink>
                                        </template>
                                    </Dropdown>
                                </div>

                                <!-- Clientes -->
                                <NavLink v-if="$page.props.auth.user.permissions?.includes('view.clients')" :href="route('clients.index')" :active="route().current('clients.*')">
                                    Clientes
                                </NavLink>

                                <!-- Inventario -->
                                <NavLink v-if="$page.props.auth.user.permissions?.includes('view.inventory')" :href="route('inventory.index')" :active="route().current('inventory.*')">
                                    Inventario
                                </NavLink>

                                <!-- Compras -->
                                <NavLink v-if="$page.props.auth.user.permissions?.includes('view.purchases')" :href="route('purchases.index')" :active="route().current('purchases.*')">
                                    Compras
                                </NavLink>

                                <!-- Ventas -->
                                <NavLink href="#" :active="false">
                                    Ventas
                                </NavLink>

                                <!-- Reportes -->
                                <NavLink href="#" :active="false">
                                    Reportes
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <div class="relative ms-3">
                                <!-- Teams Dropdown -->
                                <Dropdown v-if="$page.props.jetstream.hasTeamFeatures" align="right" width="60">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50">
                                                {{ $page.props.auth.user.current_team.name }}

                                                <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60">
                                            <!-- Team Management -->
                                            <div class="block px-4 py-2 text-xs text-gray-400">
                                                Manage Team
                                            </div>

                                            <!-- Team Settings -->
                                            <DropdownLink :href="route('teams.show', $page.props.auth.user.current_team)">
                                                Team Settings
                                            </DropdownLink>

                                            <DropdownLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')">
                                                Create New Team
                                            </DropdownLink>

                                            <!-- Team Switcher -->
                                            <template v-if="$page.props.auth.user.all_teams.length > 1">
                                                <div class="border-t border-gray-200" />

                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Switch Teams
                                                </div>

                                                <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                                    <form @submit.prevent="switchToTeam(team)">
                                                        <DropdownLink as="button">
                                                            <div class="flex items-center">
                                                                <svg v-if="team.id == $page.props.auth.user.current_team_id" class="text-green-400 me-2 size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>

                                                                <div>{{ team.name }}</div>
                                                            </div>
                                                        </DropdownLink>
                                                    </form>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm transition border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300">
                                            <img class="object-cover rounded-full size-8" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50">
                                                {{ $page.props.auth.user.name }}

                                                <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Manage Account
                                        </div>

                                        <DropdownLink :href="route('profile.show')">
                                            Profile
                                        </DropdownLink>

                                        <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                                            API Tokens
                                        </DropdownLink>

                                        <div class="border-t border-gray-200" />

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <DropdownLink as="button">
                                                Log Out
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="flex items-center -me-2 sm:hidden">
                            <button class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500" @click="showingNavigationDropdown = ! showingNavigationDropdown">
                                <svg
                                    class="size-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                        
                        <!-- Usuarios móvil -->
                        <div v-if="$page.props.auth.user.permissions?.includes('view users')">
                            <div class="block px-4 py-2 text-xs font-semibold text-gray-400">USUARIOS</div>
                            <ResponsiveNavLink :href="route('users.index')">Lista de Usuarios</ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.permissions?.includes('create users')" :href="route('users.create')">Crear Usuario</ResponsiveNavLink>
                        </div>

                        <!-- Productos móvil -->
                        <div v-if="$page.props.auth.user.permissions?.includes('view products') || $page.props.auth.user.permissions?.includes('view categories') || $page.props.auth.user.permissions?.includes('view suppliers') || $page.props.auth.user.permissions?.includes('view measurements')">
                            <div class="block px-4 py-2 text-xs font-semibold text-gray-400">PRODUCTOS</div>
                            <ResponsiveNavLink v-if="$page.props.auth.user.permissions?.includes('view products')" :href="route('products.index')" :active="route().current('products.*')">Productos</ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.permissions?.includes('view categories')" :href="route('categories.index')" :active="route().current('categories.*')">Categorías</ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.permissions?.includes('view suppliers')" :href="route('suppliers.index')" :active="route().current('suppliers.*')">Proveedores</ResponsiveNavLink>
                            <ResponsiveNavLink v-if="$page.props.auth.user.permissions?.includes('view measurements')" :href="route('measurements.index')" :active="route().current('measurements.*')">Unidades de Medida</ResponsiveNavLink>
                        </div>

                        <!-- Clientes móvil -->
                        <div v-if="$page.props.auth.user.permissions?.includes('view.clients')">
                            <div class="block px-4 py-2 text-xs font-semibold text-gray-400">CLIENTES</div>
                            <ResponsiveNavLink :href="route('clients.index')" :active="route().current('clients.*')">Gestión de Clientes</ResponsiveNavLink>
                        </div>

                        <!-- Inventario móvil -->
                        <div v-if="$page.props.auth.user.permissions?.includes('view.inventory')">
                            <div class="block px-4 py-2 text-xs font-semibold text-gray-400">INVENTARIO</div>
                            <ResponsiveNavLink :href="route('inventory.index')" :active="route().current('inventory.*')">Gestión de Stock</ResponsiveNavLink>
                        </div>

                        <!-- Compras móvil -->
                        <div v-if="$page.props.auth.user.permissions?.includes('view.purchases')">
                            <div class="block px-4 py-2 text-xs font-semibold text-gray-400">COMPRAS</div>
                            <ResponsiveNavLink :href="route('purchases.index')" :active="route().current('purchases.*')">Gestión de Compras</ResponsiveNavLink>
                        </div>

                        <!-- Otros móvil -->
                        <div>
                            <div class="block px-4 py-2 text-xs font-semibold text-gray-400">OTROS</div>
                            <ResponsiveNavLink href="#" :active="false">Ventas</ResponsiveNavLink>
                            <ResponsiveNavLink href="#" :active="false">Reportes</ResponsiveNavLink>
                        </div>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="flex items-center px-4">
                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 me-3">
                                <img class="object-cover rounded-full size-10" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                            </div>

                            <div>
                                <div class="text-base font-medium text-gray-800">
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div class="text-sm font-medium text-gray-500">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                                Profile
                            </ResponsiveNavLink>

                            <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')" :active="route().current('api-tokens.index')">
                                API Tokens
                            </ResponsiveNavLink>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <ResponsiveNavLink as="button">
                                    Log Out
                                </ResponsiveNavLink>
                            </form>

                            <!-- Team Management -->
                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                <div class="border-t border-gray-200" />

                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    Manage Team
                                </div>

                                <!-- Team Settings -->
                                <ResponsiveNavLink :href="route('teams.show', $page.props.auth.user.current_team)" :active="route().current('teams.show')">
                                    Team Settings
                                </ResponsiveNavLink>

                                <ResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')" :active="route().current('teams.create')">
                                    Create New Team
                                </ResponsiveNavLink>

                                <!-- Team Switcher -->
                                <template v-if="$page.props.auth.user.all_teams.length > 1">
                                    <div class="border-t border-gray-200" />

                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        Switch Teams
                                    </div>

                                    <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                        <form @submit.prevent="switchToTeam(team)">
                                            <ResponsiveNavLink as="button">
                                                <div class="flex items-center">
                                                    <svg v-if="team.id == $page.props.auth.user.current_team_id" class="text-green-400 me-2 size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <div>{{ team.name }}</div>
                                                </div>
                                            </ResponsiveNavLink>
                                        </form>
                                    </template>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white shadow">
                <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1">
                <slot />
            </main>

            <!-- Footer para el panel administrativo -->
            <footer class="bg-white border-t border-gray-200 mt-auto">
                <div class="px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            &copy; 2025 Ferretería TYE - Panel Administrativo
                        </div>
                        <PageVisitCounter />
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>
