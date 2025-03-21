<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Gestión de Productos
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Barra de herramientas -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
              <div class="flex flex-col md:flex-row gap-4">
                <!-- Búsqueda -->
                <div class="relative">
                  <input
                    type="text"
                    v-model="filters.search"
                    @input="debouncedSearch"
                    placeholder="Buscar por nombre, SKU o código de barras..."
                    class="px-4 py-2 border rounded-lg w-full md:w-80"
                  />
                </div>

                <!-- Filtro de categoría -->
                <select
                  v-model="filters.category_id"
                  @change="filterProducts"
                  class="px-4 py-2 border rounded-lg"
                >
                  <option value="">Todas las categorías</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>

                <!-- Filtro de stock -->
                <select
                  v-model="filters.stock_status"
                  @change="filterProducts"
                  class="px-4 py-2 border rounded-lg"
                >
                  <option value="">Todos los productos</option>
                  <option value="low">Stock bajo</option>
                  <option value="out">Sin stock</option>
                </select>
              </div>

              <!-- Botones de acción -->
              <div class="flex items-center gap-2">
                <Link
                  v-if="$page.props.auth.user.permissions.includes('products.create')"
                  :href="route('products.create')"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                >
                  Nuevo Producto
                </Link>
                <Link
                  :href="route('products.index', { ...filters, format: 'export' })"
                  class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                >
                  Exportar
                </Link>
              </div>
            </div>

            <!-- Tabla de productos -->
            <div class="overflow-x-auto bg-white rounded-lg shadow">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th @click="sort('sku')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                      SKU
                      <SortIcon :field="'sku'" :current-sort="filters.sort" :direction="filters.direction" />
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Imagen
                    </th>
                    <th @click="sort('name')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                      Nombre
                      <SortIcon :field="'name'" :current-sort="filters.sort" :direction="filters.direction" />
                    </th>
                    <th @click="sort('category_id')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                      Categoría
                      <SortIcon :field="'category_id'" :current-sort="filters.sort" :direction="filters.direction" />
                    </th>
                    <th @click="sort('sale_price')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                      Precio
                      <SortIcon :field="'sale_price'" :current-sort="filters.sort" :direction="filters.direction" />
                    </th>
                    <th @click="sort('stock')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                      Stock
                      <SortIcon :field="'stock'" :current-sort="filters.sort" :direction="filters.direction" />
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Estado
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Acciones
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="product in products.data" :key="product.id">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ product.sku }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex-shrink-0 h-10 w-10">
                        <img 
                          v-if="product.image" 
                          :src="'/storage/' + product.image" 
                          class="h-10 w-10 rounded-full object-cover"
                          alt="Producto" 
                        />
                        <div v-else class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                          <span class="text-gray-500 text-sm">N/A</span>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ product.name }}
                      </div>
                      <div class="text-sm text-gray-500">
                        <span v-if="product.featured" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                          Destacado
                        </span>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ product.category ? product.category.name : 'Sin categoría' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      ${{ formatPrice(product.sale_price) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div 
                        :class="[
                          'text-sm', 
                          product.stock <= 0 ? 'text-red-600 font-bold' : 
                          product.stock <= product.min_stock ? 'text-yellow-600 font-bold' : 
                          'text-gray-900'
                        ]"
                      >
                        {{ product.stock }} unidades
                        <span v-if="product.stock <= product.min_stock && product.stock > 0" class="text-xs text-yellow-600">
                          (Stock bajo)
                        </span>
                        <span v-if="product.stock <= 0" class="text-xs text-red-600">
                          (Sin stock)
                        </span>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span 
                        :class="[
                          'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                          product.active 
                            ? 'bg-green-100 text-green-800' 
                            : 'bg-red-100 text-red-800'
                        ]"
                      >
                        {{ product.active ? 'Activo' : 'Inactivo' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <div class="flex space-x-2">
                        <Link 
                          v-if="$page.props.auth.user.permissions.includes('products.view')"
                          :href="route('products.show', product.id)" 
                          class="text-indigo-600 hover:text-indigo-900"
                        >
                          Ver
                        </Link>
                        <Link 
                          v-if="$page.props.auth.user.permissions.includes('products.edit')"
                          :href="route('products.edit', product.id)" 
                          class="text-blue-600 hover:text-blue-900"
                        >
                          Editar
                        </Link>
                        <button 
                          v-if="$page.props.auth.user.permissions.includes('products.edit')"
                          @click="toggleActive(product)" 
                          class="text-yellow-600 hover:text-yellow-900"
                        >
                          {{ product.active ? 'Desactivar' : 'Activar' }}
                        </button>
                        <button 
                          v-if="$page.props.auth.user.permissions.includes('products.edit')"
                          @click="toggleFeatured(product)" 
                          class="text-purple-600 hover:text-purple-900"
                        >
                          {{ product.featured ? 'No destacar' : 'Destacar' }}
                        </button>
                        <button 
                          v-if="$page.props.auth.user.permissions.includes('products.delete')"
                          @click="confirmDelete(product)" 
                          class="text-red-600 hover:text-red-900"
                        >
                          Eliminar
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="products.data.length === 0">
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                      No se encontraron productos
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
              <Pagination :links="products.links" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script>
import { ref, watch } from 'vue';
import { Inertia } from '@inertiajs/core';
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';

export default {
  components: {
    AuthenticatedLayout,
    Link,
    Pagination,
    SortIcon: {
      props: ['field', 'currentSort', 'direction'],
      template: `
        <span class="ml-1">
          <template v-if="field === currentSort">
            <i v-if="direction === 'asc'" class="fas fa-sort-up"></i>
            <i v-else class="fas fa-sort-down"></i>
          </template>
          <i v-else class="fas fa-sort text-gray-300"></i>
        </span>
      `
    }
  },

  props: {
    products: Object,
    categories: Array,
    filters: Object
  },

  setup(props) {
    const filters = ref({
      search: props.filters.search || '',
      category_id: props.filters.category_id || '',
      stock_status: props.filters.stock_status || '',
      sort: props.filters.sort || 'created_at',
      direction: props.filters.direction || 'desc'
    });

    let searchTimeout;

    const debouncedSearch = () => {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        filterProducts();
      }, 300);
    };

    const filterProducts = () => {
      Inertia.get(route('products.index'), filters.value, { 
        preserveState: true,
        replace: true
      });
    };

    const sort = (field) => {
      if (filters.value.sort === field) {
        filters.value.direction = filters.value.direction === 'asc' ? 'desc' : 'asc';
      } else {
        filters.value.sort = field;
        filters.value.direction = 'asc';
      }
      filterProducts();
    };

    const toggleActive = (product) => {
      Inertia.put(route('products.toggle-active', product.id), {}, {
        preserveScroll: true
      });
    };

    const toggleFeatured = (product) => {
      Inertia.put(route('products.toggle-featured', product.id), {}, {
        preserveScroll: true
      });
    };

    const confirmDelete = (product) => {
      if (confirm(`¿Estás seguro de eliminar el producto "${product.name}"?`)) {
        Inertia.delete(route('products.destroy', product.id));
      }
    };

    const formatPrice = (price) => {
      return price.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    };

    return {
      filters,
      debouncedSearch,
      filterProducts,
      sort,
      toggleActive,
      toggleFeatured,
      confirmDelete,
      formatPrice
    };
  }
};
</script>