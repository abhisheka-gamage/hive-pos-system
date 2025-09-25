<script setup lang="ts">
import { useThrobber } from '@/stores/throbber'
import axios, { AxiosError, AxiosResponse } from 'axios'
import { Dropdown, FloatLabel, useToast } from 'primevue'
import { onMounted, ref, watch, computed } from 'vue'

const throbber = useThrobber()
const toast = useToast()

const errors = ref<Record<string, string[]>>({})

const options = ref<{ id: number; name: string }[]>([])

const props = defineProps<{
    modelValue: number | null,
    invalid?: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: number | null): void
}>()

const selectedValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val),
})

const getOptions = () => {
  throbber.setStatus(true)
  axios
    .post('/products/retailers/filter')
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
      v-model="selectedValue"
      show-clear
      :options="options"
      option-label="name"
      option-value="id"
      class="w-full"
      :invalid="props.invalid"
    />
    <label>Retailers</label>
  </FloatLabel>
</template>
