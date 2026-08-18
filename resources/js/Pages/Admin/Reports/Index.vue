<template>
  <Head title="Báo cáo - AgriVerse Admin" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Báo cáo & Thống kê</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <UiCard>
        <div class="text-center">
          <p class="text-sm text-gray-500">Tổng doanh thu</p>
          <p class="text-2xl font-bold text-emerald-600 mt-1">
            {{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(reports.total_revenue || 0) }}
          </p>
          <p class="text-xs text-gray-400 mt-1">
            <i :class="(reports.revenue_growth || 0) >= 0 ? 'pi pi-arrow-up text-emerald-500' : 'pi pi-arrow-down text-red-500'" />
            {{ Math.abs(reports.revenue_growth || 0) }}% so với tháng trước
          </p>
        </div>
      </UiCard>
      <UiCard>
        <div class="text-center">
          <p class="text-sm text-gray-500">Tổng đơn hàng</p>
          <p class="text-2xl font-bold text-gray-900 mt-1">{{ reports.total_orders || 0 }}</p>
        </div>
      </UiCard>
      <UiCard>
        <div class="text-center">
          <p class="text-sm text-gray-500">Người dùng mới</p>
          <p class="text-2xl font-bold text-gray-900 mt-1">{{ reports.new_users || 0 }}</p>
          <p class="text-xs text-gray-400 mt-1">Tháng này</p>
        </div>
      </UiCard>
      <UiCard>
        <div class="text-center">
          <p class="text-sm text-gray-500">Đơn hàng trung bình</p>
          <p class="text-2xl font-bold text-gray-900 mt-1">
            {{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(reports.average_order_value || 0) }}
          </p>
        </div>
      </UiCard>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <UiCard>
        <template #header><h2 class="font-semibold text-gray-900">Doanh thu theo tháng</h2></template>
        <div v-if="reports.monthly_revenue?.length" class="space-y-2">
          <div v-for="(item, i) in reports.monthly_revenue" :key="i" class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
            <span class="text-sm text-gray-600">{{ item.month }}</span>
            <span class="text-sm font-medium">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.revenue) }}</span>
          </div>
        </div>
        <p v-else class="text-sm text-gray-400">Chưa có dữ liệu.</p>
      </UiCard>

      <UiCard>
        <template #header><h2 class="font-semibold text-gray-900">Top sản phẩm bán chạy</h2></template>
        <div v-if="reports.top_products?.length" class="space-y-2">
          <div v-for="(item, i) in reports.top_products" :key="i" class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
            <div class="flex items-center gap-3">
              <span class="text-xs font-bold text-gray-400 w-5">#{{ i + 1 }}</span>
              <span class="text-sm text-gray-700">{{ item.name }}</span>
            </div>
            <span class="text-sm font-medium">{{ item.sold_count }} đã bán</span>
          </div>
        </div>
        <p v-else class="text-sm text-gray-400">Chưa có dữ liệu.</p>
      </UiCard>
    </div>

    <UiCard>
      <template #header>
        <div class="flex items-center justify-between">
          <h2 class="font-semibold text-gray-900">Báo cáo chi tiết</h2>
          <div class="flex gap-2">
            <Select v-model="reportType" :options="reportTypes" option-label="label" option-value="value" placeholder="Loại báo cáo" class="w-44" />
            <Button severity="secondary" @click="exportReport">Xuất báo cáo</Button>
          </div>
        </div>
      </template>
      <div class="text-sm text-gray-500">
        <p>Chọn loại báo cáo và khoảng thời gian để xem chi tiết.</p>
        <p class="mt-2">Sử dụng các bộ lọc ở trên để tùy chỉnh dữ liệu hiển thị.</p>
      </div>
    </UiCard>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'

const props = defineProps({
  reports: { type: Object, default: () => ({}) },
})

const reportType = ref('revenue')
const reportTypes = [
  { label: 'Doanh thu', value: 'revenue' },
  { label: 'Đơn hàng', value: 'orders' },
  { label: 'Người dùng', value: 'users' },
  { label: 'Sản phẩm', value: 'products' },
]

function exportReport() {
  window.open(route('admin.agriverse.reports.export', { type: reportType.value }), '_blank')
}
</script>

<style scoped>
</style>
