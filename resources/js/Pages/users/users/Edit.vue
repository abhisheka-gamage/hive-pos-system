<script setup lang="ts">
import { useThrobber } from '@/stores/throbber'
import type { AxiosError, AxiosResponse } from 'axios'
import { Dropdown, InputText, useToast } from 'primevue'
import { onMounted, ref } from 'vue'
import FloatLabel from 'primevue/floatlabel'
import { router, usePage } from "@inertiajs/vue3"
import axios from 'axios'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import UpdateButton from '@/Components/UpdateButton.vue'

interface User {
    id: number|null,
    name: string|null,
    email: string|null,
    roles: Role[]
}
interface Role {
  id: number
  name: string
}

const page = usePage();
const id = page.props.id as string;
const toast = useToast();
const throbber = useThrobber();
const user = ref<User>({
    id: null,
    name: null,
    email: null,
    roles: []
});
const role = ref<number | null>(null)
const roles = ref<Role[]>([])
const errors = ref<Record<string, string[]>>({})

const getUserDetails = () => {
    throbber.setStatus(true);
    axios.post(`/users/user/${id}`)
    .then((response:AxiosResponse) => {
        user.value = response.data.data
        role.value = user.value.roles.length == 1 ? user.value.roles[0].id : null
    })
    .catch((err:unknown) => {
        const error = err as AxiosError<{message?: string}>;
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: error.message,
            life: 3000
        });
    })
    .finally(() => throbber.setStatus(false))
}

const getRoles = async () => {
    throbber.setStatus(true)
    try {
        const response: AxiosResponse = await axios.post('/users/roles/filter')
        roles.value = response.data.data
    } catch (err: unknown) {
        const error = err as AxiosError<{ message?: string; errors?: Record<string, string[]> }>
        errors.value = error.response?.data.errors || {}
        toast.add({
        severity: 'error',
        summary: 'Error',
        detail: error.response?.data?.message || 'Failed to fetch roles',
        life: 3000
        })
    } finally {
        throbber.setStatus(false)
    }
}

const save = () => {
    throbber.setStatus(true);
    axios.post(`/users/user/${id}/update`, { user: user.value, role: role.value })
    .then(() => {
        router.visit('/users/user/index');
        toast.add({
            severity: 'success',
            summary: 'Success',
            detail: 'User Upddated Successfully',
            life: 3000
        });
    })
    .catch((err:unknown) => {
        const error = err as AxiosError<{message?: string}>;
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: error.message,
            life: 3000
        });
    })
    .finally(() => throbber.setStatus(false))
}

onMounted(async ()=>{ await getUserDetails(); getRoles() })
</script>

<template>
    <AuthenticatedLayout>
        <template #header>Edit Users</template>

        <div class="flex flex-col gap-7 w-full mt-5">
            <FloatLabel class="w-full">
                <InputText class="w-full" v-model="user.name" id="name" required />
                <label for="name">Full Name</label>
            </FloatLabel>

            <FloatLabel class="w-full">
                <InputText disabled class="w-full" v-model="user.email" id="email" required />
                <label for="email">Email</label>
            </FloatLabel>

            <FloatLabel class="w-full">
                <Dropdown
                    v-model="role"
                    :options="roles"
                    option-label="name"
                    option-value="id"
                    filter class="w-full"
                />
            <label for="role">Role</label>
            </FloatLabel>

            <div class="flex justify-center">
                <UpdateButton @click="save" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
