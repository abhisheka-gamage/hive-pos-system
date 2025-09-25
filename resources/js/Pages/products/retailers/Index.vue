<!-- eslint-disable vue/multi-word-component-names -->
<script setup lang="ts">
import axios, { AxiosError, type AxiosResponse } from 'axios'
import { ref, computed, onMounted } from 'vue'
import { useToast } from 'primevue'
import { useThrobber } from '@/stores/throbber'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PaginatedTable from '@/Components/PaginatedTable.vue'

const toast = useToast()
const retailers = ref<Retailer[]>([])
const throbber = useThrobber()

interface Retailer {
    name: string,
    code: string,
    created_at: string
}

const paginatedMeta = ref({
    current_page: 1,
    last_page: 0,
    per_page: 10,
    total: 0,
    data: [] as Retailer[],
})

const entries = ref(10)
const searchTerm = ref('')

const getRetailers = (page:number = 1) => {
    throbber.setStatus(true)
    axios.post('/products/retailers/index', {
        entries: entries.value,
        page: page,
        search: searchTerm.value,
    })
    .then((response: AxiosResponse) => {
        retailers.value = response.data.data.data

        paginatedMeta.value = {
            current_page: response.data.data.current_page,
            last_page: response.data.data.last_page,
            per_page: response.data.data.per_page,
            total: response.data.data.total,
            data: retailers.value,
        }
    })
    .catch((err: unknown) => {
        const error = err as AxiosError<{ message?: string }>
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: error.response?.data?.message || 'Failed to fetch permissions',
            life: 3000,
        })
    })
    .finally(() => {
        throbber.setStatus(false)
    })
}
onMounted(() => getRetailers())
</script>
<template>
    <AuthenticatedLayout>
        <template #header>
            View Retailers
        </template>
        <PaginatedTable
            @update:page="getRetailers($event)"
            @update:search="searchTerm = $event; getRetailers()"
            @update:per-page="entries = $event; getRetailers()"
            :pagination-meta="paginatedMeta"
        >
            <thead>
                <tr class="border-b border-gray-500 dark:border-white">
                    <th class="p-2">Name</th>
                    <th class="p-2">Code</th>
                    <th class="p-2">Created At</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="retailers.length" class="border-b border-gray-500 dark:border-white" v-for="(value, id) in retailers" :key="id">
                    <td class="text-center p-2">{{ value.name }}</td>
                    <td class="text-center p-2">{{ value.code }}</td>
                    <td class="text-center p-2">{{ new Date(value.created_at).toLocaleString() }}</td>
                </tr>
                <tr v-else>
                    <td class="p-3 text-center text-gray-500" colspan="3">No Items Found</td>
                </tr>
            </tbody>
        </PaginatedTable>
    </AuthenticatedLayout>
</template>
