<template>
  <Head title="Quản lý danh mục - AgriVerse Admin" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Quản lý danh mục</h1>
      <Link :href="route('admin.agriverse.categories.create')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-medium">
        <i class="pi pi-plus" /> Thêm danh mục
      </Link>
    </div>

    <UiCard>
      <DataTable :value="categories" striped-rows class="text-sm" :expanded-rows="expandedRows" @row-toggle="onRowToggle">
        <Column field="id" header="ID" style="width:80px" />
        <Column field="name" header="Tên danh mục" sortable>
          <template #body="{ data }">
            <div class="flex items-center gap-2">
              <span v-if="data.children?.length" class="cursor-pointer" @click="toggleRow(data)">
                <i :class="expandedRows.has(data.id) ? 'pi pi-chevron-down' : 'pi pi-chevron-right'" class="text-xs" />
              </span>
              <span>{{ data.name }}</span>
            </div>
          </template>
        </Column>
        <Column field="slug" header="Slug" sortable />
        <Column field="products_count" header="Sản phẩm" sortable />
        <Column header="Thao tác" style="width:120px">
          <template #body="{ data }">
            <div class="flex gap-2">
              <Link :href="route('admin.agriverse.categories.edit', data.id)" class="text-amber-600 hover:text-amber-800"><i class="pi pi-pencil" /></Link>
              <button class="text-red-600 hover:text-red-800" @click="deleteCategory(data.id)"><i class="pi pi-trash" /></button>
            </div>
          </template>
        </Column>
        <template #expansion="{ data }">
          <div v-if="data.children?.length" class="p-4 bg-gray-50">
            <DataTable :value="data.children" striped-rows class="text-sm">
              <Column field="id" header="ID" style="width:80px" />
              <Column field="name" header="Danh mục con" />
              <Column field="products_count" header="Sản phẩm" />
              <Column header="Thao tác" style="width:120px">
                <template #body="{ data: child }">
                  <Link :href="route('admin.agriverse.categories.edit', child.id)" class="text-amber-600 hover:text-amber-800 mr-2"><i class="pi pi-pencil" /></Link>
                  <button class="text-red-600 hover:text-red-800" @click="deleteCategory(child.id)"><i class="pi pi-trash" /></button>
                </template>
              </Column>
            </DataTable>
          </div>
        </template>
      </DataTable>
    </UiCard>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'

const props = defineProps({
  categories: { type: Array, required: true },
})

const expandedRows = ref(new Set())

function onRowToggle(event) {
  const set = new Set(expandedRows.value)
  if (set.has(event.data.id)) set.delete(event.data.id)
  else set.add(event.data.id)
  expandedRows.value = set
}

function toggleRow(data) {
  const set = new Set(expandedRows.value)
  if (set.has(data.id)) set.delete(data.id)
  else set.add(data.id)
  expandedRows.value = set
}

function deleteCategory(id) {
  if (confirm('Xóa danh mục này?')) {
    router.delete(route('admin.agriverse.categories.destroy', id))
  }
}
</script>

<style scoped>
</style>
