<template>
  <Head :title="isEdit ? 'Sửa mã giảm giá' : 'Thêm mã giảm giá'" />

  <div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
      <Link :href="route('admin.agriverse.coupons.index')" class="text-gray-400 hover:text-gray-600">
        <i class="pi pi-arrow-left text-xl" />
      </Link>
      <h1 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Sửa mã giảm giá' : 'Thêm mã giảm giá' }}</h1>
    </div>

    <UiCard>
      <form @submit.prevent="submit" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mã giảm giá <span class="text-red-500">*</span></label>
            <InputText v-model="form.code" class="w-full uppercase" :class="{ 'p-invalid': errors.code }" />
            <small v-if="errors.code" class="text-red-500">{{ errors.code }}</small>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Loại</label>
            <Select v-model="form.type" :options="typeOptions" option-label="label" option-value="value" class="w-full" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Giá trị <span class="text-red-500">*</span></label>
            <InputNumber v-model="form.value" class="w-full" :min="0" :suffix="form.type === 'percentage' ? '%' : ' VND'" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Đơn hàng tối thiểu</label>
            <InputNumber v-model="form.min_order" class="w-full" :min="0" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Giới hạn sử dụng</label>
            <InputNumber v-model="form.usage_limit" class="w-full" :min="0" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ngày hết hạn</label>
            <Calendar v-model="form.expires_at" class="w-full" date-format="dd/mm/yy" />
          </div>
        </div>
        <div class="flex items-center gap-2">
          <InputSwitch v-model="form.is_active" input-id="is_active" />
          <label for="is_active" class="text-sm text-gray-700">Kích hoạt</label>
        </div>
        <div class="flex gap-3 justify-end pt-4">
          <Link :href="route('admin.agriverse.coupons.index')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">Hủy</Link>
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
  coupon: { type: Object, default: null },
})

const page = usePage()
const isEdit = computed(() => !!props.coupon)
const errors = computed(() => page.props.errors || {})

const form = useForm({
  code: props.coupon?.code || '',
  type: props.coupon?.type || 'fixed',
  value: props.coupon?.value || 0,
  min_order: props.coupon?.min_order || 0,
  usage_limit: props.coupon?.usage_limit || null,
  expires_at: props.coupon?.expires_at || null,
  is_active: props.coupon?.is_active ?? true,
})

const typeOptions = [
  { label: 'Giảm theo %', value: 'percentage' },
  { label: 'Giảm tiền mặt', value: 'fixed' },
]

function submit() {
  if (isEdit.value) {
    form.put(route('admin.agriverse.coupons.update', props.coupon.id))
  } else {
    form.post(route('admin.agriverse.coupons.store'))
  }
}
</script>

<style scoped>
</style>
