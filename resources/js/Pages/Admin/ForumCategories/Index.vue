<template>
  <Head title="Quản lý danh mục diễn đàn - AgriVerse Admin" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Danh mục diễn đàn</h1>
      <Button @click="showCreateDialog = true" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
        <i class="pi pi-plus mr-1" /> Thêm danh mục
      </Button>
    </div>

    <UiCard>
      <DataTable :value="categories" striped-rows class="text-sm">
        <Column field="id" header="ID" style="width:80px" />
        <Column field="name" header="Tên danh mục" sortable />
        <Column field="slug" header="Slug" sortable />
        <Column field="description" header="Mô tả">
          <template #body="{ data }">
            <span class="text-gray-500 truncate block max-w-xs">{{ data.description || '—' }}</span>
          </template>
        </Column>
        <Column field="posts_count" header="Bài viết" sortable />
        <Column field="sort_order" header="Thứ tự" sortable />
        <Column field="is_active" header="Kích hoạt" sortable>
          <template #body="{ data }">
            <i :class="data.is_active ? 'pi pi-check-circle text-emerald-500' : 'pi pi-times-circle text-red-400'" />
          </template>
        </Column>
        <Column header="Thao tác" style="width:120px">
          <template #body="{ data }">
            <div class="flex gap-2">
              <button class="text-amber-600 hover:text-amber-800" @click="editCategory(data)"><i class="pi pi-pencil" /></button>
              <button class="text-red-600 hover:text-red-800" @click="deleteCategory(data.id)"><i class="pi pi-trash" /></button>
            </div>
          </template>
        </Column>
      </DataTable>
    </UiCard>

      <UiModal v-model:model-value="showDialog" :title="editing ? 'Sửa danh mục' : 'Thêm danh mục'">
      <form @submit.prevent="saveCategory" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tên danh mục <span class="text-red-500">*</span></label>
          <InputText v-model="form.name" class="w-full" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
          <InputText v-model="form.slug" class="w-full" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
          <Textarea v-model="form.description" class="w-full" rows="3" />
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự</label>
            <InputNumber v-model="form.sort_order" class="w-full" :min="0" />
          </div>
          <div class="flex items-center gap-2 pt-6">
            <InputSwitch v-model="form.is_active" input-id="fc-is-active" />
            <label for="fc-is-active" class="text-sm text-gray-700">Kích hoạt</label>
          </div>
        </div>
        <div class="flex gap-3 justify-end pt-4">
          <Button type="button" severity="secondary" @click="closeDialogs">Hủy</Button>
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
import UiModal from '@/Components/ui/UiModal.vue'

const props = defineProps({
  categories: { type: Array, required: true },
})

const showCreateDialog = ref(false)
const showEditDialog = ref(false)
const showDialog = computed(() => showCreateDialog.value || showEditDialog.value)
const editing = ref(null)
const saving = ref(false)

const form = reactive({
  name: '',
  slug: '',
  description: '',
  sort_order: 0,
  is_active: true,
})

function resetForm() {
  form.name = ''
  form.slug = ''
  form.description = ''
  form.sort_order = 0
  form.is_active = true
}

function editCategory(cat) {
  editing.value = cat
  form.name = cat.name
  form.slug = cat.slug
  form.description = cat.description || ''
  form.sort_order = cat.sort_order || 0
  form.is_active = cat.is_active
  showEditDialog.value = true
}

function closeDialogs() {
  showCreateDialog.value = false
  showEditDialog.value = false
  editing.value = null
  resetForm()
}

function saveCategory() {
  saving.value = true
  if (editing.value) {
    router.put(route('admin.agriverse.forum-categories.update', editing.value.id), { ...form }, {
      onFinish: () => { saving.value = false; closeDialogs() },
    })
  } else {
    router.post(route('admin.agriverse.forum-categories.store'), { ...form }, {
      onFinish: () => { saving.value = false; closeDialogs() },
    })
  }
}

function deleteCategory(id) {
  // TODO: Replace with PrimeVue useConfirm() for non-blocking UX
  if (confirm('Xóa danh mục này?')) {
    router.delete(route('admin.agriverse.forum-categories.destroy', id))
  }
}
</script>

<style scoped>
</style>
