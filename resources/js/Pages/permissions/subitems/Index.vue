<!-- eslint-disable vue/multi-word-component-names -->
<script setup lang="ts">
import axios, { AxiosError, type AxiosResponse } from 'axios'
import { ref, onMounted } from 'vue'
import { useToast } from 'primevue'
import { useThrobber } from '@/stores/throbber'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PaginatedTable from '@/Components/PaginatedTable.vue'

const toast = useToast()
const permissions = ref<Permissions[]>([])
const throbber = useThrobber()

interface Permissions {
    id: number
    display_name: string
    url: string
    created_at: string
    permissions: {
        name: string
        roles: {
            name: string
        }[]
    }
    parent: {
        display_name: string
        parent:{
            display_name: string
        }
    }
    types: {
        name: string
    }

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
    axios.post('/permissions/sub_items/index', {
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
            Navigation Sub Menus
        </template>

        <PaginatedTable
            @update:page="getPermission($event)"
            @update:search="searchTerm = $event; getPermission()"
            @update:per-page="entries = $event; getPermission()"
            :pagination-meta="paginatedMeta"
        >
            <thead>
                <tr class="border-b border-gray-500 dark:border-white">
                    <th class="p-2">Menu</th>
                    <th class="p-2">Type</th>
                    <th class="p-2">Sub Menu</th>
                    <th class="p-2">URL</th>
                    <th class="p-2">Header</th>
                    <th class="p-2">Permission</th>
                    <th class="p-2">Created At</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-gray-500 dark:border-white" v-for="(value, id) in permissions" :key="id">
                    <td class="text-center p-2">{{ value.parent.display_name }}</td>
                    <td class="text-center p-2">{{ value.types.name }}</td>
                    <td class="text-center p-2">{{ value.display_name }}</td>
                    <td class="text-center p-2">{{ value.url }}</td>
                    <td class="text-center p-2">{{ value.parent.parent.display_name }}</td>
                    <td class="text-center p-2">{{ value.permissions.name }}</td>
                    <td class="text-center p-2">{{ new Date(value.created_at).toLocaleString() }}</td>
                </tr>
            </tbody>
        </PaginatedTable>
  </AuthenticatedLayout>
</template>
