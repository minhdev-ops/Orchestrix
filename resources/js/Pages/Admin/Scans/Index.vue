<template>
  <Head title="Quét AI - AgriVerse Admin" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Quét AI</h1>
      <Button @click="runScan" :loading="scanning" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
        <i class="pi pi-sync mr-1" /> Quét ngay
      </Button>
    </div>

    <UiCard>
      <DataTable :value="scans.data" striped-rows paginator :rows="10" :total-records="scans.total" :first="offset" @page="onPage" class="text-sm">
        <Column field="id" header="ID" sortable style="width:80px" />
        <Column field="type" header="Loại" sortable>
          <template #body="{ data }">
            <UiBadge :variant="data.type === 'product' ? 'primary' : data.type === 'review' ? 'info' : 'warning'">{{ data.type }}</UiBadge>
          </template>
        </Column>
        <Column field="status" header="Trạng thái" sortable>
          <template #body="{ data }">
            <UiBadge :variant="data.status === 'completed' ? 'success' : data.status === 'running' ? 'warning' : data.status === 'failed' ? 'danger' : 'default'">{{ data.status }}</UiBadge>
          </template>
        </Column>
        <Column field="target_type" header="Đối tượng" sortable />
        <Column field="target_id" header="ID ĐT" sortable />
        <Column field="result" header="Kết quả">
          <template #body="{ data }">
            <span v-if="data.status === 'completed'" class="text-emerald-600">
              <i class="pi pi-check-circle" /> {{ data.result_summary || 'Hoàn thành' }}
            </span>
            <span v-else-if="data.status === 'failed'" class="text-red-600">
              <i class="pi pi-exclamation-circle" /> {{ data.error || 'Lỗi' }}
            </span>
            <span v-else class="text-gray-400">
              <i class="pi pi-spinner pi-spin" /> Đang xử lý...
            </span>
          </template>
        </Column>
        <Column field="created_at" header="Ngày tạo" sortable />
        <Column header="Thao tác" style="width:80px">
          <template #body="{ data }">
            <Link :href="route('admin.agriverse.scans.show', data.id)" class="text-blue-600 hover:text-blue-800"><i class="pi pi-eye" /></Link>
          </template>
        </Column>
      </DataTable>
    </UiCard>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'
import UiBadge from '@/Components/ui/UiBadge.vue'

const props = defineProps({
  scans: { type: Object, required: true },
})

const scanning = ref(false)
const offset = (props.scans.current_page - 1) * props.scans.per_page

function onPage(event) {
  router.get(route('admin.agriverse.scans.index'), { page: event.page + 1 }, { preserveState: true })
}

function runScan() {
  scanning.value = true
  router.post(route('admin.agriverse.scans.store'), {}, {
    onFinish: () => { scanning.value = false },
  })
}
</script>

<style scoped>
</style>
