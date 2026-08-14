<template>
  <Head :title="'Giao dịch: ' + transaction.code" />

  <div class="space-y-6">
    <div class="flex items-center gap-3">
      <Link :href="route('admin.agriverse.transactions.index')" class="text-gray-400 hover:text-gray-600">
        <i class="pi pi-arrow-left text-xl" />
      </Link>
      <h1 class="text-2xl font-bold text-gray-900">Giao dịch {{ transaction.code }}</h1>
      <UiBadge :variant="transaction.status === 'completed' ? 'success' : transaction.status === 'pending' ? 'warning' : 'danger'">{{ transaction.status }}</UiBadge>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <UiCard>
        <template #header><h2 class="font-semibold text-gray-900">Chi tiết giao dịch</h2></template>
        <div class="space-y-3 text-sm">
          <div class="flex justify-between"><span class="text-gray-500">Mã giao dịch:</span><span class="font-mono font-medium">{{ transaction.code }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Loại:</span><span class="font-medium">{{ transaction.type_text || transaction.type }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Số tiền:</span>
            <span :class="transaction.type === 'withdrawal' ? 'text-red-600' : 'text-emerald-600'" class="font-bold text-lg">
              {{ transaction.type === 'withdrawal' ? '-' : '+' }}{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(Math.abs(transaction.amount)) }}
            </span>
          </div>
          <div class="flex justify-between"><span class="text-gray-500">Phí:</span><span>{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(transaction.fee || 0) }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Phương thức:</span><span>{{ transaction.payment_method }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Trạng thái:</span><UiBadge :variant="transaction.status === 'completed' ? 'success' : transaction.status === 'pending' ? 'warning' : 'danger'">{{ transaction.status }}</UiBadge></div>
          <div class="flex justify-between"><span class="text-gray-500">Ngày tạo:</span><span>{{ transaction.created_at }}</span></div>
        </div>
      </UiCard>

      <UiCard v-if="transaction.user">
        <template #header><h2 class="font-semibold text-gray-900">Người dùng</h2></template>
        <div class="space-y-2 text-sm">
          <p class="font-medium">{{ transaction.user.name }}</p>
          <p class="text-gray-500">{{ transaction.user.email }}</p>
        </div>
      </UiCard>
    </div>

    <UiCard v-if="transaction.metadata">
      <template #header><h2 class="font-semibold text-gray-900">Thông tin bổ sung</h2></template>
      <pre class="text-sm text-gray-700 bg-gray-50 p-4 rounded-lg overflow-auto">{{ JSON.stringify(transaction.metadata, null, 2) }}</pre>
    </UiCard>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'
import UiBadge from '@/Components/ui/UiBadge.vue'

const props = defineProps({
  transaction: { type: Object, required: true },
})
</script>

<style scoped>
</style>
