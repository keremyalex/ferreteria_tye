<template>
    <SidebarLayout title="Nueva Compra">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Nueva Compra
                </h2>
                <Link :href="route('purchases.index')" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                    ← Volver a compras
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
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

                            <!-- Productos -->
                            <div class="mb-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Productos</h3>
                                    <button
                                        type="button"
                                        @click="addProduct"
                                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm"
                                    >
                                        Agregar Producto
                                    </button>
                                </div>

                                <div v-if="form.productos.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    No hay productos agregados. Haga clic en "Agregar Producto" para comenzar.
                                </div>

                                <div v-for="(producto, index) in form.productos" :key="index" class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 mb-4">
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Producto</label>
                                            <select
                                                v-model="producto.product_id"
                                                class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                                                :class="{ 'border-red-500': form.errors[`productos.${index}.product_id`] }"
                                                @change="updateProductInfo(index)"
                                            >
                                                <option value="">Seleccione un producto</option>
                                                <option v-for="product in products" :key="product.id" :value="product.id">
                                                    {{ product.nombre }} - {{ product.category.nombre }} ({{ product.measurement.nombre }})
                                                </option>
                                            </select>
                                            <div v-if="form.errors[`productos.${index}.product_id`]" class="mt-1 text-sm text-red-600">
                                                {{ form.errors[`productos.${index}.product_id`] }}
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cantidad</label>
                                            <input
                                                v-model.number="producto.cantidad"
                                                type="number"
                                                min="1"
                                                step="1"
                                                class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                                                :class="{ 'border-red-500': form.errors[`productos.${index}.cantidad`] }"
                                                @input="calculateSubtotal(index)"
                                            >
                                            <div v-if="form.errors[`productos.${index}.cantidad`]" class="mt-1 text-sm text-red-600">
                                                {{ form.errors[`productos.${index}.cantidad`] }}
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Precio Unit.</label>
                                            <input
                                                v-model.number="producto.precio"
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                class="mt-1 block w-full border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                                                :class="{ 'border-red-500': form.errors[`productos.${index}.precio`] }"
                                                @input="calculateSubtotal(index)"
                                            >
                                            <div v-if="form.errors[`productos.${index}.precio`]" class="mt-1 text-sm text-red-600">
                                                {{ form.errors[`productos.${index}.precio`] }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center mt-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            Subtotal: ${{ formatCurrency(producto.subtotal || 0) }}
                                        </div>
                                        <button
                                            type="button"
                                            @click="removeProduct(index)"
                                            class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-sm"
                                        >
                                            Eliminar
                                        </button>
                                    </div>
                                </div>

                                <!-- Total general -->
                                <div v-if="form.productos.length > 0" class="border-t border-gray-300 dark:border-gray-600 pt-4">
                                    <div class="flex justify-end">
                                        <div class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                            Total: ${{ formatCurrency(totalAmount) }}
                                        </div>
                                    </div>
                                </div>

                                <div v-if="form.errors.productos" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.productos }}
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="flex items-center justify-end space-x-4">
                                <Link :href="route('purchases.index')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Cancelar
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                                >
                                    <span v-if="form.processing">Guardando...</span>
                                    <span v-else>Guardar Compra</span>
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
import { ref, computed, onMounted } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'

export default {
    components: {
        SidebarLayout,
        Link,
    },

    props: {
        suppliers: Array,
        products: Array,
    },

    setup(props) {
        const form = useForm({
            fecha: new Date().toISOString().split('T')[0],
            hora: new Date().toTimeString().split(' ')[0].substring(0, 5),
            supplier_id: '',
            observaciones: '',
            productos: [],
        })

        const addProduct = () => {
            form.productos.push({
                product_id: '',
                cantidad: 1,
                precio: 0,
                subtotal: 0,
            })
        }

        const removeProduct = (index) => {
            form.productos.splice(index, 1)
        }

        const updateProductInfo = (index) => {
            const product = props.products.find(p => p.id == form.productos[index].product_id)
            if (product) {
                // Podrías establecer un precio por defecto si está disponible
                calculateSubtotal(index)
            }
        }

        const calculateSubtotal = (index) => {
            const producto = form.productos[index]
            producto.subtotal = (producto.cantidad || 0) * (producto.precio || 0)
        }

        const totalAmount = computed(() => {
            return form.productos.reduce((total, producto) => total + (producto.subtotal || 0), 0)
        })

        const formatCurrency = (amount) => {
            return new Intl.NumberFormat('es-ES', { 
                minimumFractionDigits: 2,
                maximumFractionDigits: 2 
            }).format(amount || 0)
        }

        const submit = () => {
            form.post(route('purchases.store'))
        }

        onMounted(() => {
            addProduct() // Agregar un producto por defecto
        })

        return {
            form,
            addProduct,
            removeProduct,
            updateProductInfo,
            calculateSubtotal,
            totalAmount,
            formatCurrency,
            submit,
        }
    }
}
</script>