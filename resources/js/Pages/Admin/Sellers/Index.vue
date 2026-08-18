<template>
  <Head title="Quản lý người bán - AgriVerse Admin" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Quản lý người bán</h1>
    </div>

    <UiCard>
      <DataTable :value="sellers.data" striped-rows paginator :rows="10" :total-records="sellers.total" :first="offset" @page="onPage" class="text-sm">
        <Column field="id" header="ID" sortable style="width:80px" />
        <Column field="name" header="Tên" sortable>
          <template #body="{ data }">
            <Link :href="route('admin.agriverse.sellers.show', data.id)" class="text-emerald-600 hover:text-emerald-800 font-medium">{{ data.name }}</Link>
          </template>
        </Column>
        <Column field="email" header="Email" sortable />
        <Column field="store.name" header="Cửa hàng" sortable />
        <Column field="store.status" header="Trạng thái" sortable>
          <template #body="{ data }">
            <UiBadge v-if="data.store" :variant="data.store.status === 'active' ? 'success' : data.store.status === 'pending' ? 'warning' : 'danger'">{{ data.store.status }}</UiBadge>
            <span v-else class="text-gray-400">—</span>
          </template>
        </Column>
        <Column field="products_count" header="Sản phẩm" sortable />
        <Column field="total_sales" header="Doanh số" sortable>
          <template #body="{ data }">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(data.total_sales || 0) }}</template>
        </Column>
        <Column field="created_at" header="Ngày tạo" sortable />
        <Column header="Thao tác" style="width:80px">
          <template #body="{ data }">
            <Link :href="route('admin.agriverse.sellers.show', data.id)" class="text-blue-600 hover:text-blue-800"><i class="pi pi-eye" /></Link>
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
  sellers: { type: Object, required: true },
})

const offset = (props.sellers.current_page - 1) * props.sellers.per_page

function onPage(event) {
  router.get(route('admin.agriverse.sellers.index'), { page: event.page + 1 }, { preserveState: true })
}
</script>

<style scoped>
</style>
