<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Entrada de Inventario
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <form @submit.prevent="submitForm">
              <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Registrar Entrada de Productos</h3>
                <p class="text-sm text-gray-600 mb-4">
                  Utilice este formulario para registrar nuevas entradas de productos al inventario (compras, devoluciones, etc.)
                </p>
              </div>

              <!-- Referencia de la entrada -->
              <div class="mb-6">
                <label for="reference" class="block text-sm font-medium text-gray-700">Referencia</label>
                <div class="mt-1">
                  <input
                    id="reference"
                    type="text"
                    v-model="form.reference"
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    placeholder="Ej: Compra #123, Devolución Cliente, etc."
                    required
                  />
                </div>
                <div v-if="form.errors.reference" class="text-red-500 text-sm mt-1">{{ form.errors.reference }}</div>
              </div>

              <!-- Lista de productos -->
              <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                  <label class="block text-sm font-medium text-gray-700">Productos a Ingresar</label>
                  <button
                    type="button"
                    @click="addEntryRow"
                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  >
                    Añadir Producto
                  </button>
                </div>

                <div v-if="form.errors['entries']" class="text-red-500 text-sm mb-2">{{ form.errors['entries'] }}</div>
                
                <div class="space-y-4">
                  <div 
                    v-for="(entry, index) in form.entries" 
                    :key="index"
                    class="flex flex-col sm:flex-row gap-4 p-4 bg-gray-50 rounded-lg"
                  >
                    <div class="flex-grow">
                      <label :for="`product_id_${index}`" class="block text-xs font-medium text-gray-700 mb-1">Producto</label>
                      <select
                        :id="`product_id_${index}`"
                        v-model="entry.product_id"
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                        required
                      >
                        <option value="" disabled>Seleccione un producto</option>
                        <option v-for="product in products" :key="product.id" :value="product.id">
                          {{ product.name }} - Stock actual: {{ product.stock }} - SKU: {{ product.sku }}
                        </option>
                      </select>
                      <div v-if="form.errors[`entries.${index}.product_id`]" class="text-red-500 text-xs mt-1">
                        {{ form.errors[`entries.${index}.product_id`] }}
                      </div>
                    </div>
                    
                    <div class="w-full sm:w-32">
                      <label :for="`quantity_${index}`" class="block text-xs font-medium text-gray-700 mb-1">Cantidad</label>
                      <input
                        :id="`quantity_${index}`"
                        type="number"
                        v-model="entry.quantity"
                        min="1"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required
                      />
                      <div v-if="form.errors[`entries.${index}.quantity`]" class="text-red-500 text-xs mt-1">
                        {{ form.errors[`entries.${index}.quantity`] }}
                      </div>
                    </div>
                    
                    <div class="flex items-center sm:pl-4 mt-4 sm:mt-0">
                      <button
                        type="button"
                        @click="removeEntryRow(index)"
                        class="text-red-600 hover:text-red-900"
                        title="Eliminar producto"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>

                <div v-if="form.entries.length === 0" class="text-sm text-gray-500 mt-2">
                  No hay productos añadidos. Haga clic en "Añadir Producto" para comenzar.
                </div>
              </div>

              <!-- Notas adicionales -->
              <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700">Notas Adicionales</label>
                <div class="mt-1">
                  <textarea
                    id="notes"
                    v-model="form.notes"
                    rows="3"
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md"
                    placeholder="Información adicional sobre esta entrada..."
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
                  :disabled="form.processing || form.entries.length === 0"
                >
                  {{ form.processing ? 'Procesando...' : 'Registrar Entrada' }}
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
import { ref } from 'vue';
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
  setup() {
    const form = useForm({
      entries: [],
      reference: '',
      notes: ''
    });

    const addEntryRow = () => {
      form.entries.push({
        product_id: '',
        quantity: 1
      });
    };

    const removeEntryRow = (index) => {
      form.entries.splice(index, 1);
    };

    const submitForm = () => {
      form.post(route('inventory.process-entry'), {
        onSuccess: () => {
          form.reset();
          form.entries = [];
        }
      });
    };

    // Añadir una fila inicial
    if (form.entries.length === 0) {
      addEntryRow();
    }

    return {
      form,
      addEntryRow,
      removeEntryRow,
      submitForm
    };
  }
};
</script>