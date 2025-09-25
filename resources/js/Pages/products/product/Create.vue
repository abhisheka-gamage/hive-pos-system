<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useThrobber } from '@/stores/throbber';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { FloatLabel, InputNumber, InputText, useToast } from 'primevue';
import { onMounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import SaveButton from '@/Components/SaveButton.vue';
import RetailerSelect from '@/Components/filters/RetialerSelect.vue';

interface Product {
    retailer:number|null
    name:string|null,
    code:string|null,
    barcode: string|null,
    retail: number|null,
    selling: number|null
}

const toast = useToast();
const throbber = useThrobber();
const errors = ref<Record<string, string[]>>({});
const product = ref<Product>({
    retailer:null,
    name:null,
    code:null,
    barcode:null,
    retail:null,
    selling:null,
})

const hasError = (key: string): boolean => {
    return Boolean(errors.value?.[key]?.length);
}

const save = () => {
    throbber.setStatus(true);
    axios.post('/products/product/save',{
        product: product.value
    })
    .then(() => {
        router.visit('/products/product/index');
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
        <template #header>Add Product</template>
        <div class="flex flex-col gap-7 w-full mt-5">
            <RetailerSelect v-model="product.retailer" />

            <FloatLabel class="w-full">
                <InputText
                    class="w-full"
                    required
                    v-model="product.name"
                    :invalid="hasError('product.name')"
                />
                <label for="name">Name</label>
            </FloatLabel>

            <FloatLabel class="w-full">
                <InputText
                    class="w-full"
                    required
                    v-model="product.code"
                    :invalid="hasError('product.code')"
                />
                <label for="name">Code</label>
            </FloatLabel>

            <FloatLabel class="w-full">
                <InputText
                    class="w-full"
                    required
                    v-model="product.barcode"
                    :invalid="hasError('product.barcode')"
                />
                <label for="name">Bar Code (Empty)</label>
            </FloatLabel>

            <FloatLabel class="w-full">
                <InputNumber
                    inputId="locale-user"
                    :minFractionDigits="2"
                    fluid
                    class="w-full"
                    required
                    v-model="product.retail"
                    :invalid="hasError('product.retail')"
                />
                <label for="name">Retail Price</label>
            </FloatLabel>

            <FloatLabel class="w-full">
                <InputNumber
                    inputId="locale-user"
                    :minFractionDigits="2"
                    fluid
                    class="w-full"
                    required
                    v-model="product.selling"
                    :invalid="hasError('product.selling')"
                />
                <label for="name">Selling Price</label>
            </FloatLabel>

            <div class="flex justify-center">
                <SaveButton @click="save()" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
