<script setup lang="ts">
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useUserStore } from '@/stores/user';
import { Head } from '@inertiajs/vue3';

const user = useUserStore();
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-md font-semibold leading-tight"
            >
                <div class="flex justify-between items-center">
                    <div>
                        Dashboard
                    </div>
                    <a :href="route('sales-create')" target="_blank">
                        <PrimaryButton>Sales Mode <i class="pi pi-tags ms-2"></i></PrimaryButton>
                    </a>
                </div>
            </h2>
        </template>
        <h1>Welcome {{ user?.details?.name }} back to the store,</h1>

    </AuthenticatedLayout>
</template>
