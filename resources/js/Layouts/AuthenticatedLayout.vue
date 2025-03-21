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
        <nav v-if="isGestion" class="bg-gray-700 text-white p-4 flex space-x-4">
            <Link href="/categories" class="hover:underline">Categorías</Link>
            <Link href="/products" class="hover:underline">Productos</Link>
            <Link href="/inventory" class="hover:underline">Inventario</Link>
            <Link href="/sales" class="hover:underline">Ventas</Link>
            <Link href="/reports" class="hover:underline">Reportes</Link>
        </nav>

        <!-- Contenido Principal con Menú Lateral -->
        <div class="flex">
            <aside class="w-64 bg-gray-800 text-white h-screen p-4">
                <nav>
                    <ul>
                        <li>
                            <Link href="/dashboard" class="block px-4 py-2 hover:bg-gray-700">Dashboard</Link>
                        </li>
                        <li>
                            <Link href="/categories" class="block px-4 py-2 hover:bg-gray-700">Gestión</Link>
                        </li>
                    </ul>
                </nav>
            </aside>

            <!-- Contenido Principal -->
            <main class="flex-1 p-6">
                <slot />
            </main>
        </div>
    </div>
</template>

<script>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';

export default {
    components: {
        Link,
    },
    setup() {
        const { props } = usePage();
        const auth = props.auth;

        const hasRole = (role) => {
            return auth.user && auth.user.roles && auth.user.roles.some(r => r.name === role);
        };

        const hasAnyRole = (roles) => {
            return auth.user && auth.user.roles && roles.some(role => auth.user.roles.some(r => r.name === role));
        };

        const logout = () => {
            router.post('/logout');
        };

        // Detectar si estamos en la sección de Gestión
        const isGestion = computed(() => {
            return window.location.pathname.startsWith('/categories') ||
                   window.location.pathname.startsWith('/products') ||
                   window.location.pathname.startsWith('/inventory') ||
                   window.location.pathname.startsWith('/sales') ||
                   window.location.pathname.startsWith('/reports');
        });

        return { auth, hasRole, hasAnyRole, logout, isGestion };
    },
};
</script>
