<!-- eslint-disable vue/multi-word-component-names -->
<script setup lang="ts">
import axios, { AxiosError, type AxiosResponse } from 'axios'
import { ref, computed, onMounted } from 'vue'
import { useToast } from 'primevue'
import { useThrobber } from '@/stores/throbber'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PaginatedTable from '@/Components/PaginatedTable.vue'

const toast = useToast()
const permissions = ref<Permissions[]>([])
const throbber = useThrobber()

interface Permissions {
    display_name: string
    code: string
    icon: string
    created_at: string
}

const paginatedMeta = ref({
    current_page: 1,
    last_page: 0,
    per_page: 10,
    total: 0,
    data: [] as Permissions[],
})

const entries = ref(10)
const searchTerm = ref('')

const getPermission = (page:number = 1) => {
    throbber.setStatus(true)
    axios.post('/permissions/headers/index', {
        entries: entries.value,
        page: page,
        search: searchTerm.value,
    })
    .then((response: AxiosResponse) => {
        permissions.value = response.data.data.data

        paginatedMeta.value = {
            current_page: response.data.data.current_page,
            last_page: response.data.data.last_page,
            per_page: response.data.data.per_page,
            total: response.data.data.total,
            data: permissions.value,
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
onMounted(() => getPermission())
</script>
<template>
    <AuthenticatedLayout>
        <template #header>
            Navigation Menu Headers
        </template>
        <PaginatedTable
            @update:page="getPermission($event)"
            @update:search="searchTerm = $event; getPermission()"
            @update:per-page="entries = $event; getPermission()"
            :pagination-meta="paginatedMeta"
        >
            <thead>
                <tr class="border-b border-gray-500 dark:border-white">
                    <th class="p-2">Display Name</th>
                    <th class="p-2">Code</th>
                    <th class="p-2">Icon</th>
                    <th class="p-2">Created At</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-gray-500 dark:border-white" v-for="(value, id) in permissions" :key="id">
                    <td class="text-center p-2">{{ value.display_name }}</td>
                    <td class="text-center p-2">{{ value.code }}</td>
                    <td class="text-center p-2">
                        <span v-if="value.icon">
                            <i :class="value.icon"></i>
                            ( {{ value.icon }} )
                        </span>
                        <span v-else>-</span></td>
                    <td class="text-center p-2">{{ new Date(value.created_at).toLocaleString() }}</td>
                </tr>
            </tbody>
        </PaginatedTable>
    </AuthenticatedLayout>
</template>
