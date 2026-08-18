<template>
  <Head title="Quản lý hợp đồng - AgriVerse Admin" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Quản lý hợp đồng</h1>
      <Link :href="route('admin.agriverse.contracts.create')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
        <i class="pi pi-plus" /> Thêm hợp đồng
      </Link>
    </div>

    <UiCard>
      <DataTable :value="contracts.data" striped-rows paginator :rows="10" :total-records="contracts.total" :first="offset" @page="onPage" class="text-sm">
        <Column field="id" header="ID" sortable style="width:80px" />
        <Column field="code" header="Mã HĐ" sortable>
          <template #body="{ data }">
            <Link :href="route('admin.agriverse.contracts.show', data.id)" class="text-emerald-600 hover:text-emerald-800 font-mono">{{ data.code }}</Link>
          </template>
        </Column>
        <Column field="seller.name" header="Người bán" sortable />
        <Column field="type" header="Loại" sortable>
          <template #body="{ data }">
            <UiBadge :variant="data.type === 'exclusive' ? 'primary' : 'info'">{{ data.type_text || data.type }}</UiBadge>
          </template>
        </Column>
        <Column field="status" header="Trạng thái" sortable>
          <template #body="{ data }">
            <UiBadge :variant="data.status === 'active' ? 'success' : data.status === 'pending' ? 'warning' : data.status === 'expired' ? 'danger' : 'default'">{{ data.status }}</UiBadge>
          </template>
        </Column>
        <Column field="start_date" header="Ngày bắt đầu" sortable />
        <Column field="end_date" header="Ngày kết thúc" sortable />
        <Column field="created_at" header="Ngày tạo" sortable />
        <Column header="Thao tác" style="width:120px">
          <template #body="{ data }">
            <div class="flex gap-2">
              <Link :href="route('admin.agriverse.contracts.show', data.id)" class="text-blue-600 hover:text-blue-800"><i class="pi pi-eye" /></Link>
              <Link :href="route('admin.agriverse.contracts.edit', data.id)" class="text-amber-600 hover:text-amber-800"><i class="pi pi-pencil" /></Link>
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
  contracts: { type: Object, required: true },
})

const offset = (props.contracts.current_page - 1) * props.contracts.per_page

function onPage(event) {
  router.get(route('admin.agriverse.contracts.index'), { page: event.page + 1 }, { preserveState: true })
}
</script>

<style scoped>
</style>
