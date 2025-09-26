<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useThrobber } from '@/stores/throbber';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { DatePicker, FloatLabel, InputNumber, InputText, useToast } from 'primevue';
import { onMounted, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import SaveButton from '@/Components/SaveButton.vue';
import RetailerDropdown from '@/Components/filters/RetialerSelect.vue';
import ProductSelect from '@/Components/filters/ProductSelect.vue';

interface Batch {
    retailer:number|null,
    product:number|null,
    code:string|null,
    retail: number|null,
    selling: number|null
    effective_from: Date
    effective_to: Date
}

const toast = useToast();
const throbber = useThrobber();
const errors = ref<Record<string, string[]>>({});
const batch = ref<Batch>({
    retailer: null,
    product: null,
    code: null,
    retail: null,
    selling: null,
    effective_from: new Date(),
    effective_to: new Date('9999-12-31')
})

const hasError = (key: string): boolean => {
    return Boolean(errors.value?.[key]?.length);
}

const save = () => {
    throbber.start();
    axios.post('/products/batches/save',{
        batch: batch.value
    })
    .then(() => {
        router.visit('/products/batches/index');
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
        detail: error.response?.data?.message || 'Failed to save batch',
        life: 3000,
        });
    })
    .finally(() => {
        throbber.stop();
    });
}

const getCode = () => {
  throbber.start()
  axios
    .post('/products/batches/get_code', {
        product_id: batch.value.product
    })
    .then((response: AxiosResponse) => {
      batch.value.code = response.data.data
    })
    .catch((err: unknown) => {
      const error = err as AxiosError<{ message?: string; errors?: Record<string, string[]> }>
      errors.value = error.response?.data.errors || {}
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: error.response?.data?.message || 'Failed to fetch data',
        life: 3000,
      })
    })
    .finally(() => {
      throbber.stop()
    })
}

watch(
    () => batch.value.product,
    () => {
        getCode()
    },
    { deep: true }
)

</script>
<template>
    <AuthenticatedLayout>
        <template #header>Add Product</template>
        <div class="flex flex-col gap-7 w-full mt-5">

            <RetailerDropdown v-model="batch.retailer" :invalid="hasError('batch.retailer')"/>

            <ProductSelect v-model="batch.product" :retailer_id="batch.retailer" :invalid="hasError('batch.product')"/>

            <FloatLabel class="w-full">
                <InputText
                    class="w-full"
                    required
                    v-model="batch.code"
                    :invalid="hasError('batch.code')"
                    disabled
                />
                <label for="name">Code</label>
            </FloatLabel>

                        <FloatLabel class="w-full">
                <InputNumber
                    inputId="locale-user"
                    :minFractionDigits="2"
                    fluid
                    class="w-full"
                    required
                    v-model="batch.retail"
                    :invalid="hasError('batch.retail')"
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
                    v-model="batch.selling"
                    :invalid="hasError('batch.selling')"
                />
                <label for="name">Selling Price</label>
            </FloatLabel>

            <FloatLabel>
                <DatePicker
                    v-model="batch.effective_from"
                    class="w-full"
                    required
                    showButtonBar
                    showIcon
                    :invalid="hasError('batch.effective_from')"
                />

                <label for="name">Effecting From</label>
            </FloatLabel>

            <FloatLabel>
                <DatePicker
                    v-model="batch.effective_to"
                    class="w-full"
                    required
                    showButtonBar
                    showIcon
                    :invalid="hasError('batch.effective_to')"
                />

                <label for="name">Effecting To</label>
            </FloatLabel>

            <div class="flex justify-center">
                <SaveButton @click="save()" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
