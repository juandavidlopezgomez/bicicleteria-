<!-- resources/js/Pages/Auth/Register.vue -->
<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mt-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div v-if="Object.keys(form.errors).length" class="mb-4 text-sm text-red-600 dark:text-red-400">
                            <p v-for="(error, field) in form.errors" :key="field">{{ error }}</p>
                        </div>

                        <form @submit.prevent="submit">
                            <div>
                                <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Name</label>
                                <input id="name" type="text" v-model="form.name" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required />
                            </div>

                            <div class="mt-4">
                                <label for="email" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Email</label>
                                <input id="email" type="email" v-model="form.email" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required />
                            </div>

                            <div class="mt-4">
                                <label for="password" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Password</label>
                                <input id="password" type="password" v-model="form.password" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required />
                            </div>

                            <div class="mt-4">
                                <label for="password_confirmation" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Confirm Password</label>
                                <input id="password_confirmation" type="password" v-model="form.password_confirmation" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required />
                            </div>

                            <div class="mt-4 flex items-center justify-end">
                                <Link href="/login" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                                    Already registered? Log in
                                </Link>

                                <button type="submit" class="ml-4 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Register
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
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
        });

        const submit = () => {
            form.post('/register', {
                onSuccess: () => form.reset(),
            });
        };

        return { form, submit };
    },
};
</script>