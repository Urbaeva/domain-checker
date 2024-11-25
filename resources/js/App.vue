<template>
    <div id="app">
        <h1>Domain Checker</h1>
        <input v-model="domains" placeholder="Enter domains, separated by commas" />
        <button @click="checkDomains">Check Domains</button>
        <ul>
            <li v-for="(result, index) in results" :key="index">
                {{ result.domain }}: {{ result.status }}
                <span v-if="result.expiry_date"> (Expiry: {{ result.expiry_date }})</span>
            </li>
        </ul>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            domains: '',
            results: [],
        };
    },
    methods: {
        async checkDomains() {
            this.results = [];
            const domainList = this.domains.split(',').map(d => d.trim());
            const responses = await Promise.all(
                domainList.map(domain => axios.post('/api/check-domain', { domain }))
            );
            this.results = responses.map(response => response.data);
        },
    },
};
</script>
