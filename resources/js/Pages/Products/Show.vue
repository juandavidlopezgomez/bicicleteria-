<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Detalles del Producto
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex justify-between">
          <Link
            :href="route('products.index')"
            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600"
          >
            Volver a Productos
          </Link>
          <div class="flex space-x-2">
            <Link
              v-if="$page.props.auth.user.permissions.includes('products.edit')"
              :href="route('products.edit', product.id)"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            >
              Editar Producto
            </Link>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Columna izquierda: Información básica -->
              <div>
                <h3 class="text-2xl font-bold mb-4">{{ product.name }}</h3>
                
                <div class="space-y-4">
                  <div>
                    <span class="text-gray-600 font-medium">SKU:</span>
                    <span class="ml-2">{{ product.sku }}</span>
                  </div>
                  
                  <div>
                    <span class="text-gray-600 font-medium">Categoría:</span>
                    <span class="ml-2">{{ product.category.name }}</span>
                  </div>
                  
                  <div>
                    <span class="text-gray-600 font-medium">Estado:</span>
                    <span 
                      :class="[
                        'ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                        product.active 
                          ? 'bg-green-100 text-green-800' 
                          : 'bg-red-100 text-red-800'
                      ]"
                    >
                      {{ product.active ? 'Activo' : 'Inactivo' }}
                    </span>
                  </div>
                  
                  <div>
                    <span class="text-gray-600 font-medium">Destacado:</span>
                    <span 
                      :class="[
                        'ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                        product.featured 
                          ? 'bg-yellow-100 text-yellow-800' 
                          : 'bg-gray-100 text-gray-800'
                      ]"
                    >
                      {{ product.featured ? 'Destacado' : 'Normal' }}
                    </span>
                  </div>
                  
                  <div>
                    <span class="text-gray-600 font-medium">Precio de Compra:</span>
                    <span class="ml-2">${{ product.purchase_price.toFixed(2) }}</span>
                  </div>
                  
                  <div>
                    <span class="text-gray-600 font-medium">Precio de Venta:</span>
                    <span class="ml-2">${{ product.sale_price.toFixed(2) }}</span>
                  </div>
                  
                  <div>
                    <span class="text-gray-600 font-medium">Margen:</span>
                    <span class="ml-2">{{ calculateMargin() }}%</span>
                  </div>
                  
                  <div>
                    <span class="text-gray-600 font-medium">Stock Actual:</span>
                    <span 
                      :class="[
                        'ml-2 px-2 py-1 rounded-full text-sm font-medium',
                        getStockStatusClass()
                      ]"
                    >
                      {{ product.stock }} unidades
                    </span>
                  </div>
                  
                  <div>
                    <span class="text-gray-600 font-medium">Stock Mínimo:</span>
                    <span class="ml-2">{{ product.min_stock }} unidades</span>
                  </div>
                  
                  <div v-if="product.description" class="mt-4">
                    <h4 class="text-gray-600 font-medium mb-2">Descripción:</h4>
                    <p class="text-gray-800 whitespace-pre-line">{{ product.description }}</p>
                  </div>
                </div>
              </div>
              
              <!-- Columna derecha: Imagen y código de barras -->
              <div>
                <div class="mb-6">
                  <h4 class="text-gray-600 font-medium mb-2">Imagen del Producto:</h4>
                  <div class="rounded-lg overflow-hidden border border-gray-200">
                    <img 
                      v-if="product.image" 
                      :src="'/storage/' + product.image" 
                      alt="Imagen del producto"
                      class="w-full object-cover h-64"
                    />
                    <div v-else class="w-full h-64 bg-gray-100 flex items-center justify-center">
                      <span class="text-gray-400">Sin imagen</span>
                    </div>
                  </div>
                </div>
                
                <div>
                  <h4 class="text-gray-600 font-medium mb-2">Código de Barras:</h4>
                  <div class="p-4 border border-gray-200 rounded-lg bg-white">
                    <div class="text-center mb-2">{{ product.barcode }}</div>
                    <div class="flex justify-center">
                      <img 
                        :src="`data:image/png;base64,${barcodeImage}`" 
                        alt="Código de barras"
                        class="h-20"
                      />
                    </div>
                    <div class="mt-4 flex justify-center">
                      <button
                        @click="printBarcode"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 flex items-center"
                      >
                        <span class="mr-2">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                          </svg>
                        </span>
                        Imprimir Etiqueta
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

// Props
const props = defineProps({
  product: Object,
  barcodeImage: String
});

// Calcular el margen de ganancia
const calculateMargin = () => {
  if (props.product.purchase_price === 0) return '0';
  const margin = ((props.product.sale_price - props.product.purchase_price) / props.product.sale_price) * 100;
  return margin.toFixed(2);
};

// Obtener la clase de estado de stock
const getStockStatusClass = () => {
  if (props.product.stock === 0) {
    return 'bg-red-100 text-red-800';
  } else if (props.product.stock <= props.product.min_stock) {
    return 'bg-yellow-100 text-yellow-800';
  } else {
    return 'bg-green-100 text-green-800';
  }
};

// Función para imprimir el código de barras
const printBarcode = () => {
  const printWindow = window.open('', '_blank');
  printWindow.document.write(`
    <html>
      <head>
        <title>Etiqueta - ${props.product.name}</title>
        <style>
          body {
            font-family: Arial, sans-serif;
            padding: 10mm;
          }
          .barcode-container {
            text-align: center;
            padding: 5mm;
            border: 1px solid #ccc;
            display: inline-block;
          }
          .product-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3mm;
          }
          .product-price {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3mm;
          }
          .barcode {
            margin-bottom: 2mm;
          }
          .barcode-number {
            font-size: 12px;
          }
          @media print {
            @page {
              size: 50mm 30mm;
              margin: 0;
            }
            body {
              padding: 2mm;
            }
          }
        </style>
      </head>
      <body onload="window.print(); window.setTimeout(function(){ window.close(); }, 500);">
        <div class="barcode-container">
          <div class="product-name">${props.product.name}</div>
          <div class="product-price">$${props.product.sale_price.toFixed(2)}</div>
          <div class="barcode">
            <img src="data:image/png;base64,${props.barcodeImage}" alt="Código de barras" style="height: 15mm;" />
          </div>
          <div class="barcode-number">${props.product.barcode}</div>
        </div>
      </body>
    </html>
  `);
  printWindow.document.close();
};
</script>