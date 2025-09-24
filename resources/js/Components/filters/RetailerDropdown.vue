<script setup lang="ts">
import DropDownFilter from '../DropDownFilter.vue'

const props = defineProps<{
  modelValue: number | null
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: number | null): void
}>()

</script>

<template>
  <DropDownFilter
    v-model="props.modelValue"
    url="/products/retailers/filter"
    label="Retailer"
    option-label="name"
    option-value="id"
    class="w-full"
    @update:modelValue="emit('update:modelValue', $event)"
  />
</template>
