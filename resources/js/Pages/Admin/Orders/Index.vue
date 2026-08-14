<template>
  <Head title="Quản lý đơn hàng - AgriVerse Admin" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Quản lý đơn hàng</h1>
      <Link :href="route('admin.agriverse.orders.create')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
        <i class="pi pi-plus" /> Tạo đơn hàng
      </Link>
    </div>

    <div class="flex items-center gap-4">
      <span class="p-input-icon-left relative">
        <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm" />
        <InputText v-model="filters.search" placeholder="Tìm mã đơn..." class="pl-9" @input="search" />
      </span>
      <Select v-model="filters.status" :options="statusOptions" option-label="label" option-value="value" placeholder="Trạng thái" class="w-44" @change="search" />
      <Calendar v-model="filters.date_from" placeholder="Từ ngày" date-format="dd/mm/yy" class="w-40" @date-select="search" />
      <Calendar v-model="filters.date_to" placeholder="Đến ngày" date-format="dd/mm/yy" class="w-40" @date-select="search" />
    </div>

    <UiCard>
      <DataTable :value="orders.data" striped-rows paginator :rows="10" :total-records="orders.total" :first="offset" @page="onPage" class="text-sm">
        <Column field="code" header="Mã đơn" sortable>
          <template #body="{ data }">
            <Link :href="route('admin.agriverse.orders.show', data.id)" class="text-emerald-600 hover:text-emerald-800 font-medium">#{{ data.code }}</Link>
          </template>
        </Column>
        <Column field="user.name" header="Khách hàng" sortable />
        <Column field="total" header="Tổng tiền" sortable>
          <template #body="{ data }">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(data.total) }}</template>
        </Column>
        <Column field="status" header="Trạng thái" sortable>
          <template #body="{ data }">
            <UiBadge :variant="data.status === 'completed' ? 'success' : data.status === 'cancelled' ? 'danger' : data.status === 'pending' ? 'warning' : 'info'">{{ data.status_text || data.status }}</UiBadge>
          </template>
        </Column>
        <Column field="payment_method" header="Thanh toán" sortable />
        <Column field="created_at" header="Ngày tạo" sortable />
        <Column header="Thao tác" style="width:120px">
          <template #body="{ data }">
            <div class="flex gap-2">
              <Link :href="route('admin.agriverse.orders.show', data.id)" class="text-blue-600 hover:text-blue-800"><i class="pi pi-eye" /></Link>
              <Link :href="route('admin.agriverse.orders.edit', data.id)" class="text-amber-600 hover:text-amber-800"><i class="pi pi-pencil" /></Link>
            </div>
          </template>
        </Column>
      </DataTable>
    </UiCard>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'
import UiBadge from '@/Components/ui/UiBadge.vue'

const props = defineProps({
  orders: { type: Object, required: true },
  filters: { type: Object, default: () => ({ search: '', status: '', date_from: '', date_to: '' }) },
})

const statusOptions = [
  { label: 'Tất cả trạng thái', value: '' },
  { label: 'Chờ xử lý', value: 'pending' },
  { label: 'Đã xác nhận', value: 'confirmed' },
  { label: 'Đang giao', value: 'shipping' },
  { label: 'Hoàn thành', value: 'completed' },
  { label: 'Đã hủy', value: 'cancelled' },
]

const filters = reactive({
  search: props.filters.search || '',
  status: props.filters.status || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
})

let timeout
function search() {
  clearTimeout(timeout)
  timeout = setTimeout(() => {
    router.get(route('admin.agriverse.orders.index'), filters, { preserveState: true, replace: true })
  }, 300)
}

const offset = (props.orders.current_page - 1) * props.orders.per_page

function onPage(event) {
  router.get(route('admin.agriverse.orders.index'), { ...filters, page: event.page + 1 }, { preserveState: true })
}
</script>

<style scoped>
</style>
