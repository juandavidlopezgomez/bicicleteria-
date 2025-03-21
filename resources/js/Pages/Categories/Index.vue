<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Gestión de Categorías
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Barra de herramientas -->
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center">
                <div class="relative">
                  <input
                    type="text"
                    v-model="search"
                    @input="debouncedSearch"
                    placeholder="Buscar categorías..."
                    class="px-4 py-2 border rounded-lg"
                  />
                </div>
              </div>
              <Link
                v-if="$page.props.auth.user.permissions.includes('categories.create')"
                :href="route('categories.create')"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
              >
                Crear Categoría
              </Link>
            </div>

            <!-- Tabla de categorías -->
            <div class="overflow-x-auto bg-white rounded-lg shadow">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Orden
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Imagen
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Nombre
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
                  <tr v-for="category in categories.data" :key="category.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <input 
                          type="number" 
                          v-model="category.sort_order" 
                          @change="updateOrder"
                          class="w-16 border rounded p-1"
                          :disabled="!$page.props.auth.user.permissions.includes('categories.edit')"
                        />
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex-shrink-0 h-10 w-10">
                        <img 
                          v-if="category.image" 
                          :src="'/storage/' + category.image" 
                          class="h-10 w-10 rounded-full object-cover"
                          alt="Categoría" 
                        />
                        <div v-else class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                          <span class="text-gray-500 text-sm">N/A</span>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        {{ category.name }}
                      </div>
                      <div class="text-sm text-gray-500" v-if="category.description">
                        {{ truncate(category.description, 50) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span 
                        :class="[
                          'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                          category.active 
                            ? 'bg-green-100 text-green-800' 
                            : 'bg-red-100 text-red-800'
                        ]"
                      >
                        {{ category.active ? 'Activa' : 'Inactiva' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <div class="flex space-x-2">
                        <Link 
                          v-if="$page.props.auth.user.permissions.includes('categories.view')"
                          :href="route('categories.show', category.id)" 
                          class="text-indigo-600 hover:text-indigo-900"
                        >
                          Ver
                        </Link>
                        <Link 
                          v-if="$page.props.auth.user.permissions.includes('categories.edit')"
                          :href="route('categories.edit', category.id)" 
                          class="text-blue-600 hover:text-blue-900"
                        >
                          Editar
                        </Link>
                        <button 
                          v-if="$page.props.auth.user.permissions.includes('categories.edit')"
                          @click="toggleActive(category)" 
                          class="text-yellow-600 hover:text-yellow-900"
                        >
                          {{ category.active ? 'Desactivar' : 'Activar' }}
                        </button>
                        <button 
                          v-if="$page.props.auth.user.permissions.includes('categories.delete')"
                          @click="confirmDelete(category)" 
                          class="text-red-600 hover:text-red-900"
                        >
                          Eliminar
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="categories.data.length === 0">
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                      No se encontraron categorías
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
              <Pagination :links="categories.links" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import { ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';

export default {
  components: {
    AuthenticatedLayout,
    Link,
    Pagination
  },
  props: {
    categories: Object,
    filters: Object
  },
  setup(props) {
    const search = ref(props.filters.search);
    
    const debouncedSearch = debounce(() => {
      router.get(route('categories.index'), { search: search.value }, {
        preserveState: true,
        replace: true
      });
    }, 300);
    
    const updateOrder = debounce(() => {
      Inertia.post(route('categories.update-order'), {
        categories: props.categories.data.map(category => ({
          id: category.id,
          sort_order: category.sort_order
        }))
      });
    }, 500);
    
    const toggleActive = (category) => {
      Inertia.put(route('categories.toggle-active', category.id));
    };
    
    const confirmDelete = (category) => {
      if (confirm(`¿Estás seguro que deseas eliminar la categoría "${category.name}"?`)) {
        Inertia.delete(route('categories.destroy', category.id));
      }
    };
    
    const truncate = (text, length) => {
      if (!text) return '';
      return text.length > length ? text.substr(0, length) + '...' : text;
    };
    
    return {
      search,
      debouncedSearch,
      updateOrder,
      toggleActive,
      confirmDelete,
      truncate
    };
  }
};
</script>