<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Button, Card, InputText, Password } from 'primevue';
import Throbber from '@/Components/Throbber.vue';
import { useThrobber } from '@/stores/throbber';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const throbber = useThrobber();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-cyan-800 to-cyan-900 relative overflow-hidden">
        <div class="absolute top-10 left-5 w-80 h-80 bg-cyan-700 rounded-full opacity-30 blur-3xl animate-pulse"></div>
        <div class="absolute top-1/3 left-10 w-96 h-96 bg-cyan-600 rounded-full opacity-20 blur-2xl animate-pulse"></div>
        <div class="absolute bottom-20 left-1/6 w-72 h-72 bg-cyan-500 rounded-full opacity-25 blur-xl animate-pulse"></div>
        <div class="absolute top-1/2 left-1/4 w-80 h-80 bg-cyan-700 rounded-full opacity-15 blur-2xl animate-pulse"></div>
        <Throbber />

        <div class="w-screen h-screen overflow-hidden md:grid md:grid-cols-2">
            <div class="flex justify-center items-center order-2 mt-10">
                <Card class="w-8/10 p-12 shadow-2xl rounded-xl bg-white/90 dark:bg-gray-800/90 backdrop-blur-md">
                <template #header>
                    <div class="text-4xl uppercase font-bold text-cyan-900">Sign In</div>
                    <span class="text-cyan-900">Sign-in to continue with sales</span>
                </template>
                <template #content>
                    <form @submit.prevent="submit" class="space-y-4 m-auto">
                        <div class="flex flex-col ">
                            <label for="username" class="text-gray-700 dark:text-gray-300 mb-1">Username</label>
                            <InputText
                            id="username"
                            v-model="form.email"
                            type="text"
                            placeholder="Enter your username"
                            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 dark:focus:ring-cyan-300 transition duration-150"
                            />
                        </div>

                        <div class="flex flex-col">
                            <label for="password" class="text-gray-700 dark:text-gray-300 mb-1">Password</label>
                            <Password
                            id="password"
                            v-model="form.password"
                            placeholder="Enter your password"
                            toggleMask
                            :feedback="false"
                            inputClass="border border-gray-300 dark:border-gray-600 w-full"
                            class="w-full rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 dark:focus:ring-cyan-300 transition duration-150"
                            />
                        </div>

                        <div class="flex justify-center">
                            <PrimaryButton
                                class="w-1/2 justify-center"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Log in
                            </PrimaryButton>
                        </div>
                    </form>
                </template>

                <!-- Footer slot -->
                <template #footer>
                    <div class="mt-4 text-center text-gray-400 text-xs dark:text-gray-300">
                    &copy; 2025 Hive-POS. All rights reserved.
                    </div>
                </template>

                </Card>
            </div>
            <div class="flex flex-col items-center mt-[100px] order-1">
                <img src="http://localhost:8000/storage/logo.png" alt="Logo" class="w-40 h-40 mx-auto mb-2"/>
                <div>
                    <h1 class="text-5xl font-bold text-white drop-shadow-sm">Welcome,</h1>
                    <h1 class="text-3xl font-bold text-white drop-shadow-sm">to Hive-POS</h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-1">Sign in to continue</p>
                </div>
            </div>
        </div>
    </div>
</template>
