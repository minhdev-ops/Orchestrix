<template>
  <MarketplaceLayout>
    <section class="max-w-[840px] mx-auto px-5 py-8">
      <nav class="flex items-center gap-2 text-sm text-[var(--ag-text-muted)] mb-6">
        <Link :href="route('agriverse.shop.home')" class="hover:text-[var(--ag-primary-500)] transition-colors">Trang chủ</Link>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="text-[var(--ag-text-primary)] font-medium">Địa chỉ</span>
      </nav>

      <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-[var(--ag-text-primary)] tracking-tight">Địa chỉ của tôi</h1>
        <Link :href="route('agriverse.shop.addresses.create')"
          class="h-10 px-5 rounded-2xl bg-[var(--ag-primary-500)] text-white text-sm font-semibold flex items-center gap-1.5 hover:bg-[var(--ag-primary-600)] transition-all active:scale-[0.97]">
          <span class="material-symbols-outlined text-base">add</span>
          Thêm địa chỉ
        </Link>
      </div>

      <div v-if="addresses.length" class="space-y-3">
        <div v-for="addr in addresses" :key="addr.id"
          class="bg-white rounded-2xl border border-[var(--ag-border)] p-4 transition-all hover:border-[var(--ag-primary-500)]/20">
          <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2">
                <span class="text-sm font-bold text-[var(--ag-text-primary)]">{{ addr.recipient_name }}</span>
                <span class="text-xs text-[var(--ag-text-muted)]">— {{ addr.phone }}</span>
                <span v-if="addr.label" class="text-[10px] px-2 py-0.5 rounded-full bg-[var(--ag-primary-500)]/10 text-[var(--ag-primary-500)] font-semibold">{{ addr.label }}</span>
                <span v-if="addr.is_default" class="text-[10px] px-2 py-0.5 rounded-full bg-[#D97706]/10 text-[#D97706] font-semibold">Mặc định</span>
              </div>
              <div class="text-sm text-[var(--ag-text-secondary)] mt-1">
                {{ addr.address_detail }}, {{ addr.ward }}, {{ addr.district }}, {{ addr.province }}
              </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
              <button v-if="!addr.is_default" @click="setDefault(addr)"
                class="h-8 px-3 rounded-xl text-[11px] font-semibold text-[#D97706] hover:bg-[#D97706]/8 transition-all">
                Đặt mặc định
              </button>
              <Link :href="route('agriverse.shop.addresses.edit', addr.id)"
                class="h-8 w-8 rounded-xl flex items-center justify-center text-[var(--ag-text-muted)] hover:text-[var(--ag-primary-500)] hover:bg-[var(--ag-primary-500)]/8 transition-all">
                <span class="material-symbols-outlined text-base">edit</span>
              </Link>
              <button @click="confirmDelete(addr)"
                class="h-8 w-8 rounded-xl flex items-center justify-center text-[var(--ag-text-muted)] hover:text-[var(--ag-danger)] hover:bg-[var(--ag-danger)]/8 transition-all">
                <span class="material-symbols-outlined text-base">delete</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="bg-white rounded-2xl border border-[var(--ag-border)] p-16 text-center">
        <div class="w-20 h-20 rounded-2xl bg-[var(--ag-bg)] flex items-center justify-center mx-auto">
          <span class="material-symbols-outlined text-4xl text-[var(--ag-neutral-300)]">location_on</span>
        </div>
        <div class="text-base font-bold text-[var(--ag-text-secondary)] mt-5">Chưa có địa chỉ nào</div>
        <div class="text-sm text-[var(--ag-text-muted)] mt-1">Thêm địa chỉ để việc đặt hàng nhanh hơn.</div>
        <Link :href="route('agriverse.shop.addresses.create')"
          class="inline-flex items-center gap-2 mt-6 h-11 px-6 rounded-2xl bg-[var(--ag-primary-500)] text-white text-sm font-semibold hover:bg-[var(--ag-primary-600)] transition-all active:scale-[0.97]">
          <span class="material-symbols-outlined text-base">add</span>
          Thêm địa chỉ
        </Link>
      </div>

      <!-- Delete confirm -->
      <ConfirmDialog />
    </section>
  </MarketplaceLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import MarketplaceLayout from '@agriverse/Layouts/MarketplaceLayout.vue';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';

const toast = useToast();
const confirm = useConfirm();
const props = defineProps({ addresses: Array });

function setDefault(addr) {
  router.post(route('agriverse.shop.addresses.set-default', addr.id), {}, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => toast.add({ severity: 'success', summary: 'Đã cập nhật', life: 2000 }),
  });
}

function confirmDelete(addr) {
  confirm.require({
    message: `Xóa địa chỉ "${addr.label || 'không nhãn'}"?`,
    header: 'Xác nhận',
    icon: 'pi pi-exclamation-triangle',
    rejectClass: 'p-button-text p-button-sm',
    acceptClass: 'p-button-danger p-button-sm',
    accept: () => {
      router.delete(route('agriverse.shop.addresses.destroy', addr.id), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => toast.add({ severity: 'success', summary: 'Đã xóa', life: 2000 }),
      });
    },
  });
}
</script>
