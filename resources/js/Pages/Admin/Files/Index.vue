<template>
  <Head title="Quản lý tệp - AgriVerse Admin" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Quản lý tệp</h1>
      <div class="flex gap-2">
        <Button @click="createFolder" severity="secondary">
          <i class="pi pi-folder-plus mr-1" /> Thư mục mới
        </Button>
        <Button @click="triggerUpload">
          <i class="pi pi-upload mr-1" /> Tải lên
        </Button>
        <input ref="fileInput" type="file" multiple class="hidden" @change="uploadFiles" />
      </div>
    </div>

    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
      <Link :href="route('admin.agriverse.files.index')" class="hover:text-emerald-600">Gốc</Link>
      <i class="pi pi-chevron-right text-xs" />
      <span v-for="(crumb, i) in breadcrumbs" :key="i">
        <template v-if="i < breadcrumbs.length - 1">
          <Link :href="route('admin.agriverse.files.index', { folder: crumb.path })" class="hover:text-emerald-600">{{ crumb.name }}</Link>
          <i class="pi pi-chevron-right text-xs mx-1" />
        </template>
        <span v-else class="text-gray-900 font-medium">{{ crumb.name }}</span>
      </span>
    </div>

    <UiCard>
      <DataTable :value="files" striped-rows class="text-sm">
        <Column header="Tên">
          <template #body="{ data }">
            <div v-if="data.type === 'folder'" class="flex items-center gap-2">
              <i class="pi pi-folder text-amber-500" />
              <Link :href="route('admin.agriverse.files.index', { folder: data.path })" class="text-emerald-600 hover:text-emerald-800 font-medium">{{ data.name }}</Link>
            </div>
            <div v-else class="flex items-center gap-2">
              <i :class="getFileIcon(data.extension)" class="text-gray-500" />
              <span class="text-gray-900">{{ data.name }}</span>
            </div>
          </template>
        </Column>
        <Column field="size" header="Kích thước" sortable />
        <Column field="extension" header="Loại" sortable>
          <template #body="{ data }">
            <span v-if="data.extension" class="uppercase text-xs font-mono">{{ data.extension }}</span>
            <span v-else class="text-gray-400">—</span>
          </template>
        </Column>
        <Column field="updated_at" header="Cập nhật" sortable />
        <Column header="Thao tác" style="width:160px">
          <template #body="{ data }">
            <div class="flex gap-2">
              <button v-if="data.type !== 'folder'" class="text-blue-600 hover:text-blue-800" @click="downloadFile(data)">
                <i class="pi pi-download" />
              </button>
              <button class="text-red-600 hover:text-red-800" @click="deleteFile(data)">
                <i class="pi pi-trash" />
              </button>
            </div>
          </template>
        </Column>
      </DataTable>
    </UiCard>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'

const props = defineProps({
  files: { type: Array, required: true },
})

const fileInput = ref(null)
const currentFolder = ref('')

const breadcrumbs = computed(() => {
  if (!currentFolder.value) return []
  const parts = currentFolder.value.split('/')
  return parts.map((name, i) => ({
    name,
    path: parts.slice(0, i + 1).join('/'),
  }))
})

function getFileIcon(ext) {
  const icons = {
    pdf: 'pi pi-file-pdf text-red-500',
    doc: 'pi pi-file-word text-blue-500',
    docx: 'pi pi-file-word text-blue-500',
    xls: 'pi pi-file-excel text-emerald-500',
    xlsx: 'pi pi-file-excel text-emerald-500',
    zip: 'pi pi-file-archive text-amber-500',
    rar: 'pi pi-file-archive text-amber-500',
    jpg: 'pi pi-image text-purple-500',
    jpeg: 'pi pi-image text-purple-500',
    png: 'pi pi-image text-purple-500',
    gif: 'pi pi-image text-purple-500',
    mp4: 'pi pi-video text-indigo-500',
    mp3: 'pi pi-music text-pink-500',
  }
  return icons[ext?.toLowerCase()] || 'pi pi-file text-gray-400'
}

function triggerUpload() {
  fileInput.value?.click()
}

function uploadFiles(event) {
  const formData = new FormData()
  for (const file of event.target.files) {
    formData.append('files[]', file)
  }
  if (currentFolder.value) {
    formData.append('folder', currentFolder.value)
  }
  router.post(route('admin.agriverse.files.store'), formData, {
    onFinish: () => { fileInput.value.value = '' },
  })
}

function createFolder() {
  const name = prompt('Nhập tên thư mục:')
  if (name) {
    router.post(route('admin.agriverse.files.create-folder'), { name, path: currentFolder.value })
  }
}

function downloadFile(file) {
  window.open(route('admin.agriverse.files.download', file.id), '_blank')
}

function deleteFile(file) {
  if (confirm(`Xóa "${file.name}"?`)) {
    router.delete(route('admin.agriverse.files.destroy', file.id))
  }
}
</script>

<style scoped>
</style>
