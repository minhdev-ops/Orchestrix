<template>
  <Head title="Quản lý hoàn tiền - AgriVerse Admin" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Quản lý hoàn tiền</h1>
    </div>

    <UiCard>
      <DataTable :value="refunds.data" striped-rows paginator :rows="10" :total-records="refunds.total" :first="offset" @page="onPage" class="text-sm">
        <Column field="id" header="ID" sortable style="width:80px" />
        <Column field="code" header="Mã yêu cầu" sortable>
          <template #body="{ data }">
            <Link :href="route('admin.agriverse.refunds.show', data.id)" class="text-emerald-600 hover:text-emerald-800 font-mono">{{ data.code }}</Link>
          </template>
        </Column>
        <Column field="order.code" header="Đơn hàng" sortable />
        <Column field="user.name" header="Người yêu cầu" sortable />
        <Column field="amount" header="Số tiền" sortable>
          <template #body="{ data }">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(data.amount) }}</template>
        </Column>
        <Column field="reason" header="Lý do" sortable>
          <template #body="{ data }">
            <span class="text-sm text-gray-600 truncate block max-w-xs">{{ data.reason }}</span>
          </template>
        </Column>
        <Column field="status" header="Trạng thái" sortable>
          <template #body="{ data }">
            <UiBadge :variant="data.status === 'approved' ? 'success' : data.status === 'rejected' ? 'danger' : data.status === 'pending' ? 'warning' : 'info'">{{ data.status_text || data.status }}</UiBadge>
          </template>
        </Column>
        <Column field="created_at" header="Ngày tạo" sortable />
        <Column header="Thao tác" style="width:80px">
          <template #body="{ data }">
            <Link :href="route('admin.agriverse.refunds.show', data.id)" class="text-blue-600 hover:text-blue-800"><i class="pi pi-eye" /></Link>
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
  refunds: { type: Object, required: true },
})

const offset = (props.refunds.current_page - 1) * props.refunds.per_page

function onPage(event) {
  router.get(route('admin.agriverse.refunds.index'), { page: event.page + 1 }, { preserveState: true })
}
</script>

<style scoped>
</style>
