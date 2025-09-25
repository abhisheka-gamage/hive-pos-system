<script setup lang="ts">
import { ref, onMounted } from "vue"
import { usePage, router } from "@inertiajs/vue3"
import Toast from "primevue/toast"
import { useToast } from "primevue/usetoast"
import Throbber from "@/Components/Throbber.vue"
import { PermissionStore } from "@/stores/permissions"
import { useUserStore } from "@/stores/user"
import { useThemeStore } from "@/stores/theme"
import { useThrobber } from "@/stores/throbber"
import { Sun, Moon, User, LogOut, Settings, ChevronRight } from "lucide-vue-next"

const openHeader = ref<string | null>(null)
const openItem = ref<string | null>(null)

const page = usePage()
const user = useUserStore()
const throbber = useThrobber()
const permissions = PermissionStore()
const themeStore = useThemeStore()
const toast = useToast()

async function handleLogout() {
  try {
    await router.post(route("logout"))
    toast.add({ severity: "success", summary: "Logged out", detail: "You have been logged out successfully.", life: 3000 })
  } catch (err: unknown) {
    const message = err instanceof Error ? err.message : "Unknown error"
    toast.add({ severity: "error", summary: "Logout failed", detail: message, life: 3000 })
  }
}

function goTo(nameOrUrl: string) {
  throbber.setStatus(true)
  try {
    const target = route ? route(nameOrUrl) : nameOrUrl
    router.visit(target)
  } catch (err: unknown) {
    const message = err instanceof Error ? err.message : "Unknown error"
    toast.add({ severity: "error", summary: "Navigation failed", detail: message, life: 3000 })
  } finally {
    throbber.setStatus(false)
  }
}

onMounted(() => {
  permissions.routePermissions = page.props.auth.permissions
  permissions.details = page.props.auth.nav_data.map((header: any) => ({
    id: header.id,
    icon: header.icon,
    label: header.display_name,
    items: header.children.map((item: any) => ({
      id: item.id,
      label: item.display_name,
      items: item.children.map((sub: any) => ({
        id: sub.id,
        label: sub.display_name,
        command: () => goTo(sub.permissions.name),
      })),
    })),
  }))
  user.details = page.props.auth.user
})
</script>

<template>
  <div class="flex min-h-screen bg-gradient-to-br from-slate-100 to-sky-100 dark:from-gray-900 dark:to-slate-800">
    <Throbber />
    <Toast />

    <aside class="w-64 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-r border-gray-200 dark:border-gray-700 flex flex-col justify-between">
      <div>
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
          <button @click="goTo('dashboard')" class="flex items-center space-x-2">
            <img src="http://localhost:8000/storage/logo.png" class="w-10 h-10 rounded" />
            <span class="font-bold text-xl text-gray-800 dark:text-gray-100">Hive POS</span>
          </button>
        </div>

        <nav class="mt-6 space-y-2 px-2 h-1/2">
          <div v-for="header in permissions.details" :key="header.label" class="mb-1">
            <button
              class="w-full flex items-center justify-between gap-2 px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700 font-semibold"
              @click="openHeader = openHeader === header.label ? null : header.label"
            >
              <div class="flex items-center gap-2">
                <i :class="header.icon"></i>
                <span>{{ header.label }}</span>
              </div>
              <ChevronRight class="w-4 h-4 transition-transform" :class="{ 'rotate-90': openHeader === header.label }" />
            </button>

            <transition name="slide-fade">
              <div v-if="openHeader === header.label" class="ml-4 mt-1 space-y-1 border-l border-gray-200 dark:border-gray-700 pl-2">
                <div v-for="item in header.items" :key="item.label">
                  <button
                    class="w-full flex items-center justify-between px-3 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-800 rounded"
                    @click="openItem = openItem === item.label ? null : item.label"
                  >
                    <span>{{ item.label }}</span>
                    <ChevronRight class="w-3 h-3 transition-transform" :class="{ 'rotate-90': openItem === item.label }" />
                  </button>

                  <transition name="slide-fade">
                    <div v-if="openItem === item.label" class="ml-4 mt-1 space-y-1 border-l">
                      <button
                        v-for="sub in item.items"
                        :key="sub.label"
                        class="w-full text-left px-3 py-1 hover:bg-blue-500 hover:text-white rounded text-sm"
                        @click="sub.command()"
                      >
                        {{ sub.label }}
                      </button>
                    </div>
                  </transition>
                </div>
              </div>
            </transition>
          </div>
        </nav>
      </div>

      <div class="p-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <User class="w-6 h-6 text-gray-600 dark:text-gray-300" />
            <span class="text-gray-800 dark:text-gray-200 font-medium">{{ user?.details?.name }}</span>
          </div>
          <button
            @click="themeStore.toggleDarkMode()"
            class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
          >
            <Sun v-if="!themeStore.darkMode" class="w-5 h-5" />
            <Moon v-else class="w-5 h-5" />
          </button>
        </div>

        <div class="mt-4 space-y-2">
          <button
            @click="goTo('settings')"
            class="flex items-center gap-3 w-full px-4 py-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition"
          >
            <Settings class="w-5 h-5" />
            <span>Settings</span>
          </button>
          <button
            @click="handleLogout"
            class="flex items-center gap-3 w-full px-4 py-2 rounded-lg text-red-600 hover:bg-red-100 dark:hover:bg-red-900/30 transition"
          >
            <LogOut class="w-5 h-5" />
            <span>Logout</span>
          </button>
        </div>
      </div>
    </aside>

    <main class="flex-1 p-6 overflow-y-auto">
      <header v-if="$slots.header" class="mb-6">
        <div class="bg-white dark:bg-gray-800 shadow rounded-xl px-6 py-4">
          <slot name="header" />
        </div>
      </header>

      <section class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-md shadow-xl rounded-2xl p-6">
        <slot />
      </section>
    </main>
  </div>
</template>

<style scoped>
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.2s ease;
}
.slide-fade-enter-from,
.slide-fade-leave-to {
  opacity: 0;
  max-height: 0;
}
</style>
