<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Editar Categoría
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <form @submit.prevent="submit">
              <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input
                  id="name"
                  type="text"
                  v-model="form.name"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  required
                  autofocus
                />
                <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
              </div>

              <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="3"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                ></textarea>
                <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
              </div>

              <div class="mb-6">
                <label for="image" class="block text-sm font-medium text-gray-700">Imagen</label>
                <input
                  id="image"
                  type="file"
                  @input="form.image = $event.target.files[0]"
                  class="mt-1 block w-full"
                  accept="image/*"
                />
                <div v-if="form.errors.image" class="text-red-500 text-sm mt-1">{{ form.errors.image }}</div>
                <div v-if="imagePreview || currentImage" class="mt-2">
                  <img :src="imagePreview || currentImage" class="h-20 w-20 object-cover rounded-md" />
                </div>
              </div>

              <div class="mb-6">
                <label for="sort_order" class="block text-sm font-medium text-gray-700">Orden</label>
                <input
                  id="sort_order"
                  type="number"
                  v-model="form.sort_order"
                  class="mt-1 block w-32 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  min="0"
                />
                <div v-if="form.errors.sort_order" class="text-red-500 text-sm mt-1">{{ form.errors.sort_order }}</div>
              </div>

              <div class="mb-6">
                <div class="flex items-center">
                  <input
                    id="active"
                    type="checkbox"
                    v-model="form.active"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                  />
                  <label for="active" class="ml-2 block text-sm font-medium text-gray-700">Activa</label>
                </div>
                <div v-if="form.errors.active" class="text-red-500 text-sm mt-1">{{ form.errors.active }}</div>
              </div>

              <div class="flex items-center justify-end">
                <Link
                  :href="route('categories.index')"
                  class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 mr-2"
                >
                  Cancelar
                </Link>
                <button
                  type="submit"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                  :disabled="form.processing"
                >
                  Actualizar
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
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

export default {
  components: {
    AuthenticatedLayout,
    Link
  },
  props: {
    category: Object
  },
  setup(props) {
    const form = useForm({
      name: props.category.name,
      description: props.category.description,
      image: null,
      sort_order: props.category.sort_order,
      active: props.category.active
    });
    
    const imagePreview = ref(null);
    const currentImage = computed(() => {
      return props.category.image ? `/storage/${props.category.image}` : null;
    });
    
    watch(() => form.image, (image) => {
      if (!image) {
        imagePreview.value = null;
        return;
      }
      
      const reader = new FileReader();
      reader.onload = (e) => {
        imagePreview.value = e.target.result;
      };
      reader.readAsDataURL(image);
    });
    
    const submit = () => {
      form.post(route('categories.update', props.category.id), {
        method: 'put'
      });
    };
    
    return {
      form,
      imagePreview,
      currentImage,
      submit
    };
  }
};
</script>