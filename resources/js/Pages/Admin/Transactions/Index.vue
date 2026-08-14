<template>
  <Head title="Quản lý giao dịch - AgriVerse Admin" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Quản lý giao dịch</h1>
    </div>

    <UiCard>
      <DataTable :value="transactions.data" striped-rows paginator :rows="10" :total-records="transactions.total" :first="offset" @page="onPage" class="text-sm">
        <Column field="id" header="ID" sortable style="width:80px" />
        <Column field="code" header="Mã GD" sortable>
          <template #body="{ data }">
            <Link :href="route('admin.agriverse.transactions.show', data.id)" class="text-emerald-600 hover:text-emerald-800 font-mono">{{ data.code }}</Link>
          </template>
        </Column>
        <Column field="user.name" header="Người dùng" sortable />
        <Column field="type" header="Loại" sortable>
          <template #body="{ data }">
            <UiBadge :variant="data.type === 'payment' ? 'primary' : data.type === 'withdrawal' ? 'danger' : 'info'">{{ data.type_text || data.type }}</UiBadge>
          </template>
        </Column>
        <Column field="amount" header="Số tiền" sortable>
          <template #body="{ data }">
            <span :class="data.type === 'withdrawal' ? 'text-red-600' : 'text-emerald-600'" class="font-medium">
              {{ data.type === 'withdrawal' ? '-' : '+' }}{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(Math.abs(data.amount)) }}
            </span>
          </template>
        </Column>
        <Column field="status" header="Trạng thái" sortable>
          <template #body="{ data }">
            <UiBadge :variant="data.status === 'completed' ? 'success' : data.status === 'pending' ? 'warning' : 'danger'">{{ data.status }}</UiBadge>
          </template>
        </Column>
        <Column field="payment_method" header="Phương thức" sortable />
        <Column field="created_at" header="Ngày tạo" sortable />
        <Column header="Thao tác" style="width:80px">
          <template #body="{ data }">
            <Link :href="route('admin.agriverse.transactions.show', data.id)" class="text-blue-600 hover:text-blue-800"><i class="pi pi-eye" /></Link>
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
  transactions: { type: Object, required: true },
})

const offset = (props.transactions.current_page - 1) * props.transactions.per_page

function onPage(event) {
  router.get(route('admin.agriverse.transactions.index'), { page: event.page + 1 }, { preserveState: true })
}
</script>

<style scoped>
</style>
