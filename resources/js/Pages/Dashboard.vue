<!-- resources/js/Pages/Dashboard.vue -->
<template>
    <AuthenticatedLayout>
        <Head title="Dashboard" />

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h1 class="text-2xl font-bold">Dashboard</h1>
                <p>Welcome to your dashboard, {{ auth.user.name }}!</p>
                <p>Your role: {{ auth.user.roles.map(role => role.name).join(', ') }}</p>

                <!-- Contenido para Propietario -->
                <div v-if="hasRole('propietario')">
                    <h2 class="text-xl font-semibold mt-4">Propietario Dashboard</h2>
                    <p>Access to all features and reports.</p>
                    <div class="mt-4 flex space-x-4">
                        <Link href="/users" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500">Manage Users</Link>
                        <Link href="/reports" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500">View Reports</Link>
                    </div>
                </div>

                <!-- Contenido para Administrador -->
                <div v-if="hasRole('administrador')">
                    <h2 class="text-xl font-semibold mt-4">Administrador Dashboard</h2>
                    <p>Manage users, products, and sales.</p>
                    <div class="mt-4 flex space-x-4">
                        <Link href="/users" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500">Manage Users</Link>
                        <Link href="/products" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500">Manage Products</Link>
                    </div>
                </div>

                <!-- Contenido para Cajero -->
                <div v-if="hasRole('cajero')">
                    <h2 class="text-xl font-semibold mt-4">Cajero Dashboard</h2>
                    <p>Handle sales and view products.</p>
                    <div class="mt-4 flex space-x-4">
                        <Link href="/sales" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500">Create Sale</Link>
                        <Link href="/products" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500">View Products</Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

export default {
    components: {
        AuthenticatedLayout,
        Head,
        Link,
    },
    setup() {
        const { props } = usePage();
        const auth = props.auth;

        const hasRole = (role) => {
            return auth.user && auth.user.roles && auth.user.roles.some(r => r.name === role);
        };

        return { auth, hasRole };
    },
};
</script>