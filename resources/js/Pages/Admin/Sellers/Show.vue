<template>
  <Head :title="'Người bán: ' + seller.name" />

  <div class="space-y-6">
    <div class="flex items-center gap-3">
      <Link :href="route('admin.agriverse.sellers.index')" class="text-gray-400 hover:text-gray-600">
        <i class="pi pi-arrow-left text-xl" />
      </Link>
      <h1 class="text-2xl font-bold text-gray-900">{{ seller.name }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <UiCard>
        <template #header><h2 class="font-semibold text-gray-900">Thông tin</h2></template>
        <div class="space-y-3 text-sm">
          <div><span class="text-gray-500">Email:</span><p class="font-medium">{{ seller.email }}</p></div>
          <div><span class="text-gray-500">Số điện thoại:</span><p class="font-medium">{{ seller.phone || '—' }}</p></div>
          <div><span class="text-gray-500">Ngày tham gia:</span><p class="font-medium">{{ seller.created_at }}</p></div>
        </div>
      </UiCard>

      <UiCard>
        <template #header><h2 class="font-semibold text-gray-900">Cửa hàng</h2></template>
        <div v-if="seller.store" class="space-y-3 text-sm">
          <p class="font-medium text-emerald-600">{{ seller.store.name }}</p>
          <UiBadge :variant="seller.store.status === 'active' ? 'success' : 'warning'">{{ seller.store.status }}</UiBadge>
          <p class="text-gray-500">{{ seller.store.address || '—' }}</p>
          <Link :href="route('admin.agriverse.stores.show', seller.store.id)" class="text-emerald-600 hover:text-emerald-800 text-sm">
            Xem cửa hàng <i class="pi pi-arrow-right text-xs" />
          </Link>
        </div>
        <p v-else class="text-sm text-gray-400">Chưa có cửa hàng.</p>
      </UiCard>

      <UiCard>
        <template #header><h2 class="font-semibold text-gray-900">Thống kê</h2></template>
        <div class="space-y-3 text-sm">
          <div class="flex justify-between"><span class="text-gray-500">Sản phẩm:</span><span class="font-medium">{{ seller.store?.products_count || 0 }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Doanh số:</span><span class="font-medium">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(seller.total_sales || 0) }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Đơn hàng:</span><span class="font-medium">{{ seller.total_orders || 0 }}</span></div>
        </div>
      </UiCard>
    </div>

    <UiCard v-if="seller.store?.products?.length">
      <template #header><h2 class="font-semibold text-gray-900">Sản phẩm</h2></template>
      <DataTable :value="seller.store.products" striped-rows class="text-sm">
        <Column field="name" header="Tên sản phẩm">
          <template #body="{ data }">
            <Link :href="route('admin.agriverse.products.show', data.id)" class="text-emerald-600 hover:text-emerald-800">{{ data.name }}</Link>
          </template>
        </Column>
        <Column field="price" header="Giá">
          <template #body="{ data }">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(data.price) }}</template>
        </Column>
        <Column field="stock" header="Tồn kho" />
        <Column field="status" header="Trạng thái">
          <template #body="{ data }">
            <UiBadge :variant="data.status === 'active' ? 'success' : 'warning'">{{ data.status }}</UiBadge>
          </template>
        </Column>
      </DataTable>
    </UiCard>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'
import UiBadge from '@/Components/ui/UiBadge.vue'

const props = defineProps({
  seller: { type: Object, required: true },
})
</script>

<style scoped>
</style>
