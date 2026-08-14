<template>
  <Head :title="'Hợp đồng: ' + contract.code" />

  <div class="space-y-6">
    <div class="flex items-center gap-3">
      <Link :href="route('admin.agriverse.contracts.index')" class="text-gray-400 hover:text-gray-600">
        <i class="pi pi-arrow-left text-xl" />
      </Link>
      <h1 class="text-2xl font-bold text-gray-900">Hợp đồng {{ contract.code }}</h1>
      <UiBadge :variant="contract.status === 'active' ? 'success' : contract.status === 'pending' ? 'warning' : contract.status === 'expired' ? 'danger' : 'default'">{{ contract.status }}</UiBadge>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <UiCard>
        <template #header><h2 class="font-semibold text-gray-900">Thông tin hợp đồng</h2></template>
        <div class="space-y-3 text-sm">
          <div class="flex justify-between"><span class="text-gray-500">Mã hợp đồng:</span><span class="font-mono font-medium">{{ contract.code }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Loại:</span><span>{{ contract.type_text || contract.type }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Trạng thái:</span><UiBadge :variant="contract.status === 'active' ? 'success' : contract.status === 'pending' ? 'warning' : 'danger'">{{ contract.status }}</UiBadge></div>
          <div class="flex justify-between"><span class="text-gray-500">Ngày bắt đầu:</span><span>{{ contract.start_date }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Ngày kết thúc:</span><span>{{ contract.end_date || '—' }}</span></div>
        </div>
      </UiCard>

      <UiCard v-if="contract.seller">
        <template #header><h2 class="font-semibold text-gray-900">Người bán</h2></template>
        <div class="space-y-2 text-sm">
          <p class="font-medium">{{ contract.seller.name }}</p>
          <p class="text-gray-500">{{ contract.seller.email }}</p>
        </div>
      </UiCard>

      <UiCard>
        <template #header><h2 class="font-semibold text-gray-900">Điều khoản</h2></template>
        <div class="space-y-3 text-sm">
          <div class="flex justify-between"><span class="text-gray-500">Hoa hồng:</span><span class="font-medium">{{ contract.commission_rate || 0 }}%</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Phí cố định:</span><span class="font-medium">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(contract.fixed_fee || 0) }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Thanh toán:</span><span class="font-medium">{{ contract.payment_terms || '—' }}</span></div>
        </div>
      </UiCard>
    </div>

    <UiCard v-if="contract.terms">
      <template #header><h2 class="font-semibold text-gray-900">Nội dung hợp đồng</h2></template>
      <div class="text-sm text-gray-700 whitespace-pre-line">{{ contract.terms }}</div>
    </UiCard>

    <UiCard v-if="contract.files?.length">
      <template #header><h2 class="font-semibold text-gray-900">Tệp đính kèm</h2></template>
      <div class="space-y-2">
        <div v-for="file in contract.files" :key="file.id" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
          <i class="pi pi-file-pdf text-red-500 text-xl" />
          <span class="text-sm font-medium text-gray-700">{{ file.name }}</span>
          <a :href="file.url" target="_blank" class="ml-auto text-emerald-600 hover:text-emerald-800 text-sm">Tải xuống</a>
        </div>
      </div>
    </UiCard>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'
import UiBadge from '@/Components/ui/UiBadge.vue'

const props = defineProps({
  contract: { type: Object, required: true },
})
</script>

<style scoped>
</style>
