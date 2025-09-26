<script setup lang="ts">
import { useThrobber } from '@/stores/throbber'
import type { AxiosError, AxiosResponse } from 'axios'
import { Checkbox, IconField, InputIcon, InputText, useToast } from 'primevue'
import { computed, onMounted, ref, watch } from 'vue'
import FloatLabel from 'primevue/floatlabel'
import { router, usePage } from "@inertiajs/vue3"
import axios from 'axios'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

interface Permissions {
    id: number
    type: string
    data:{
        id: number,
        name: string,
        guard_name: string,
        checked?: boolean,
    }[]
}

interface Role {
    name: string|null,
    created_at: Date,
    permissions: Permissions[],
}

const page = usePage();
const id = page.props.id as string;
const toast = useToast();
const throbber = useThrobber();
const role = ref<Role>({ name: null, created_at: new Date, permissions: [] });
const permissions = ref<Permissions[]>([]);
const searchTerms = ref<Record<string, string>>({});
const allSelected = ref<Record<string, boolean>>({});

const getRoleDetails = () => {
    throbber.start();
    axios.post(`/users/roles/${id}`)
    .then((response:AxiosResponse) => role.value = response.data.data)
    .catch((err:unknown) => {
        const error = err as AxiosError<{message?: string}>;
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: error.message,
            life: 3000
        });
    })
    .finally(() => throbber.stop())
}

const getPermissions  = () => {
    throbber.start();
    axios.post('/permissions/index')
    .then((response:AxiosResponse) => {
        permissions.value = response.data.data.map((data: any) => {
            const items = Object.values(data.menus).map((item: any) => ({
                id: item.id,
                name: item.permissions.name,
                checked: role.value.permissions.some(p => p.id ===  item.permissions.id),
            }));
            allSelected.value[data.name] = items.every(i => i.checked);
            return { type: data.name, data: items };
        })
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
    .finally(()=>throbber.stop())
}

const toggleAll = (type: string, value: boolean) => {
    const group = permissions.value.find(g => g.type === type);
    if (!group) return;
    group.data.forEach(item => item.checked = value);
    allSelected.value[type] = value;
}

const updateAllSelected = (type: string) => {
    const group = permissions.value.find(g => g.type === type);
    if (!group) return;
    allSelected.value[type] = group.data.every(i => i.checked);
}

watch(permissions, (newPermissions) => {
    newPermissions.forEach(group => updateAllSelected(group.type));
}, { deep: true })

const filteredPermissions = computed(() => {
    return permissions.value.map(group => {
        const term = (searchTerms.value[group.type] || "").toLowerCase().trim();
        if (!term) return group;
        return { ...group, data: group.data.filter(item => item.name.toLowerCase().includes(term)) };
    })
})

const save = () => {
    throbber.start();
    axios.post(`/users/roles/${id}/update`, { permissions: permissions.value })
    .then(() => {
        router.visit('/users/roles/index');
        toast.add({
            severity: 'success',
            summary: 'Success',
            detail: 'User Role Saved Successfully',
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
    .finally(() => throbber.stop())
}

onMounted(async ()=>{ await getRoleDetails(); getPermissions() })
</script>

<template>
    <AuthenticatedLayout>
        <template #header>Edit Roles</template>

        <div class="grid grid-cols-2 m-5 gap-5 mb-20">
            <FloatLabel>
                <InputText class="w-full" disabled v-model="role.name" />
                <label>Role Name</label>
            </FloatLabel>
            <FloatLabel>
                <input disabled class="w-full p-inputtext p-component p-filled" :value="new Date(role.created_at).toLocaleString()" />
                <label>Created At</label>
            </FloatLabel>
        </div>
        <div>
            <div class="flex flex-col gap-5">
                <div v-for="group in filteredPermissions" :key="group.type" class="border flex h-[200px] rounded">
                    <div class="flex flex-col items-center w-1/4 border-r p-2 justify-between">
                        <div class="flex items-center gap-2">
                            <IconField>
                                <InputIcon class="pi pi-search" />
                                <InputText v-model="searchTerms[group.type]" placeholder="Search..." class="w-full h-8 text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"/>
                            </IconField>
                            <Checkbox
                                v-model="allSelected[group.type]"
                                binary
                                @update:modelValue="toggleAll(group.type, $event)"
                            />
                        </div>
                        <div class="font-bold mb-2">{{ group.type }}</div>
                        <div></div>
                    </div>
                    <div class="ms-2 w-full overflow-y-scroll p-5">
                        <div v-if="group.data.length > 0" v-for="permission in group.data" :key="permission.id" class="flex items-center gap-5 py-3 border-b">
                            <Checkbox v-model="permission.checked" binary />
                            <div>{{ permission.name }}</div>
                        </div>
                        <div v-else class="text-gray-500">No Items found</div>
                    </div>
                </div>
            </div>
            <div class="mt-5 flex justify-center">
                <button @click="save()" class="bg-green-700 text-white px-6 py-2 shadow rounded me-3 transition hover:scale-105 ease-in-out">Save</button>
                <button @click="getPermissions()" class="bg-black text-white px-6 py-2 shadow rounded me-3 transition hover:scale-105 ease-in-out">Reset</button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
