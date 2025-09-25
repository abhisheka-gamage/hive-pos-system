<!-- eslint-disable vue/multi-word-component-names -->
<script setup lang="ts">
import axios, { AxiosError, type AxiosResponse } from 'axios'
import { ref, computed, onMounted } from 'vue'
import { Badge, useToast } from 'primevue'
import { useThrobber } from '@/stores/throbber'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PaginatedTable from '@/Components/PaginatedTable.vue'
import RetailerSelect from '@/Components/filters/RetialerSelect.vue'
import SearchButton from '@/Components/SearchButton.vue'
import ProductSelect from '@/Components/filters/ProductSelect.vue'
import Status from '@/Components/filters/Status.vue'

const toast = useToast()
const batches = ref<Batch[]>([])
const throbber = useThrobber()
const searchFitler = ref<{
    retailer: number|null,
    product: number|null,
    search: string|null,
    status: boolean|null
}>({
    retailer: null,
    product: null,
    search: null,
    status: null
});

interface Batch {
    batch_code: string,
    retail_price : number,
    selling_price : number,
    effective_from: string,
    effective_to: string,
    status: boolean
    product: {
        id: number,
        name: string,
        code: string,
        retailer: {
            name:string
        }
    }
}

const paginatedMeta = ref({
    current_page: 1,
    last_page: 0,
    per_page: 10,
    total: 0,
    data: [] as Batch[],
})

const entries = ref(10)

const getBatches = (page:number = 1) => {
    throbber.setStatus(true)
    axios.post('/products/batches/index', {
        entries: entries.value,
        page: page,
        search_filter: searchFitler.value
    })
    .then((response: AxiosResponse) => {
        batches.value = response.data.data.data

        paginatedMeta.value = {
            current_page: response.data.data.current_page,
            last_page: response.data.data.last_page,
            per_page: response.data.data.per_page,
            total: response.data.data.total,
            data: batches.value,
        }
    })
    .catch((err: unknown) => {
        const error = err as AxiosError<{ message?: string }>
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: error.response?.data?.message || 'Failed to fetch batch data',
            life: 3000,
        })
    })
    .finally(() => {
        throbber.setStatus(false)
    })
}
onMounted(() => getBatches())
</script>
<template>
    <AuthenticatedLayout>
        <template #header>
            View Batches
        </template>

        <div class="grid grid-cols-3 mb-10 m-3 gap-5">
            <RetailerSelect v-model="searchFitler.retailer" />

            <ProductSelect v-model="searchFitler.product" :retailer_id="searchFitler.retailer"/>

            <Status v-model="searchFitler.status" />
            <div></div>
            <div></div>
            <div class="flex justify-end">
                <SearchButton @click="getBatches" />
            </div>
        </div>

        <PaginatedTable
            @update:page="getBatches($event)"
            @update:search="searchFitler.search = $event; getBatches()"
            @update:per-page="entries = $event; getBatches()"
            :pagination-meta="paginatedMeta"
        >
            <thead>
                <tr class="border-b border-gray-500 dark:border-white">
                    <th class="p-2">Batch Code</th>
                    <th class="p-2">Retailer</th>
                    <th class="p-2">Product</th>
                    <th class="p-2">Retail Price</th>
                    <th class="p-2">Selling Price</th>
                    <th class="p-2">Effective From</th>
                    <th class="p-2">Effective To</th>
                    <th class="p-2">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="batches.length" class="border-b border-gray-500 dark:border-white" v-for="(value, id) in batches" :key="id">
                    <td class="text-center p-2">{{ value.batch_code }}</td>
                    <td class="text-center p-2">{{ value.product.retailer.name }}</td>
                    <td class="text-center p-2">{{ value.product.name }}</td>
                    <td class="text-center p-2">{{ value.retail_price.toFixed(2) }}</td>
                    <td class="text-center p-2">{{ value.selling_price.toFixed(2) }}</td>
                    <td class="text-center p-2">{{ new Date(value.effective_from).toLocaleDateString() }}</td>
                    <td class="text-center p-2">{{ new Date(value.effective_to).toLocaleDateString() }}</td>
                    <td class="text-center p-2">
                        <Badge v-if="value.status" severity="success">Active</Badge>
                        <Badge v-else severity="danger">Inactive</Badge>
                    </td>
                </tr>
                <tr v-else>
                    <td class="p-3 text-center text-gray-500" colspan="7">No Items Found</td>
                </tr>
            </tbody>
        </PaginatedTable>
    </AuthenticatedLayout>
</template>
