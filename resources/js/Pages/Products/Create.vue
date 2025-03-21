<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Crear Nuevo Producto
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Alertas de validación -->
            <div v-if="Object.keys($page.props.errors).length > 0" class="mb-4 p-4 bg-red-50 border border-red-500 text-red-700 rounded-lg">
              <p class="font-bold">Por favor corrige los siguientes errores:</p>
              <ul class="mt-2 list-disc list-inside">
                <li v-for="(error, key) in $page.props.errors" :key="key">{{ error }}</li>
              </ul>
            </div>

            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Información básica -->
                <div class="bg-gray-50 p-4 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-700 mb-4">Información Básica</h3>

                  <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nombre del Producto *</label>
                    <input 
                      type="text" 
                      id="name" 
                      v-model="form.name" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                      required
                    />
                  </div>

                  <div class="mb-4">
                    <label for="barcode" class="block text-sm font-medium text-gray-700">Código de Barras</label>
                    <div class="flex items-center">
                      <input 
                        type="text" 
                        id="barcode" 
                        v-model="form.barcode" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                      />
                      <span class="ml-2 text-xs text-gray-500">Se generará automáticamente si lo dejas en blanco</span>
                    </div>
                  </div>

                  <div class="mb-4">
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Categoría *</label>
                    <select 
                      id="category_id" 
                      v-model="form.category_id" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                      required
                    >
                      <option :value="null" disabled>Seleccione una categoría</option>
                      <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                      </option>
                    </select>
                  </div>

                  <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea 
                      id="description" 
                      v-model="form.description" 
                      rows="4" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                    ></textarea>
                  </div>
                </div>

                <!-- Precios y Stock -->
                <div class="bg-gray-50 p-4 rounded-lg">
                  <h3 class="text-lg font-medium text-gray-700 mb-4">Precios y Stock</h3>

                  <div class="mb-4">
                    <label for="purchase_price" class="block text-sm font-medium text-gray-700">Precio de Compra *</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                      </div>
                      <input 
                        type="number" 
                        id="purchase_price" 
                        v-model="form.purchase_price" 
                        step="0.01" 
                        min="0" 
                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm"
                        required
                      />
                    </div>
                  </div>

                  <div class="mb-4">
                    <label for="sale_price" class="block text-sm font-medium text-gray-700">Precio de Venta *</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                      </div>
                      <input 
                        type="number" 
                        id="sale_price" 
                        v-model="form.sale_price" 
                        step="0.01" 
                        min="0" 
                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm"
                        required
                      />
                    </div>
                  </div>

                  <div class="mb-4">
                    <label for="stock" class="block text-sm font-medium text-gray-700">Stock Inicial *</label>
                    <input 
                      type="number" 
                      id="stock" 
                      v-model="form.stock" 
                      min="0" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                      required
                    />
                  </div>

                  <div class="mb-4">
                    <label for="min_stock" class="block text-sm font-medium text-gray-700">Stock Mínimo *</label>
                    <input 
                      type="number" 
                      id="min_stock" 
                      v-model="form.min_stock" 
                      min="0" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                      required
                    />
                  </div>

                  <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                      <label class="flex items-center">
                        <input 
                          type="checkbox" 
                          v-model="form.active" 
                          class="rounded border-gray-300 text-indigo-600 shadow-sm"
                        />
                        <span class="ml-2 text-sm text-gray-700">Activo</span>
                      </label>
                    </div>
                    <div class="mb-4">
                      <label class="flex items-center">
                        <input 
                          type="checkbox" 
                          v-model="form.featured" 
                          class="rounded border-gray-300 text-indigo-600 shadow-sm"
                        />
                        <span class="ml-2 text-sm text-gray-700">Destacado</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Imagen del producto -->
              <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-700 mb-4">Imagen del Producto</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="mb-4">
                    <label for="image" class="block text-sm font-medium text-gray-700">Seleccionar Imagen</label>
                    <input 
                      type="file" 
                      id="image" 
                      @input="form.image = $event.target.files[0]" 
                      class="mt-1 block w-full"
                      accept="image/*"
                    />
                    <p class="mt-1 text-sm text-gray-500">
                      La imagen debe ser menor a 1MB. Formatos permitidos: JPG, PNG, GIF.
                    </p>
                  </div>
                  
                  <div class="mb-4 flex items-center justify-center">
                    <div v-if="previewUrl" class="w-32 h-32 border rounded-lg overflow-hidden">
                      <img :src="previewUrl" class="w-full h-full object-cover" alt="Vista previa" />
                    </div>
                    <div v-else class="w-32 h-32 border rounded-lg flex items-center justify-center bg-gray-100">
                      <span class="text-gray-400">Sin imagen</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Botones de acción -->
              <div class="mt-6 flex items-center justify-end">
                <Link 
                  :href="route('products.index')" 
                  class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 mr-2"
                >
                  Cancelar
                </Link>
                <button 
                  type="submit" 
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                  :disabled="form.processing"
                >
                  {{ form.processing ? 'Guardando...' : 'Guardar Producto' }}
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
import { Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
  components: {
    AuthenticatedLayout,
    Link
  },

  props: {
    categories: Array
  },

  setup() {
    const form = useForm({
      name: '',
      barcode: '',
      description: '',
      purchase_price: 0,
      sale_price: 0,
      stock: 0,
      min_stock: 5,
      category_id: null,
      image: null,
      active: true,
      featured: false
    });

    const previewUrl = ref(null);

    const createImagePreview = (file) => {
      if (!file) {
        previewUrl.value = null;
        return;
      }
      
      const reader = new FileReader();
      reader.onload = (e) => {
        previewUrl.value = e.target.result;
      };
      reader.readAsDataURL(file);
    };

    // Watch for changes to the image and create preview
    watch(() => form.image, (newImage) => {
      createImagePreview(newImage);
    });

    const submit = () => {
      form.post(route('products.store'));
    };

    return {
      form,
      previewUrl,
      submit
    };
  }
};
</script>