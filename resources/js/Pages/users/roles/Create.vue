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

const role = reactive<{
    name: string| null
}>({
    name: null,
});

const throbber = useThrobber();
const toast = useToast();
const errors = ref<Record<string, string[]>>({});

const hasError = (key: string): boolean => {
    return Boolean(errors.value?.[key]?.length);
}

const save = () => {
    throbber.start();
    axios.post('/users/roles/save',{
        role
    })
    .then(() => {
        router.visit('/users/roles/index');
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
        detail: error.response?.data?.message || 'Failed to save Role',
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
            Create User Roles
        </template>

        <div class="flex flex-col items-center gap-7 mt-10">
            <FloatLabel class="w-full">
                <InputText
                    class="w-full"
                    required
                    v-model="role.name"
                    :invalid="hasError('role.name')"
                />
                <label for="name">Name</label>
            </FloatLabel>

            <div class="flex justify-center">
                <SaveButton @click="save()" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
