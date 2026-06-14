<template>
  <MarketplaceLayout>
    <section class="max-w-[1320px] mx-auto px-5 py-8">
      <h1 class="text-xl font-bold text-[var(--ag-text-primary)] tracking-tight mb-6">Hợp đồng #{{ contract.uuid || contract.id }}</h1>

      <div class="bg-white rounded-2xl border border-[var(--ag-border)] p-6 max-w-2xl">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-[var(--ag-border)]/60">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[var(--ag-primary-500)] to-[var(--ag-primary-600)] text-white flex items-center justify-center font-bold text-sm shadow-sm">
            <span class="material-symbols-outlined text-lg">contract</span>
          </div>
          <div>
            <div class="text-sm font-bold text-[var(--ag-text-primary)]">Thông tin hợp đồng</div>
            <div class="text-xs text-[var(--ag-text-muted)]">Chi tiết thỏa thuận mua bán</div>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div class="p-3 rounded-xl bg-[var(--ag-bg)]">
            <div class="text-[var(--ag-text-muted)] text-xs">Sản phẩm</div>
            <div class="font-semibold text-[var(--ag-text-primary)] mt-0.5">{{ contract.order?.product?.name }}</div>
          </div>
          <div class="p-3 rounded-xl bg-[var(--ag-bg)]">
            <div class="text-[var(--ag-text-muted)] text-xs">Trạng thái</div>
            <div class="mt-0.5">
              <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-0.5 rounded-full"
                :class="contract.status === 'active' ? 'bg-[var(--ag-primary-500)]/10 text-[var(--ag-primary-500)]' : 'bg-[var(--ag-warning)]/10 text-[var(--ag-warning)]'">
                <span class="w-1.5 h-1.5 rounded-full" :class="contract.status === 'active' ? 'bg-[var(--ag-primary-500)]' : 'bg-[var(--ag-warning)]'" />
                {{ contract.status === 'active' ? 'Đang hiệu lực' : 'Chờ xử lý' }}
              </span>
            </div>
          </div>
          <div class="p-3 rounded-xl bg-[var(--ag-bg)]">
            <div class="text-[var(--ag-text-muted)] text-xs">Người mua</div>
            <div class="font-semibold text-[var(--ag-text-primary)] mt-0.5">{{ contract.order?.buyer?.name }}</div>
          </div>
          <div class="p-3 rounded-xl bg-[var(--ag-bg)]">
            <div class="text-[var(--ag-text-muted)] text-xs">Người bán</div>
            <div class="font-semibold text-[var(--ag-text-primary)] mt-0.5">{{ contract.order?.seller?.name }}</div>
          </div>
          <div class="p-3 rounded-xl bg-[var(--ag-bg)]">
            <div class="text-[var(--ag-text-muted)] text-xs">Ngày tạo</div>
            <div class="font-semibold text-[var(--ag-text-primary)] mt-0.5">{{ contract.created_at }}</div>
          </div>
          <div v-if="contract.signed_at" class="p-3 rounded-xl bg-[var(--ag-bg)]">
            <div class="text-[var(--ag-text-muted)] text-xs">Ngày ký</div>
            <div class="font-semibold text-[var(--ag-text-primary)] mt-0.5">{{ contract.signed_at }}</div>
          </div>
        </div>
      </div>
    </section>
  </MarketplaceLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import MarketplaceLayout from '@agriverse/Layouts/MarketplaceLayout.vue';
defineProps({ contract: Object });
</script>
