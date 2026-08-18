<template>
  <Head title="Quản lý banner - AgriVerse Admin" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Quản lý banner</h1>
      <Button @click="showCreateDialog = true" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
        <i class="pi pi-plus mr-1" /> Thêm banner
      </Button>
    </div>

    <UiCard>
      <DataTable :value="banners" striped-rows class="text-sm">
        <Column field="id" header="ID" style="width:80px" />
        <Column field="image" header="Hình ảnh">
          <template #body="{ data }">
            <img v-if="data.image" :src="data.image" class="w-24 h-12 object-cover rounded" alt="" />
            <span v-else class="text-gray-400">—</span>
          </template>
        </Column>
        <Column field="title" header="Tiêu đề" sortable />
        <Column field="subtitle" header="Phụ đề" sortable />
        <Column field="link" header="Liên kết" sortable>
          <template #body="{ data }">
            <span class="text-xs text-blue-600 truncate block max-w-xs">{{ data.link || '—' }}</span>
          </template>
        </Column>
        <Column field="position" header="Vị trí" sortable>
          <template #body="{ data }">
            <UiBadge>{{ data.position }}</UiBadge>
          </template>
        </Column>
        <Column field="sort_order" header="Thứ tự" sortable />
        <Column field="is_active" header="Kích hoạt" sortable>
          <template #body="{ data }">
            <i :class="data.is_active ? 'pi pi-check-circle text-emerald-500' : 'pi pi-times-circle text-red-400'" />
          </template>
        </Column>
        <Column header="Thao tác" style="width:120px">
          <template #body="{ data }">
            <div class="flex gap-2">
              <button class="text-amber-600 hover:text-amber-800" @click="editBanner(data)"><i class="pi pi-pencil" /></button>
              <button class="text-red-600 hover:text-red-800" @click="deleteBanner(data.id)"><i class="pi pi-trash" /></button>
            </div>
          </template>
        </Column>
      </DataTable>
    </UiCard>

    <UiModal v-model:model-value="showDialog" :title="editing ? 'Sửa banner' : 'Thêm banner'">
      <form @submit.prevent="saveBanner" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề</label>
          <InputText v-model="form.title" class="w-full" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Phụ đề</label>
          <InputText v-model="form.subtitle" class="w-full" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Liên kết</label>
          <InputText v-model="form.link" class="w-full" placeholder="https://..." />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Hình ảnh URL</label>
          <InputText v-model="form.image" class="w-full" placeholder="https://..." />
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Vị trí</label>
            <Select v-model="form.position" :options="positionOptions" option-label="label" option-value="value" class="w-full" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự</label>
            <InputNumber v-model="form.sort_order" class="w-full" :min="0" />
          </div>
        </div>
        <div class="flex items-center gap-2">
          <InputSwitch v-model="form.is_active" input-id="banner-active" />
          <label for="banner-active" class="text-sm text-gray-700">Kích hoạt</label>
        </div>
        <div class="flex gap-3 justify-end pt-4">
          <Button type="button" severity="secondary" @click="closeDialog">Hủy</Button>
          <Button type="submit" :loading="saving" severity="success">{{ editing ? 'Cập nhật' : 'Tạo mới' }}</Button>
        </div>
      </form>
    </UiModal>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'
import UiBadge from '@/Components/ui/UiBadge.vue'
import UiModal from '@/Components/ui/UiModal.vue'

const props = defineProps({
  banners: { type: Array, required: true },
})

const showCreateDialog = ref(false)
const editing = ref(null)
const saving = ref(false)

const showDialog = computed({
  get: () => showCreateDialog.value || !!editing.value,
  set: (v) => { if (!v) closeDialog() },
})

const form = reactive({
  title: '',
  subtitle: '',
  link: '',
  image: '',
  position: 'hero',
  sort_order: 0,
  is_active: true,
})

const positionOptions = [
  { label: 'Hero', value: 'hero' },
  { label: 'Sidebar', value: 'sidebar' },
  { label: 'Giữa trang', value: 'middle' },
  { label: 'Footer', value: 'footer' },
]

function resetForm() {
  form.title = ''
  form.subtitle = ''
  form.link = ''
  form.image = ''
  form.position = 'hero'
  form.sort_order = 0
  form.is_active = true
}

function editBanner(banner) {
  editing.value = banner
  Object.assign(form, {
    title: banner.title || '',
    subtitle: banner.subtitle || '',
    link: banner.link || '',
    image: banner.image || '',
    position: banner.position || 'hero',
    sort_order: banner.sort_order || 0,
    is_active: banner.is_active,
  })
}

function closeDialog() {
  showCreateDialog.value = false
  editing.value = null
  resetForm()
}

function saveBanner() {
  saving.value = true
  if (editing.value) {
    router.put(route('admin.agriverse.banners.update', editing.value.id), { ...form }, {
      onFinish: () => { saving.value = false; closeDialog() },
    })
  } else {
    router.post(route('admin.agriverse.banners.store'), { ...form }, {
      onFinish: () => { saving.value = false; closeDialog() },
    })
  }
}

function deleteBanner(id) {
  if (confirm('Xóa banner này?')) {
    router.delete(route('admin.agriverse.banners.destroy', id))
  }
}
</script>

<style scoped>
</style>
