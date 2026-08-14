<template>
  <Head title="Quản lý nhóm chat - AgriVerse Admin" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Quản lý nhóm chat</h1>
    </div>

    <UiCard>
      <DataTable :value="groups.data" striped-rows paginator :rows="10" :total-records="groups.total" :first="offset" @page="onPage" class="text-sm">
        <Column field="id" header="ID" sortable style="width:80px" />
        <Column field="name" header="Tên nhóm" sortable>
          <template #body="{ data }">
            <Link :href="route('admin.agriverse.chat-groups.show', data.id)" class="text-emerald-600 hover:text-emerald-800 font-medium">
              <i class="pi pi-users mr-1" /> {{ data.name }}
            </Link>
          </template>
        </Column>
        <Column field="type" header="Loại" sortable>
          <template #body="{ data }">
            <UiBadge :variant="data.type === 'support' ? 'primary' : data.type === 'general' ? 'info' : 'success'">{{ data.type }}</UiBadge>
          </template>
        </Column>
        <Column field="members_count" header="Thành viên" sortable>
          <template #body="{ data }">
            <span class="flex items-center gap-1"><i class="pi pi-user text-gray-400" /> {{ data.members_count || 0 }}</span>
          </template>
        </Column>
        <Column field="messages_count" header="Tin nhắn" sortable>
          <template #body="{ data }">
            <span class="flex items-center gap-1"><i class="pi pi-comments text-gray-400" /> {{ data.messages_count || 0 }}</span>
          </template>
        </Column>
        <Column field="is_active" header="Kích hoạt" sortable>
          <template #body="{ data }">
            <i :class="data.is_active ? 'pi pi-check-circle text-emerald-500' : 'pi pi-times-circle text-red-400'" />
          </template>
        </Column>
        <Column field="created_at" header="Ngày tạo" sortable />
        <Column header="Thao tác" style="width:80px">
          <template #body="{ data }">
            <Link :href="route('admin.agriverse.chat-groups.show', data.id)" class="text-blue-600 hover:text-blue-800"><i class="pi pi-eye" /></Link>
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
  groups: { type: Object, required: true },
})

const offset = (props.groups.current_page - 1) * props.groups.per_page

function onPage(event) {
  router.get(route('admin.agriverse.chat-groups.index'), { page: event.page + 1 }, { preserveState: true })
}
</script>

<style scoped>
</style>
