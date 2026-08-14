<template>
  <Head :title="'Đơn hàng #' + order.code" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <Link :href="route('admin.agriverse.orders.index')" class="text-gray-400 hover:text-gray-600">
          <i class="pi pi-arrow-left text-xl" />
        </Link>
        <h1 class="text-2xl font-bold text-gray-900">Đơn hàng #{{ order.code }}</h1>
        <UiBadge :variant="order.status === 'completed' ? 'success' : order.status === 'cancelled' ? 'danger' : order.status === 'pending' ? 'warning' : 'info'">{{ order.status_text || order.status }}</UiBadge>
      </div>
      <div class="flex gap-2">
        <Button v-if="order.status === 'pending'" severity="warn" @click="confirmOrder">Xác nhận</Button>
        <Button v-if="order.status !== 'cancelled' && order.status !== 'completed'" severity="danger" @click="cancelOrder">Hủy</Button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <UiCard>
        <template #header><h2 class="font-semibold text-gray-900">Thông tin đơn hàng</h2></template>
        <div class="space-y-2 text-sm">
          <div class="flex justify-between"><span class="text-gray-500">Mã đơn:</span><span class="font-medium">#{{ order.code }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Ngày tạo:</span><span class="font-medium">{{ order.created_at }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Thanh toán:</span><span class="font-medium">{{ order.payment_method }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Phí ship:</span><span class="font-medium">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(order.shipping_fee || 0) }}</span></div>
          <div class="flex justify-between border-t pt-2"><span class="font-semibold">Tổng cộng:</span><span class="font-bold text-lg text-emerald-600">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(order.total) }}</span></div>
        </div>
      </UiCard>

      <UiCard>
        <template #header><h2 class="font-semibold text-gray-900">Người mua</h2></template>
        <div class="space-y-2 text-sm">
          <p class="font-medium">{{ order.buyer?.name }}</p>
          <p class="text-gray-500">{{ order.buyer?.email }}</p>
          <p class="text-gray-500">{{ order.buyer?.phone || '—' }}</p>
        </div>
      </UiCard>

      <UiCard v-if="order.seller">
        <template #header><h2 class="font-semibold text-gray-900">Người bán</h2></template>
        <div class="space-y-2 text-sm">
          <p class="font-medium">{{ order.seller?.name }}</p>
          <p class="text-gray-500">{{ order.seller?.email }}</p>
        </div>
      </UiCard>
    </div>

    <UiCard>
      <template #header><h2 class="font-semibold text-gray-900">Sản phẩm</h2></template>
      <DataTable :value="order.items" striped-rows class="text-sm">
        <Column field="product.name" header="Sản phẩm" />
        <Column field="quantity" header="Số lượng" />
        <Column field="price" header="Đơn giá">
          <template #body="{ data }">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(data.price) }}</template>
        </Column>
        <Column field="total" header="Thành tiền">
          <template #body="{ data }">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(data.total) }}</template>
        </Column>
      </DataTable>
    </UiCard>

    <UiCard v-if="order.statuses?.length">
      <template #header><h2 class="font-semibold text-gray-900">Lịch sử trạng thái</h2></template>
      <Timeline :value="order.statuses" class="text-sm">
        <template #marker="{ item }">
          <span class="w-2 h-2 rounded-full" :class="item.status === 'completed' ? 'bg-emerald-500' : 'bg-gray-300'" />
        </template>
        <template #content="{ item }">
          <p class="font-medium">{{ item.status_text || item.status }}</p>
          <p class="text-gray-400 text-xs">{{ item.created_at }}</p>
        </template>
      </Timeline>
    </UiCard>
  </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'
import UiBadge from '@/Components/ui/UiBadge.vue'

const props = defineProps({
  order: { type: Object, required: true },
})

function confirmOrder() {
  // TODO: Replace with PrimeVue useConfirm() for non-blocking UX
  if (confirm('Xác nhận đơn hàng này?')) {
    router.put(route('admin.agriverse.orders.update-status', props.order.id), { status: 'confirmed' })
  }
}

function cancelOrder() {
  // TODO: Replace with PrimeVue useConfirm() for non-blocking UX
  if (confirm('Hủy đơn hàng này?')) {
    router.put(route('admin.agriverse.orders.update-status', props.order.id), { status: 'cancelled' })
  }
}
</script>

<style scoped>
</style>
