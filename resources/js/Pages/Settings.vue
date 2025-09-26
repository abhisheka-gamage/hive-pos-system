<script setup lang="ts">
import PrimaryButton from '@/Components/PrimaryButton.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useThrobber } from '@/stores/throbber'
import axios from 'axios'
import { useToast } from 'primevue/usetoast'
import { ref, onMounted } from 'vue'

const logoEditMode = ref<boolean>(false)
const imgUrl = ref<string>('')
const file = ref<File | null>(null)

const throbber = useThrobber()
const toast = useToast()

// file select
const handleFileChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files?.length) {
    file.value = target.files[0]
  }
}

// fetch logo from backend
const getLogo = async () => {
  try {
    const res = await axios.get('/logo')
    imgUrl.value = res.data ?? '' // backend returns path or null
  } catch (err) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load logo' })
  }
}

// upload logo
const uploadLogo = async () => {
  if (!file.value) {
    toast.add({ severity: 'warn', summary: 'Warning', detail: 'No file selected' })
    return
  }

  const formData = new FormData()
  formData.append('file', file.value)

  try {
    throbber.start()
    const res = await axios.post('/logo/update', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    imgUrl.value = res.data.path
    logoEditMode.value = false
    toast.add({ severity: 'success', summary: 'Success', detail: 'Logo updated' })
  } catch (err) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to update logo' })
  } finally {
    throbber.stop()
  }
}

onMounted(() => {
  getLogo()
})
</script>

<template>
  <AuthenticatedLayout>
    <template #header>Settings</template>

    <h1 class="text-lg font-semibold mb-4">App Logo</h1>

    <!-- Edit mode -->
    <template v-if="logoEditMode">
      <div class="flex items-start gap-6">
        <!-- File input -->
        <div class="relative w-32 h-32">
          <input
            type="file"
            accept=".png,.jpg,.jpeg"
            id="logo-upload"
            class="absolute inset-0 opacity-0 cursor-pointer z-10"
            @change="handleFileChange"
          />
          <label
            for="logo-upload"
            class="w-full h-full border-2 border-dashed border-cyan-500 rounded-lg flex flex-col justify-center items-center text-center cursor-pointer hover:bg-cyan-50 dark:hover:bg-gray-600 transition"
          >
            <span class="text-4xl mb-1">📁</span>
            <span class="font-medium">Choose Logo</span>
            <span class="text-xs text-gray-500 dark:text-gray-300">or drag & drop</span>
          </label>
        </div>

        <!-- Info + Actions -->
        <div>
          <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            Supported formats: <code>.png, .jpg, .jpeg</code>
          </p>
          <p v-if="file" class="mt-1 text-sm text-gray-700 dark:text-gray-300">
            Selected: {{ file.name }}
          </p>

          <div class="flex gap-3 mt-5">
            <PrimaryButton @click="uploadLogo">Save</PrimaryButton>
            <PrimaryButton @click="logoEditMode = !logoEditMode" severity="danger">
              Cancel
            </PrimaryButton>
          </div>
        </div>
      </div>
    </template>

    <!-- View mode -->
    <template v-else>
      <div class="flex items-start gap-4">
        <img
          v-if="imgUrl"
          :src="imgUrl"
          alt="App Logo"
          class="rounded-lg border border-gray-300 dark:border-gray-600 object-cover w-32 h-32"
        />
        <p v-else class="w-32 h-32 text-gray-500 dark:text-gray-400 border rounded flex justify-center items-center">No logo set</p>
        <div class="mt-5">
          <PrimaryButton @click="logoEditMode = !logoEditMode">Edit</PrimaryButton>
        </div>
      </div>
    </template>
  </AuthenticatedLayout>
</template>
