<template>
    <div>
        <Head :title="title" />

        <Banner />

        <div class="antialiased bg-gray-50 dark:bg-gray-900">
            <!-- Navbar -->
            <nav class="bg-white border-b border-gray-200 px-4 py-2.5 dark:bg-gray-800 dark:border-gray-700 fixed left-0 right-0 top-0 z-50">
                <div class="flex flex-wrap items-center justify-between">
                    <div class="flex items-center justify-start">
                        <!-- Sidebar Toggle -->
                        <button
                            @click="toggleSidebar"
                            data-drawer-target="drawer-navigation"
                            data-drawer-toggle="drawer-navigation"
                            aria-controls="drawer-navigation"
                            class="p-2 mr-2 text-gray-600 rounded-lg cursor-pointer md:hidden hover:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 dark:focus:bg-gray-700 focus:ring-2 focus:ring-gray-100 dark:focus:ring-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                        >
                            <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <svg aria-hidden="true" class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>

                        <!-- Logo -->
                        <Link :href="route('dashboard')" class="flex items-center justify-between mr-4">
                            <ApplicationMark class="w-auto h-8" />
                            <span class="self-center ml-2 text-2xl font-semibold whitespace-nowrap dark:text-white">Ferretería TYE</span>
                        </Link>
                    </div>

                    <div class="flex items-center lg:order-2">
                        <!-- Search Button -->
                        <button type="button" class="p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600">
                            <svg class="w-4 h-4" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                            </svg>
                        </button>

                        <!-- Theme Toggle -->
                        <ThemeToggle />

                        <!-- Notifications -->
                        <button type="button" class="p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600">
                            <span class="sr-only">Ver notificaciones</span>
                            <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                            </svg>
                        </button>

                        <!-- User Menu -->
                        <div class="relative">
                            <button 
                                @click="toggleUserDropdown"
                                class="flex mx-3 text-sm bg-gray-800 rounded-full md:mr-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                                :class="{ 'ring-4 ring-gray-300 dark:ring-gray-600': userDropdownOpen }"
                            >
                                <span class="sr-only">Abrir menú de usuario</span>
                                <img 
                                    v-if="$page.props.jetstream?.managesProfilePhotos && $page.props.auth?.user?.profile_photo_url"
                                    class="w-8 h-8 rounded-full" 
                                    :src="$page.props.auth.user.profile_photo_url" 
                                    :alt="$page.props.auth.user.name"
                                />
                                <div v-else class="flex items-center justify-center w-8 h-8 text-sm font-medium text-white bg-blue-600 rounded-full">
                                    {{ $page.props.auth?.user?.name?.charAt(0).toUpperCase() || 'U' }}
                                </div>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div 
                                v-show="userDropdownOpen"
                                class="absolute right-0 z-50 w-56 mt-2 bg-white divide-y divide-gray-100 rounded-lg shadow-lg dark:bg-gray-700 dark:divide-gray-600"
                                @click.away="userDropdownOpen = false"
                            >
                                <div class="px-4 py-3">
                                    <span class="block text-sm font-semibold text-gray-900 dark:text-white">{{ $page.props.auth?.user?.name || 'Usuario' }}</span>
                                    <span class="block text-sm text-gray-500 truncate dark:text-gray-400">{{ $page.props.auth?.user?.email || '' }}</span>
                                </div>
                                <ul class="py-2">
                                    <li>
                                        <Link :href="route('profile.show')" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                            </svg>
                                            Mi Perfil
                                        </Link>
                                    </li>
                                    <li v-if="$page.props.jetstream?.hasApiFeatures">
                                        <Link :href="route('api-tokens.index')" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            API Tokens
                                        </Link>
                                    </li>
                                </ul>
                                <div class="py-1">
                                    <button @click="logout" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Cerrar Sesión
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Sidebar -->
            <aside 
                :class="[
                    'fixed top-0 left-0 z-40 w-64 h-screen pt-14 transition-transform bg-white border-r border-gray-200 md:translate-x-0 dark:bg-gray-800 dark:border-gray-700',
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full'
                ]"
                aria-label="Sidenav"
                id="drawer-navigation"
            >
                <div class="h-full px-3 py-5 overflow-y-auto bg-white dark:bg-gray-800">
                    <ul class="space-y-2">
                        <!-- Dashboard -->
                        <li>
                            <Link 
                                :href="route('dashboard')" 
                                :class="[
                                    'flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group',
                                    route().current('dashboard') ? 'bg-gray-100 dark:bg-gray-700' : ''
                                ]"
                            >
                                <svg aria-hidden="true" class="w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                                    <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                                </svg>
                                <span class="ml-3">Dashboard</span>
                            </Link>
                        </li>

                        <!-- Usuarios -->
                        <li v-if="hasPermission('view.users')">
                            <Link 
                                :href="route('users.index')" 
                                :class="[
                                    'flex items-center p-2 text-base font-medium rounded-lg group hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors',
                                    route().current('users.*') ? 'text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-800' : 'text-gray-900 dark:text-white'
                                ]"
                            >
                                <svg aria-hidden="true" class="w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-3">Usuarios</span>
                            </Link>
                        </li>

                        <!-- Productos -->
                        <li v-if="hasPermission('view.products') || hasPermission('view.categories') || hasPermission('view.suppliers') || hasPermission('view.measurements')">
                            <button 
                                type="button" 
                                @click="toggleSubmenu('products')"
                                :class="[
                                    'flex items-center w-full p-2 text-base font-medium transition duration-75 rounded-lg group hover:bg-gray-100 dark:hover:bg-gray-700',
                                    (route().current('products.*') || route().current('categories.*') || route().current('suppliers.*') || route().current('measurements.*')) 
                                        ? 'text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-800' 
                                        : 'text-gray-900 dark:text-white'
                                ]"
                            >
                                <svg aria-hidden="true" class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 2L3 7v11a1 1 0 001 1h12a1 1 0 001-1V7l-7-5zM9 18v-6h2v6H9z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="flex-1 ml-3 text-left whitespace-nowrap">Productos</span>
                                <svg :class="[
                                    'w-3 h-3 transition-transform',
                                    openSubmenus.products ? 'rotate-180' : ''
                                ]" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <ul :class="[
                                'py-2 space-y-2 transition-all duration-300',
                                openSubmenus.products ? 'block' : 'hidden'
                            ]">
                                <li v-if="hasPermission('view.products')">
                                    <Link :href="route('products.index')" :class="[
                                        'flex items-center w-full p-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:hover:bg-gray-700',
                                        route().current('products.*') ? 'text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-800' : 'text-gray-900 dark:text-white'
                                    ]">
                                        Lista de Productos
                                    </Link>
                                </li>
                                <li v-if="hasPermission('view.categories')">
                                    <Link :href="route('categories.index')" :class="[
                                        'flex items-center w-full p-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:hover:bg-gray-700',
                                        route().current('categories.*') ? 'text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-800' : 'text-gray-900 dark:text-white'
                                    ]">
                                        Categorías
                                    </Link>
                                </li>
                                <li v-if="hasPermission('view.suppliers')">
                                    <Link :href="route('suppliers.index')" :class="[
                                        'flex items-center w-full p-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:hover:bg-gray-700',
                                        route().current('suppliers.*') ? 'text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-800' : 'text-gray-900 dark:text-white'
                                    ]">
                                        Proveedores
                                    </Link>
                                </li>
                                <li v-if="hasPermission('view.measurements')">
                                    <Link :href="route('measurements.index')" :class="[
                                        'flex items-center w-full p-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:hover:bg-gray-700',
                                        route().current('measurements.*') ? 'text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-800' : 'text-gray-900 dark:text-white'
                                    ]">
                                        Unidades de Medida
                                    </Link>
                                </li>
                            </ul>
                        </li>

                        <!-- Clientes -->
                        <li v-if="hasPermission('view.clients')">
                            <Link 
                                :href="route('clients.index')" 
                                :class="[
                                    'flex items-center p-2 text-base font-medium rounded-lg group hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors',
                                    route().current('clients.*') ? 'text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-800' : 'text-gray-900 dark:text-white'
                                ]"
                            >
                                <svg aria-hidden="true" class="w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                                </svg>
                                <span class="ml-3">Clientes</span>
                            </Link>
                        </li>

                        <!-- Inventario y Stock -->
                        <li v-if="hasPermission('view.inventory')">
                            <button 
                                type="button" 
                                @click="toggleSubmenu('inventory')"
                                :class="[
                                    'flex items-center w-full p-2 text-base font-medium transition duration-75 rounded-lg group hover:bg-gray-100 dark:hover:bg-gray-700',
                                    (route().current('inventory.*')) 
                                        ? 'text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-800' 
                                        : 'text-gray-900 dark:text-white'
                                ]"
                            >
                                <svg aria-hidden="true" class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                </svg>
                                <span class="flex-1 ml-3 text-left whitespace-nowrap">Inventario</span>
                                <svg :class="[
                                    'w-3 h-3 transition-transform',
                                    openSubmenus.inventory ? 'rotate-180' : ''
                                ]" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <ul :class="[
                                'py-2 space-y-2 transition-all duration-300',
                                openSubmenus.inventory ? 'block' : 'hidden'
                            ]">
                                <li>
                                    <Link :href="route('inventory.index')" :class="[
                                        'flex items-center w-full p-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:hover:bg-gray-700',
                                        route().current('inventory.index') ? 'text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-800' : 'text-gray-900 dark:text-white'
                                    ]">
                                        Stock Actual
                                    </Link>
                                </li>
                                <li>
                                    <Link :href="route('inventory.movements.index')" :class="[
                                        'flex items-center w-full p-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:hover:bg-gray-700',
                                        route().current('inventory.movements.index') ? 'text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-800' : 'text-gray-900 dark:text-white'
                                    ]">
                                        Movimientos
                                    </Link>
                                </li>
                            </ul>
                        </li>

                        <!-- Compras -->
                        <li v-if="hasPermission('view.purchases')">
                            <Link 
                                :href="route('purchases.index')" 
                                :class="[
                                    'flex items-center p-2 text-base font-medium rounded-lg group hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors',
                                    route().current('purchases.*') ? 'text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-800' : 'text-gray-900 dark:text-white'
                                ]"
                            >
                                <svg aria-hidden="true" class="w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 2L3 7v11a1 1 0 001 1h12a1 1 0 001-1V7l-7-5zM8 10a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H9a1 1 0 01-1-1v-5z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-3">Compras</span>
                            </Link>
                        </li>

                        <!-- Ventas -->
                        <li>
                            <Link 
                                :href="route('orders.index')" 
                                :class="[
                                    'flex items-center p-2 text-base font-medium rounded-lg group hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors',
                                    route().current('orders.*') ? 'text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-800' : 'text-gray-900 dark:text-white'
                                ]"
                            >
                                <svg aria-hidden="true" class="w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                </svg>
                                <span class="ml-3">Ventas</span>
                            </Link>
                        </li>

                        <!-- Reportes -->
                        <li>
                            <Link 
                                href="#" 
                                class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                            >
                                <svg aria-hidden="true" class="w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                                </svg>
                                <span class="ml-3">Reportes</span>
                            </Link>
                        </li>

                        <!-- Configuración -->
                        <li v-if="hasPermission('view.settings')">
                            <Link 
                                href="#" 
                                class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                            >
                                <svg aria-hidden="true" class="w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-3">Configuración</span>
                            </Link>
                        </li>
                    </ul>
                </div>
            </aside>

            <!-- Main content -->
            <main class="min-h-screen p-4 pt-20 md:ml-64 bg-gray-50 dark:bg-gray-900">
                <!-- Page Heading -->
                <header v-if="$slots.header" class="mb-6 bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="px-4 py-6 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>

                <!-- Page Content -->
                <div class="w-full">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import ApplicationMark from '@/Components/ApplicationMark.vue'
