<template>
    <Head title="Manage Cloudflare Accounts" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Cloudflare Accounts</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-lg text-gray-800">Add New Cloudflare Account</h3>

                    <form @submit.prevent="addAccount">
                        <div class="mt-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Account Name</label>
                            <input v-model="account.name" id="name" type="text" required class="mt-1 p-2 block w-full border rounded" placeholder="Enter account name">
                        </div>

                        <div class="mt-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input v-model="account.email" id="email" type="email" required class="mt-1 p-2 block w-full border rounded" placeholder="Enter account email">
                        </div>

                        <div class="mt-4">
                            <label for="api_key" class="block text-sm font-medium text-gray-700">API Key</label>
                            <input v-model="account.api_key" id="api_key" type="text" required class="mt-1 p-2 block w-full border rounded" placeholder="Enter API key">
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Add Account</button>
                        </div>
                    </form>

                    <div class="mt-10">
                        <h3 class="font-semibold text-lg text-gray-800">Existing Accounts</h3>
                        <ul class="mt-4">
                            <li v-for="acc in accounts" :key="acc.id" class="p-2 border rounded mt-2">
                                <Link :href="`/cloudflare/accounts/${acc.id}`" class="text-blue-600 hover:underline">
                                    {{ acc.name }} ({{ acc.email }})
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import axios from 'axios';

// Данные для нового аккаунта
const account = ref({
    name: '',
    email: '',
    api_key: ''
});

// Список существующих аккаунтов
const accounts = ref([]);

// Функция для получения списка аккаунтов
const fetchAccounts = async () => {
    try {
        const response = await axios.get('/cloudflare/get-accounts');
        accounts.value = response.data.accounts;
    } catch (error) {
        console.error('Failed to fetch accounts', error);
    }
};

// Получение списка аккаунтов при загрузке компонента
onMounted(fetchAccounts);

// Функция для добавления нового аккаунта
const addAccount = async () => {
    try {
        await axios.post('/cloudflare/accounts', account.value);

        // Очищаем форму после добавления
        account.value.name = '';
        account.value.email = '';
        account.value.api_key = '';

        // Повторно загружаем список аккаунтов после добавления нового
        await fetchAccounts();

        alert('Account added successfully');
    } catch (error) {
        console.error('Failed to add account', error);
        alert('Error adding account');
    }
};
</script>
