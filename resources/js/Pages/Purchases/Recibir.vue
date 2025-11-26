<template>
    <SidebarLayout title="Recibir Mercancía">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    🚚 Recibir Mercancía - Compra #{{ purchase.nro }}
                </h2>
                <div class="flex space-x-3">
                    <Link :href="route('purchases.show', purchase.id)" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                        ← Volver al detalle
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Información de la compra -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Información de la Compra</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Número</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ purchase.nro }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha</dt>
                                <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ formatDate(purchase.fecha) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Proveedor</dt>
                                <dd class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ purchase.supplier.nombre_empresa }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Monto Total</dt>
                                <dd class="mt-1 text-lg font-bold text-green-600 dark:text-green-400">${{ formatCurrency(purchase.monto_total) }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario de recepción -->
                <form @submit.prevent="submit" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Productos a Recibir</h3>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Marque las cantidades que realmente recibió
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Producto
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Cantidad Pedida
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Cantidad Recibida
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Precio Unitario
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Observaciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="(detail, index) in purchase.purchase_details" :key="detail.id" 
                                        :class="{ 'bg-red-50 dark:bg-red-900/20': form.items[index]?.cantidad_recibida > detail.cantidad }">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                            {{ detail.product.nombre.charAt(0).toUpperCase() }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ detail.product.nombre }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ detail.product.category?.nombre }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ detail.cantidad }} {{ detail.product.measurement?.simbolo }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input
                                                type="number"
                                                v-model.number="form.items[index].cantidad_recibida"
                                                :min="0"
                                                :max="detail.cantidad * 1.1"
                                                class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
                                                :class="{ 'border-red-500': form.items[index]?.cantidad_recibida > detail.cantidad }"
                                            />
                                            <div v-if="form.items[index]?.cantidad_recibida > detail.cantidad" 
                                                 class="text-xs text-red-600 dark:text-red-400 mt-1">
                                                ⚠️ Cantidad mayor a la pedida
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                ${{ formatCurrency(detail.precio) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <input
                                                type="text"
                                                v-model="form.items[index].observaciones"
                                                placeholder="Opcional..."
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 dark:placeholder-gray-400"
                                            />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Resumen -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-center">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Productos Pedidos</div>
                                <div class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ getTotalPedidos() }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Productos Recibidos</div>
                                <div class="text-lg font-bold text-green-600 dark:text-green-400">{{ getTotalRecibidos() }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Valor Recibido</div>
                                <div class="text-lg font-bold text-purple-600 dark:text-purple-400">${{ formatCurrency(getValorRecibido()) }}</div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="mt-6 flex justify-end space-x-4">
                            <Link 
                                :href="route('purchases.show', purchase.id)"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="button"
                                @click="autoCompletar"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600"
                            >
                                📋 Auto-completar
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing || !tieneProductosRecibidos()"
                                class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-green-500 dark:hover:bg-green-600"
                            >
                                <span v-if="form.processing">Procesando...</span>
                                <span v-else>✅ Confirmar Recepción</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </SidebarLayout>
</template>

<script>
import { useForm, Link } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'

export default {
    components: {
        SidebarLayout,
        Link,
    },

    props: {
        purchase: Object,
    },

    setup(props) {
        // Inicializar formulario con los items de la compra
        const form = useForm({
            items: props.purchase.purchase_details.map(detail => ({
                id: detail.id,
                cantidad_recibida: detail.cantidad, // Por defecto, cantidad pedida
                observaciones: ''
            }))
        })

        const submit = () => {
            form.post(route('purchases.procesarRecepcion', props.purchase.id))
        }

        const autoCompletar = () => {
            form.items.forEach((item, index) => {
                item.cantidad_recibida = props.purchase.purchase_details[index].cantidad
                item.observaciones = ''
            })
        }

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

        const getTotalPedidos = () => {
            return props.purchase.purchase_details.reduce((sum, detail) => sum + detail.cantidad, 0)
        }

        const getTotalRecibidos = () => {
            return form.items.reduce((sum, item) => sum + (item.cantidad_recibida || 0), 0)
        }

        const getValorRecibido = () => {
            return form.items.reduce((sum, item, index) => {
                const cantidad = item.cantidad_recibida || 0
                const precio = props.purchase.purchase_details[index].precio
                return sum + (cantidad * precio)
            }, 0)
        }

        const tieneProductosRecibidos = () => {
            return form.items.some(item => (item.cantidad_recibida || 0) > 0)
        }

        return {
            form,
            submit,
            autoCompletar,
            formatDate,
            formatCurrency,
            getTotalPedidos,
            getTotalRecibidos,
            getValorRecibido,
            tieneProductosRecibidos
        }
    }
}
</script>