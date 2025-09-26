<!-- eslint-disable vue/multi-word-component-names -->
<script setup lang="ts">
import { InputText, useToast } from 'primevue';
import FloatLabel from 'primevue/floatlabel';
import { reactive, ref } from 'vue';
import axios, { AxiosError } from 'axios';
import { useThrobber } from '@/stores/throbber';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from "@inertiajs/vue3"
import SaveButton from '@/Components/SaveButton.vue';

const permission = reactive<{
    name: string|null,
    code: string|null,
    icon: string|null,
}>({
    name: null,
    code: null,
    icon: null,
});

const throbber = useThrobber();
const toast = useToast();
const errors = ref<Record<string, string[]>>({});

const hasError = (key: string): boolean => {
    return Boolean(errors.value?.[key]?.length);
}

const save = () => {
    throbber.start();
    axios.post('/permissions/headers/save',{
        permission
    })
    .then(() => {
        router.visit('/permissions/headers/index');
        toast.add({
            severity:'success',
            summary: "Successful",
            detail: "Saved Successfully!",
            life: 3000,
        });
    })
    .catch((err: unknown) => {
        const error = err as AxiosError<{message?: string, errors?: Record<string, string[]>}>;
        errors.value = error.response?.data.errors || {};
        toast.add({
        severity:'error',
        summary:"Error",
        detail: error.response?.data?.message || 'Failed to save permission',
        life: 3000,
        });
    })
    .finally(() => {
        throbber.stop();
    });
}

</script>
<template>
    <AuthenticatedLayout>
        <template #header>
            Create Navbar Menu Header
        </template>

        <div class="flex flex-col gap-7 w-full mt-5">
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

            <FloatLabel class="w-full">
                <InputText
                    class="w-full"
                    required
                    v-model="permission.icon"
                    :invalid="hasError('permission.icon')"
                />
                <label for="code">Icon</label>
            </FloatLabel>
            <div class="flex justify-center">
                <SaveButton @click="save()" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
