<template>
  <Head title="Dashboard - AgriVerse Admin" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Tổng quan</h1>
      <span class="text-sm text-gray-500">{{ new Date().toLocaleDateString('vi-VN') }}</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <UiCard>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500">Người dùng</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.users_count }}</p>
          </div>
          <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
            <i class="pi pi-users text-xl text-blue-600" />
          </div>
        </div>
      </UiCard>
      <UiCard>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500">Cửa hàng</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.stores_count }}</p>
          </div>
          <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center">
            <i class="pi pi-shop text-xl text-emerald-600" />
          </div>
        </div>
      </UiCard>
      <UiCard>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500">Sản phẩm</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.products_count }}</p>
          </div>
          <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center">
            <i class="pi pi-box text-xl text-amber-600" />
          </div>
        </div>
      </UiCard>
      <UiCard>
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-500">Đơn hàng</p>
            <p class="text-2xl font-bold text-gray-900">{{ stats.orders_count }}</p>
          </div>
          <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
            <i class="pi pi-shopping-cart text-xl text-purple-600" />
          </div>
        </div>
      </UiCard>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <UiCard>
        <template #header>
          <h2 class="font-semibold text-gray-900">Doanh thu</h2>
        </template>
        <p class="text-3xl font-bold text-emerald-600">
          {{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(stats.revenue) }}
        </p>
      </UiCard>
      <UiCard>
        <template #header>
          <h2 class="font-semibold text-gray-900">Đơn hàng gần đây</h2>
        </template>
        <div v-if="stats.recent_orders?.length" class="space-y-3">
          <div v-for="order in stats.recent_orders" :key="order.id" class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
            <div>
              <p class="text-sm font-medium text-gray-900">#{{ order.code }}</p>
              <p class="text-xs text-gray-500">{{ order.user?.name }}</p>
            </div>
            <span :class="order.status === 'completed' ? 'text-emerald-600' : order.status === 'cancelled' ? 'text-red-600' : 'text-amber-600'" class="text-sm font-medium">
              {{ order.status_text || order.status }}
            </span>
          </div>
        </div>
        <p v-else class="text-sm text-gray-400">Chưa có đơn hàng nào.</p>
      </UiCard>
    </div>
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'

const props = defineProps({
  stats: { type: Object, default: () => ({ users_count: 0, stores_count: 0, products_count: 0, orders_count: 0, revenue: 0, recent_orders: [] }) },
})
</script>

<style scoped>
</style>
