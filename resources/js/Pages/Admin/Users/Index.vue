<template>
  <Head title="Quản lý người dùng - AgriVerse Admin" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Quản lý người dùng</h1>
      <Link :href="route('admin.agriverse.users.create')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
        <i class="pi pi-plus" /> Thêm người dùng
      </Link>
    </div>

    <div class="flex items-center gap-4">
      <span class="p-input-icon-left relative">
        <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm" />
        <InputText v-model="filters.search" placeholder="Tìm kiếm..." class="pl-9" @input="search" />
      </span>
      <Select v-model="filters.role" :options="roleOptions" option-label="label" option-value="value" placeholder="Vai trò" class="w-44" @change="search" />
    </div>

    <UiCard>
      <DataTable :value="users.data" striped-rows paginator :rows="10" :total-records="users.total" :rows-per-page-options="[10, 20, 50]" :first="offset" @page="onPage" class="text-sm">
        <Column field="id" header="ID" sortable style="width:80px" />
        <Column field="name" header="Tên" sortable>
          <template #body="{ data }">
            <Link :href="route('admin.agriverse.users.show', data.id)" class="text-emerald-600 hover:text-emerald-800 font-medium">{{ data.name }}</Link>
          </template>
        </Column>
        <Column field="email" header="Email" sortable />
        <Column field="role" header="Vai trò" sortable>
          <template #body="{ data }">
            <UiBadge :variant="data.role === 'admin' ? 'primary' : data.role === 'seller' ? 'warning' : 'default'">{{ data.role }}</UiBadge>
          </template>
        </Column>
        <Column field="email_verified_at" header="Xác thực" sortable>
          <template #body="{ data }">
            <i :class="data.email_verified_at ? 'pi pi-check-circle text-emerald-500' : 'pi pi-times-circle text-red-400'" />
          </template>
        </Column>
        <Column field="created_at" header="Ngày tạo" sortable />
        <Column header="Thao tác" style="width:120px">
          <template #body="{ data }">
            <div class="flex gap-2">
              <Link :href="route('admin.agriverse.users.show', data.id)" class="text-blue-600 hover:text-blue-800"><i class="pi pi-eye" /></Link>
              <Link :href="route('admin.agriverse.users.edit', data.id)" class="text-amber-600 hover:text-amber-800"><i class="pi pi-pencil" /></Link>
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
  users: { type: Object, required: true },
  filters: { type: Object, default: () => ({ search: '', role: '' }) },
})

const roleOptions = [
  { label: 'Tất cả', value: '' },
  { label: 'Quản trị', value: 'admin' },
  { label: 'Người bán', value: 'seller' },
  { label: 'Người dùng', value: 'user' },
]

const filters = reactive({ search: props.filters.search || '', role: props.filters.role || '' })

let timeout
function search() {
  clearTimeout(timeout)
  timeout = setTimeout(() => {
    router.get(route('admin.agriverse.users.index'), filters, { preserveState: true, replace: true })
  }, 300)
}

const offset = (props.users.current_page - 1) * props.users.per_page

function onPage(event) {
  router.get(route('admin.agriverse.users.index'), { ...filters, page: event.page + 1 }, { preserveState: true })
}
</script>

<style scoped>
</style>
