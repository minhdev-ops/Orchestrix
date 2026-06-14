<template>
  <MarketplaceLayout>
    <section class="max-w-[1200px] mx-auto px-6 py-10 md:py-14" style="font-family: var(--ag-font-body);">
      <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
          <div class="flex items-center gap-3 mb-3">
            <Link :href="route('agriverse.shop.orders.index')" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-white border border-black/5 shadow-sm text-[var(--ag-text-secondary)] hover:text-[var(--ag-primary-500)] transition-colors text-sm font-bold">
              <span class="material-symbols-outlined text-[18px]">arrow_back</span>
              Quay lại
            </Link>
          </div>
          <h1 class="text-2xl sm:text-4xl md:text-5xl font-medium tracking-tight mb-2" style="font-family: var(--ag-font-display); color: var(--ag-text-primary);">
            Đơn hàng #{{ order.id }}
          </h1>
          <p class="text-[var(--ag-text-secondary)] text-sm font-semibold flex items-center gap-2">
            <span class="material-symbols-outlined text-[16px]">calendar_month</span>
            Đặt ngày {{ formatDate(order.created_at) }}
          </p>
        </div>
        <div class="flex items-center self-start md:self-auto">
          <span class="px-4 py-2 md:px-5 md:py-2.5 rounded-full text-xs md:text-sm font-bold shadow-[0_4px_14px_rgba(0,0,0,0.06)] border border-white" :class="statusClass(order.status)">
            {{ statusLabel(order.status) }}
          </span>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
        <!-- Left Column: Details -->
        <div class="md:col-span-8 space-y-8">
          <!-- Product Card -->
          <div class="bg-white rounded-[24px] p-7 md:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-black/[0.03]">
            <h2 class="text-xl font-medium mb-6" style="font-family: var(--ag-font-display); color: var(--ag-text-primary);">Sản phẩm</h2>
            
            <div class="flex flex-col sm:flex-row sm:items-center gap-6">
              <div class="w-24 h-24 rounded-3xl bg-[var(--ag-surface)] flex items-center justify-center shrink-0 border border-black/5 shadow-inner">
                <span class="text-4xl font-medium" style="font-family: var(--ag-font-display); color: var(--ag-neutral-300);">{{ order.product?.name?.charAt(0)?.toUpperCase() }}</span>
              </div>
              <div class="flex-1">
                <h3 class="text-lg md:text-xl font-bold text-[var(--ag-text-primary)] leading-tight">{{ order.product?.name }}</h3>
                <p class="text-sm text-[var(--ag-text-secondary)] mt-1.5 font-bold flex items-center gap-1.5">
                  <span class="material-symbols-outlined text-[16px]">storefront</span>
                  {{ order.store?.name || '—' }}
                </p>
              </div>
              <div class="text-left sm:text-right bg-[var(--ag-bg)]/50 p-4 rounded-2xl border border-black/5 sm:border-none sm:bg-transparent sm:p-0">
                <div class="text-xl font-black text-[var(--ag-text-primary)]">{{ formatPrice(order.unit_price) }}₫</div>
                <div class="text-sm text-[var(--ag-text-secondary)] font-bold mt-1 bg-white sm:bg-transparent inline-block px-3 py-1 rounded-full border border-black/5 sm:border-none sm:p-0">Số lượng: x{{ order.quantity }}</div>
              </div>
            </div>
          </div>

          <!-- Order Info -->
          <div class="bg-white rounded-[24px] p-7 md:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-black/[0.03]">
             <h2 class="text-xl font-medium mb-6" style="font-family: var(--ag-font-display); color: var(--ag-text-primary);">Thông tin giao hàng</h2>
             <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-[var(--ag-bg)]/30 p-5 rounded-2xl border border-black/5">
                  <div class="flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-[var(--ag-primary-500)]">location_on</span>
                    <p class="text-xs text-[var(--ag-text-secondary)] font-black uppercase tracking-widest">Địa chỉ nhận</p>
                  </div>
                  <p class="text-sm text-[var(--ag-text-primary)] font-semibold leading-relaxed">{{ order.shipping_address }}</p>
                </div>
                <div class="bg-[var(--ag-bg)]/30 p-5 rounded-2xl border border-black/5" v-if="order.tracking_number">
                  <div class="flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-[var(--ag-primary-500)]">local_shipping</span>
                    <p class="text-xs text-[var(--ag-text-secondary)] font-black uppercase tracking-widest">Vận chuyển</p>
                  </div>
                  <div class="text-sm text-[var(--ag-text-primary)] font-bold space-y-1.5">
                    <p>{{ order.shipping_method || 'Giao Hàng Nhanh' }}</p>
                    <p class="text-[var(--ag-primary-600)] bg-[var(--ag-primary-500)]/10 inline-block px-3 py-1.5 rounded-lg border border-[var(--ag-primary-500)]/20 mt-1">MVD: {{ order.tracking_number }}</p>
                  </div>
                  <a v-if="order.tracking_url" :href="order.tracking_url" target="_blank" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 rounded-xl bg-white border border-black/5 shadow-sm text-xs font-bold text-[var(--ag-primary-600)] hover:text-[var(--ag-primary-700)] hover:border-[var(--ag-primary-500)]/30 transition-all">
                    Theo dõi hành trình <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                  </a>
                </div>
                <div v-if="order.notes" class="md:col-span-2 p-5 rounded-2xl bg-[#FFF9E6]/50 border border-[#FFF9E6]">
                  <p class="text-xs text-[#B48100] font-black uppercase tracking-widest mb-2">Ghi chú từ bạn</p>
                  <p class="text-sm text-[var(--ag-text-primary)] font-semibold italic">"{{ order.notes }}"</p>
                </div>
                <div v-if="order.cancel_reason" class="md:col-span-2 p-5 rounded-2xl bg-[var(--ag-danger)]/5 border border-[var(--ag-danger)]/10">
                  <p class="text-xs text-[var(--ag-danger)] font-black uppercase tracking-widest mb-2">Lý do hủy</p>
                  <p class="text-sm text-[var(--ag-danger)] font-bold">{{ order.cancel_reason }}</p>
                </div>
             </div>
          </div>

          <!-- Timeline -->
          <div v-if="statuses.length" class="bg-white rounded-[24px] p-7 md:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-black/[0.03]">
            <h2 class="text-xl font-medium mb-8" style="font-family: var(--ag-font-display); color: var(--ag-text-primary);">Lịch sử trạng thái</h2>
            <div class="space-y-0 relative before:absolute before:inset-0 before:ml-[1.125rem] before:-translate-x-px md:before:ml-[1.375rem] md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-black/10 before:to-transparent">
              <div v-for="(s, i) in statuses" :key="s.id" class="relative flex items-start gap-6 pb-8 last:pb-0">
                <div class="relative z-10 w-9 h-9 md:w-11 md:h-11 flex items-center justify-center rounded-full bg-white border-[6px]" :class="i === 0 ? 'border-[var(--ag-primary-500)]/20 shadow-[0_0_0_1px_rgba(16,185,129,0.5)]' : 'border-[var(--ag-neutral-300)]/20 shadow-[0_0_0_1px_rgba(0,0,0,0.1)]'">
                   <div class="w-3 h-3 rounded-full" :class="i === 0 ? 'bg-[var(--ag-primary-500)]' : 'bg-[var(--ag-neutral-400)]'"></div>
                </div>
                <div class="pt-1.5 flex-1">
                  <div class="text-base font-bold text-[var(--ag-text-primary)]">{{ statusLabel(s.status) }}</div>
                  <div class="text-xs font-semibold text-[var(--ag-text-secondary)] mt-1.5">{{ formatDate(s.created_at) }}</div>
                  <div v-if="s.note" class="mt-3 text-sm text-[var(--ag-text-secondary)] bg-[var(--ag-bg)]/50 rounded-2xl p-4 border border-black/5 font-semibold">{{ s.note }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex flex-wrap gap-4">
             <button v-if="canCancel" @click="showCancel = true" class="px-7 py-3.5 rounded-full bg-white border border-[var(--ag-danger)]/20 text-[var(--ag-danger)] font-bold text-sm shadow-sm hover:bg-[var(--ag-danger)]/5 transition-all focus:ring-4 focus:ring-[var(--ag-danger)]/10">
               Hủy đơn hàng
             </button>
             <button v-if="canRefund" @click="showRefund = true" class="px-7 py-3.5 rounded-full bg-white border border-[var(--ag-warning)]/20 text-[var(--ag-warning)] font-bold text-sm shadow-sm hover:bg-[var(--ag-warning)]/5 transition-all focus:ring-4 focus:ring-[var(--ag-warning)]/10">
               Yêu cầu hoàn tiền
             </button>
             <button v-if="canConfirmReceived" @click="handleConfirmReceived" class="px-8 py-3.5 rounded-full bg-[var(--ag-success)] text-white font-bold text-sm shadow-[0_4px_20px_rgba(16,185,129,0.4)] hover:bg-[var(--ag-success)]/90 transition-all transform hover:-translate-y-0.5 focus:ring-4 focus:ring-[var(--ag-success)]/30">
               Đã nhận được hàng
             </button>
          </div>
        </div>

        <!-- Right Column: Summary & Passport -->
        <div class="md:col-span-4 space-y-8">
          <!-- Summary -->
          <div class="bg-white rounded-[24px] p-7 shadow-[0_8px_30px_rgb(0,0,0,0.06)] border border-black/[0.03] sticky top-24">
            <h2 class="text-xl font-medium mb-6" style="font-family: var(--ag-font-display); color: var(--ag-text-primary);">Tổng quan</h2>
            <div class="space-y-4 text-sm font-semibold">
              <div class="flex justify-between text-[var(--ag-text-secondary)]">
                <span>Tạm tính</span>
                <span class="text-[var(--ag-text-primary)]">{{ formatPrice(order.total_price) }}₫</span>
              </div>
              <div v-if="order.shipping_fee > 0" class="flex justify-between text-[var(--ag-text-secondary)]">
                <span>Phí vận chuyển</span>
                <span class="text-[var(--ag-text-primary)]">{{ formatPrice(order.shipping_fee) }}₫</span>
              </div>
              <div v-if="order.discount_amount > 0" class="flex justify-between text-[var(--ag-primary-500)]">
                <span>Giảm giá</span>
                <span class="bg-[var(--ag-primary-500)]/10 px-2 py-0.5 rounded-md">-{{ formatPrice(order.discount_amount) }}₫</span>
              </div>
              <div class="flex justify-between text-[var(--ag-text-secondary)]">
                <span>Phí giao dịch</span>
                <span class="text-[var(--ag-text-primary)]">{{ formatPrice(order.commission_fee) }}₫</span>
              </div>
              
              <div class="h-px bg-black/5 my-5"></div>
              
              <div class="flex flex-col gap-1 items-end">
                <div class="w-full flex justify-between items-center mb-1">
                   <span class="font-bold text-[var(--ag-text-primary)] text-base">Thành tiền</span>
                   <span class="font-medium text-3xl" style="color: var(--ag-primary-600); font-family: var(--ag-font-display);">{{ formatPrice(order.total_amount) }}₫</span>
                </div>
                <span class="text-xs text-[var(--ag-text-muted)] font-medium">(Đã bao gồm VAT nếu có)</span>
              </div>
            </div>

            <div class="mt-8 pt-6 border-t border-black/5">
              <div class="flex items-start gap-4 p-5 rounded-2xl bg-[var(--ag-primary-500)]/5 border border-[var(--ag-primary-500)]/10">
                <span class="material-symbols-outlined text-[var(--ag-primary-500)] bg-white w-8 h-8 flex items-center justify-center rounded-full shadow-sm shrink-0" style="font-size: 18px;">handshake</span>
                <div>
                  <p class="text-sm font-bold text-[var(--ag-primary-700)]">Thanh toán trực tiếp</p>
                  <p class="text-xs text-[var(--ag-text-secondary)] font-semibold mt-1.5 leading-relaxed">Vui lòng trao đổi với người bán qua khung chat để thống nhất phương thức thanh toán.</p>
                </div>
              </div>
            </div>
            
            <Link v-if="order.contract" :href="route('agriverse.shop.contracts.show', order.contract.id)"
              class="mt-6 flex items-center justify-center w-full h-12 rounded-full bg-[var(--ag-bg)] text-[var(--ag-text-primary)] font-bold text-sm hover:bg-black/5 border border-black/5 transition-all gap-2">
              <span class="material-symbols-outlined" style="font-size: 20px;">contract</span>
              Xem hợp đồng điện tử
            </Link>
          </div>
          
          <!-- Passport info -->
          <div v-if="order.product?.passport_logs?.length" class="bg-[var(--ag-surface)]/80 backdrop-blur-md rounded-[24px] p-7 border border-black/[0.04]">
             <div class="flex items-center gap-3 mb-4">
               <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm text-[var(--ag-primary-500)]">
                  <span class="material-symbols-outlined">qr_code_scanner</span>
               </div>
               <h2 class="text-lg font-medium text-[var(--ag-text-primary)]" style="font-family: var(--ag-font-display);">Hộ chiếu số</h2>
             </div>
             <p class="text-sm text-[var(--ag-text-secondary)] font-medium mb-6 leading-relaxed">Sản phẩm này được cấp hộ chiếu số định danh Blockchain. Bạn có thể theo dõi toàn bộ vòng đời sinh trưởng.</p>
             <button class="w-full py-3.5 rounded-full bg-white shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-black/5 text-sm font-bold text-[var(--ag-text-primary)] hover:border-black/15 hover:shadow-md transition-all">
                Truy xuất nguồn gốc
             </button>
          </div>
        </div>

        <!-- Chat section full width -->
        <div class="md:col-span-12 mt-4">
          <div class="bg-white rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-black/[0.03] overflow-hidden">
            <div class="px-8 py-6 border-b border-black/5 bg-[var(--ag-bg)]/30 flex items-center gap-3">
               <span class="material-symbols-outlined text-[var(--ag-primary-500)] text-2xl">forum</span>
               <div>
                 <h2 class="text-xl font-medium" style="font-family: var(--ag-font-display); color: var(--ag-text-primary);">Trao đổi với người bán</h2>
                 <p class="text-xs text-[var(--ag-text-secondary)] font-bold mt-1">Lưu trữ bảo mật làm bằng chứng giao dịch</p>
               </div>
            </div>
            <div class="p-8">
               <ChatBox :order-id="order.id" />
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Cancel modal -->
    <div v-if="showCancel" class="fixed inset-0 z-50 flex items-center justify-center bg-[var(--ag-text-primary)]/40 backdrop-blur-md" @click.self="showCancel = false">
      <div class="bg-white rounded-[24px] p-8 max-w-md w-full mx-4 shadow-2xl border border-white/20">
        <div class="w-12 h-12 rounded-full bg-[var(--ag-danger)]/10 text-[var(--ag-danger)] flex items-center justify-center mb-5">
           <span class="material-symbols-outlined">warning</span>
        </div>
        <h3 class="text-2xl font-medium text-[var(--ag-text-primary)] mb-4" style="font-family: var(--ag-font-display);">Hủy đơn hàng</h3>
        <p class="text-sm text-[var(--ag-text-secondary)] font-medium mb-5">Bạn có chắc chắn muốn hủy đơn hàng này không? Vui lòng cho chúng tôi biết lý do.</p>
        <textarea v-model="cancelReason" placeholder="Nhập lý do hủy..."
          class="w-full h-28 px-5 py-4 rounded-2xl border border-black/10 bg-[var(--ag-bg)]/50 text-sm font-medium outline-none resize-none focus:border-[var(--ag-danger)]/40 focus:ring-4 focus:ring-[var(--ag-danger)]/10 focus:bg-white mb-6 transition-all" />
        <div class="flex gap-4">
          <button @click="showCancel = false" class="flex-1 h-12 rounded-full bg-[var(--ag-bg)] border border-black/5 text-[var(--ag-text-secondary)] text-sm font-bold hover:bg-black/5 transition-all">Đóng</button>
          <button @click="handleCancel" class="flex-1 h-12 rounded-full bg-[var(--ag-danger)] shadow-[0_4px_14px_rgba(239,68,68,0.3)] text-white text-sm font-bold hover:bg-[var(--ag-danger)]/90 transition-all">Xác nhận hủy</button>
        </div>
      </div>
    </div>

    <!-- Refund modal -->
    <div v-if="showRefund" class="fixed inset-0 z-50 flex items-center justify-center bg-[var(--ag-text-primary)]/40 backdrop-blur-md" @click.self="showRefund = false">
      <div class="bg-white rounded-[24px] p-8 max-w-md w-full mx-4 shadow-2xl border border-white/20">
        <div class="w-12 h-12 rounded-full bg-[var(--ag-warning)]/10 text-[var(--ag-warning)] flex items-center justify-center mb-5">
           <span class="material-symbols-outlined">currency_exchange</span>
        </div>
        <h3 class="text-2xl font-medium text-[var(--ag-text-primary)] mb-4" style="font-family: var(--ag-font-display);">Yêu cầu hoàn tiền</h3>
        <select v-model="refundReason" class="w-full h-14 px-5 rounded-2xl border border-black/10 bg-[var(--ag-bg)]/50 text-sm font-bold text-[var(--ag-text-primary)] outline-none focus:border-[var(--ag-primary-500)]/40 focus:ring-4 focus:ring-[var(--ag-primary-500)]/10 focus:bg-white mb-4 transition-all">
          <option value="">Chọn lý do hoàn tiền...</option>
          <option value="Sản phẩm không đúng mô tả">Sản phẩm không đúng mô tả</option>
          <option value="Sản phẩm bị hư hỏng">Sản phẩm bị hư hỏng</option>
          <option value="Giao hàng sai">Giao hàng sai</option>
          <option value="Không còn nhu cầu">Không còn nhu cầu</option>
          <option value="Khác">Khác</option>
        </select>
        <textarea v-model="refundDesc" placeholder="Mô tả chi tiết vấn đề bạn gặp phải..."
          class="w-full h-24 px-5 py-4 rounded-2xl border border-black/10 bg-[var(--ag-bg)]/50 text-sm font-medium outline-none resize-none focus:border-[var(--ag-primary-500)]/40 focus:ring-4 focus:ring-[var(--ag-primary-500)]/10 focus:bg-white mb-6 transition-all" />
        <div class="flex gap-4">
          <button @click="showRefund = false" class="flex-1 h-12 rounded-full bg-[var(--ag-bg)] border border-black/5 text-[var(--ag-text-secondary)] text-sm font-bold hover:bg-black/5 transition-all">Đóng</button>
          <button @click="handleRefund" class="flex-1 h-12 rounded-full bg-[var(--ag-warning)] shadow-[0_4px_14px_rgba(245,158,11,0.3)] text-white text-sm font-bold hover:bg-[var(--ag-warning)]/90 transition-all">Gửi yêu cầu</button>
        </div>
      </div>
    </div>
  </MarketplaceLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import MarketplaceLayout from '@agriverse/Layouts/MarketplaceLayout.vue';
import { formatPrice, statusLabel, statusClass } from '@agriverse/utils';
import ChatBox from '@agriverse/Components/ChatBox.vue';
import { useToast } from 'primevue/usetoast';

const toast = useToast();
const props = defineProps({ order: Object });

const statuses = computed(() => props.order?.statuses || []);

const canCancel = computed(() => ['pending', 'confirmed'].includes(props.order?.status));
const canConfirmReceived = computed(() => props.order?.status === 'delivered');
const canRefund = computed(() => ['delivered', 'completed'].includes(props.order?.status) && !hasPendingRefund.value);

const hasPendingRefund = computed(() =>
  props.order?.refunds?.some(r => ['pending', 'approved'].includes(r.status))
);

const showCancel = ref(false);
const showRefund = ref(false);
const cancelReason = ref('');
const refundReason = ref('');
const refundDesc = ref('');

function formatDate(dateString) {
  if (!dateString) return '—';
  const d = new Date(dateString);
  return d.toLocaleString('vi-VN', { 
    year: 'numeric', 
    month: '2-digit', 
    day: '2-digit', 
    hour: '2-digit', 
    minute: '2-digit' 
  }).replace(',', ' lúc');
}

function handleCancel() {
  if (!cancelReason.value) { toast.add({ severity: 'error', summary: 'Vui lòng nhập lý do hủy', life: 3000 }); return; }
  router.post(route('agriverse.api.orders.cancel', props.order.id), { reason: cancelReason.value }, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => { showCancel.value = false; cancelReason.value = ''; },
    onError: () => toast.add({ severity: 'error', summary: 'Hủy đơn thất bại', life: 3000 }),
  });
}

function handleConfirmReceived() {
  router.post(route('agriverse.api.orders.confirm-received', props.order.id), {}, {
    preserveState: true,
    preserveScroll: true,
    onError: () => toast.add({ severity: 'error', summary: 'Xác nhận thất bại', life: 3000 }),
  });
}

function handleRefund() {
  if (!refundReason.value) { toast.add({ severity: 'error', summary: 'Vui lòng chọn lý do', life: 3000 }); return; }
  router.post(route('agriverse.api.orders.refund', props.order.id), { reason: refundReason.value, description: refundDesc.value }, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => { showRefund.value = false; refundReason.value = ''; refundDesc.value = ''; },
    onError: () => toast.add({ severity: 'error', summary: 'Gửi yêu cầu thất bại', life: 3000 }),
  });
}
</script>
