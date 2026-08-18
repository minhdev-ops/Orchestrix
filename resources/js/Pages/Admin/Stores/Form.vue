<template>
  <Head :title="isEdit ? 'Sửa cửa hàng' : 'Thêm cửa hàng'" />

  <div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
      <Link :href="route('admin.agriverse.stores.index')" class="text-gray-400 hover:text-gray-600">
        <i class="pi pi-arrow-left text-xl" />
      </Link>
      <h1 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Sửa cửa hàng' : 'Thêm cửa hàng' }}</h1>
    </div>

    <UiCard>
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tên cửa hàng <span class="text-red-500">*</span></label>
          <InputText v-model="form.name" class="w-full" :class="{ 'p-invalid': errors.name }" />
          <small v-if="errors.name" class="text-red-500">{{ errors.name }}</small>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
          <Textarea v-model="form.description" class="w-full" rows="4" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
          <InputText v-model="form.address" class="w-full" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
          <InputText v-model="form.phone" class="w-full" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
          <Select v-model="form.status" :options="statusOptions" option-label="label" option-value="value" class="w-full" />
        </div>
        <div class="flex gap-3 justify-end pt-4">
          <Link :href="route('admin.agriverse.stores.index')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">Hủy</Link>
          <Button type="submit" :loading="form.processing" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">
            {{ isEdit ? 'Cập nhật' : 'Tạo mới' }}
          </Button>
        </div>
      </form>
    </UiCard>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'

const props = defineProps({
  store: { type: Object, default: null },
})

const page = usePage()
const isEdit = computed(() => !!props.store)

const errors = computed(() => page.props.errors || {})

const form = useForm({
  name: props.store?.name || '',
  description: props.store?.description || '',
  address: props.store?.address || '',
  phone: props.store?.phone || '',
  status: props.store?.status || 'active',
})

const statusOptions = [
  { label: 'Hoạt động', value: 'active' },
  { label: 'Chờ duyệt', value: 'pending' },
  { label: 'Tạm khóa', value: 'suspended' },
]

function submit() {
  if (isEdit.value) {
    form.put(route('admin.agriverse.stores.update', props.store.id))
  } else {
    form.post(route('admin.agriverse.stores.store'))
  }
}
</script>

<style scoped>
</style>
