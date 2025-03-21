<!-- resources/js/Pages/Auth/Login.vue -->
<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mt-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div v-if="form.errors.email" class="mb-4 text-sm text-red-600 dark:text-red-400">
                            {{ form.errors.email }}
                        </div>

                        <form @submit.prevent="submit">
                            <div>
                                <label for="email" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Email</label>
                                <input id="email" type="email" v-model="form.email" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required />
                            </div>

                            <div class="mt-4">
                                <label for="password" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Password</label>
                                <input id="password" type="password" v-model="form.password" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required />
                            </div>

                            <div class="mt-4 flex items-center">
                                <label class="flex items-center">
                                    <input type="checkbox" v-model="form.remember" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded shadow-sm">
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                                </label>
                            </div>

                            <div class="mt-4 flex items-center justify-end">
                                <Link href="/register" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                                    Don't have an account? Register
                                </Link>

                                <button type="submit" class="ml-4 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Log in
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

export default {
    components: {
        GuestLayout,
        Head,
        Link,
    },
    setup() {
        const form = useForm({
            email: '',
            password: '',
            remember: false,
        });

        const submit = () => {
            form.post('/login', {
                onSuccess: () => form.reset('password'),
            });
        };

        return { form, submit };
    },
};
</script>