<script setup lang="ts">
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SalesLayout from '@/Layouts/SalesLayout.vue';
import { useThrobber } from '@/stores/throbber';
import axios from 'axios';
import type { AxiosResponse, AxiosError } from 'axios';
import { Button, Dialog, InputNumber, InputText, useToast } from 'primevue';
import { ref, computed, nextTick, onMounted } from 'vue';

interface Product {
  id: number;          // batch ID
  product_id: number;  // original product ID
  batch_code: string;
  selling_price: number;
  product: { id: number; name: string; code: string };
  stock: { id: number; amount: number };
}

interface InvoiceItem {
  product_id: number;   // batch ID
  name: string;
  batch_code: string;
  price: number;
  quantity: number;
  discount?: number;
}

const throbber = useThrobber();
const toast = useToast();

const products = ref<Product[]>([]);
const invoiceItems = ref<InvoiceItem[]>([]);
const invoiceDiscount = ref(0);
const paymentDialog = ref(false);
const payment_amount = ref(0);
const searchTerm = ref('');
const searchInput = ref<InstanceType<typeof InputText> | null>(null);
const currentStock = ref<Record<number, number>>({});

const balance = computed(() => payment_amount.value - invoiceTotal.value);
const invoiceTotal = computed(() => {
  return invoiceItems.value.reduce((sum, item) => {
    return sum + (item.price - (item.discount ?? 0)) * item.quantity;
  }, 0) - invoiceDiscount.value;
});

// Load products
const getProducts = async () => {
  throbber.start();
  try {
    const response: AxiosResponse<{ data: Product[] }> = await axios.post('/sales/invoice/products');
    products.value = response.data.data;
    products.value.forEach(p => {
      currentStock.value[p.id] = p.stock.amount;
    });
  } catch (err) {
    console.error(err);
  } finally {
    throbber.stop();
  }
};

// Add item to invoice
const addToInvoice = (batch: Product) => {
  if (currentStock.value[batch.id] <= 0) {
    toast.add({ severity: 'warn', summary: 'Stock', detail: 'Not enough stock!', life: 2000 });
    return;
  }

  const existing = invoiceItems.value.find(i => i.product_id === batch.id);
  if (existing) {
    existing.quantity++;
    currentStock.value[batch.id]--;
  } else {
    invoiceItems.value.push({
      product_id: batch.id,
      name: batch.product.name,
      batch_code: batch.batch_code,
      price: batch.selling_price,
      quantity: 1
    });
    currentStock.value[batch.id]--;
  }
};


// Remove from invoice
const removeFromInvoice = (item: InvoiceItem) => {
  const batch = products.value.find(p => p.id === item.product_id);
  if (batch) batch.stock.amount += item.quantity;
  invoiceItems.value = invoiceItems.value.filter(i => i.product_id !== item.product_id);
};

// Handle quantity change
const onQuantityChange = (item: InvoiceItem) => {
    console.log('item >>', item);
  const batch = products.value.find(p => p.id === item.product_id);
  if (!batch) return;

  const qty = invoiceItems.value.find(i => i.product_id === item.product_id)?.quantity ?? 0;
  currentStock.value[batch.id] = batch.stock.amount - qty;
};

// Payment
const submitInvoice = async () => {
  if (!invoiceItems.value.length) return;

  const request = {
    items: invoiceItems.value,
    discount: invoiceDiscount.value,
    payment: payment_amount.value,
    balance: balance.value
  };

  throbber.start();
  try {
    await axios.post('/sales/invoice/create', request);
    toast.add({ severity: 'success', summary: 'Success', detail: 'Invoice Saved', life: 3000 });

    // Update actual stock on save
    invoiceItems.value.forEach(item => {
      const batch = products.value.find(p => p.id === item.product_id);
      if (batch) batch.stock.amount = currentStock.value[item.product_id];
    });

    invoiceItems.value = [];
    invoiceDiscount.value = 0;
    payment_amount.value = 0;
    paymentDialog.value = false;
    resetSearch();
  } catch (err: unknown) {
    const error = err as AxiosError<{ message: string }>;
    toast.add({ severity: 'error', summary: 'Failed', detail: error.message, life: 3000 });
  } finally {
    throbber.stop();
  }
};

const paymentConfirm = () => {
  if (!invoiceItems.value.length) return;
  paymentDialog.value = true;
};

const cancelInvoice = () => {
  invoiceItems.value.forEach(item => {
    const batch = products.value.find(p => p.id === item.product_id);
    if (batch) batch.stock.amount += item.quantity;
  });
  invoiceItems.value = [];
  invoiceDiscount.value = 0;
  payment_amount.value = 0;
  paymentDialog.value = false;
  resetSearch();
};

// Search
const resetSearch = () => {
  searchTerm.value = '';
  focus();
};

const handleSearchEnter = () => {
  if (!filteredProducts.value.length) return;
  addToInvoice(filteredProducts.value[0]);
};

const filteredProducts = computed(() => {
  if (!searchTerm.value) return products.value;
  const term = searchTerm.value.toLowerCase();
  return products.value.filter(p =>
    p.product.name.toLowerCase().includes(term) ||
    p.batch_code.toLowerCase().includes(term) ||
    p.product.code.toLowerCase().includes(term)
  );
});

