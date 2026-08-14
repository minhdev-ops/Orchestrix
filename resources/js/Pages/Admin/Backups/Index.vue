<template>
  <Head title="Sao lưu - AgriVerse Admin" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Sao lưu dữ liệu</h1>
      <Button :loading="creating" @click="createBackup" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
        <i class="pi pi-plus mr-1" /> Tạo sao lưu
      </Button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <UiCard>
        <div class="text-center">
          <p class="text-sm text-gray-500">Tổng số bản sao lưu</p>
          <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_backups || 0 }}</p>
        </div>
      </UiCard>
      <UiCard>
        <div class="text-center">
          <p class="text-sm text-gray-500">Dung lượng</p>
          <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_size || '0 B' }}</p>
        </div>
      </UiCard>
      <UiCard>
        <div class="text-center">
          <p class="text-sm text-gray-500">Lần sao lưu cuối</p>
          <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.last_backup || 'Chưa có' }}</p>
        </div>
      </UiCard>
    </div>

    <UiCard>
      <DataTable :value="backups" striped-rows class="text-sm">
        <Column field="id" header="ID" style="width:80px" />
        <Column field="filename" header="Tên tệp" sortable>
          <template #body="{ data }">
            <div class="flex items-center gap-2">
              <i class="pi pi-file-archive text-amber-500" />
              <span class="font-mono text-sm">{{ data.filename }}</span>
            </div>
          </template>
        </Column>
        <Column field="size" header="Dung lượng" sortable />
        <Column field="type" header="Loại" sortable>
          <template #body="{ data }">
            <UiBadge :variant="data.type === 'database' ? 'primary' : 'info'">{{ data.type === 'database' ? 'CSDL' : 'Tệp' }}</UiBadge>
          </template>
        </Column>
        <Column field="created_at" header="Ngày tạo" sortable />
        <Column header="Thao tác" style="width:160px">
          <template #body="{ data }">
            <div class="flex gap-2">
              <Button severity="info" size="small" @click="downloadBackup(data.id)">
                <i class="pi pi-download" />
              </Button>
              <Button severity="warn" size="small" @click="restoreBackup(data.id)">
                <i class="pi pi-refresh" />
              </Button>
              <Button severity="danger" size="small" @click="deleteBackup(data.id)">
                <i class="pi pi-trash" />
              </Button>
            </div>
          </template>
        </Column>
      </DataTable>
    </UiCard>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'
import UiBadge from '@/Components/ui/UiBadge.vue'

const props = defineProps({
  backups: { type: Array, default: () => [] },
  stats: { type: Object, default: () => ({}) },
})

const creating = ref(false)

function createBackup() {
  creating.value = true
  router.post(route('admin.agriverse.backups.store'), {}, {
    onFinish: () => { creating.value = false },
  })
}

function downloadBackup(id) {
  window.open(route('admin.agriverse.backups.download', id), '_blank')
}

function restoreBackup(id) {
  // TODO: Replace with PrimeVue useConfirm() for non-blocking UX
  if (confirm('Khôi phục từ bản sao lưu này? Dữ liệu hiện tại sẽ bị ghi đè.')) {
    router.put(route('admin.agriverse.backups.restore', id))
  }
}

function deleteBackup(id) {
  // TODO: Replace with PrimeVue useConfirm() for non-blocking UX
  if (confirm('Xóa bản sao lưu này?')) {
    router.delete(route('admin.agriverse.backups.destroy', id))
  }
}
</script>

<style scoped>
</style>