import Banner from '@/Components/Banner.vue'
import ThemeToggle from '@/Components/ThemeToggle.vue'

const $page = usePage()

defineProps({
    title: String,
})

const sidebarOpen = ref(false)
const userDropdownOpen = ref(false)
const openSubmenus = ref({
    users: false,
    products: false,
    inventory: false,
})

// Watch for route changes to keep submenus in sync
watch(() => $page.url, (newUrl) => {
    if (newUrl.includes('/inventory/')) {
        openSubmenus.value.inventory = true
    }
    if (newUrl.includes('/products/') || newUrl.includes('/categories/') || newUrl.includes('/suppliers/') || newUrl.includes('/measurements/')) {
        openSubmenus.value.products = true
    }
    if (newUrl.includes('/users/')) {
        openSubmenus.value.users = true
    }
})

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value
}

const toggleSubmenu = (menu) => {
    openSubmenus.value[menu] = !openSubmenus.value[menu]
}

const toggleUserDropdown = () => {
    userDropdownOpen.value = !userDropdownOpen.value
}

const logout = () => {
    router.post(route('logout'))
}

// Helper function to safely check permissions
const hasPermission = (permission) => {
    const permissions = $page.props.auth.user.permissions
    return Array.isArray(permissions) && permissions.includes(permission)
}

onMounted(() => {
    // Auto-open submenus based on current route
    if (route().current('products.*') || route().current('categories.*') || route().current('suppliers.*') || route().current('measurements.*')) {
        openSubmenus.value.products = true
    }
    if (route().current('users.*')) {
        openSubmenus.value.users = true
    }
    if (route().current('inventory.*')) {
        openSubmenus.value.inventory = true
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
        const sidebar = document.getElementById('drawer-navigation')
        const button = e.target.closest('[data-drawer-toggle="drawer-navigation"]')
        
        if (sidebar && !sidebar.contains(e.target) && !button && window.innerWidth < 768) {
            sidebarOpen.value = false
        }
        
        // Close user dropdown when clicking outside
        const userMenu = e.target.closest('.relative')
        if (!userMenu || !userMenu.querySelector('button')) {
            userDropdownOpen.value = false
        }
    })
})
</script>