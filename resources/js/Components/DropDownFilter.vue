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
  modelValue: number | null
  url: string
  label: string
  params?: any
  optionLabel?: string | null | undefined
  optionValue?: string | null | undefined
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: number | null): void
}>()

const selectedValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val),
})

watch(
  () => props.params,
  () => {
    getOptions()
  },
  { deep: true }
)

const getOptions = () => {
  throbber.start()
  axios
    .post(props.url, {
        params: props.params
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
      throbber.stop()
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
      :options="options"
      :option-label="props.optionLabel || 'name'"
      :option-value="props.optionValue || 'id'"
      class="w-full"
    />
    <label>{{ props.label }}</label>
  </FloatLabel>
</template>
