<template>
  <Head title="Tạo đơn hàng - AgriVerse Admin" />

  <div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
      <Link :href="route('admin.agriverse.orders.index')" class="text-gray-400 hover:text-gray-600">
        <i class="pi pi-arrow-left text-xl" />
      </Link>
      <h1 class="text-2xl font-bold text-gray-900">Tạo đơn hàng thủ công</h1>
    </div>

    <UiCard>
      <form @submit.prevent="submit" class="space-y-6">
        <h3 class="font-semibold text-gray-900">Thông tin khách hàng</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tên khách hàng <span class="text-red-500">*</span></label>
            <InputText v-model="form.customer_name" class="w-full" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
            <InputText v-model="form.customer_email" class="w-full" type="email" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
            <InputText v-model="form.customer_phone" class="w-full" />
          </div>
        </div>

        <h3 class="font-semibold text-gray-900">Địa chỉ giao hàng</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
            <InputText v-model="form.shipping_address" class="w-full" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tỉnh/Thành</label>
            <InputText v-model="form.shipping_province" class="w-full" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Quận/Huyện</label>
            <InputText v-model="form.shipping_district" class="w-full" />
          </div>
        </div>

        <h3 class="font-semibold text-gray-900">Sản phẩm</h3>
        <div v-for="(item, i) in form.items" :key="i" class="flex items-end gap-3 p-3 bg-gray-50 rounded-lg">
          <div class="flex-1">
            <label class="block text-xs font-medium text-gray-700 mb-1">Sản phẩm</label>
            <InputText v-model="item.product_name" placeholder="Tên sản phẩm" class="w-full" />
          </div>
          <div class="w-24">
            <label class="block text-xs font-medium text-gray-700 mb-1">SL</label>
            <InputNumber v-model="item.quantity" class="w-full" :min="1" />
          </div>
          <div class="w-32">
            <label class="block text-xs font-medium text-gray-700 mb-1">Đơn giá</label>
            <InputNumber v-model="item.price" class="w-full" :min="0" />
          </div>
          <button type="button" class="text-red-500 hover:text-red-700 p-2" @click="removeItem(i)">
            <i class="pi pi-trash" />
          </button>
        </div>
        <Button type="button" severity="secondary" outlined @click="addItem">
          <i class="pi pi-plus mr-1" /> Thêm sản phẩm
        </Button>

        <div class="flex gap-3 justify-end pt-4 border-t">
          <Link :href="route('admin.agriverse.orders.index')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">Hủy</Link>
          <Button type="submit" :loading="form.processing" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
            Tạo đơn hàng
          </Button>
        </div>
      </form>
    </UiCard>
  </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'

const form = useForm({
  customer_name: '',
  customer_email: '',
  customer_phone: '',
  shipping_address: '',
  shipping_province: '',
  shipping_district: '',
  items: [{ product_name: '', quantity: 1, price: 0 }],
})

function addItem() {
  form.items.push({ product_name: '', quantity: 1, price: 0 })
}

function removeItem(index) {
  form.items.splice(index, 1)
}

function submit() {
  form.post(route('admin.agriverse.orders.store'))
}
</script>

<style scoped>
</style>
