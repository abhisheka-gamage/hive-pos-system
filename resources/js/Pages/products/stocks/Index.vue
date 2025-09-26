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
import BatchSelect from '@/Components/filters/BatchSelect.vue'

const toast = useToast()
const stocks = ref<Stock[]>([])
const throbber = useThrobber()
const searchFitler = ref<{
    retailer: number|null,
    product: number|null,
    search: string|null,
    batch: number|null
}>({
    retailer: null,
    product: null,
    search: null,
    batch: null
});

interface Stock {
    amount: number,
    batch: {
        batch_code: string,
        selling_price: number
    },
    product: {
        id: number,
        name: string,
        code: string,
    }
}

const paginatedMeta = ref({
    current_page: 1,
    last_page: 0,
    per_page: 10,
    total: 0,
    data: [] as Stock[],
})

const entries = ref(10)

const getBatches = (page:number = 1) => {
    throbber.start()
    axios.post('/products/stocks/index', {
        entries: entries.value,
        page: page,
        search_filter: searchFitler.value
    })
    .then((response: AxiosResponse) => {
        stocks.value = response.data.data.data

        paginatedMeta.value = {
            current_page: response.data.data.current_page,
            last_page: response.data.data.last_page,
            per_page: response.data.data.per_page,
            total: response.data.data.total,
            data: stocks.value,
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
        throbber.stop()
    })
}
onMounted(() => getBatches())
</script>
<template>
    <AuthenticatedLayout>
        <template #header>
            View Stock
        </template>

        <div class="grid grid-cols-3 mb-10 m-3 gap-5">
            <RetailerSelect v-model="searchFitler.retailer" />

            <ProductSelect v-model="searchFitler.product" :retailer_id="searchFitler.retailer"/>

            <BatchSelect v-model="searchFitler.batch" :product_id="searchFitler.product"/>

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
                    <th class="p-2">Product</th>
                    <th class="p-2">Product Code</th>
                    <th class="p-2">Batch</th>
                    <th class="p-2">Selling</th>
                    <th class="p-2">Remaining</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="stocks.length" class="border-b border-gray-500 dark:border-white" v-for="(value, id) in stocks" :key="id">
                    <td class="text-center p-2">{{ value.product.name }}</td>
                    <td class="text-center p-2">{{ value.product.code }}</td>
                    <td class="text-center p-2">{{ value.batch.batch_code }}</td>
                    <td class="text-center p-2">{{ value.batch.selling_price }}</td>
                    <td class="text-center p-2">{{ value.amount }}</td>
                </tr>
                <tr v-else>
                    <td class="p-3 text-center text-gray-500" colspan="7">No Items Found</td>
                </tr>
            </tbody>
        </PaginatedTable>
    </AuthenticatedLayout>
</template>
