<template>
  <Head title="Quản lý mã giảm giá - AgriVerse Admin" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Quản lý mã giảm giá</h1>
      <Link :href="route('admin.agriverse.coupons.create')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
        <i class="pi pi-plus" /> Thêm mã giảm giá
      </Link>
    </div>

    <UiCard>
      <DataTable :value="coupons.data" striped-rows paginator :rows="10" :total-records="coupons.total" :first="offset" @page="onPage" class="text-sm">
        <Column field="id" header="ID" sortable style="width:80px" />
        <Column field="code" header="Mã" sortable>
          <template #body="{ data }">
            <span class="font-mono font-bold text-emerald-600">{{ data.code }}</span>
          </template>
        </Column>
        <Column field="type" header="Loại" sortable>
          <template #body="{ data }">
            <UiBadge :variant="data.type === 'percentage' ? 'primary' : 'success'">{{ data.type === 'percentage' ? '% Giảm' : 'Giảm tiền' }}</UiBadge>
          </template>
        </Column>
        <Column field="value" header="Giá trị" sortable>
          <template #body="{ data }">
            {{ data.type === 'percentage' ? data.value + '%' : new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(data.value) }}
          </template>
        </Column>
        <Column field="min_order" header="Đơn tối thiểu" sortable>
          <template #body="{ data }">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(data.min_order || 0) }}</template>
        </Column>
        <Column field="usage_limit" header="SL sử dụng" sortable />
        <Column field="expires_at" header="Hết hạn" sortable>
          <template #body="{ data }">
            <span :class="isExpired(data.expires_at) ? 'text-red-500' : 'text-gray-900'">{{ data.expires_at || '—' }}</span>
          </template>
        </Column>
        <Column field="is_active" header="Kích hoạt" sortable>
          <template #body="{ data }">
            <i :class="data.is_active ? 'pi pi-check-circle text-emerald-500' : 'pi pi-times-circle text-red-400'" />
          </template>
        </Column>
        <Column header="Thao tác" style="width:120px">
          <template #body="{ data }">
            <div class="flex gap-2">
              <Link :href="route('admin.agriverse.coupons.edit', data.id)" class="text-amber-600 hover:text-amber-800"><i class="pi pi-pencil" /></Link>
              <button class="text-red-600 hover:text-red-800" @click="deleteCoupon(data.id)"><i class="pi pi-trash" /></button>
            </div>
          </template>
        </Column>
      </DataTable>
    </UiCard>
  </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'
import UiBadge from '@/Components/ui/UiBadge.vue'

const props = defineProps({
  coupons: { type: Object, required: true },
})

const offset = (props.coupons.current_page - 1) * props.coupons.per_page

function isExpired(date) {
  if (!date) return false
  return new Date(date) < new Date()
}

function onPage(event) {
  router.get(route('admin.agriverse.coupons.index'), { page: event.page + 1 }, { preserveState: true })
}

function deleteCoupon(id) {
  if (confirm('Xóa mã giảm giá này?')) {
    router.delete(route('admin.agriverse.coupons.destroy', id))
  }
}
</script>

<style scoped>
</style>
