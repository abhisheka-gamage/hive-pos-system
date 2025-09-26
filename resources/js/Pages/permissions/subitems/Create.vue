<!-- eslint-disable vue/multi-word-component-names -->
<script setup lang="ts">
import { Dropdown, InputText, useToast } from 'primevue'
import FloatLabel from 'primevue/floatlabel'
import { onMounted, reactive, ref, watch } from 'vue'
import axios, { AxiosError, type AxiosResponse } from 'axios'
import { useThrobber } from '@/stores/throbber'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { router } from '@inertiajs/vue3'
import SaveButton from '@/Components/SaveButton.vue'

interface HeaderItem {
    id: number
    code: string
}

interface TypeItem {
    id: number
    name: string
}

const permission = reactive<{
    header: number | null
    parent: number | null
    name: string | null
    permission_name: string | null
    url: string | null
    type: number | null
}>({
    header: null,
    parent: null,
    name: null,
    permission_name: null,
    type: null,
    url: null,
})

const throbber = useThrobber()
const toast = useToast()
const errors = ref<Record<string, string[]>>({})
const header_options = ref([])
const items_options = ref<HeaderItem[]>([])
const type_options = ref<TypeItem[]>([])

const save = () => {
    throbber.start()
    axios.post('/permissions/sub_items/save', {
        permission,
    })
    .then(() => {
        router.visit('/permissions/sub_items/index')
        toast.add({
            severity: 'success',
            summary: 'Successful',
            detail: 'Saved Successfully!',
            life: 3000,
        })
    })
    .catch((err: unknown) => {
        const error = err as AxiosError<{ message?: string; errors?: Record<string, string[]> }>
        errors.value = error.response?.data.errors || {}
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: error.response?.data?.message || 'Failed to save permission',
            life: 3000,
        })
    })
    .finally(() => {
        throbber.stop()
    })
}

const getHeaders = () => {
    throbber.start()
    axios.post('/permissions/headers/filter')
    .then((response: AxiosResponse) => {
        header_options.value = response.data.data
    })
    .catch((err: unknown) => {
        const error = err as AxiosError<{ message?: string; errors?: Record<string, string[]> }>
        errors.value = error.response?.data.errors || {}
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

const getItems = () => {
    throbber.start()
    axios.post('/permissions/items/filter', { parent: permission.header })
    .then((response: AxiosResponse) => {
        items_options.value = response.data.data
    })
    .catch((err: unknown) => {
        const error = err as AxiosError<{ message?: string; errors?: Record<string, string[]> }>
        errors.value = error.response?.data.errors || {}
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

const getTypes = () => {
    throbber.start()
    axios.post('/permissions/types/filter')
    .then((response: AxiosResponse) => {
        type_options.value = response.data.data
    })
    .catch((err: unknown) => {
        const error = err as AxiosError<{ message?: string; errors?: Record<string, string[]> }>
        errors.value = error.response?.data.errors || {}
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

const hasError = (key: string): boolean => {
    return Boolean(errors.value?.[key]?.length)
}

onMounted(() => {
    getHeaders()
    getTypes()
});

watch([() => permission.parent, () => permission.type], () => {
    const item = items_options.value.find(i => i.id === permission.parent)
    const type = type_options.value.find(t => t.id === permission.type)

    permission.permission_name = `${item?.code?.toLowerCase() ?? ''}-${type?.name?.toLowerCase() ?? ''}`
})

</script>
<template>
    <AuthenticatedLayout>
        <template #header>
            Create Navigation Sub Menus
        </template>

        <div class="flex flex-col items-center gap-6 mt-6">
            <FloatLabel class="w-full">
                <Dropdown
                    class="w-full"
                    :options="header_options"
                    filter
                    v-model="permission.header"
                    option-label="code"
                    option-value="id"
                    :invalid="hasError('permission.header')"
                    @change="getItems()"
                />
                <label for="parent">Navbar Header</label>
            </FloatLabel>

            <FloatLabel class="w-full">
                <Dropdown
                    class="w-full"
                    :options="items_options"
                    filter
                    v-model="permission.parent"
                    option-label="code"
                    option-value="id"
                    :invalid="hasError('permission.parent')"
                />
                <label for="parent">Navbar Item</label>
            </FloatLabel>

            <FloatLabel class="w-full">
                <InputText
                    class="w-full"
                    required
                    v-model="permission.name"
                    :invalid="hasError('permission.name')"
                />
                <label for="name">Name</label>
            </FloatLabel>

            <FloatLabel class="w-full">
                <Dropdown
                    class="w-full"
                    :options="type_options"
                    filter
                    v-model="permission.type"
                    option-label="name"
                    option-value="id"
                    :invalid="hasError('permission.parent')"
                />
                <label for="parent">Permission Type</label>
            </FloatLabel>

            <FloatLabel class="w-full">
                <InputText
                    class="w-full"
                    v-model="permission.permission_name"
                    disabled
                    :invalid="hasError('permission.permission_name')"
                />
                <label for="permission">Permission</label>
            </FloatLabel>

            <FloatLabel class="w-full">
                <InputText
                    class="w-full"
                    v-model="permission.url"
                    :invalid="hasError('permission.url')"
                />
                <label for="url">URL</label>
            </FloatLabel>

            <div class="flex justify-center">
                <SaveButton @click="save()" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
