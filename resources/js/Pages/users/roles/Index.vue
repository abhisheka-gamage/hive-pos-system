<script setup lang="ts">
import PaginatedTable from '@/Components/PaginatedTable.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useThrobber } from '@/stores/throbber'
import { PencilIcon } from '@heroicons/vue/24/solid'
import type { AxiosError, AxiosResponse } from 'axios'
import axios from 'axios'
import { useToast } from 'primevue'
import { computed, onMounted, ref } from 'vue'

interface Roles {
    id: number
    name: string
    created_at: string
}

const toast = useToast()
const entries = ref(10)
const searchTerm = ref('')
const throbber = useThrobber()
const paginatedMetaWithData = computed(() => paginatedMeta.value)
const roles = ref<Roles[]>([])

const paginatedMeta = ref({
    current_page: 1,
    last_page: 0,
    per_page: 10,
    total: 0,
    data: [] as Roles[],
})

const getUserRoles = (page:number = 1) => {
    throbber.start();
    axios.post('/users/roles/index', {
        entries: entries.value,
        page: page,
        search: searchTerm.value,
    })
    .then((response: AxiosResponse) => {
        roles.value = response.data.data.data

        paginatedMeta.value = {
            current_page: response.data.data.current_page,
            last_page: response.data.data.last_page,
            per_page: response.data.data.per_page,
            total: response.data.data.total,
            data: roles.value,
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
        throbber.stop()
    })
}

onMounted(() => getUserRoles())
</script>
<template>
    <AuthenticatedLayout>
        <template #header>
            User Roles
        </template>
        <PaginatedTable
            @update:page="getUserRoles($event)"
            @update:search="searchTerm = $event; getUserRoles()"
            @update:per-page="entries = $event; getUserRoles()"
            :pagination-meta="paginatedMeta"
        >
            <thead>
                <tr class="border-b border-gray-500 dark:border-white">
                    <th class="p-2">Name</th>
                    <th class="p-2">Created At</th>
                    <th class="p-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-gray-500 dark:border-white" v-for="(value, id) in roles" :key="id">
                    <td class="text-center p-2">{{ value.name }}</td>
                    <td class="text-center p-2">{{ new Date(value.created_at).toLocaleString() }}</td>
                    <td class="text-center p-2">
                        <a class="flex justify-center items-center" target="_blank" :href="route('user_roles-edit', value.id)">
                        <PencilIcon
                            class="w-6 h-6 text-green-500 hover:scale-125 transition ease-in-out"
                        />
                        </a>
                    </td>
                </tr>
            </tbody>
        </PaginatedTable>
    </AuthenticatedLayout>
</template>
