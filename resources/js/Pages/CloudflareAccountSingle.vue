<template>
    <Head :title="`Account: ${account.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Account: {{ account.name }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="font-semibold text-lg text-gray-800">Add New Domain</h3>
                    <input v-model="newDomain.name" placeholder="Domain Name" class="mt-2 p-2 border rounded w-full" />
                    <input v-model="newDomain.type" placeholder="Domain Type" class="mt-2 p-2 border rounded w-full" />
                    <button @click="addDomain" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">
                        Add Domain
                    </button>
                </div>


                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-lg text-gray-800">Domains</h3>
                    <table class="min-w-full bg-white">
                        <thead>
                        <tr>
                            <td class="py-2 px-4 font-semibold">Domain Name</td>
                            <td class="py-2 px-4 font-semibold">Status</td>
                            <td class="py-2 px-4 font-semibold">Type</td>
                            <td class="py-2 px-4 font-semibold">Details</td>
                            <td class="py-2 px-4 font-semibold">Delete</td>
                        </tr>
                        </thead>

                        <tbody>
                        <tr v-for="domain in domains" :key="domain.id" class="border-t">
                            <td class="py-2 px-4">{{ domain.name }}</td>
                            <td class="py-2 px-4">{{ domain.status }}</td>
                            <td class="py-2 px-4">{{ domain.type }}</td>
                            <td class="py-2 px-4">
                                <a :href="route('domains.settings', { id: domain.id, account_id: account.id })" class="text-blue-500 hover:underline">Edit</a>
                            </td>
                            <td class="py-2 px-4">
                                <a @click="deleteDomain(domain.id)" class="bg-red-600 text-white px-4 py-2 rounded cursor-pointer">
                                    Delete
                                </a>
                            </td>
                        </tr>
                        </tbody>

                        <tfoot v-if="loading">
                        <tr>
                            <td colspan="4" class="py-4">
                                <div class="flex justify-center">
                                    <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12"></div>
                                </div>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps(['account']);

const domains = ref([]);
const loading = ref(true);

const newDomain = ref({
    name: '',
    type: '',
});
const fetchDomains = async () => {
    try {
        const response = await axios.get('/domains', {
            params: {
                accountId: props.account.id,
                email: props.account.email,
                api_key: props.account.api_key,
            },
        });
        domains.value = response.data.result;
    } catch (error) {
        console.error('Failed to fetch domains', error);
    } finally {
        loading.value = false;
    }
};
const addDomain = async () => {
    try {
        const response = await axios.post('/domains', {
            type: newDomain.value.type,
            email: props.account.email,
            api_key: props.account.api_key,
            domain_name: newDomain.value.name,
        });
        await fetchDomains()
        domains.value.push(response.data.domain);
        newDomain.value.name = '';
        newDomain.value.type = '';
        alert('Domain added successfully!');
    } catch (error) {
        console.error('Failed to add domain', error);
        alert('Error adding domain');
    }
};
const deleteDomain = async (id) => {
    try {
        const response = await axios.post('/domain/delete', {
            email: props.account.email,
            api_key: props.account.api_key,
            id: id,
        });
        await fetchDomains()
        domains.value.push(response.data.domain);
        newDomain.value.name = '';
        newDomain.value.type = '';
        alert('Domain deleted successfully!');
    } catch (error) {
        console.error('Failed to add domain', error);
        alert('Error adding domain');
    }
};
onMounted(fetchDomains);

</script>

<style>
.loader {
    border-top-color: #3490dc;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
