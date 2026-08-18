<template>
  <Head :title="'Quét AI #' + scan.id" />

  <div class="space-y-6">
    <div class="flex items-center gap-3">
      <Link :href="route('admin.agriverse.scans.index')" class="text-gray-400 hover:text-gray-600">
        <i class="pi pi-arrow-left text-xl" />
      </Link>
      <h1 class="text-2xl font-bold text-gray-900">Chi tiết quét #{{ scan.id }}</h1>
      <UiBadge :variant="scan.status === 'completed' ? 'success' : scan.status === 'running' ? 'warning' : scan.status === 'failed' ? 'danger' : 'default'">{{ scan.status }}</UiBadge>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <UiCard>
        <template #header><h2 class="font-semibold text-gray-900">Thông tin</h2></template>
        <div class="space-y-3 text-sm">
          <div class="flex justify-between"><span class="text-gray-500">ID:</span><span>#{{ scan.id }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Loại:</span><span>{{ scan.type }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Trạng thái:</span><UiBadge :variant="scan.status === 'completed' ? 'success' : scan.status === 'running' ? 'warning' : 'danger'">{{ scan.status }}</UiBadge></div>
          <div class="flex justify-between"><span class="text-gray-500">Đối tượng:</span><span>{{ scan.target_type }} #{{ scan.target_id }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Ngày tạo:</span><span>{{ scan.created_at }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Hoàn thành:</span><span>{{ scan.completed_at || '—' }}</span></div>
        </div>
      </UiCard>

      <UiCard v-if="scan.status === 'completed' && scan.result">
        <template #header><h2 class="font-semibold text-gray-900">Kết quả</h2></template>
        <div class="space-y-3">
          <div v-if="scan.result.risk_score !== undefined" class="flex items-center gap-2">
            <span class="text-sm text-gray-500">Điểm rủi ro:</span>
            <span :class="scan.result.risk_score > 70 ? 'text-red-600' : scan.result.risk_score > 40 ? 'text-amber-600' : 'text-emerald-600'" class="font-bold text-lg">
              {{ scan.result.risk_score }}/100
            </span>
          </div>
          <div v-if="scan.result.summary" class="text-sm text-gray-700 bg-gray-50 p-4 rounded-lg">
            {{ scan.result.summary }}
          </div>
          <div v-if="scan.result.details" class="text-sm">
            <pre class="bg-gray-50 p-4 rounded-lg overflow-auto text-xs">{{ JSON.stringify(scan.result.details, null, 2) }}</pre>
          </div>
        </div>
      </UiCard>
    </div>

    <UiCard v-if="scan.log">
      <template #header><h2 class="font-semibold text-gray-900">Nhật ký</h2></template>
      <pre class="text-xs text-gray-600 bg-gray-50 p-4 rounded-lg overflow-auto max-h-64">{{ scan.log }}</pre>
    </UiCard>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'
import UiBadge from '@/Components/ui/UiBadge.vue'

const props = defineProps({
  scan: { type: Object, required: true },
})
</script>

<style scoped>
</style>
