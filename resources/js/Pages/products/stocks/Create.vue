<script setup lang="ts">
import SaveButton from '@/Components/SaveButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useThrobber } from '@/stores/throbber';
import axios, { AxiosError } from 'axios';
import { useToast } from 'primevue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3'

const file = ref<File | null>(null);
const throbber = useThrobber();
const toast = useToast();

const rules: string[] = [
    `Use the downloaded sample data format exactly`,
    `Date Format: <strong>YYYY-MM-DD</strong> Ex, <strong>2025-09-25</strong>`,
    `Always use Code as reference`,
    `Use Excel Files Only`,
    `Don't put empty rows in excel`,
];

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files?.length) file.value = target.files[0];
};

const downloadSample = async () => {
    throbber.start();
    try {
        const response = await axios.post('/products/stocks/sample', {}, {
            responseType: 'blob',
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'stocks_sample.xlsx');
        document.body.appendChild(link);
        link.click();
        link.remove();
        toast.add({
            severity: 'success',
            detail: 'Download should start now',
            summary: 'Success',
            life: 3000
        });
    } catch (err: unknown) {
        const error = err as AxiosError<{message: string}>;
        console.error('Download failed', err);
        toast.add({
            severity: 'error',
            detail: error.message,
            summary: 'Error',
            life: 3000
        });
    } finally {
        throbber.stop();
    }
};

const uploadExcel = async () => {
    if (!file.value) {
        toast.add({
            severity: 'warn',
            summary: 'No File',
            detail: 'Please select a file before uploading',
            life: 3000
        });
        return;
    }

    throbber.start();
    const formData = new FormData();
    formData.append('file', file.value);

    axios.post('/products/stocks/upload', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
    })
    .then(() => {
        router.visit('/products/stocks/index')
        toast.add({
            severity: 'success',
            summary: 'Success',
            detail: 'Stock Added',
            life: 3000
        });
    })
    .catch((err: unknown) => {
        const error = err as AxiosError<{ message: string }>;
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: error.response?.data?.message || error.message,
            life: 3000
        });
    })
    .finally(() => throbber.stop());
};


</script>

<template>
    <AuthenticatedLayout>
        <template #header>Upload Stock Excel</template>
        <div class="flex flex-col md:flex-row gap-12">
        <!-- Left Column -->
            <div class="flex-1 flex flex-col gap-8">
                <!-- Step 1: Download Sample -->
                <div class="flex flex-col items-center bg-white dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                <label class="mb-3 text-lg font-medium">Download Sample Template</label>
                <button
                    @click="downloadSample"
                    class="px-5 py-2 bg-cyan-600 text-white rounded-md shadow hover:bg-cyan-700 flex items-center gap-2"
                >
                    <i>⬇</i> Download Sample Excel
                </button>
                </div>

                <!-- Step 2: Upload File -->
                <div class="flex flex-col items-center bg-white dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                <label class="mb-3 text-lg font-medium">Upload Your Excel File</label>
                <div class="relative w-full max-w-xs">
                    <input
                        type="file"
                        accept=".xlsx,.xls"
                        id="excel-upload"
                        class="opacity-0 absolute w-full h-full cursor-pointer z-10"
                        @change="handleFileChange"
                    />
                    <label
                        for="excel-upload"
                        class="w-full h-32 border-2 border-dashed border-cyan-500 rounded-lg flex flex-col justify-center items-center text-center cursor-pointer hover:bg-cyan-50 dark:hover:bg-gray-600 transition"
                    >
                        <i class="text-4xl mb-2">📁</i>
                        <span class="font-medium">Choose Excel File</span>
                        <span class="text-sm text-gray-500 dark:text-gray-300">or drag and drop</span>
                    </label>
                </div>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Supported formats: .xlsx, .xls
                </p>
                <p v-if="file" class="mt-1 text-sm text-gray-700 dark:text-gray-300">Selected: {{ file.name }}</p>
                </div>
            </div>

            <!-- Right Column: Rules -->
            <div class="flex-1 flex flex-col gap-4">
                <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                    <label class="text-lg font-semibold mb-4 block text-center">Review Requirements</label>
                    <div class="flex flex-col gap-3">
                        <div v-for="rule in rules" :key="rule" class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-800 border-l-4 border-cyan-600 rounded">
                        <i class="text-cyan-600 mt-0.5 font-bold">✓</i>
                        <span v-html="rule"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex justify-center gap-4 mt-10">
            <SaveButton @click="uploadExcel()"/>
        </div>
    </AuthenticatedLayout>
</template>
