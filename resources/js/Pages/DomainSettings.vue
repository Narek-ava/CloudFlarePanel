<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Domain Settings: {{ domainId }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- SSL/TLS Management -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <h3 class="font-semibold text-lg text-gray-800">Manage SSL/TLS Mode</h3>
                        <select v-model="sslMode" class="mt-2 p-2 border rounded">
                            <option value="off">Off</option>
                            <option value="flexible">Flexible</option>
                            <option value="full">Full</option>
                            <option value="strict">Full (strict)</option>
                        </select>
                        <button @click="updateSSLMode" class="ml-4 bg-blue-500 text-white px-4 py-2 rounded">
                            Update SSL Mode
                        </button>
                    </div>
                </div>

                <!-- Page Rules Management -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <h3 class="font-semibold text-lg text-gray-800">Manage Page Rules</h3>
                        <input v-model="urlPattern" placeholder="URL Pattern" class="mt-2 p-2 border rounded w-full" />

                        <label class="mt-2 font-semibold text-gray-800">Actions</label>
                        <div class="mt-2">
                            <div v-for="action in availableActions" :key="action.value" class="flex items-center">
                                <input
                                    type="checkbox"
                                    :value="action.value"
                                    v-model="selectedActions"
                                    class="mr-2"
                                />
                                <label>{{ action.text }}</label>
                            </div>
                        </div>

                        <button @click="createPageRule" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">
                            Create Page Rule
                        </button>
                    </div>
                </div>

                <!-- Existing Page Rules -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <h3 class="font-semibold text-lg text-gray-800">Existing Page Rules</h3>
                        <ul>
                            <li v-for="rule in pageRules" :key="rule.id" class="mt-2 p-2 border rounded">
                                <strong>URL Pattern:</strong> {{ rule.targets[0].constraint.value }}<br />
                                <strong>Actions:</strong>
                                <ul>
                                    <li v-for="action in rule.actions" :key="action.id">
                                        {{ action.id }}: {{ action.value || 'N/A' }}
                                    </li>
                                </ul>
                                <button @click="deletePageRule(rule.id)" class="mt-2 bg-red-500 text-white px-4 py-2 rounded">
                                    Delete
                                </button>
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
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

const page = usePage();

const domainId = ref(page.props.domainId);
const account_id = ref(page.props.account_id);

const sslMode = ref('full');
const urlPattern = ref('');
const selectedActions = ref([]);
const pageRules = ref([]);
const availableActions = ref([

    {value:'browser_check', text: 'Browser Check'},
    { value: 'forwarding_url', text: 'Forwarding URL' },
    { value: 'always_use_https', text: 'Always Use HTTPS' },
    { value: 'cache_level', text: 'Cache Level' },
    { value: 'browser_cache_ttl', text: 'Browser Cache TTL' },
    { value: 'disable_apps', text: 'Disable Apps' },
    { value: 'disable_performance', text: 'Disable Performance' },
    { value: 'ssl', text: 'SSL/TLS' },
]);

const fetchPageRules = async () => {
    try {
        const response = await axios.get(`/cloudflare/${domainId.value}/page-rules`, {
            params: { account_id: account_id.value }
        });

        pageRules.value = response.data;
    } catch (error) {
        console.error('Failed to fetch page rules', error);
    }
};

const updateSSLMode = async () => {
    try {
        const response = await axios.post(`/cloudflare/${domainId.value}/ssl-mode`, { ssl_mode: sslMode.value,  account_id: account_id.value });
        alert(response.data.message);
    } catch (error) {
        console.error('Failed to update SSL/TLS mode', error);
        alert('Error updating SSL/TLS mode');
    }
};

const createPageRule = async () => {
    try {
        const actionsFormatted = selectedActions.value.map(action => {
            return { id: action, value: 'on' };
        });

        const response = await axios.post(`/cloudflare/${domainId.value}/page-rule`, {
            url_pattern: urlPattern.value,
            actions: actionsFormatted,
            account_id: account_id.value
        });
        alert(response.data.message);
        await fetchPageRules();

    } catch (error) {
        console.error('Failed to create page rule', error);
        alert(error);
    }
};

const deletePageRule = async (ruleId) => {
    try {
        const response = await axios.delete(`/cloudflare/${domainId.value}/page-rule/${ruleId}`, {
            params: { account_id: account_id.value }
        });
        alert(response.data.message);
        await fetchPageRules();
    } catch (error) {
        console.error('Failed to delete page rule', error);
        alert('Error deleting page rule');
    }
};

onMounted(() => {
    fetchPageRules();
});
</script>
