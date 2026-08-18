<template>
  <Head :title="isEdit ? 'Sửa gói đăng ký' : 'Thêm gói đăng ký'" />

  <div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-3">
      <Link :href="route('admin.agriverse.subscription-plans.index')" class="text-gray-400 hover:text-gray-600">
        <i class="pi pi-arrow-left text-xl" />
      </Link>
      <h1 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Sửa gói đăng ký' : 'Thêm gói đăng ký' }}</h1>
    </div>

    <UiCard>
      <form @submit.prevent="submit" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tên gói <span class="text-red-500">*</span></label>
            <InputText v-model="form.name" class="w-full" :class="{ 'p-invalid': errors.name }" />
            <small v-if="errors.name" class="text-red-500">{{ errors.name }}</small>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
            <InputText v-model="form.slug" class="w-full" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Giá <span class="text-red-500">*</span></label>
            <InputNumber v-model="form.price" class="w-full" :min="0" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Chu kỳ</label>
            <Select v-model="form.interval" :options="intervalOptions" option-label="label" option-value="value" class="w-full" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
          <Textarea v-model="form.description" class="w-full" rows="3" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tính năng (mỗi dòng một tính năng)</label>
          <Textarea v-model="form.features_text" class="w-full" rows="4" placeholder="Tính năng 1&#10;Tính năng 2&#10;Tính năng 3" />
        </div>

        <div class="flex items-center gap-6">
          <div class="flex items-center gap-2">
            <InputSwitch v-model="form.is_active" input-id="plan-active" />
            <label for="plan-active" class="text-sm text-gray-700">Kích hoạt</label>
          </div>
          <div class="flex items-center gap-2">
            <InputSwitch v-model="form.is_popular" input-id="plan-popular" />
            <label for="plan-popular" class="text-sm text-gray-700">Gói phổ biến</label>
          </div>
        </div>

        <div class="flex gap-3 justify-end pt-4">
          <Link :href="route('admin.agriverse.subscription-plans.index')" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">Hủy</Link>
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
  plan: { type: Object, default: null },
})

const page = usePage()
const isEdit = computed(() => !!props.plan)
const errors = computed(() => page.props.errors || {})

const form = useForm({
  name: props.plan?.name || '',
  slug: props.plan?.slug || '',
  description: props.plan?.description || '',
  price: props.plan?.price || 0,
  interval: props.plan?.interval || 'monthly',
  features_text: props.plan?.features?.join('\n') || '',
  is_active: props.plan?.is_active ?? true,
  is_popular: props.plan?.is_popular ?? false,
})

const intervalOptions = [
  { label: 'Hàng tháng', value: 'monthly' },
  { label: 'Hàng năm', value: 'yearly' },
]

function submit() {
  const data = {
    ...form,
    features: form.features_text.split('\n').filter(f => f.trim()),
  }
  if (isEdit.value) {
    form.put(route('admin.agriverse.subscription-plans.update', props.plan.id), data)
  } else {
    form.post(route('admin.agriverse.subscription-plans.store'), data)
  }
}
</script>

<style scoped>
</style>
