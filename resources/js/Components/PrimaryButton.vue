<script setup lang="ts">
const props = defineProps<{
  severity?: string | null
}>()
</script>

<template>
  <button
    :class="[
      'inline-flex items-center rounded-md border border-transparent px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2',
      props.severity === 'primary' && 'bg-indigo-600 text-white hover:bg-indigo-500 focus:ring-indigo-500 active:bg-indigo-700',
      props.severity === 'danger' && 'bg-red-600 text-white hover:bg-red-500 focus:ring-red-500 active:bg-red-700',
      props.severity === 'success' && 'bg-green-600 text-white hover:bg-green-500 focus:ring-green-500 active:bg-green-700',
      props.severity === 'info' && 'bg-sky-500 text-white hover:bg-sky-500 focus:ring-green-500 active:bg-green-700',
      !props.severity && 'bg-gray-800 text-white dark:bg-white dark:text-black dark:hover:bg-gray-300 hover:bg-gray-700 focus:ring-indigo-500 active:bg-gray-900'
    ]"
  >
    <slot />
  </button>
</template>
