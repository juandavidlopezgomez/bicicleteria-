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
                        
                        <!-- SOLO INVENTARIO con Submenú Desplegable para Productos -->
                        <li class="pt-4">
                            <div @click="toggleInventarioMenu" class="flex justify-between items-center cursor-pointer px-4 py-2 rounded-md hover:bg-gray-700" :class="{'bg-gray-700 font-bold': isCurrentPath('/inventory')}">
                                <span>Inventario</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform" :class="{ 'rotate-180': inventarioMenuOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <ul v-show="inventarioMenuOpen" class="ml-4 mt-2 space-y-1">
                                <li>
                                    <Link href="/inventory/products" :class="{'bg-gray-700 font-bold': isCurrentPath('/inventory/products'), 'hover:bg-gray-700': !isCurrentPath('/inventory/products')}" class="block px-4 py-2 rounded-md">
                                        Productos
                                    </Link>
                                </li>
                            </ul>
                        </li>
                        
                        <!-- Administración (mantenido) -->
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
        const inventarioMenuOpen = ref(false);

        const toggleInventarioMenu = () => {
            inventarioMenuOpen.value = !inventarioMenuOpen.value;
        };

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

            // Abrir automáticamente el menú de inventario si estamos en una ruta de inventario
            if (isCurrentPath('/inventory')) {
                inventarioMenuOpen.value = true;
            }
        });

        return { 
            auth, 
            hasRole, 
            hasAnyRole, 
            hasPermission,
            logout, 
            isGestion, 
            isCurrentPath, 
            lowStockCount,
            inventarioMenuOpen,
            toggleInventarioMenu
        };
    },
};
</script>