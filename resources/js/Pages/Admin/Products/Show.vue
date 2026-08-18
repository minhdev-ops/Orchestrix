<template>
  <Head :title="'Sản phẩm: ' + product.name" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <Link :href="route('admin.agriverse.products.index')" class="text-gray-400 hover:text-gray-600">
          <i class="pi pi-arrow-left text-xl" />
        </Link>
        <h1 class="text-2xl font-bold text-gray-900">{{ product.name }}</h1>
        <UiBadge :variant="product.status === 'active' ? 'success' : product.status === 'draft' ? 'warning' : 'danger'">{{ product.status }}</UiBadge>
      </div>
      <div class="flex gap-2">
        <Link :href="route('admin.agriverse.products.edit', product.id)" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors text-sm font-medium">
          <i class="pi pi-pencil" /> Sửa
        </Link>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2 space-y-6">
        <UiCard>
          <template #header><h2 class="font-semibold text-gray-900">Thông tin sản phẩm</h2></template>
          <div class="space-y-3">
            <div class="flex gap-2"><span class="text-sm text-gray-500 w-28">Tên:</span><p class="text-sm font-medium text-gray-900">{{ product.name }}</p></div>
            <div class="flex gap-2"><span class="text-sm text-gray-500 w-28">Slug:</span><p class="text-sm font-medium text-gray-900">{{ product.slug }}</p></div>
            <div class="flex gap-2"><span class="text-sm text-gray-500 w-28">Giá:</span><p class="text-sm font-medium text-gray-900">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(product.price) }}</p></div>
            <div v-if="product.compare_price" class="flex gap-2"><span class="text-sm text-gray-500 w-28">Giá KM:</span><p class="text-sm font-medium text-red-600">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(product.compare_price) }}</p></div>
            <div class="flex gap-2"><span class="text-sm text-gray-500 w-28">Tồn kho:</span><p class="text-sm font-medium text-gray-900">{{ product.stock }}</p></div>
            <div class="flex gap-2"><span class="text-sm text-gray-500 w-28">Danh mục:</span><p class="text-sm font-medium text-gray-900">{{ product.category?.name || '—' }}</p></div>
            <div class="flex gap-2"><span class="text-sm text-gray-500 w-28">Cửa hàng:</span><p class="text-sm font-medium text-gray-900">{{ product.store?.name || '—' }}</p></div>
          </div>
        </UiCard>

        <UiCard v-if="product.description">
          <template #header><h2 class="font-semibold text-gray-900">Mô tả</h2></template>
          <div class="text-sm text-gray-700 whitespace-pre-line">{{ product.description }}</div>
        </UiCard>
      </div>

      <div class="space-y-6">
        <UiCard>
          <template #header><h2 class="font-semibold text-gray-900">Hình ảnh</h2></template>
          <div v-if="product.images?.length" class="grid grid-cols-2 gap-2">
            <img v-for="(img, i) in product.images" :key="i" :src="img" class="w-full h-24 object-cover rounded-lg" alt="" />
          </div>
          <p v-else class="text-sm text-gray-400">Chưa có hình ảnh.</p>
        </UiCard>

        <UiCard>
          <template #header><h2 class="font-semibold text-gray-900">Thống kê</h2></template>
          <div class="space-y-3">
            <div class="flex justify-between"><span class="text-sm text-gray-500">Đã bán:</span><span class="text-sm font-medium">{{ product.sold_count || 0 }}</span></div>
            <div class="flex justify-between"><span class="text-sm text-gray-500">Lượt xem:</span><span class="text-sm font-medium">{{ product.view_count || 0 }}</span></div>
            <div class="flex justify-between"><span class="text-sm text-gray-500">Đánh giá:</span><span class="text-sm font-medium">{{ product.reviews_count || 0 }}</span></div>
          </div>
        </UiCard>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'
import UiBadge from '@/Components/ui/UiBadge.vue'

const props = defineProps({
  product: { type: Object, required: true },
})
</script>

<style scoped>
</style>
