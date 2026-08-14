<template>
  <Head :title="isEdit ? 'Sửa sản phẩm' : 'Thêm sản phẩm'" />

  <div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
      <Link :href="route('admin.agriverse.products.index')" class="text-gray-400 hover:text-gray-600">
        <i class="pi pi-arrow-left text-xl" />
      </Link>
      <h1 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Sửa sản phẩm' : 'Thêm sản phẩm' }}</h1>
    </div>

    <UiCard>
      <form @submit.prevent="submit" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tên sản phẩm <span class="text-red-500">*</span></label>
            <InputText v-model="form.name" class="w-full" :class="{ 'p-invalid': errors.name }" />
            <small v-if="errors.name" class="text-red-500">{{ errors.name }}</small>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
            <InputText v-model="form.slug" class="w-full" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
          <Textarea v-model="form.description" class="w-full" rows="4" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Giá <span class="text-red-500">*</span></label>
            <InputNumber v-model="form.price" class="w-full" :min="0" currency="VND" locale="vi-VN" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Giá khuyến mãi</label>
            <InputNumber v-model="form.compare_price" class="w-full" :min="0" currency="VND" locale="vi-VN" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tồn kho</label>
            <InputNumber v-model="form.stock" class="w-full" :min="0" />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục</label>
            <Select v-model="form.category_id" :options="categories" option-label="name" option-value="id" class="w-full" placeholder="Chọn danh mục" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cửa hàng</label>
            <Select v-model="form.store_id" :options="stores" option-label="name" option-value="id" class="w-full" placeholder="Chọn cửa hàng" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
          <Select v-model="form.status" :options="statusOptions" option-label="label" option-value="value" class="w-full" />
        </div>

        <div class="flex gap-3 justify-end pt-4">
          <Link :href="route('admin.agriverse.products.index')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">Hủy</Link>
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
  product: { type: Object, default: null },
  categories: { type: Array, default: () => [] },
  stores: { type: Array, default: () => [] },
})

const page = usePage()
const isEdit = computed(() => !!props.product)
const errors = computed(() => page.props.errors || {})

const form = useForm({
  name: props.product?.name || '',
  slug: props.product?.slug || '',
  description: props.product?.description || '',
  price: props.product?.price || 0,
  compare_price: props.product?.compare_price || null,
  stock: props.product?.stock || 0,
  category_id: props.product?.category_id || null,
  store_id: props.product?.store_id || null,
  status: props.product?.status || 'draft',
})

const statusOptions = [
  { label: 'Bản nháp', value: 'draft' },
  { label: 'Đang bán', value: 'active' },
  { label: 'Ngừng bán', value: 'inactive' },
]

function submit() {
  if (isEdit.value) {
    form.put(route('admin.agriverse.products.update', props.product.id))
  } else {
    form.post(route('admin.agriverse.products.store'))
  }
}
</script>

<style scoped>
</style>
