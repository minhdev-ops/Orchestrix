<template>
  <Head title="Quản lý sản phẩm - AgriVerse Admin" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Quản lý sản phẩm</h1>
      <Link :href="route('admin.agriverse.products.create')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
        <i class="pi pi-plus" /> Thêm sản phẩm
      </Link>
    </div>

    <div class="flex items-center gap-4">
      <span class="p-input-icon-left relative">
        <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm" />
        <InputText v-model="filters.search" placeholder="Tìm kiếm..." class="pl-9" @input="search" />
      </span>
      <Select v-model="filters.category" :options="categoryOptions" option-label="label" option-value="value" placeholder="Danh mục" class="w-44" @change="search" />
    </div>

    <UiCard>
      <DataTable :value="products.data" striped-rows paginator :rows="10" :total-records="products.total" :first="offset" @page="onPage" class="text-sm">
        <Column field="id" header="ID" sortable style="width:80px" />
        <Column field="name" header="Tên sản phẩm" sortable>
          <template #body="{ data }">
            <div class="flex items-center gap-3">
              <img v-if="data.thumbnail" :src="data.thumbnail" class="w-10 h-10 rounded object-cover" alt="" />
              <Link :href="route('admin.agriverse.products.show', data.id)" class="text-emerald-600 hover:text-emerald-800 font-medium">{{ data.name }}</Link>
            </div>
          </template>
        </Column>
        <Column field="price" header="Giá" sortable>
          <template #body="{ data }">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(data.price) }}</template>
        </Column>
        <Column field="stock" header="Tồn kho" sortable />
        <Column field="category.name" header="Danh mục" sortable />
        <Column field="status" header="Trạng thái" sortable>
          <template #body="{ data }">
            <UiBadge :variant="data.status === 'active' ? 'success' : data.status === 'draft' ? 'warning' : 'danger'">{{ data.status }}</UiBadge>
          </template>
        </Column>
        <Column header="Thao tác" style="width:120px">
          <template #body="{ data }">
            <div class="flex gap-2">
              <Link :href="route('admin.agriverse.products.show', data.id)" class="text-blue-600 hover:text-blue-800"><i class="pi pi-eye" /></Link>
              <Link :href="route('admin.agriverse.products.edit', data.id)" class="text-amber-600 hover:text-amber-800"><i class="pi pi-pencil" /></Link>
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
  products: { type: Object, required: true },
  filters: { type: Object, default: () => ({ search: '', category: '' }) },
})

const categoryOptions = [{ label: 'Tất cả danh mục', value: '' }]

const filters = reactive({ search: props.filters.search || '', category: props.filters.category || '' })

let timeout
function search() {
  clearTimeout(timeout)
  timeout = setTimeout(() => {
    router.get(route('admin.agriverse.products.index'), filters, { preserveState: true, replace: true })
  }, 300)
}

const offset = (props.products.current_page - 1) * props.products.per_page

function onPage(event) {
  router.get(route('admin.agriverse.products.index'), { ...filters, page: event.page + 1 }, { preserveState: true })
}
</script>

<style scoped>
</style>
