<!-- resources/js/Pages/Auth/ResetPassword.vue -->
<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mt-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit">
                            <div>
                                <label for="email" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Email</label>
                                <input id="email" type="email" v-model="form.email" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required />
                                <div v-if="form.errors.email" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <div class="mt-4">
                                <label for="password" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Password</label>
                                <input id="password" type="password" v-model="form.password" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required />
                                <div v-if="form.errors.password" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.password }}
                                </div>
                            </div>

                            <div class="mt-4">
                                <label for="password_confirmation" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Confirm Password</label>
                                <input id="password_confirmation" type="password" v-model="form.password_confirmation" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required />
                                <div v-if="form.errors.password_confirmation" class="mt-2 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.password_confirmation }}
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Reset Password
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
import { Head, useForm } from '@inertiajs/vue3';

export default {
    components: {
        GuestLayout,
        Head,
    },
    props: {
        email: String,
        token: String,
    },
    setup(props) {
        const form = useForm({
            token: props.token,
            email: props.email,
            password: '',
            password_confirmation: '',
        });

        const submit = () => {
            form.post('/reset-password', {
                onSuccess: () => form.reset('password', 'password_confirmation'),
            });
        };

        return { form, submit };
    },
};
</script>