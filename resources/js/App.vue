<template>
    <div id="app" class="min-h-screen bg-gray-100 flex flex-col items-center justify-center p-6">
        <div class="w-full max-w-md bg-white shadow-md rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Domain Checker</h1>
            <input
                v-model="domains"
                placeholder="Enter domains, separated by commas"
                class="w-full border border-gray-300 rounded-md p-2 mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <button
                @click="checkDomains"
                :disabled="isLoading"
                class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition disabled:opacity-50"
            >
                Check Domains
            </button>

            <!-- Loading State -->
            <div v-if="isLoading" class="mt-4 text-center text-gray-500">
                <svg
                    class="animate-spin h-6 w-6 mx-auto"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    ></circle>
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 2.042.654 3.927 1.757 5.443l2.243-2.152z"
                    ></path>
                </svg>
                Checking domains...
            </div>

            <!-- Results -->
            <ul class="mt-4 bg-white rounded-lg shadow-lg divide-y divide-gray-200" v-if="results.length">
                <li
                    v-for="(result, index) in results"
                    :key="index"
                    class="px-4 py-3 flex items-center justify-between"
                >
                    <div>
                        <span class="font-semibold text-gray-800">{{ result.domain }}: </span>
                        <span
                            :class="{
                                'text-green-500 font-medium': result.status === 'Available',
                                'text-red-500 font-medium': result.status === 'Unavailable',
                            }"
                        >
                            {{ result.status }}
                        </span>
                    </div>
                    <div v-if="result.expiry_date" class="text-sm text-gray-500">
                        (Expiry: {{ new Date(result.expiry_date).toLocaleDateString('en-US', {
                        weekday: 'short',
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    }) }})
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>


<script>
import axios from 'axios';

export default {
    data() {
        return {
            domains: '',
            results: [],
            isLoading: false,
        };
    },
    methods: {
        async checkDomains() {
            this.results = [];
            this.isLoading = true;
            try {
                const domainList = this.domains.split(',').map((d) => d.trim());
                const responses = await Promise.all(
                    domainList.map((domain) =>
                        axios.post('/api/check-domain', { domain })
                    )
                );
                this.results = responses.map((response) => response.data);
            } catch (error) {
                console.error('Error fetching domain data:', error);
            } finally {
                this.isLoading = false;
            }
        },
    },
};
</script>

