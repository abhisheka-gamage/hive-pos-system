<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { reactive, ref, onMounted } from 'vue'
import { useToast } from 'primevue/usetoast'
import { useThrobber } from '@/stores/throbber'
import Throbber from '@/Components/Throbber.vue'
import Toast from 'primevue/toast'
import axios, { AxiosError, AxiosResponse } from 'axios'
import { router } from '@inertiajs/vue3'
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Dropdown from 'primevue/dropdown'
import Button from 'primevue/button'
import { FloatLabel } from 'primevue'
import SaveButton from '@/Components/SaveButton.vue'

interface Role {
  id: number
  name: string
}

const user = reactive({
  name: '',
  email: '',
  role: null as number | null,
  password: '',
  password_confirmation: ''
})

const roles = ref<Role[]>([])
const errors = ref<Record<string, string[]>>({})
const loading = ref(false)

const toast = useToast()
const throbber = useThrobber()

const getRoles = async () => {
  throbber.setStatus(true)
  try {
    const response: AxiosResponse = await axios.post('/users/roles/filter')
    roles.value = response.data.data
  } catch (err: unknown) {
    const error = err as AxiosError<{ message?: string; errors?: Record<string, string[]> }>
    errors.value = error.response?.data.errors || {}
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: error.response?.data?.message || 'Failed to fetch roles',
      life: 3000
    })
  } finally {
    throbber.setStatus(false)
  }
}

const save = async () => {
  loading.value = true
  throbber.setStatus(true)
  try {
    await axios.post('/users/user/save', { user })
    toast.add({
      severity: 'success',
      summary: 'Success',
      detail: 'User created successfully!',
      life: 3000
    })
    router.visit('/users/user/index')
  } catch (err: unknown) {
    const error = err as AxiosError<{ message?: string; errors?: Record<string, string[]> }>
    errors.value = error.response?.data.errors || {}
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: error.response?.data?.message || 'Failed to create user',
      life: 3000
    })
  } finally {
    loading.value = false
    throbber.setStatus(false)
  }
}

onMounted(() => {
  getRoles()
})
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Create New User</h1>
    </template>

    <div class="flex flex-col gap-7 w-full">
        <FloatLabel class="w-full">
            <InputText class="w-full" v-model="user.name" id="name" required />
            <label for="name">Full Name</label>
        </FloatLabel>

        <FloatLabel class="w-full">
            <InputText class="w-full" v-model="user.email" id="email" required />
            <label for="email">Email</label>
        </FloatLabel>

        <FloatLabel class="w-full">
            <Dropdown
                v-model="user.role"
                :options="roles"
                option-label="name"
                option-value="id"
                filter class="w-full"
            />
        <label for="role">Role</label>
        </FloatLabel>

        <FloatLabel class="w-full">
            <Password class="w-full" input-class="w-full" v-model="user.password" id="password" :feedback="false" toggleMask />
            <label for="password">Password</label>
        </FloatLabel>

        <FloatLabel class="w-full">
            <Password class="w-full" input-class="w-full" v-model="user.password_confirmation" id="password_confirmation" :feedback="false" toggleMask />
            <label for="password_confirmation">Confirm Password</label>
        </FloatLabel>

        <div class="flex justify-center">
            <SaveButton @click="save()" />
        </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
/* Optional: Enhance glassy floating look for the form panel */
.bg-white.dark\:bg-gray-800 {
  backdrop-filter: blur(8px);
}
</style>
