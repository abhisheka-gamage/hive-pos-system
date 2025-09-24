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

watch(searchTerm, (newVal) => {
  if (debounceTimer) clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    emit("update:search", newVal);
  }, 400);
});

// computed text
const showingText = computed(() => {
  const { current_page, per_page, total } = props.paginationMeta;
  const start = (current_page - 1) * per_page + 1;
  const end = Math.min(start + per_page - 1, total);
  return `Showing ${start} - ${end} of ${total}`;
});

// dynamic pages
const pages = computed(() => {
  const { current_page, last_page } = props.paginationMeta;
  const items: number[] = [];

  if (current_page > 1) items.push(current_page - 1);
  items.push(current_page);
  if (current_page < last_page) items.push(current_page + 1);

  return items;
});

const goToPage = (page: number) => emit("update:page", page);
const updatePerPage = (e: Event) =>
  emit("update:perPage", Number((e.target as HTMLSelectElement).value));
</script>

<template>
    <!-- Top Controls -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-5">
      <!-- Per page -->
      <select
        :value="props.paginationMeta.per_page"
        @change="updatePerPage"
        class="rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm h-10 ps-3 pe-6 dark:text-gray-200 shadow-sm focus:ring-2 focus:ring-cyan-500"
      >
        <option v-for="n in [5, 10, 25, 50]" :key="n" :value="n">{{ n }}</option>
      </select>

      <!-- Search -->
      <IconField class="relative w-full sm:w-72">
        <InputIcon class="pi pi-search text-gray-400" />
        <InputText
          v-model="searchTerm"
          placeholder="Search records..."
          class="w-full h-10 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-cyan-500 shadow-sm"
        />
      </IconField>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
      <table class="w-full text-sm">
        <slot />
      </table>
    </div>

    <!-- Footer -->
    <div class="flex flex-col sm:flex-row justify-between items-center mt-5 gap-4">
      <div class="text-sm text-gray-600 dark:text-gray-400">
        {{ showingText }}
      </div>

      <!-- Pagination -->
      <div class="flex gap-2">
        <!-- Prev -->
        <button
          class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-cyan-50 dark:hover:bg-gray-700 transition disabled:opacity-40"
          :disabled="props.paginationMeta.current_page <= 1"
          @click="goToPage(props.paginationMeta.current_page - 1)"
        >
          <i class="pi pi-angle-left"></i>
        </button>

        <!-- Page numbers -->
        <button
          v-for="p in pages"
          :key="p"
          class="w-9 h-9 flex items-center justify-center rounded-lg border text-sm font-medium transition transform hover:scale-105"
          :class="
            p === props.paginationMeta.current_page
              ? 'bg-cyan-600 border-cyan-600 text-white shadow-md'
              : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-cyan-50 dark:hover:bg-gray-700'
          "
          @click="goToPage(p)"
        >
          {{ p }}
        </button>

        <!-- Next -->
        <button
          class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-cyan-50 dark:hover:bg-gray-700 transition disabled:opacity-40"
          :disabled="props.paginationMeta.current_page >= props.paginationMeta.last_page"
          @click="goToPage(props.paginationMeta.current_page + 1)"
        >
          <i class="pi pi-angle-right"></i>
        </button>
      </div>
    </div>
</template>
