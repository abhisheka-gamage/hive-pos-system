<script setup lang="ts">
import axios, { AxiosError, AxiosResponse } from 'axios'
import { Dropdown, FloatLabel, useToast } from 'primevue'
import { onMounted, ref, watch, computed } from 'vue'

const toast = useToast()

const loading = ref<boolean>(false);
const errors = ref<Record<string, string[]>>({})

const options = ref<{ id: number; name: string }[]>([])

const props = defineProps<{
    modelValue: number | null,
    product_id: number | null,
    invalid?: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: number | null): void
}>()

const selectedValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val),
})

watch(
  () => props.product_id,
  () => {
    getOptions()
  },
  { deep: true }
)

const getOptions = () => {
    loading.value = true;
    if(props.product_id){
        axios.post('/products/batches/filter', {
            filter: {
                product_id: props.product_id
            }
        })
        .then((response: AxiosResponse) => {
            options.value = response.data.data
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
            loading.value = false;
        })
    } else {
        options.value = [];
    }
}

onMounted(() => {
  getOptions()
})
</script>

<template>
    <FloatLabel class="w-full">
        <Dropdown
            filter
            show-clear
            v-model="selectedValue"
            :options="options"
            option-label="batch_code"
            option-value="id"
            class="w-full"
            :invalid="props.invalid"
            :loading="loading"
        />
        <label>Batch</label>
    </FloatLabel>
</template>
