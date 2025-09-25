<script setup lang="ts">
import { ref, defineProps, defineEmits } from 'vue';

const props = defineProps<{
  modelValue: File | null;
  label?: string;
  mainClass?: string;
  inputClass?: string;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', file: File | null): void;
}>();

const fileInput = ref<HTMLInputElement | null>(null);

const triggerFileSelect = () => {
  fileInput.value?.click();
};

const handleFileChange = (e: Event) => {
  const target = e.target as HTMLInputElement;
  const selectedFile = target.files?.[0] || null;
  emit('update:modelValue', selectedFile);
};

const clearFile = () => {
  if (fileInput.value) fileInput.value.value = '';
  emit('update:modelValue', null);
};

</script>

<template>
  <div class="flex flex-col gap-2 max-w-md" :class="mainClass">
    <label class="font-medium text-gray-700 dark:text-gray-200">{{ label || 'Upload Excel File' }}</label>

    <div class="flex gap-2" :class="inputClass">
      <!-- Styled Span Trigger -->
      <span
        class="flex-1 cursor-pointer border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:border-cyan-500 transition flex items-center justify-between"
        @click="triggerFileSelect"
      >
        <span>
          {{ modelValue ? modelValue.name : 'Choose file...' }}
        </span>
        <span class="text-cyan-600 dark:text-cyan-400 text-sm">
          Browse
        </span>
      </span>

      <button
        v-if="modelValue"
        type="button"
        @click="clearFile"
        class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
      >
        Clear
      </button>
    </div>

    <!-- Hidden File Input -->
    <input
      ref="fileInput"
      type="file"
      accept=".xlsx,.xls,.csv"
      @change="handleFileChange"
      class="hidden"
    />

    <div v-if="modelValue" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
      Size: {{ (modelValue.size / 1024).toFixed(2) }} KB
    </div>
  </div>
</template>
