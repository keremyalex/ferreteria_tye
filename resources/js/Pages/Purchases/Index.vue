<template>
    <SidebarLayout title="Gestión de Compras">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Gestión de Compras
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Estadísticas -->
                <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-4">
                    <div class="p-6 overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-500 bg-opacity-75 rounded-full">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="font-semibold text-gray-800 dark:text-gray-200">Total Compras</h2>
                                <p class="text-gray-600 dark:text-gray-400">{{ stats.total_purchases }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-500 bg-opacity-75 rounded-full">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="font-semibold text-gray-800 dark:text-gray-200">Monto Total</h2>
                                <p class="text-gray-600 dark:text-gray-400">Bs {{ formatCurrency(stats.total_amount) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-500 bg-opacity-75 rounded-full">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="font-semibold text-gray-800 dark:text-gray-200">Este Mes</h2>
                                <p class="text-gray-600 dark:text-gray-400">{{ stats.this_month_purchases }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-500 bg-opacity-75 rounded-full">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="font-semibold text-gray-800 dark:text-gray-200">Monto Mes</h2>
                                <p class="text-gray-600 dark:text-gray-400">Bs {{ formatCurrency(stats.this_month_amount) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros y Nueva Compra -->
                <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                        <div class="flex flex-col justify-between sm:flex-row">
                            <div class="flex flex-col mb-4 space-y-2 sm:flex-row sm:space-y-0 sm:space-x-4 sm:mb-0">
                                <input
                                    v-model="form.search"
                                    type="text"
                                    placeholder="Buscar compras..."
                                    class="px-4 py-2 text-gray-900 bg-white border border-gray-300 rounded-md dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                    @keyup.enter="search"
                                >
                                
                                <select
                                    v-model="form.supplier"
                                    class="px-4 py-2 text-gray-900 bg-white border border-gray-300 rounded-md dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                    @change="search"
                                >
                                    <option value="">Todos los proveedores</option>
                                    <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                        {{ supplier.nombre_empresa }}
                                    </option>
                                </select>

                                <input
                                    v-model="form.date_start"
                                    type="date"
                                    class="px-4 py-2 text-gray-900 bg-white border border-gray-300 rounded-md dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                    @change="search"
                                >

                                <input
                                    v-model="form.date_end"
                                    type="date"
                                    class="px-4 py-2 text-gray-900 bg-white border border-gray-300 rounded-md dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                    @change="search"
                                >

                                <button
                                    @click="clearFilters"
                                    class="px-4 py-2 text-gray-700 bg-gray-300 rounded-md dark:bg-gray-600 dark:text-gray-300 hover:bg-gray-400 dark:hover:bg-gray-500"
                                >
                                    Limpiar
                                </button>
                            </div>

                            <div v-if="$page.props.auth?.user?.permissions?.includes('create.purchases')" class="flex space-x-2">
                                <Link 
                                    :href="route('purchases.create')" 
                                    class="px-4 py-2 font-bold text-white bg-blue-600 rounded hover:bg-blue-700"
                                >
                                    Nueva Compra
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de compras -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase cursor-pointer dark:text-gray-300" @click="sort('nro')">
                                        Número
                                        <span v-if="filters.sort === 'nro'">
                                            <span v-if="filters.direction === 'asc'">↑</span>
                                            <span v-else>↓</span>
                                        </span>
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase cursor-pointer dark:text-gray-300" @click="sort('fecha')">
                                        Fecha
                                        <span v-if="filters.sort === 'fecha'">
                                            <span v-if="filters.direction === 'asc'">↑</span>
                                            <span v-else>↓</span>
                                        </span>
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        Proveedor
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        Estado
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase cursor-pointer dark:text-gray-300" @click="sort('monto_total')">
                                        Monto Total
                                        <span v-if="filters.sort === 'monto_total'">
                                            <span v-if="filters.direction === 'asc'">↑</span>
                                            <span v-else>↓</span>
                                        </span>
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        Productos
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                <tr v-for="purchase in purchases.data" :key="purchase.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ purchase.nro }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ formatDate(purchase.fecha) }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ purchase.hora }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ purchase.supplier.nombre_empresa }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="getEstadoBadgeClass(purchase.estado)"
                                              class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                            {{ getEstadoLabel(purchase.estado) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-green-600 dark:text-green-400">
                                            Bs {{ formatCurrency(purchase.monto_total) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ purchase.purchase_details.length }} producto(s)
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 space-x-2 text-sm font-medium whitespace-nowrap">
                                        <Link 
                                            :href="route('purchases.show', purchase.id)" 
                                            class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300"
                                        >
                                            Ver
                                        </Link>
                                        <Link 
                                            :href="route('purchases.edit', purchase.id)" 
                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300"
                                        >
                                            Editar
                                        </Link>
                                        <button
                                            @click="confirmDelete(purchase)"
                                            class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                        >
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="px-4 py-3 bg-white border-t border-gray-200 dark:bg-gray-800 dark:border-gray-700 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex justify-between flex-1 sm:hidden">
                                <Link 
                                    v-if="purchases.prev_page_url"
                                    :href="purchases.prev_page_url" 
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                >
                                    Anterior
                                </Link>
                                <Link 
                                    v-if="purchases.next_page_url"
                                    :href="purchases.next_page_url" 
                                    class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                                >
                                    Siguiente
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        Mostrando <span class="font-medium">{{ purchases.from }}</span>
                                        a <span class="font-medium">{{ purchases.to }}</span>
                                        de <span class="font-medium">{{ purchases.total }}</span> resultados
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex -space-x-px rounded-md shadow-sm">
                                        <Link 
                                            v-if="purchases.prev_page_url"
                                            :href="purchases.prev_page_url"
                                            class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50"
                                        >
                                            <span class="sr-only">Anterior</span>
                                            ←
                                        </Link>
                                        
                                        <template v-for="link in purchases.links" :key="link.label">
                                            <Link
                                                v-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                                                :href="link.url"
                                                :class="[
                                                    link.active 
                                                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' 
                                                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                                                ]"
                                                v-html="link.label"
                                            />
                                        </template>

                                        <Link 
                                            v-if="purchases.next_page_url"
                                            :href="purchases.next_page_url"
                                            class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50"
                                        >
                                            <span class="sr-only">Siguiente</span>
                                            →
                                        </Link>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de confirmación de eliminación -->
        <ConfirmationModal :show="showingDeleteModal" @close="closeDeleteModal">
            <template #title>
                Eliminar Compra
            </template>

            <template #content>
                ¿Está seguro que desea eliminar esta compra? Esta acción no se puede deshacer.
            </template>

            <template #footer>
                <SecondaryButton @click="closeDeleteModal">
                    Cancelar
                </SecondaryButton>

                <DangerButton class="ml-3" @click="deletePurchase" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Eliminar
                </DangerButton>
            </template>
        </ConfirmationModal>
    </SidebarLayout>
</template>

<script>
import { ref, reactive, watch } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'
import DangerButton from '@/Components/DangerButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

export default {
    components: {
        SidebarLayout,
        Link,
        ConfirmationModal,
        DangerButton,
        SecondaryButton
    },

    props: {
        purchases: Object,
        suppliers: Array,
        filters: Object,
        stats: Object,
    },

    setup(props) {
        const showingDeleteModal = ref(false)
        const purchaseToDelete = ref(null)
        
        const form = useForm({
            search: props.filters.search,
            supplier: props.filters.supplier,
            date_start: props.filters.date_start,
            date_end: props.filters.date_end,
            sort: props.filters.sort,
            direction: props.filters.direction,
        })

        const deleteForm = useForm({})

        // Búsqueda con debounce
        let searchTimeout = null
        watch(() => form.search, () => {
            if (searchTimeout) clearTimeout(searchTimeout)
            searchTimeout = setTimeout(search, 500)
        })

        const search = () => {
            form.get(route('purchases.index'), {
                preserveState: true,
                replace: true,
            })
        }

        const sort = (field) => {
            if (form.sort === field) {
                form.direction = form.direction === 'asc' ? 'desc' : 'asc'
            } else {
                form.sort = field
                form.direction = 'asc'
            }
            search()
        }

        const clearFilters = () => {
            form.search = ''
            form.supplier = ''
            form.date_start = ''
            form.date_end = ''
            search()
        }

        const confirmDelete = (purchase) => {
            purchaseToDelete.value = purchase
            showingDeleteModal.value = true
        }

        const deletePurchase = () => {
            if (purchaseToDelete.value) {
                deleteForm.delete(route('purchases.destroy', purchaseToDelete.value.id), {
                    onSuccess: () => closeDeleteModal(),
                    onFinish: () => deleteForm.reset(),
                })
            }
        }

        const closeDeleteModal = () => {
            showingDeleteModal.value = false
            purchaseToDelete.value = null
        }

        const formatDate = (dateString) => {
            const options = { year: 'numeric', month: '2-digit', day: '2-digit' }
            return new Date(dateString).toLocaleDateString('es-ES', options)
        }

        const formatCurrency = (amount) => {
            return new Intl.NumberFormat('es-ES', { 
                minimumFractionDigits: 2,
                maximumFractionDigits: 2 
            }).format(amount || 0)
        }

        const getEstadoLabel = (estado) => {
            const labels = {
                'pendiente': 'Pendiente',
                'recibida': 'Recibida',
                'parcial': 'Parcial', 
                'cancelada': 'Cancelada'
            }
            return labels[estado] || 'Desconocido'
        }

        const getEstadoBadgeClass = (estado) => {
            const classes = {
                'pendiente': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                'recibida': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                'parcial': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                'cancelada': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
            }
            return classes[estado] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
        }

        return {
            form,
            showingDeleteModal,
            search,
            sort,
            clearFilters,
            confirmDelete,
            deletePurchase,
            closeDeleteModal,
            formatDate,
            formatCurrency,
            getEstadoLabel,
            getEstadoBadgeClass,
        }
    }
}
</script>