<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed, onMounted } from 'vue'
import { useToast } from 'primevue'
import { useThrobber } from '@/stores/throbber'
import PaginatedTable from '@/Components/PaginatedTable.vue'
import axios, { AxiosError, AxiosResponse } from 'axios';
import { router } from '@inertiajs/vue3';

interface User {
    id: number,
    name: string,
    email:string,
    created_at: string,
    roles: {
        name: string
    }[]
}

const toast = useToast()
const users = ref<User[]>([])
const throbber = useThrobber()


const paginatedMeta = ref({
    current_page: 1,
    last_page: 0,
    per_page: 10,
    total: 0,
    data: [] as User[],
})

const entries = ref(10)
const searchTerm = ref('')

const getUsers = (page:number = 1) => {
    throbber.setStatus(true)
    axios.post('/users/user/index', {
        entries: entries.value,
        page: page,
        search: searchTerm.value,
    })
    .then((response: AxiosResponse) => {
        users.value = response.data.data.data

        paginatedMeta.value = {
            current_page: response.data.data.current_page,
            last_page: response.data.data.last_page,
            per_page: response.data.data.per_page,
            total: response.data.data.total,
            data: users.value,
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

onMounted(() => getUsers())
</script>
<template>
    <AuthenticatedLayout>
        <template #header>
            View Users
        </template>
        <PaginatedTable
            @update:page="getUsers($event)"
            @update:search="searchTerm = $event; getUsers()"
            @update:per-page="entries = $event; getUsers()"
            :pagination-meta="paginatedMeta"
        >
            <thead>
                <tr class="border-b border-gray-500 dark:border-white">
                    <th class="p-2">Name</th>
                    <th class="p-2">E-Mail</th>
                    <th class="p-2">Role</th>
                    <th class="p-2">Created At</th>
                    <th class="p-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-gray-500 dark:border-white" v-for="(value, id) in users" :key="id">
                    <td class="text-center p-2">{{ value.name }}</td>
                    <td class="text-center p-2">{{ value.email }}</td>
                    <td class="text-center p-2"><div v-for="roles in value.roles" :key="roles.name">{{ roles.name }}</div></td>
                    <td class="text-center p-2">{{ new Date(value.created_at).toLocaleString() }}</td>
                    <td class="text-center p-2">
                        <a target="_blank" :href="route('users-edit', value.id)">
                            <i class="pi pi-pencil text-sky-500 hover:scale-105 transition ease-in-out cursor-pointer"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </PaginatedTable>
    </AuthenticatedLayout>
</template>
