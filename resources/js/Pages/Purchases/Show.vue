<template>
    <SidebarLayout title="Detalle de Compra">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Detalle de Compra #{{ purchase.nro }}
                </h2>
                <div class="flex space-x-3">
                    <Link :href="route('purchases.index')" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                        ← Volver a compras
                    </Link>
                    <Link 
                        v-if="purchase.estado === 'pendiente'"
                        :href="route('purchases.recibir', purchase.id)" 
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm"
                    >
                        🚚 Recibir Mercancía
                    </Link>
                    <Link 
                        v-if="$page.props.user?.permissions?.includes('edit.purchases')"
                        :href="route('purchases.edit', purchase.id)" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm"
                    >
                        Editar
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Información de la compra -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">Información de la Compra</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Número</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ purchase.nro }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha</dt>
                                <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ formatDate(purchase.fecha) }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Hora</dt>
                                <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ purchase.hora }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado</dt>
                                <dd class="mt-1">
                                    <span :class="getEstadoBadgeClass(purchase.estado)"
                                          class="inline-flex px-3 py-1 text-sm font-semibold rounded-full">
                                        {{ getEstadoLabel(purchase.estado) }}
                                    </span>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Monto Total</dt>
                                <dd class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">${{ formatCurrency(purchase.monto_total) }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información del proveedor -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">Información del Proveedor</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Empresa</dt>
                                <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ purchase.supplier.nombre_empresa }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Contacto</dt>
                                <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ purchase.supplier.nombre_persona || 'N/A' }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Teléfono</dt>
                                <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ purchase.supplier.telefono || 'N/A' }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ purchase.supplier.correo || 'N/A' }}</dd>
                            </div>
                            
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Dirección</dt>
                                <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ purchase.supplier.direccion || 'N/A' }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detalle de productos -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">Productos Comprados</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Producto
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Categoría
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Unidad
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Cantidad
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Precio Unit.
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Subtotal
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="detail in purchase.purchase_details" :key="detail.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ detail.product.nombre }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ detail.product.descripcion }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ detail.product.category.nombre }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ detail.product.measurement.nombre }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                            {{ detail.cantidad }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                            ${{ formatCurrency(detail.precio) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 text-right">
                                            ${{ formatCurrency(detail.cantidad * detail.precio) }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-right text-lg font-medium text-gray-900 dark:text-gray-100">
                                            Total:
                                        </td>
                                        <td class="px-6 py-4 text-right text-xl font-bold text-green-600 dark:text-green-400">
                                            ${{ formatCurrency(purchase.monto_total) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div v-if="purchase.observaciones" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Observaciones</h3>
                        <div class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ purchase.observaciones }}</div>
                    </div>
                </div>

                <!-- Resumen estadístico -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-500 bg-opacity-75">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="font-semibold text-gray-800 dark:text-gray-200">Total Productos</h4>
                                <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ purchase.purchase_details.length }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-500 bg-opacity-75">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="font-semibold text-gray-800 dark:text-gray-200">Cantidad Total</h4>
                                <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">
                                    {{ purchase.purchase_details.reduce((sum, detail) => sum + detail.cantidad, 0) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-500 bg-opacity-75">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="font-semibold text-gray-800 dark:text-gray-200">Precio Promedio</h4>
                                <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">
                                    ${{ formatCurrency(purchase.purchase_details.length > 0 ? 
                                        purchase.monto_total / purchase.purchase_details.reduce((sum, detail) => sum + detail.cantidad, 0) : 0) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>

<script>
import { Link } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'

export default {
    components: {
        SidebarLayout,
        Link,
    },

    props: {
        purchase: Object,
    },

    setup() {
        const formatDate = (dateString) => {
            const options = { year: 'numeric', month: 'long', day: 'numeric' }
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
            formatDate,
            formatCurrency,
            getEstadoLabel,
            getEstadoBadgeClass
        }
    }
}
</script>