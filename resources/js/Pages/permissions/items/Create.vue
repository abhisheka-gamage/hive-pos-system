<!-- eslint-disable vue/multi-word-component-names -->
<script setup lang="ts">
import { LockClosedIcon } from '@heroicons/vue/24/solid'
import { Dropdown, InputText, useToast } from 'primevue'
import Card from 'primevue/card'
import FloatLabel from 'primevue/floatlabel'
import { onMounted, reactive, ref } from 'vue'
import axios, { AxiosError, type AxiosResponse } from 'axios'
import { useThrobber } from '@/stores/throbber'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { router } from "@inertiajs/vue3"
import SaveButton from '@/Components/SaveButton.vue'

const permission = reactive<{
    parent: number | null
    name: string | null
    code: string | null
}>({
    parent: null,
    name: null,
    code: null,
})

const throbber = useThrobber()
const toast = useToast()
const errors = ref<Record<string, string[]>>({})
const parent_options = ref([])

const hasError = (key: string): boolean => {
  return Boolean(errors.value?.[key]?.length)
}

const save = () => {
  throbber.setStatus(true)
    axios.post('/permissions/items/save', {
        permission,
    })
    .then(() => {
        router.visit('/permissions/items/index')
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
        throbber.setStatus(false)
    })
}

onMounted(() => {
    getParents()
})

const getParents = () => {
    throbber.setStatus(true)
    axios.post('/permissions/headers/filter')
    .then((response: AxiosResponse) => {
        parent_options.value = response.data.data
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
      throbber.setStatus(false)
    })
}
</script>
<template>
    <AuthenticatedLayout>
        <template #header>
            Create Navigation Menus
        </template>
        <div class="flex flex-col gap-7 w-full mt-5">
            <FloatLabel class="w-full">
                <Dropdown
                    class="w-full"
                    :options="parent_options"
                    filter
                    v-model="permission.parent"
                    option-label="code"
                    option-value="id"
                    :invalid="hasError('permission.parent')"
                />
                <label for="parent">Parent</label>
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
                <InputText
                    class="w-full"
                    required
                    v-model="permission.code"
                    :invalid="hasError('permission.code')"
                />
                <label for="code">Code</label>
            </FloatLabel>

            <div class="flex justify-center">
                <SaveButton @click="save()" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
