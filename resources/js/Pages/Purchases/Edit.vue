<template>
    <SidebarLayout title="Editar Compra">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Editar Compra #{{ purchase.nro }}
                </h2>
                <div class="flex space-x-3">
                    <Link :href="route('purchases.index')" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                        ← Volver a compras
                    </Link>
                    <Link :href="route('purchases.show', purchase.id)" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">
                        Ver detalle
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <!-- Mensaje de información -->
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-md p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                        Información importante
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                        <p>Solo se pueden editar los datos básicos de la compra. Para modificar los productos, debe eliminar la compra y crear una nueva.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form @submit.prevent="submit">
                            <!-- Información básica -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div>
                                    <label for="fecha" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha</label>
                                    <input
                                        v-model="form.fecha"
                                        type="date"
                                        id="fecha"
                                        class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                                        :class="{ 'border-red-500': form.errors.fecha }"
                                    >
                                    <div v-if="form.errors.fecha" class="mt-2 text-sm text-red-600">
                                        {{ form.errors.fecha }}
                                    </div>
                                </div>

                                <div>
                                    <label for="hora" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hora</label>
                                    <input
                                        v-model="form.hora"
                                        type="time"
                                        id="hora"
                                        class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                                        :class="{ 'border-red-500': form.errors.hora }"
                                    >
                                    <div v-if="form.errors.hora" class="mt-2 text-sm text-red-600">
                                        {{ form.errors.hora }}
                                    </div>
                                </div>

                                <div>
                                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Proveedor</label>
                                    <select
                                        v-model="form.supplier_id"
                                        id="supplier_id"
                                        class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                                        :class="{ 'border-red-500': form.errors.supplier_id }"
                                    >
                                        <option value="">Seleccione un proveedor</option>
                                        <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                            {{ supplier.nombre_empresa }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.supplier_id" class="mt-2 text-sm text-red-600">
                                        {{ form.errors.supplier_id }}
                                    </div>
                                </div>
                            </div>

                            <!-- Observaciones -->
                            <div class="mb-6">
                                <label for="observaciones" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Observaciones</label>
                                <textarea
                                    v-model="form.observaciones"
                                    id="observaciones"
                                    rows="3"
                                    class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                                    :class="{ 'border-red-500': form.errors.observaciones }"
                                    placeholder="Observaciones opcionales..."
                                ></textarea>
                                <div v-if="form.errors.observaciones" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.observaciones }}
                                </div>
                            </div>

                            <!-- Productos (solo lectura) -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Productos de la Compra</h3>
                                
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full">
                                            <thead>
                                                <tr class="border-b border-gray-300 dark:border-gray-600">
                                                    <th class="text-left py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Producto</th>
                                                    <th class="text-right py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Cantidad</th>
                                                    <th class="text-right py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Precio Unit.</th>
                                                    <th class="text-right py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="detail in purchase.purchase_details" :key="detail.id" class="border-b border-gray-200 dark:border-gray-600">
                                                    <td class="py-3 text-sm text-gray-900 dark:text-gray-100">
                                                        {{ detail.product.nombre }}
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ detail.product.category.nombre }} - {{ detail.product.measurement.nombre }}
                                                        </div>
                                                    </td>
                                                    <td class="py-3 text-sm text-gray-900 dark:text-gray-100 text-right">{{ detail.cantidad }}</td>
                                                    <td class="py-3 text-sm text-gray-900 dark:text-gray-100 text-right">${{ formatCurrency(detail.precio) }}</td>
                                                    <td class="py-3 text-sm font-medium text-gray-900 dark:text-gray-100 text-right">${{ formatCurrency(detail.cantidad * detail.precio) }}</td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr class="border-t-2 border-gray-400 dark:border-gray-500">
                                                    <td colspan="3" class="py-3 text-right font-medium text-gray-900 dark:text-gray-100">Total:</td>
                                                    <td class="py-3 text-right text-lg font-bold text-green-600 dark:text-green-400">${{ formatCurrency(purchase.monto_total) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="flex items-center justify-end space-x-4">
                                <Link :href="route('purchases.show', purchase.id)" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Cancelar
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                                >
                                    <span v-if="form.processing">Guardando...</span>
                                    <span v-else>Actualizar Compra</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
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
        suppliers: Array,
        products: Array,
    },

    setup(props) {
        const form = useForm({
            fecha: props.purchase.fecha,
            hora: props.purchase.hora,
            supplier_id: props.purchase.supplier_id,
            observaciones: props.purchase.observaciones,
        })

        const formatCurrency = (amount) => {
            return new Intl.NumberFormat('es-ES', { 
                minimumFractionDigits: 2,
                maximumFractionDigits: 2 
            }).format(amount || 0)
        }

        const submit = () => {
            form.put(route('purchases.update', props.purchase.id))
        }

        return {
            form,
            formatCurrency,
            submit,
        }
    }
}
</script>