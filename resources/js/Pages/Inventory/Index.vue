<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Panel de Inventario
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Tarjetas de resumen -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 border-b border-gray-200">
              <div class="flex justify-between items-center">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">Productos con Stock Bajo</h3>
                  <p class="text-3xl font-bold mt-2 text-red-600">{{ totalLowStock }}</p>
                </div>
                <div class="p-3 rounded-full bg-red-100">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                </div>
              </div>
              <div class="mt-4">
                <Link :href="route('inventory.movements')" class="text-sm text-blue-600 hover:text-blue-800">
                  Ver movimientos de inventario →
                </Link>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 border-b border-gray-200">
              <div class="flex justify-between items-center">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">Productos sin Stock</h3>
                  <p class="text-3xl font-bold mt-2 text-orange-600">{{ outOfStockCount }}</p>
                </div>
                <div class="p-3 rounded-full bg-orange-100">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                  </svg>
                </div>
              </div>
              <div class="mt-4">
                <Link :href="route('products.index', { stock_status: 'out' })" class="text-sm text-blue-600 hover:text-blue-800">
                  Ver productos sin stock →
                </Link>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 border-b border-gray-200">
              <div class="flex justify-between items-center">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">Gestión de Inventario</h3>
                  <p class="text-sm text-gray-600 mt-2">Registra entradas y ajustes</p>
                </div>
                <div class="p-3 rounded-full bg-green-100">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                </div>
              </div>
              <div class="mt-4 flex space-x-4">
                <Link :href="route('inventory.entry')" class="text-sm text-blue-600 hover:text-blue-800">
                  Registrar entrada →
                </Link>
                <Link :href="route('inventory.adjust')" class="text-sm text-blue-600 hover:text-blue-800">
                  Ajustar inventario →
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabla de productos con stock bajo -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Productos con Stock Bajo</h3>
            
            <div class="overflow-x-auto bg-white rounded-lg shadow mb-6">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Producto
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Categoría
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Stock Actual
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Stock Mínimo
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Acciones
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="product in lowStockProducts" :key="product.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                          <img 
                            v-if="product.image" 
                            :src="'/storage/' + product.image" 
                            class="h-10 w-10 rounded-full object-cover"
                            alt="Producto" 
                          />
                          <div v-else class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-500 text-xs">Sin img</span>
                          </div>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900">
                            {{ product.name }}
                          </div>
                          <div class="text-sm text-gray-500">
                            SKU: {{ product.sku }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ product.category.name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="[
                        'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                        product.stock === 0 ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800'
                      ]">
                        {{ product.stock }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ product.min_stock }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <div class="flex space-x-2">
                        <Link 
                          v-if="$page.props.auth.user.permissions.includes('inventory.entry')"
                          :href="route('inventory.entry')" 
                          class="text-green-600 hover:text-green-900"
                        >
                          Añadir stock
                        </Link>
                        <Link 
                          v-if="$page.props.auth.user.permissions.includes('products.edit')"
                          :href="route('products.edit', product.id)" 
                          class="text-blue-600 hover:text-blue-900"
                        >
                          Editar
                        </Link>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="lowStockProducts.length === 0">
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                      No hay productos con stock bajo
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="flex justify-end">
              <Link 
                :href="route('products.index', { stock_status: 'low' })" 
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300"
              >
                Ver todos los productos con stock bajo
              </Link>
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

export default {
  components: {
    AuthenticatedLayout,
    Link
  },
  props: {
    lowStockProducts: Array,
    totalLowStock: Number,
    outOfStockCount: Number
  }
}
</script>