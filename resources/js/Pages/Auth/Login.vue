<script setup lang="ts">
import { onMounted, ref } from "vue"
import { useToast } from "primevue/usetoast"
import Toast from "primevue/toast"
import Throbber from "@/Components/Throbber.vue"
import { useThrobber } from "@/stores/throbber"
import { router } from "@inertiajs/vue3"
import type { AxiosError } from "axios"
import axios from "axios"

const email = ref("")
const password = ref("")
const loading = ref(false)
const toast = useToast()
const throbber = useThrobber()
const imgUrl = ref<string>('')

const getLogo = async () => {
  try {
    const res = await axios.get('/logo')
    imgUrl.value = res.data ?? '' // backend returns path or null
  } catch (err) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load logo' })
  }
}

async function handleLogin() {
  loading.value = true
  throbber.start()
  try {
    await router.post(
      route("login"),
      { email: email.value, password: password.value },
      {
        onError: (errors) => {
          // Loop through validation errors and show them
          Object.values(errors).forEach((msg) => {
            toast.add({
              severity: "error",
              summary: "Login failed",
              detail: String(msg),
              life: 4000,
            })
          })
        },
        onSuccess: () => {
          toast.add({
            severity: "success",
            summary: "Welcome",
            detail: "You have logged in successfully",
            life: 3000,
          })
        },
      }
    )
  } catch (err: unknown) {
    // Handle unexpected errors (network, 500, etc.)
    let message = "Something went wrong. Please try again."
    if ((err as AxiosError<{message:string}>)?.response?.data?.message) {
      message = (err as AxiosError<{message:string}>).response?.data?.message as string
    } else if (err instanceof Error) {
      message = err.message
    }

    toast.add({
      severity: "error",
      summary: "Error",
      detail: message,
      life: 4000,
    })
  } finally {
    loading.value = false
    throbber.stop()
  }
}

onMounted(() => getLogo())
</script>


<template>
  <div class="relative flex items-center justify-center min-h-screen bg-gradient-to-br from-slate-100 to-sky-100 dark:from-gray-900 dark:to-slate-800 overflow-hidden">
    <Throbber />
    <Toast />

    <!-- floating background blobs -->
    <div class="absolute -top-24 -left-24 w-72 h-72 bg-sky-400/30 dark:bg-sky-600/30 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-purple-400/30 dark:bg-purple-600/30 rounded-full blur-3xl animate-pulse delay-700"></div>

    <!-- login card -->
    <div class="relative w-full max-w-md p-8 bg-white/70 dark:bg-gray-900/70 backdrop-blur-xl rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700">
      <div class="flex flex-col items-center space-y-4">
        <img :src="imgUrl" class="w-16 h-16 rounded" />
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Welcome back</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Sign in to continue to Hive POS</p>
      </div>

      <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
          <input
            v-model="email"
            type="email"
            class="mt-1 w-full px-4 py-3 rounded-lg bg-white/60 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sky-400 dark:focus:ring-sky-600"
            placeholder="you@example.com"
            required
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
          <input
            v-model="password"
            type="password"
            class="mt-1 w-full px-4 py-3 rounded-lg bg-white/60 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sky-400 dark:focus:ring-sky-600"
            placeholder="••••••••"
            required
          />
        </div>

        <div class="flex items-center justify-between">
          <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
            <input type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-sky-500 focus:ring-sky-400" />
            Remember me
          </label>
          <a href="#" class="text-sm text-sky-500 hover:underline">Forgot password?</a>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full py-3 rounded-lg bg-sky-500 text-white font-semibold hover:bg-sky-600 dark:bg-sky-600 dark:hover:bg-sky-500 transition disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ loading ? "Signing in..." : "Sign in" }}
        </button>
      </form>

      <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
        Don’t have an account?
        <a href="#" class="text-sky-500 hover:underline">Create one</a>
      </div>
    </div>
  </div>
</template>
