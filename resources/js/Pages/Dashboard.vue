<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useThrobber } from '@/stores/throbber';
import { useUserStore } from '@/stores/user';
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const user = useUserStore();
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-md font-semibold leading-tight"
            >
                Dashboard
            </h2>
        </template>
        <h1>Welcome {{ user?.details?.name }} back to the store,</h1>

    </AuthenticatedLayout>
</template>
