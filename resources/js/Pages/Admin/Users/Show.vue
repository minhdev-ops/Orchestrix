<template>
  <Head :title="'Người dùng: ' + user.name" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <Link :href="route('admin.agriverse.users.index')" class="text-gray-400 hover:text-gray-600">
          <i class="pi pi-arrow-left text-xl" />
        </Link>
        <h1 class="text-2xl font-bold text-gray-900">{{ user.name }}</h1>
        <UiBadge :variant="user.role === 'admin' ? 'primary' : user.role === 'seller' ? 'warning' : 'default'">{{ user.role }}</UiBadge>
      </div>
      <div class="flex gap-2">
        <Link :href="route('admin.agriverse.users.edit', user.id)" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors text-sm font-medium">
          <i class="pi pi-pencil" /> Sửa
        </Link>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <UiCard>
        <template #header><h2 class="font-semibold text-gray-900">Thông tin cá nhân</h2></template>
        <div class="space-y-3">
          <div><span class="text-sm text-gray-500">Email:</span><p class="text-sm font-medium text-gray-900">{{ user.email }}</p></div>
          <div><span class="text-sm text-gray-500">Số điện thoại:</span><p class="text-sm font-medium text-gray-900">{{ user.phone || '—' }}</p></div>
          <div><span class="text-sm text-gray-500">Xác thực email:</span><i :class="user.email_verified_at ? 'pi pi-check text-emerald-500' : 'pi pi-times text-red-400'" /></div>
          <div><span class="text-sm text-gray-500">Ngày tham gia:</span><p class="text-sm font-medium text-gray-900">{{ user.created_at }}</p></div>
        </div>
      </UiCard>

      <UiCard>
        <template #header><h2 class="font-semibold text-gray-900">Địa chỉ</h2></template>
        <div v-if="user.addresses?.length" class="space-y-2">
          <div v-for="addr in user.addresses" :key="addr.id" class="p-3 bg-gray-50 rounded-lg text-sm text-gray-700">
            <p>{{ addr.address }}, {{ addr.ward }}, {{ addr.district }}, {{ addr.province }}</p>
            <p v-if="addr.is_default" class="text-xs text-emerald-600 mt-1"><i class="pi pi-check-circle" /> Mặc định</p>
          </div>
        </div>
        <p v-else class="text-sm text-gray-400">Chưa có địa chỉ.</p>
      </UiCard>

      <UiCard>
        <template #header><h2 class="font-semibold text-gray-900">Thống kê</h2></template>
        <div class="space-y-3">
          <div class="flex justify-between"><span class="text-sm text-gray-500">Đơn hàng:</span><span class="text-sm font-medium">{{ user.orders?.length || 0 }}</span></div>
          <div class="flex justify-between"><span class="text-sm text-gray-500">Đánh giá:</span><span class="text-sm font-medium">{{ user.reviews?.length || 0 }}</span></div>
        </div>
      </UiCard>
    </div>

    <UiCard v-if="user.orders?.length">
      <template #header><h2 class="font-semibold text-gray-900">Đơn hàng</h2></template>
      <DataTable :value="user.orders" striped-rows class="text-sm">
        <Column field="code" header="Mã đơn" />
        <Column field="total" header="Tổng tiền">
          <template #body="{ data }">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(data.total) }}</template>
        </Column>
        <Column field="status" header="Trạng thái">
          <template #body="{ data }">
            <UiBadge :variant="data.status === 'completed' ? 'success' : data.status === 'cancelled' ? 'danger' : 'warning'">{{ data.status }}</UiBadge>
          </template>
        </Column>
        <Column field="created_at" header="Ngày tạo" />
      </DataTable>
    </UiCard>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'
import UiBadge from '@/Components/ui/UiBadge.vue'

const props = defineProps({
  user: { type: Object, required: true },
})
</script>

<style scoped>
</style>
