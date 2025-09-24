<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useThrobber } from '@/stores/throbber';
import axios, { AxiosError } from 'axios';
import { FloatLabel, InputText, useToast } from 'primevue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import SaveButton from '@/Components/SaveButton.vue';
interface Retailer {
    name:string|null
    code:string|null
}

const toast = useToast();
const throbber = useThrobber();
const errors = ref<Record<string, string[]>>({});
const retailer = ref<Retailer>({
    name:null,
    code:null,
})

const hasError = (key: string): boolean => {
    return Boolean(errors.value?.[key]?.length);
}

const save = () => {
    throbber.setStatus(true);
    axios.post('/products/retailers/save',{
        retailer: retailer.value
    })
    .then(() => {
        router.visit('/products/retailers/index');
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
        throbber.setStatus(false);
    });
}

</script>
<template>
    <AuthenticatedLayout>
        <template #header>Create Retailer</template>

        <div class="flex flex-col gap-7 w-full mt-5">
            <FloatLabel class="w-full">
                <InputText
                    class="w-full"
                    required
                    v-model="retailer.name"
                    :invalid="hasError('retailer.name')"
                />
                <label for="name">Name</label>
            </FloatLabel>

            <FloatLabel class="w-full">
                <InputText
                    class="w-full"
                    required
                    v-model="retailer.code"
                    :invalid="hasError('retailer.code')"
                />
                <label for="name">Code</label>
            </FloatLabel>

            <div class="flex justify-center">
                <SaveButton @click="save()" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
