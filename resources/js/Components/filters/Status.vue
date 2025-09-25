<script setup lang="ts">
import { Dropdown, FloatLabel } from 'primevue'
import { ref, computed } from 'vue'

const options = ref<{ id: boolean; name: string }[]>([
    { name: 'Active', id: true },
    { name: 'Inactive', id: false },
])

const props = defineProps<{
    modelValue: boolean | null,
    invalid?: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean | null): void
}>()

const selectedValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val),
})

</script>

<template>
  <FloatLabel class="w-full">
    <Dropdown
      filter
      show-clear
      v-model="selectedValue"
      :options="options"
      option-label="name"
      option-value="id"
      class="w-full"
      :invalid="props.invalid"
    />
    <label>Status</label>
  </FloatLabel>
</template>
