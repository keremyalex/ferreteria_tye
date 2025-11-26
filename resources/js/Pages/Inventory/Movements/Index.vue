<template>
    <div>
        <Head title="Movimientos de Inventario" />
        <SidebarLayout>
            <div class="min-h-screen py-6 bg-gray-50 dark:bg-gray-900">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    
                    <!-- Header -->
                    <div class="mb-8">
                        <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                                        Movimientos de Inventario
                                    </h1>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        Historial de entradas, salidas y ajustes de inventario
                                    </p>
                                </div>
                                <div class="flex space-x-3">
                                    <Link 
                                        :href="route('inventory.movements.entrada.create')"
                                        v-if="$page.props.auth.user.permissions?.includes('create.inventory')"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                    >
                                        <ArrowDownIcon class="w-4 h-4 mr-2" />
                                        Nueva Entrada
                                    </Link>
                                    <Link 
                                        :href="route('inventory.movements.salida.create')"
                                        v-if="$page.props.auth.user.permissions?.includes('create.inventory')"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                    >
                                        <ArrowUpIcon class="w-4 h-4 mr-2" />
                                        Nueva Salida
                                    </Link>
                                    <Link 
                                        :href="route('inventory.movements.ajuste.create')"
                                        v-if="$page.props.auth.user.permissions?.includes('create.inventory')"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    >
                                        <ArrowPathIcon class="w-4 h-4 mr-2" />
                                        Ajuste Stock
                                    </Link>
                                </div>
                            </div>

                            <!-- Estadísticas -->
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                                <div class="p-4 rounded-lg bg-blue-50 dark:bg-blue-900">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <ClipboardDocumentListIcon class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                                        </div>
                                        <div class="ml-4">
                                            <dt class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Movimientos</dt>
                                            <dd class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ stats.total_movimientos }}</dd>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 rounded-lg bg-green-50 dark:bg-green-900">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <ArrowDownIcon class="w-8 h-8 text-green-600 dark:text-green-400" />
                                        </div>
                                        <div class="ml-4">
                                            <dt class="text-sm font-medium text-green-600 dark:text-green-400">Entradas Este Mes</dt>
                                            <dd class="text-2xl font-bold text-green-900 dark:text-green-100">{{ stats.entradas_mes }}</dd>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 rounded-lg bg-red-50 dark:bg-red-900">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <ArrowUpIcon class="w-8 h-8 text-red-600 dark:text-red-400" />
                                        </div>
                                        <div class="ml-4">
                                            <dt class="text-sm font-medium text-red-600 dark:text-red-400">Salidas Este Mes</dt>
                                            <dd class="text-2xl font-bold text-red-900 dark:text-red-100">{{ stats.salidas_mes }}</dd>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 rounded-lg bg-yellow-50 dark:bg-yellow-900">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <ClockIcon class="w-8 h-8 text-yellow-600 dark:text-yellow-400" />
                                        </div>
                                        <div class="ml-4">
                                            <dt class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Pendientes</dt>
                                            <dd class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">{{ stats.pendientes }}</dd>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="p-6 mb-6 bg-white rounded-lg shadow dark:bg-gray-800">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-6">
                            <!-- Búsqueda -->
                            <div class="md:col-span-2">
                                <input
                                    v-model="searchForm.search"
                                    @input="search"
                                    type="text"
                                    placeholder="Buscar por referencia o observaciones..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                >
                            </div>

                            <!-- Tipo -->
                            <div>
                                <select
                                    v-model="searchForm.tipo"
                                    @change="search"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                >
                                    <option value="">Todos los tipos</option>
                                    <option value="entrada">Entradas</option>
                                    <option value="salida">Salidas</option>
                                    <option value="ajuste">Ajustes</option>
                                </select>
                            </div>

                            <!-- Estado -->
                            <div>
                                <select
                                    v-model="searchForm.estado"
                                    @change="search"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                >
                                    <option value="">Todos los estados</option>
                                    <option value="aplicado">Aplicados</option>
                                    <option value="pendiente">Pendientes</option>
                                    <option value="cancelado">Cancelados</option>
                                </select>
                            </div>

                            <!-- Fecha Desde -->
                            <div>
                                <input
                                    v-model="searchForm.fecha_desde"
                                    @change="search"
                                    type="date"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                >
                            </div>

                            <!-- Fecha Hasta -->
                            <div>
                                <input
                                    v-model="searchForm.fecha_hasta"
                                    @change="search"
                                    type="date"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Movimientos -->
                    <div class="overflow-hidden bg-white rounded-lg shadow dark:bg-gray-800">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                Movimientos de Inventario
                            </h3>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            <button @click="sort('fecha')" class="flex items-center space-x-1 hover:text-gray-700">
                                                <span>Fecha</span>
                                                <ChevronUpDownIcon class="w-4 h-4" />
                                            </button>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Tipo
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Referencia
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Productos
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Usuario
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Estado
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <!-- Estado vacío -->
                                    <tr v-if="!movements.data || movements.data.length === 0">
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <ClipboardDocumentListIcon class="mx-auto h-12 w-12 text-gray-400" />
                                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay movimientos</h3>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comience creando su primer movimiento de inventario.</p>
                                            <div class="mt-6 flex justify-center space-x-3">
                                                <Link 
                                                    :href="route('inventory.movements.entrada.create')"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
                                                >
                                                    Nueva Entrada
                                                </Link>
                                                <Link 
                                                    :href="route('inventory.movements.salida.create')"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
                                                >
                                                    Nueva Salida
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- Datos de movimientos -->
                                    <tr v-for="movement in movements.data" :key="movement.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ formatDate(movement.fecha) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span 
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                                :class="getTipoBadgeClass(movement.tipo)"
                                            >
                                                {{ getTipoText(movement.tipo) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ movement.referencia || '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ movement.details?.length || 0 }} producto(s)
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ movement.user?.name || 'Sistema' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span 
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                                :class="getEstadoBadgeClass(movement.estado)"
                                            >
                                                {{ getEstadoText(movement.estado) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <Link 
                                                    :href="route('inventory.movements.show', movement.id)"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                                >
                                                    <EyeIcon class="w-4 h-4" />
                                                </Link>
                                                
                                                <button 
                                                    v-if="movement.estado === 'pendiente' && $page.props.auth.user.permissions?.includes('edit.inventory')"
                                                    @click="applyMovement(movement)"
                                                    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                                    title="Aplicar movimiento"
                                                >
                                                    <CheckIcon class="w-4 h-4" />
                                                </button>
                                                
                                                <button 
                                                    v-if="movement.estado === 'aplicado' && $page.props.auth.user.permissions?.includes('edit.inventory')"
                                                    @click="revertMovement(movement)"
                                                    class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                                                    title="Revertir movimiento"
                                                >
                                                    <ArrowUturnLeftIcon class="w-4 h-4" />
                                                </button>
                                                
                                                <button 
                                                    v-if="movement.estado !== 'aplicado' && $page.props.auth.user.permissions?.includes('delete.inventory')"
                                                    @click="deleteMovement(movement)"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                    title="Eliminar movimiento"
                                                >
                                                    <TrashIcon class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div v-if="movements.links && movements.data.length > 0" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                            <nav class="flex items-center justify-between">
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    Mostrando {{ movements.from || 0 }} a {{ movements.to || 0 }} de {{ movements.total || 0 }} resultados
                                </div>
                                <div class="flex space-x-1">
                                    <template v-for="link in movements.links" :key="link.label">
                                        <Link 
                                            v-if="link.url"
                                            :href="link.url"
                                            v-html="link.label"
                                            :class="[
                                                'px-3 py-2 text-sm border rounded-md',
                                                link.active 
                                                    ? 'bg-indigo-50 border-indigo-500 text-indigo-600 dark:bg-indigo-900 dark:border-indigo-400 dark:text-indigo-200' 
                                                    : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700'
                                            ]"
                                        ></Link>
                                        <span 
                                            v-else
                                            v-html="link.label"
                                            class="px-3 py-2 text-sm border rounded-md opacity-50 cursor-not-allowed bg-white border-gray-300 text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300"
                                        ></span>
                                    </template>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </SidebarLayout>
    </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, reactive } from 'vue'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import { 
    ArrowDownIcon, 
    ArrowUpIcon, 
    ArrowPathIcon,
    ClipboardDocumentListIcon,
    ClockIcon,
    ChevronUpDownIcon,
    EyeIcon,
    CheckIcon,
    ArrowUturnLeftIcon,
    TrashIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    movements: {
        type: Object,
        required: true,
        default: () => ({
            data: [],
            links: [],
            total: 0,
            from: 0,
            to: 0
        })
    },
    stats: {
        type: Object,
        required: true,
        default: () => ({
            total_movimientos: 0,
            entradas_mes: 0,
            salidas_mes: 0,
            pendientes: 0
        })
    },
    filters: {
        type: Object,
        default: () => ({})
    }
})

const searchForm = reactive({
    search: props.filters.search || '',
    tipo: props.filters.tipo || '',
    estado: props.filters.estado || '',
    fecha_desde: props.filters.fecha_desde || '',
    fecha_hasta: props.filters.fecha_hasta || '',
})

const search = () => {
    router.get(route('inventory.movements.index'), searchForm, {
        preserveState: true,
        preserveScroll: true
    })
}

const sort = (field) => {
    const direction = props.filters.direction === 'asc' ? 'desc' : 'asc'
    router.get(route('inventory.movements.index'), {
        ...searchForm,
        sort: field,
        direction: direction
    })
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const getTipoText = (tipo) => {
    const tipos = {
        entrada: 'Entrada',
        salida: 'Salida',
        ajuste: 'Ajuste'
    }
    return tipos[tipo] || tipo
}

const getTipoBadgeClass = (tipo) => {
    const classes = {
        entrada: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        salida: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        ajuste: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
    }
    return classes[tipo] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
}

const getEstadoText = (estado) => {
    const estados = {
        aplicado: 'Aplicado',
        pendiente: 'Pendiente',
        cancelado: 'Cancelado'
    }
    return estados[estado] || estado
}

const getEstadoBadgeClass = (estado) => {
    const classes = {
        aplicado: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        pendiente: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        cancelado: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    }
    return classes[estado] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
}

const applyMovement = (movement) => {
    if (confirm('¿Está seguro que desea aplicar este movimiento? Esta acción afectará el stock de los productos.')) {
        router.post(route('inventory.movements.apply', movement.id))
    }
}

const revertMovement = (movement) => {
    if (confirm('¿Está seguro que desea revertir este movimiento? Esto revertirá los cambios de stock realizados.')) {
        router.post(route('inventory.movements.revert', movement.id))
    }
}

const deleteMovement = (movement) => {
    if (confirm('¿Está seguro que desea eliminar este movimiento? Esta acción no se puede deshacer.')) {
        router.delete(route('inventory.movements.destroy', movement.id))
    }
}
</script>