<script setup lang="ts">
import { IconField, InputIcon, InputText } from "primevue";
import { computed, ref, watch } from "vue";

const props = defineProps<{
  paginationMeta: {
    current_page: number;
    total: number;
    per_page: number;
    last_page: number;
  };
}>();

const emit = defineEmits<{
  (e: "update:page", page: number): void;
  (e: "update:perPage", perPage: number): void;
  (e: "update:search", term: string): void;
}>();

// search state
const searchTerm = ref("");
let debounceTimer: ReturnType<typeof setTimeout> | null = null;

// debounce search watcher
watch(searchTerm, (newVal) => {
  if (debounceTimer) clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    emit("update:search", newVal);
  }, 500); // ⏱ adjust delay here (ms)
});

// computed range display
const showingText = computed(() => {
  const { current_page, per_page, total } = props.paginationMeta;
  const start = (current_page - 1) * per_page + 1;
  const end = Math.min(start + per_page - 1, total);
  return `Showing ${start} - ${end} of ${total}`;
});

// dynamic page buttons
const pages = computed(() => {
  const { current_page, last_page } = props.paginationMeta;
  const items: number[] = [];

  if (current_page > 1) items.push(current_page - 1);
  items.push(current_page);
  if (current_page < last_page) items.push(current_page + 1);

  return items;
});

// actions
const goToPage = (page: number) => emit("update:page", page);
const updatePerPage = (e: Event) =>
  emit("update:perPage", Number((e.target as HTMLSelectElement).value));
</script>

<template>
  <div class="flex justify-between items-center mb-3">
    <!-- Per page selector -->
    <select
      :value="props.paginationMeta.per_page"
      @change="updatePerPage"
      class="rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm h-9 ps-2 dark:text-gray-200"
    >
      <option v-for="n in [1, 2, 5, 10, 25, 50]" :key="n" :value="n">{{ n }}</option>
    </select>

    <!-- Search -->
    <IconField class="w-64">
      <InputIcon class="pi pi-search" />
      <InputText
        v-model="searchTerm"
        placeholder="Search..."
        class="w-full h-9 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
      />
    </IconField>
  </div>

  <table class="w-full border-collapse">
    <slot />
  </table>

  <!-- Footer -->
  <div class="flex justify-between items-center mt-4">
    <div class="text-sm text-gray-600 dark:text-gray-400">
      {{ showingText }}
    </div>

    <div class="flex gap-1">
      <!-- Prev -->
      <button
        class="w-8 h-8 flex items-center justify-center rounded-md border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40"
        :disabled="props.paginationMeta.current_page <= 1"
        @click="goToPage(props.paginationMeta.current_page - 1)"
      >
        <i class="pi pi-angle-left"></i>
      </button>

      <!-- Page numbers -->
      <button
        v-for="p in pages"
        :key="p"
        class="w-8 h-8 flex items-center justify-center rounded-md border border-gray-300 dark:border-gray-600 text-sm"
        :class="
          p === props.paginationMeta.current_page
            ? 'bg-cyan-600 text-white font-semibold'
            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'
        "
        @click="goToPage(p)"
      >
        {{ p }}
      </button>

      <!-- Next -->
      <button
        class="w-8 h-8 flex items-center justify-center rounded-md border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40"
        :disabled="props.paginationMeta.current_page >= props.paginationMeta.last_page"
        @click="goToPage(props.paginationMeta.current_page + 1)"
      >
        <i class="pi pi-angle-right"></i>
      </button>
    </div>
  </div>
</template>
