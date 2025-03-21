<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Movimientos de Inventario
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Filtros -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
              <h3 class="text-lg font-medium text-gray-700 mb-4">Filtros</h3>
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Producto</label>
                  <select 
                    v-model="filters.product_id"
                    @change="applyFilters"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="">Todos los productos</option>
                    <option v-for="product in products" :key="product.id" :value="product.id">
                      {{ product.name }}
                    </option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de movimiento</label>
                  <select 
                    v-model="filters.type"
                    @change="applyFilters"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="">Todos los tipos</option>
                    <option value="entry">Entrada</option>
                    <option value="exit">Salida</option>
                    <option value="adjustment">Ajuste</option>
                    <option value="sale">Venta</option>
                    <option value="return">Devolución</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Fecha desde</label>
                  <input 
                    type="date" 
                    v-model="filters.date_from"
                    @change="applyFilters"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Fecha hasta</label>
                  <input 
                    type="date" 
                    v-model="filters.date_to"
                    @change="applyFilters"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  />
                </div>
              </div>
              <div class="mt-4 flex justify-end">
                <button 
                  @click="resetFilters"
                  class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                  Restablecer filtros
                </button>
              </div>
            </div>

            <!-- Tabla de movimientos -->
            <div class="overflow-x-auto bg-white rounded-lg shadow">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Fecha y Hora
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Producto
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Tipo
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Cantidad
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Stock anterior
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Stock actual
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Usuario
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Notas
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="movement in movements.data" :key="movement.id">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(movement.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ movement.product.name }}
                      </div>
                      <div class="text-xs text-gray-500">
                        SKU: {{ movement.product.sku }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="getTypeClass(movement.type)">
                        {{ getTypeLabel(movement.type) }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      <span :class="movement.quantity > 0 ? 'text-green-600' : 'text-red-600'">
                        {{ formatQuantity(movement.quantity) }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ movement.previous_stock }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ movement.current_stock }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ movement.user.name }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                      {{ movement.notes || 'Sin notas' }}
                    </td>
                  </tr>
                  <tr v-if="movements.data.length === 0">
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                      No se encontraron movimientos
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
              <Pagination :links="movements.links" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/inertia-vue3';
import Pagination from '@/Components/Pagination.vue';
import { Inertia } from '@inertiajs/inertia';
import { reactive } from 'vue';

export default {
  components: {
    AuthenticatedLayout,
    Link,
    Pagination
  },
  props: {
    movements: Object,
    products: Array,
    filters: Object
  },
  setup(props) {
    const filters = reactive({
      product_id: props.filters.product_id || '',
      type: props.filters.type || '',
      date_from: props.filters.date_from || '',
      date_to: props.filters.date_to || ''
    });

    function applyFilters() {
      Inertia.get(route('inventory.movements'), {
        product_id: filters.product_id,
        type: filters.type,
        date_from: filters.date_from,
        date_to: filters.date_to
      }, { preserveState: true });
    }

    function resetFilters() {
      filters.product_id = '';
      filters.type = '';
      filters.date_from = '';
      filters.date_to = '';
      applyFilters();
    }

    function formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleString();
    }

    function formatQuantity(quantity) {
      return quantity > 0 ? `+${quantity}` : quantity;
    }

    function getTypeLabel(type) {
      const types = {
        'entry': 'Entrada',
        'exit': 'Salida',
        'adjustment': 'Ajuste',
        'sale': 'Venta',
        'return': 'Devolución'
      };
      return types[type] || type;
    }

    function getTypeClass(type) {
      const classes = {
        'entry': 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800',
        'exit': 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800',
        'adjustment': 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800',
        'sale': 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800',
        'return': 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800'
      };
      return classes[type] || 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800';
    }

    return {
      filters,
      applyFilters,
      resetFilters,
      formatDate,
      formatQuantity,
      getTypeLabel,
      getTypeClass
    };
  }
}
</script>