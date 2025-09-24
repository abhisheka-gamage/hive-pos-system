<script setup lang="ts">
import { useThrobber } from '@/stores/throbber'
import axios, { AxiosError, AxiosResponse } from 'axios'
import { Dropdown, FloatLabel, useToast } from 'primevue'
import { onMounted, ref } from 'vue'

const throbber = useThrobber()
const toast = useToast()

const errors = ref<Record<string, string[]>>({})

const options = ref<{ id: number; name: string }[]>([])

const props = defineProps<{
    modelValue: number|null,
    url: string,
    label:string,
    optionLabel?: string|null|undefined,
    optionValue?: string|null|undefined
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: number | null): void
}>()

const getOptions = () => {
  throbber.setStatus(true)
  axios
    .post(props.url)
    .then((response: AxiosResponse) => {
      options.value = response.data.data
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

onMounted(() => {
  getOptions()
})
</script>

<template>
    <FloatLabel class="w-full">
        <Dropdown
            filter
            v-model="props.modelValue"
            :options="options"
            :option-label="props.optionLabel || 'name'"
            :option-value="props.optionValue || 'id'"
            class="w-full"
            @update:modelValue="emit('update:modelValue', $event)"
        />
        <label>{{ props.label }}</label>
    </FloatLabel>
</template>