const focus = () => {
  nextTick(() => {
    // PrimeVue InputText exposes .focus() method
    $refs
    (searchInput.value as any)?.$refs?.input?.focus?.();
  });
};

onMounted(() => {
  getProducts();
  focus();
});
</script>

<template>
  <SalesLayout>
    <template #header>Sales POS</template>

    <div class="grid grid-cols-12 h-full">
      <!-- Products + Search -->
      <div class="col-span-6 border-r p-4 flex flex-col">
        <div class="relative w-full mb-4">
          <InputText
            v-model="searchTerm"
            ref="searchInput"
            placeholder="Scan barcode or search..."
            class="h-12 w-full text-lg border rounded px-4 pr-10 focus:ring-2 focus:ring-cyan-500"
            @keyup.enter="handleSearchEnter"
          />
          <button
            v-if="searchTerm"
            @click="resetSearch(); focus()"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
          >✕</button>
        </div>

        <div class="overflow-y-auto flex-1">
          <table class="w-full table-auto text-sm">
            <thead class="sticky top-0 bg-gray-100">
              <tr>
                <th class="p-2 text-left">Product</th>
                <th class="p-2 text-center">Batch</th>
                <th class="p-2 text-center">Stock</th>
                <th class="p-2 text-center">Price</th>
                <th class="p-2 text-center">Add</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="p in filteredProducts" :key="p.id" class="hover:bg-cyan-50 cursor-pointer" @dblclick="addToInvoice(p)">
                <td class="p-2">{{ p.product.name }}</td>
                <td class="p-2 text-center">{{ p.batch_code }}</td>
                <td class="p-2 text-center">{{ currentStock[p.id] }}</td>
                <td class="p-2 text-center">{{ p.selling_price }}</td>
                <td class="p-2 text-center">
                  <Button icon="pi pi-plus" size="small" @click="addToInvoice(p)" />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Invoice -->
      <div class="col-span-6 flex flex-col">
        <div class="flex-1 overflow-y-auto p-4">
          <table class="w-full text-sm">
            <thead class="bg-gray-100">
              <tr>
                <th class="text-start p-1">Product</th>
                <th class="text-start p-1">Qty</th>
                <th class="text-start p-1">Price</th>
                <th class="text-start p-1">Disc</th>
                <th class="text-start p-1">Sub</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in invoiceItems" :key="item.product_id" class="border-b">
                <td>{{ item.name }}</td>
                <td>
                  <input v-model.number="item.quantity" type="number" min="1" class="w-16 text-center border rounded"
                    @change="onQuantityChange(item)" />
                </td>
                <td>{{ item.price.toFixed(2) }}</td>
                <td>
                  <input v-model.number="item.discount" type="number" min="0" class="w-24 text-center border rounded" placeholder="0"/>
                </td>
                <td>{{ ((item.price - (item.discount ?? 0)) * item.quantity).toFixed(2) }}</td>
                <td>
                  <Button icon="pi pi-trash" size="small" severity="danger" @click="removeFromInvoice(item)" />
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Totals & Checkout -->
        <div class="p-4 border-t bg-gray-50">
          <div class="flex justify-between mb-2">
            <span>Invoice Discount:</span>
            <InputNumber v-model.number="invoiceDiscount" input-class="w-24 text-center" placeholder="0" :min-fraction-digits="2"/>
          </div>
          <div class="flex justify-between text-lg font-bold mb-4">
            <span>Total:</span>
            <InputNumber v-model.number="invoiceTotal" input-class="w-24 text-center" placeholder="0" :min-fraction-digits="2" disabled/>
          </div>
          <div class="flex gap-2">
            <Button label="Cancel" severity="contrast" class="w-1/4" @click="cancelInvoice" icon="pi pi-trash"/>
            <Button label="Proceed" class="w-3/4" @click="paymentConfirm"/>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Dialog -->
    <Dialog modal header="Make Payment" v-model:visible="paymentDialog" class="w-[500px]">
      <div class="flex items-center gap-4 mb-4">
        <label class="font-semibold w-28">Total Bill</label>
        <InputNumber :model-value="invoiceTotal" disabled class="flex-auto" input-class="text-right" :min-fraction-digits="2"/>
      </div>
      <div class="flex items-center gap-4 mb-4">
        <label class="font-semibold w-28">Payment</label>
        <InputNumber v-model="payment_amount" class="flex-auto" input-class="text-right" :min-fraction-digits="2"/>
      </div>
      <div class="flex items-center gap-4 mb-8">
        <label class="font-semibold w-28">Balance</label>
        <InputNumber :model-value="balance" readonly class="flex-auto"
          :input-class="balance >= 0 ? 'bg-green-200 text-right' : 'bg-red-200 text-right'" :min-fraction-digits="2"/>
      </div>
      <div class="flex justify-end gap-2">
        <PrimaryButton :disabled="balance < 0" @click="submitInvoice">Confirm</PrimaryButton>
      </div>
    </Dialog>
  </SalesLayout>
</template>
