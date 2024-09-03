<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Welcome" />

    <div
        class="relative flex flex-col sm:flex-row sm:justify-center sm:items-center min-h-screen bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 dark:from-gray-800 dark:via-gray-900 dark:to-black selection:bg-red-500 selection:text-white"
    >
        <div class="max-w-2xl mx-auto p-8 bg-white dark:bg-gray-800 rounded-lg shadow-lg transform transition hover:scale-105">
            <h1 class="text-4xl font-extrabold text-center text-gray-800 dark:text-white mb-6">
                Welcome to Your Cloudflare Panel
            </h1>

            <div v-if="canLogin" class="text-center">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('cloudflare')"
                    class="block font-semibold text-blue-600 dark:text-blue-400 hover:text-white dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-opacity-50 transition ease-in-out duration-300"
                >Go to Cloudflare Account</Link
                >

                <template v-else>
                    <Link
                        :href="route('login')"
                        class="block font-semibold text-blue-600 dark:text-blue-400 hover:text-white dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-opacity-50 transition ease-in-out duration-300 mb-4"
                    >Log in</Link
                    >

                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="block font-semibold text-blue-600 dark:text-blue-400 hover:text-white dark:hover:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-opacity-50 transition ease-in-out duration-300"
                    >Register</Link
                    >
                </template>
            </div>
        </div>
    </div>
</template>

<style>
.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}
@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}

.transition {
    transition: all 0.3s ease-in-out;
}

.bg-gradient-to-r {
    background-image: linear-gradient(to right, var(--tw-gradient-stops));
}

.from-blue-500 {
    --tw-gradient-from: #3b82f6;
}

.via-indigo-500 {
    --tw-gradient-stops: var(--tw-gradient-from), #6366f1, var(--tw-gradient-to, rgba(99, 102, 241, 0));
}

.to-purple-500 {
    --tw-gradient-to: #8b5cf6;
}

.dark\:from-gray-800 {
    --tw-gradient-from: #1f2937;
}

.dark\:via-gray-900 {
    --tw-gradient-stops: var(--tw-gradient-from), #111827, var(--tw-gradient-to, rgba(17, 24, 39, 0));
}

.dark\:to-black {
    --tw-gradient-to: #000000;
}

.shadow-lg {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.rounded-lg {
    border-radius: 0.5rem;
}

.hover\:scale-105:hover {
    transform: scale(1.05);
}

.focus\:ring-2:focus {
    outline: 2px solid transparent;
    outline-offset: 2px;
}

.focus\:ring-opacity-50:focus {
    opacity: 0.5;
}

.transition {
    transition: all 0.3s ease-in-out;
}
</style>
