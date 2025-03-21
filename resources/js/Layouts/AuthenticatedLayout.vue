<!-- resources/js/Layouts/AuthenticatedLayout.vue -->
<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Barra de Navegación Superior -->
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <Link href="/dashboard">
                                <span class="text-xl font-bold text-gray-800 dark:text-gray-200">Bicicletería</span>
                            </Link>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <span class="text-gray-600 dark:text-gray-400 mr-4">{{ auth.user.name }}</span>
                        <form @submit.prevent="logout">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Menú Superior Dinámico -->
        <nav v-if="isGestion" class="bg-gray-700 text-white p-4 flex overflow-x-auto space-x-6">
            <Link href="/categories" :class="{'text-yellow-300 font-bold': isCurrentPath('/categories'), 'hover:text-yellow-200': !isCurrentPath('/categories')}">Categorías</Link>
            <Link href="/products" :class="{'text-yellow-300 font-bold': isCurrentPath('/products'), 'hover:text-yellow-200': !isCurrentPath('/products')}">Productos</Link>
            <Link href="/inventory" :class="{'text-yellow-300 font-bold': isCurrentPath('/inventory'), 'hover:text-yellow-200': !isCurrentPath('/inventory')}">Inventario</Link>
            <Link href="/inventory/low-stock" :class="{'text-yellow-300 font-bold': isCurrentPath('/inventory/low-stock'), 'hover:text-yellow-200': !isCurrentPath('/inventory/low-stock')}">Stock Bajo</Link>
            <Link href="/products/import" :class="{'text-yellow-300 font-bold': isCurrentPath('/products/import'), 'hover:text-yellow-200': !isCurrentPath('/products/import')}">Importar/Exportar</Link>
            <Link href="/barcode/print" :class="{'text-yellow-300 font-bold': isCurrentPath('/barcode/print'), 'hover:text-yellow-200': !isCurrentPath('/barcode/print')}">Etiquetas</Link>
        </nav>

        <!-- Contenido Principal con Menú Lateral -->
        <div class="flex">
            <aside class="w-64 bg-gray-800 text-white min-h-screen p-4">
                <nav>
                    <ul class="space-y-2">
                        <li>
                            <Link href="/dashboard" :class="{'bg-gray-700 font-bold': isCurrentPath('/dashboard'), 'hover:bg-gray-700': !isCurrentPath('/dashboard')}" class="block px-4 py-2 rounded-md">
                                Dashboard
                            </Link>
                        </li>
                        
                        <!-- Gestión de Catálogo -->
                        <li class="pt-4">
                            <h3 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">CATÁLOGO</h3>
                        </li>
                        <li>
                            <Link href="/categories" :class="{'bg-gray-700 font-bold': isCurrentPath('/categories'), 'hover:bg-gray-700': !isCurrentPath('/categories')}" class="block px-4 py-2 rounded-md">
                                Categorías
                            </Link>
                        </li>
                        <li>
                            <Link href="/products" :class="{'bg-gray-700 font-bold': isCurrentPath('/products'), 'hover:bg-gray-700': !isCurrentPath('/products')}" class="block px-4 py-2 rounded-md">
                                Productos
                            </Link>
                        </li>
                        
                        <!-- Inventario -->
                        <li class="pt-4">
                            <h3 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">INVENTARIO</h3>
                        </li>
                        <li>
                            <Link href="/inventory" :class="{'bg-gray-700 font-bold': isCurrentPath('/inventory'), 'hover:bg-gray-700': !isCurrentPath('/inventory')}" class="block px-4 py-2 rounded-md">
                                Movimientos
                            </Link>
                        </li>
                        <li>
                            <Link href="/inventory/low-stock" :class="{'bg-gray-700 font-bold': isCurrentPath('/inventory/low-stock'), 'hover:bg-gray-700': !isCurrentPath('/inventory/low-stock')}" class="block px-4 py-2 rounded-md">
                                Stock Bajo
                                <span v-if="lowStockCount > 0" class="inline-flex items-center justify-center ml-2 w-5 h-5 text-xs font-semibold text-white bg-red-500 rounded-full">
                                    {{ lowStockCount }}
                                </span>
                            </Link>
                        </li>
                        
                        <!-- Herramientas -->
                        <li class="pt-4">
                            <h3 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">HERRAMIENTAS</h3>
                        </li>
                        <li>
                            <Link href="/products/import" :class="{'bg-gray-700 font-bold': isCurrentPath('/products/import'), 'hover:bg-gray-700': !isCurrentPath('/products/import')}" class="block px-4 py-2 rounded-md">
                                Importar/Exportar
                            </Link>
                        </li>
                        <li>
                            <Link href="/barcode/print" :class="{'bg-gray-700 font-bold': isCurrentPath('/barcode/print'), 'hover:bg-gray-700': !isCurrentPath('/barcode/print')}" class="block px-4 py-2 rounded-md">
                                Etiquetas
                            </Link>
                        </li>
                        
                        <!-- Administración -->
                        <li v-if="hasRole('admin')" class="pt-4">
                            <h3 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">ADMINISTRACIÓN</h3>
                        </li>
                        <li v-if="hasRole('admin')">
                            <Link href="/users" :class="{'bg-gray-700 font-bold': isCurrentPath('/users'), 'hover:bg-gray-700': !isCurrentPath('/users')}" class="block px-4 py-2 rounded-md">
                                Usuarios
                            </Link>
                        </li>
                    </ul>
                </nav>
            </aside>

            <!-- Contenido Principal -->
            <main class="flex-1 p-6 overflow-auto">
                <slot />
            </main>
        </div>
    </div>
</template>

<script>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

export default {
    components: {
        Link,
    },
    setup() {
        const { props } = usePage();
        const auth = props.auth;
        const lowStockCount = ref(0);

        const hasRole = (role) => {
            return auth.user && auth.user.roles && auth.user.roles.some(r => r.name === role);
        };

        const hasAnyRole = (roles) => {
            return auth.user && auth.user.roles && roles.some(role => auth.user.roles.some(r => r.name === role));
        };
        
        // Añadimos la función hasPermission
        const hasPermission = (permission) => {
            return auth.user && 
                auth.user.permissions && 
                auth.user.permissions.includes(permission);
        };

        const logout = () => {
            router.post('/logout');
        };

        // Detectar si estamos en la sección de Gestión
        const isGestion = computed(() => {
            return [
                '/categories', 
                '/products', 
                '/inventory',
                '/barcode',
                '/import-export'
            ].some(path => window.location.pathname.startsWith(path));
        });
        
        // Comprobar si estamos en una ruta específica
        const isCurrentPath = (path) => {
            return window.location.pathname === path || window.location.pathname.startsWith(path + '/');
        };
        
        // Cargar el contador de productos con stock bajo
        onMounted(() => {
            // Aquí se podría hacer una petición AJAX para obtener el número de productos con stock bajo
            // Por ahora, usaremos un valor de ejemplo
            lowStockCount.value = 5;
        });

        return { 
            auth, 
            hasRole, 
            hasAnyRole, 
            hasPermission, // Exponemos la función
            logout, 
            isGestion, 
            isCurrentPath, 
            lowStockCount 
        };
    },
};
</script>