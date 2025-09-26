<script setup lang="ts">
import { useUserStore } from '@/stores/user';
import { Button, ConfirmDialog, Menubar, TieredMenu, Toast, useToast } from 'primevue';
import { useConfirm } from "primevue/useconfirm";
import { router, usePage } from '@inertiajs/vue3';
import { onMounted, ref } from "vue";
import { ChevronDown } from 'lucide-vue-next';
import axios from 'axios';

const user = useUserStore();
const confirm = useConfirm();
const page = usePage();
const toast = useToast();

const menu = ref();
const imgUrl = ref<string>('')

const exitSales = () => {
    confirm.require({
        message: 'Are you sure You want to exit Sales Mode?',
        header: 'Confirmation',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Exit',
            severity: 'info'
        },
        accept: () => {
            router.visit('/')
        }
    });
}

const items = ref([
    {
        label: 'Exit Sales Mode',
        icon: 'pi pi-angle-double-left',
        command: () => exitSales()
    }
]);

const toggle = (event: PointerEvent) => {
    menu.value.toggle(event);
};

const getLogo = async () => {
  try {
    const res = await axios.get('/logo')
    imgUrl.value = res.data ?? '' // backend returns path or null
  } catch (err) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load logo' })
  }
}

onMounted(() => {
    user.details = page.props.auth.user;
    getLogo();
});
</script>
<template>
    <div class="h-screen">
        <ConfirmDialog></ConfirmDialog>
        <Toast />
        <Menubar>
            <template #start>
                <img :src="imgUrl" class="w-10 h-10 rounded" />
            </template>

            <template #end>
                <div class="card flex justify-center">
                    <button class="hover:bg-gray-300 px-3 rounded-md flex gap-2 items-center" @click="toggle">{{ user?.details?.name }} <ChevronDown class="w-4 h-4"/></button>
                    <TieredMenu ref="menu" id="overlay_tmenu" :model="items" popup />
                </div>
            </template>
        </Menubar>
        <slot></slot>
    </div>
</template>
