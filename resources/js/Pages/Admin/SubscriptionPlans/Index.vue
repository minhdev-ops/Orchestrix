<template>
  <Head title="Quản lý gói đăng ký - AgriVerse Admin" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Gói đăng ký</h1>
      <Link :href="route('admin.agriverse.subscription-plans.create')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
        <i class="pi pi-plus" /> Thêm gói
      </Link>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="plan in plans" :key="plan.id" class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
        <div class="p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">{{ plan.name }}</h3>
            <UiBadge v-if="plan.is_popular" variant="primary">Phổ biến</UiBadge>
          </div>
          <p class="text-sm text-gray-500 mb-4">{{ plan.description || '—' }}</p>
          <div class="mb-4">
            <span class="text-3xl font-bold text-gray-900">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(plan.price) }}</span>
            <span class="text-sm text-gray-500">/{{ plan.interval === 'monthly' ? 'tháng' : 'năm' }}</span>
          </div>
          <ul class="space-y-2 text-sm text-gray-700 mb-6">
            <li v-for="(feature, i) in (plan.features || [])" :key="i" class="flex items-center gap-2">
              <i class="pi pi-check-circle text-emerald-500 text-xs" />
              {{ feature }}
            </li>
          </ul>
          <div class="flex items-center justify-between pt-4 border-t">
            <UiBadge :variant="plan.is_active ? 'success' : 'danger'">{{ plan.is_active ? 'Đang bán' : 'Tạm ngưng' }}</UiBadge>
            <div class="flex gap-2">
              <Link :href="route('admin.agriverse.subscription-plans.edit', plan.id)" class="text-amber-600 hover:text-amber-800">
                <i class="pi pi-pencil" />
              </Link>
              <button class="text-red-600 hover:text-red-800" @click="deletePlan(plan.id)">
                <i class="pi pi-trash" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import UiBadge from '@/Components/ui/UiBadge.vue'

const props = defineProps({
  plans: { type: Array, required: true },
})

function deletePlan(id) {
  if (confirm('Xóa gói đăng ký này?')) {
    router.delete(route('admin.agriverse.subscription-plans.destroy', id))
  }
}
</script>

<style scoped>
</style>
