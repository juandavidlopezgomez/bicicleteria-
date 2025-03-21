<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Editar Producto
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4">
          <Link
            :href="route('products.index')"
            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600"
          >
            Volver a Productos
          </Link>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Columna izquierda -->
                <div class="space-y-6">
                  <!-- Nombre del producto -->
                  <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nombre del producto</label>
                    <input 
                      id="name" 
                      v-model="form.name" 
                      type="text" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      :class="{ 'border-red-500': form.errors.name }"
                    />
                    <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                  </div>

                  <!-- Categoría -->
                  <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Categoría</label>
                    <select 
                      id="category_id" 
                      v-model="form.category_id" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      :class="{ 'border-red-500': form.errors.category_id }"
                    >
                      <option value="" disabled>Seleccionar categoría</option>
                      <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                      </option>
                    </select>
                    <div v-if="form.errors.category_id" class="text-red-500 text-xs mt-1">{{ form.errors.category_id }}</div>
                  </div>

                  <!-- Precios -->
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label for="purchase_price" class="block text-sm font-medium text-gray-700">Precio de compra</label>
                      <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input 
                          id="purchase_price" 
                          v-model="form.purchase_price" 
                          type="number" 
                          step="0.01"
                          min="0" 
                          class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                          :class="{ 'border-red-500': form.errors.purchase_price }"
                        />
                      </div>
                      <div v-if="form.errors.purchase_price" class="text-red-500 text-xs mt-1">{{ form.errors.purchase_price }}</div>
                    </div>
                    <div>
                      <label for="sale_price" class="block text-sm font-medium text-gray-700">Precio de venta</label>
                      <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input 
                          id="sale_price" 
                          v-model="form.sale_price" 
                          type="number" 
                          step="0.01"
                          min="0" 
                          class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                          :class="{ 'border-red-500': form.errors.sale_price }"
                        />
                      </div>
                      <div v-if="form.errors.sale_price" class="text-red-500 text-xs mt-1">{{ form.errors.sale_price }}</div>
                    </div>
                  </div>

                  <!-- Stock mínimo -->
                  <div>
                    <label for="min_stock" class="block text-sm font-medium text-gray-700">Stock mínimo</label>
                    <input 
                      id="min_stock" 
                      v-model="form.min_stock" 
                      type="number" 
                      min="0" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      :class="{ 'border-red-500': form.errors.min_stock }"
                    />
                    <div v-if="form.errors.min_stock" class="text-red-500 text-xs mt-1">{{ form.errors.min_stock }}</div>
                  </div>

                  <!-- Código de barras -->
                  <div>
                    <label for="barcode" class="block text-sm font-medium text-gray-700">
                      Código de barras
                      <span class="text-xs text-gray-500 font-normal ml-1">(Dejar en blanco para generar automáticamente)</span>
                    </label>
                    <input 
                      id="barcode" 
                      v-model="form.barcode" 
                      type="text" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      :class="{ 'border-red-500': form.errors.barcode }"
                    />
                    <div v-if="form.errors.barcode" class="text-red-500 text-xs mt-1">{{ form.errors.barcode }}</div>
                  </div>

                  <!-- Estado -->
                  <div class="flex items-center space-x-6">
                    <div class="flex items-center">
                      <input 
                        id="active" 
                        v-model="form.active" 
                        type="checkbox" 
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                      />
                      <label for="active" class="ml-2 block text-sm text-gray-700">Activo</label>
                    </div>
                    <div class="flex items-center">
                      <input 
                        id="featured" 
                        v-model="form.featured" 
                        type="checkbox" 
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                      />
                      <label for="featured" class="ml-2 block text-sm text-gray-700">Destacado</label>
                    </div>
                  </div>
                </div>

                <!-- Columna derecha -->
                <div class="space-y-6">
                  <!-- Imagen del producto -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Imagen del producto</label>
                    <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                      <div class="space-y-1 text-center" v-if="!preview && !product.image">
                        <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                          <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                            <span>Subir imagen</span>
                            <input id="image" type="file" class="sr-only" @change="previewImage" accept="image/*" />
                          </label>
                          <p class="pl-1">o arrastrar y soltar</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF hasta 1MB</p>
                      </div>
                      <div v-else class="w-full">
                        <img 
                          :src="preview || (product.image ? '/storage/' + product.image : '')" 
                          alt="Vista previa"
                          class="mx-auto h-40 object-contain"
                        />
                        <div class="flex justify-center mt-2 space-x-2">
                          <label for="image" class="cursor-pointer text-sm text-indigo-600 hover:text-indigo-500">
                            Cambiar
                            <input id="image" type="file" class="sr-only" @change="previewImage" accept="image/*" />
                          </label>
                          <button 
                            type="button" 
                            class="text-sm text-red-600 hover:text-red-500"
                            @click="clearImage"
                          >
                            Eliminar
                          </button>
                        </div>
                      </div>
                    </div>
                    <div v-if="form.errors.image" class="text-red-500 text-xs mt-1">{{ form.errors.image }}</div>
                  </div>

                  <!-- Descripción del producto -->
                  <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea 
                      id="description" 
                      v-model="form.description" 
                      rows="8" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      :class="{ 'border-red-500': form.errors.description }"
                    ></textarea>
                    <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
                  </div>
                </div>
              </div>

              <!-- Botones de acción -->
              <div class="flex justify-end mt-6 space-x-3">
                <Link
                  :href="route('products.index')"
                  class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600"
                >
                  Cancelar
                </Link>
                <button
                  type="submit"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                  :disabled="form.processing"
                >
                  Guardar cambios
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/inertia-vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  product: Object,
  categories: Array
});

const preview = ref(null);

const form = useForm({
  name: props.product.name,
  category_id: props.product.category_id,
  description: props.product.description,
  purchase_price: props.product.purchase_price,
  sale_price: props.product.sale_price,
  min_stock: props.product.min_stock,
  barcode: props.product.barcode,
  active: props.product.active,
  featured: props.product.featured,
  image: null
});

const previewImage = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.image = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      preview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const clearImage = () => {
  form.image = null;
  preview.value = null;
  // Si quieres eliminar la imagen actual del servidor, puedes agregar un campo adicional
  // Por ejemplo: form.remove_image = true
};

const submit = () => {
  form.post(route('products.update', props.product.id), {
    _method: 'put',
    preserveScroll: true,
    onSuccess: () => {
      // Opcional: puedes hacer algo después de guardar exitosamente
    }
  });
};
</script>