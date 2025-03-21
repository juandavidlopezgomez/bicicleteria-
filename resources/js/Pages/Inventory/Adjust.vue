<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Ajuste de Inventario
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <form @submit.prevent="submitForm">
              <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Ajustar Stock de Producto</h3>
                <p class="text-sm text-gray-600 mb-4">
                  Use este formulario para corregir discrepancias en el inventario. Cada ajuste quedará registrado en el historial de movimientos.
                </p>
              </div>

              <!-- Seleccionar Producto -->
              <div class="mb-6">
                <label for="product_id" class="block text-sm font-medium text-gray-700">Seleccionar Producto</label>
                <div class="mt-1">
                  <select
                    id="product_id"
                    v-model="form.product_id"
                    @change="productSelected"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                    required
                  >
                    <option value="" disabled>Seleccione un producto</option>
                    <option v-for="product in products" :key="product.id" :value="product.id">
                      {{ product.name }} - Stock actual: {{ product.stock }} - SKU: {{ product.sku }}
                    </option>
                  </select>
                </div>
                <div v-if="form.errors.product_id" class="text-red-500 text-sm mt-1">{{ form.errors.product_id }}</div>
              </div>

              <!-- Información del Producto Seleccionado -->
              <div v-if="selectedProduct" class="mb-6 p-4 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <h4 class="text-sm font-medium text-gray-500">Información del Producto</h4>
                    <p class="font-medium">{{ selectedProduct.name }}</p>
                    <p class="text-sm text-gray-600">SKU: {{ selectedProduct.sku }}</p>
                    <p class="text-sm text-gray-600">Categoría: {{ selectedProduct.category ? selectedProduct.category.name : 'N/A' }}</p>
                  </div>
                  <div>
                    <h4 class="text-sm font-medium text-gray-500">Stock Actual</h4>
                    <p class="font-medium text-2xl">{{ selectedProduct.stock }}</p>
                    <p class="text-sm" :class="isLowStock ? 'text-red-600' : 'text-gray-600'">
                      Stock mínimo: {{ selectedProduct.min_stock }}
                      <span v-if="isLowStock" class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                        Stock bajo
                      </span>
                    </p>
                  </div>
                  <div>
                    <h4 class="text-sm font-medium text-gray-500">Precio</h4>
                    <p class="font-medium">Compra: ${{ selectedProduct.purchase_price }}</p>
                    <p class="font-medium">Venta: ${{ selectedProduct.sale_price }}</p>
                  </div>
                </div>
              </div>

              <!-- Nuevo Stock -->
              <div class="mb-6">
                <label for="new_stock" class="block text-sm font-medium text-gray-700">Nuevo Stock</label>
                <div class="mt-1">
                  <input
                    id="new_stock"
                    type="number"
                    v-model="form.new_stock"
                    min="0"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    required
                  />
                </div>
                <div v-if="form.errors.new_stock" class="text-red-500 text-sm mt-1">{{ form.errors.new_stock }}</div>
                <p v-if="selectedProduct && form.new_stock !== ''" class="mt-2 text-sm text-gray-600">
                  Este ajuste 
                  <span v-if="form.new_stock > selectedProduct.stock" class="font-medium text-green-600">
                    aumentará
                  </span>
                  <span v-else-if="form.new_stock < selectedProduct.stock" class="font-medium text-red-600">
                    disminuirá
                  </span>
                  <span v-else class="font-medium text-gray-600">
                    mantendrá
                  </span>
                  el stock en 
                  <span v-if="form.new_stock !== selectedProduct.stock" class="font-medium">
                    {{ Math.abs(form.new_stock - selectedProduct.stock) }} 
                    {{ form.new_stock > selectedProduct.stock ? 'unidades más' : 'unidades menos' }}
                  </span>
                  <span v-else class="font-medium">
                    el mismo nivel
                  </span>
                </p>
              </div>

              <!-- Motivo del Ajuste -->
              <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700">Motivo del Ajuste</label>
                <div class="mt-1">
                  <textarea
                    id="notes"
                    v-model="form.notes"
                    rows="3"
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md"
                    placeholder="Explique brevemente por qué es necesario este ajuste..."
                    required
                  ></textarea>
                </div>
                <div v-if="form.errors.notes" class="text-red-500 text-sm mt-1">{{ form.errors.notes }}</div>
              </div>

              <!-- Botones de Acción -->
              <div class="flex justify-end space-x-3">
                <Link
                  :href="route('inventory.index')"
                  class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Cancelar
                </Link>
                <button
                  type="submit"
                  class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  :disabled="form.processing"
                >
                  {{ form.processing ? 'Procesando...' : 'Guardar Ajuste' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/inertia-vue3';

export default {
  components: {
    AuthenticatedLayout,
    Link
  },
  props: {
    products: Array
  },
  setup(props) {
    const selectedProduct = ref(null);
    const isLowStock = computed(() => {
      if (!selectedProduct.value) return false;
      return selectedProduct.value.stock <= selectedProduct.value.min_stock;
    });

    const form = useForm({
      product_id: '',
      new_stock: '',
      notes: ''
    });

    const productSelected = () => {
      const product = props.products.find(p => p.id === form.product_id);
      if (product) {
        selectedProduct.value = product;
        form.new_stock = product.stock;
      } else {
        selectedProduct.value = null;
        form.new_stock = '';
      }
    };

    const submitForm = () => {
      form.post(route('inventory.process-adjustment'), {
        onSuccess: () => {
          form.reset();
          selectedProduct.value = null;
        }
      });
    };

    return {
      selectedProduct,
      isLowStock,
      form,
      productSelected,
      submitForm
    };
  }
};
</script>