<template>
  <Head :title="'Hoàn tiền: ' + refund.code" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <Link :href="route('admin.agriverse.refunds.index')" class="text-gray-400 hover:text-gray-600">
          <i class="pi pi-arrow-left text-xl" />
        </Link>
        <h1 class="text-2xl font-bold text-gray-900">Yêu cầu hoàn tiền {{ refund.code }}</h1>
        <UiBadge :variant="refund.status === 'approved' ? 'success' : refund.status === 'rejected' ? 'danger' : 'warning'">{{ refund.status_text || refund.status }}</UiBadge>
      </div>
      <div v-if="refund.status === 'pending'" class="flex gap-2">
        <Button severity="success" @click="approveRefund">
          <i class="pi pi-check mr-1" /> Phê duyệt
        </Button>
        <Button severity="danger" @click="showRejectDialog = true">
          <i class="pi pi-times mr-1" /> Từ chối
        </Button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <UiCard>
        <template #header><h2 class="font-semibold text-gray-900">Chi tiết yêu cầu</h2></template>
        <div class="space-y-3 text-sm">
          <div class="flex justify-between"><span class="text-gray-500">Mã yêu cầu:</span><span class="font-mono font-medium">{{ refund.code }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Số tiền:</span><span class="font-bold text-lg text-emerald-600">{{ new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(refund.amount) }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Trạng thái:</span><UiBadge :variant="refund.status === 'approved' ? 'success' : refund.status === 'rejected' ? 'danger' : 'warning'">{{ refund.status }}</UiBadge></div>
          <div class="flex justify-between"><span class="text-gray-500">Đơn hàng:</span>
            <Link :href="route('admin.agriverse.orders.show', refund.order_id)" class="text-emerald-600 hover:text-emerald-800">#{{ refund.order?.code }}</Link>
          </div>
          <div class="flex justify-between"><span class="text-gray-500">Người yêu cầu:</span><span>{{ refund.user?.name }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Ngày tạo:</span><span>{{ refund.created_at }}</span></div>
        </div>
      </UiCard>

      <UiCard>
        <template #header><h2 class="font-semibold text-gray-900">Lý do</h2></template>
        <div class="text-sm text-gray-700 bg-gray-50 p-4 rounded-lg">{{ refund.reason }}</div>
      </UiCard>
    </div>

    <UiCard v-if="refund.notes">
      <template #header><h2 class="font-semibold text-gray-900">Ghi chú</h2></template>
      <div class="text-sm text-gray-700">{{ refund.notes }}</div>
    </UiCard>

    <UiModal v-model:model-value="showRejectDialog" title="Từ chối hoàn tiền">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Lý do từ chối</label>
          <Textarea v-model="rejectReason" class="w-full" rows="3" placeholder="Nhập lý do từ chối..." />
        </div>
        <div class="flex gap-3 justify-end">
          <Button severity="secondary" @click="showRejectDialog = false">Hủy</Button>
          <Button severity="danger" :loading="rejecting" @click="rejectRefund">Xác nhận từ chối</Button>
        </div>
      </div>
    </UiModal>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'
import UiBadge from '@/Components/ui/UiBadge.vue'
import UiModal from '@/Components/ui/UiModal.vue'

const props = defineProps({
  refund: { type: Object, required: true },
})

const showRejectDialog = ref(false)
const rejectReason = ref('')
const rejecting = ref(false)

function approveRefund() {
  // TODO: Replace with PrimeVue useConfirm() for non-blocking UX
  if (confirm('Phê duyệt yêu cầu hoàn tiền này?')) {
    router.put(route('admin.agriverse.refunds.approve', props.refund.id))
  }
}

function rejectRefund() {
  rejecting.value = true
  router.put(route('admin.agriverse.refunds.reject', props.refund.id), {
    reason: rejectReason.value,
  }, {
    onFinish: () => {
      rejecting.value = false
      showRejectDialog.value = false
    },
  })
}
</script>

<style scoped>
</style>
