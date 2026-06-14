<template>
  <MarketplaceLayout>
    <main style="padding-top: 40px;">
      <section class="success-header">
        <div class="success-header-icon">
          <span class="material-symbols-outlined" style="font-size: 36px; font-variation-settings: 'FILL' 1;">check_circle</span>
        </div>
        <h1 class="success-title">Đặt hàng thành công!</h1>
        <p class="success-desc">
          Đơn hàng của bạn đã được ghi nhận. Vui lòng liên hệ trực tiếp với người bán để thỏa thuận phương thức thanh toán và xác nhận đơn hàng.
        </p>
        <div class="success-cards">
          <div class="success-card">
            <span class="success-card-label">MÃ ĐƠN HÀNG</span>
            <span class="success-card-value">#{{ orderId }}</span>
          </div>
          <div class="success-card">
            <span class="success-card-label">SẢN PHẨM</span>
            <span class="success-card-value" style="color: var(--ag-text-primary);">{{ product?.name || '—' }}</span>
          </div>
        </div>
      </section>

      <div class="success-bento">
        <div class="success-order-summary">
          <div class="success-contact-card" v-if="seller || store">
            <h2 class="success-contact-title">Thông tin người bán</h2>
            <p class="success-contact-desc">Chủ động liên hệ với người bán qua thông tin dưới đây để thanh toán và xác nhận đơn hàng.</p>
            <div class="success-contact-info">
              <div class="success-contact-row">
                <span class="material-symbols-outlined success-contact-icon">store</span>
                <div>
                  <span class="success-contact-label">Cửa hàng</span>
                  <span class="success-contact-value">{{ store?.name || '—' }}</span>
                </div>
              </div>
              <div class="success-contact-row">
                <span class="material-symbols-outlined success-contact-icon">person</span>
                <div>
                  <span class="success-contact-label">Người bán</span>
                  <span class="success-contact-value">{{ seller?.name || '—' }}</span>
                </div>
              </div>
              <div class="success-contact-row">
                <span class="material-symbols-outlined success-contact-icon">call</span>
                <div>
                  <span class="success-contact-label">Số điện thoại</span>
                  <span class="success-contact-value">{{ seller?.phone || store?.phone || '—' }}</span>
                </div>
              </div>
              <div class="success-contact-row">
                <span class="material-symbols-outlined success-contact-icon">mail</span>
                <div>
                  <span class="success-contact-label">Email</span>
                  <span class="success-contact-value">{{ seller?.email || '—' }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="success-notice-card">
            <span class="material-symbols-outlined" style="color: var(--ag-warning); font-size: 24px;">handshake</span>
            <div>
              <h3 class="success-notice-title">Thanh toán trực tiếp</h3>
              <p class="success-notice-desc">
                Nền tảng không thu hộ tiền. Bạn vui lòng chủ động liên hệ với người bán qua số điện thoại hoặc email bên trên để thống nhất phương thức thanh toán (chuyển khoản, tiền mặt khi nhận hàng...).
              </p>
            </div>
          </div>

          <div class="success-shipping-card" v-if="shippingMethod">
            <span class="material-symbols-outlined" style="color: var(--ag-primary-500);">local_shipping</span>
            <div>
              <h3 class="success-shipping-label">Vận chuyển</h3>
              <p class="success-shipping-method">Dịch vụ: <strong>{{ shippingMethod }}</strong></p>
              <p class="success-shipping-note">Phí ship sẽ do bạn và người bán thỏa thuận.</p>
            </div>
          </div>
        </div>

        <div class="success-green-tech">
          <div class="success-green-content">
            <div class="success-green-badge">TIẾP THEO</div>
            <h2 class="success-green-title">Các bước tiếp theo</h2>
            <div class="success-steps-grid">
              <div class="success-step">
                <div class="success-step-icon">
                  <span class="material-symbols-outlined" style="font-size: 20px;">call</span>
                </div>
                <h4 class="success-step-title">1. LIÊN HỆ NGƯỜI BÁN</h4>
                <p class="success-step-desc">Gọi hoặc nhắn tin cho người bán qua số điện thoại được cung cấp để thông báo đơn hàng.</p>
              </div>
              <div class="success-step">
                <div class="success-step-icon">
                  <span class="material-symbols-outlined" style="font-size: 20px;">payments</span>
                </div>
                <h4 class="success-step-title">2. THỎA THUẬN THANH TOÁN</h4>
                <p class="success-step-desc">Thống nhất phương thức thanh toán với người bán: chuyển khoản, tiền mặt khi nhận hàng, hoặc hình thức khác.</p>
              </div>
              <div class="success-step">
                <div class="success-step-icon">
                  <span class="material-symbols-outlined" style="font-size: 20px;">package_2</span>
                </div>
                <h4 class="success-step-title">3. CHỜ XÁC NHẬN & GIAO HÀNG</h4>
                <p class="success-step-desc">Sau khi thanh toán, người bán sẽ xác nhận và tiến hành đóng gói, giao hàng cho bạn.</p>
              </div>
              <div class="success-step">
                <div class="success-step-icon">
                  <span class="material-symbols-outlined" style="font-size: 20px;">verified</span>
                </div>
                <h4 class="success-step-title">4. NHẬN HÀNG & ĐÁNH GIÁ</h4>
                <p class="success-step-desc">Kiểm tra hàng khi nhận. Xác nhận đã nhận trên hệ thống và để lại đánh giá cho người bán.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <section class="success-actions">
        <h3 class="success-actions-title">Quản lý đơn hàng</h3>
        <div class="success-actions-group">
          <Link :href="route('agriverse.shop.orders.show', orderId)" class="success-btn success-btn-primary">
            <span class="material-symbols-outlined">receipt_long</span>
            Xem chi tiết đơn hàng
          </Link>
          <Link :href="route('agriverse.shop.orders.index')" class="success-btn success-btn-secondary">
            <span class="material-symbols-outlined">list_alt</span>
            Danh sách đơn hàng
          </Link>
        </div>
      </section>
    </main>
  </MarketplaceLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import MarketplaceLayout from '@agriverse/Layouts/MarketplaceLayout.vue';

const props = defineProps({
  orderId: { type: String, default: '' },
  seller: { type: Object, default: null },
  store: { type: Object, default: null },
  product: { type: Object, default: null },
  total: { type: Number, default: 0 },
  shippingMethod: { type: String, default: '' },
});
</script>

<style scoped>
.success-header {
  text-align: center;
  max-width: var(--ag-container-max, 1280px);
  margin: 0 auto;
  padding: 80px var(--ag-margin-desktop, 64px) 0;
}
@media (max-width: 768px) {
  .success-header { padding: 60px var(--ag-margin-mobile, 20px) 0; }
}
.success-header-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: color-mix(in srgb, var(--ag-primary-500) 12%, transparent);
  color: var(--ag-primary-500);
  margin-bottom: 24px;
}
.success-title {
  font-family: var(--ag-font-display);
  font-size: clamp(2.25rem, 1.7rem + 1.4vw, 3rem);
  font-weight: 500;
  line-height: 1.15;
  letter-spacing: -0.02em;
  color: var(--ag-text-primary);
  margin-bottom: 12px;
}
.success-desc {
  font-family: var(--ag-font-body);
  font-size: 16px;
  line-height: 24px;
  color: var(--ag-text-secondary);
  max-width: 560px;
  margin: 0 auto 32px;
}
.success-cards {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 12px;
}
.success-card {
  background: white;
  padding: 20px 24px;
  border-radius: 12px;
  border: 1px solid rgba(116, 121, 108, 0.12);
  text-align: left;
  min-width: 200px;
}
.success-card-label {
  display: block;
  font-family: var(--ag-font-body);
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.08em;
  color: var(--ag-text-secondary);
  margin-bottom: 4px;
  text-transform: uppercase;
}
.success-card-value {
  font-family: var(--ag-font-display);
  font-size: 22px;
  font-weight: 500;
  color: var(--ag-primary-500);
  line-height: 28px;
}

.success-bento {
  max-width: var(--ag-container-max, 1280px);
  margin: 48px auto 0;
  padding: 0 var(--ag-margin-desktop, 64px);
  display: grid;
  grid-template-columns: 1fr;
  gap: 24px;
}
@media (min-width: 1024px) {
  .success-bento { grid-template-columns: 5fr 7fr; gap: 32px; }
}
@media (max-width: 768px) {
  .success-bento { padding: 0 var(--ag-margin-mobile, 20px); }
}

.success-order-summary { display: flex; flex-direction: column; gap: 16px; }

.success-contact-card {
  background: white;
  border-radius: 14px;
  padding: 28px;
  border: 1px solid rgba(116, 121, 108, 0.08);
}
.success-contact-title {
  font-family: var(--ag-font-display);
  font-size: 22px;
  font-weight: 500;
  color: var(--ag-text-primary);
  margin-bottom: 6px;
}
.success-contact-desc {
  font-family: var(--ag-font-body);
  font-size: 14px;
  color: var(--ag-text-secondary);
  margin-bottom: 20px;
  line-height: 20px;
}
.success-contact-info { display: flex; flex-direction: column; gap: 12px; }
.success-contact-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 0;
  border-bottom: 1px solid rgba(116, 121, 108, 0.05);
}
.success-contact-row:last-child { border-bottom: none; }
.success-contact-icon {
  font-size: 20px;
  color: var(--ag-primary-400);
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: color-mix(in srgb, var(--ag-primary-500) 6%, transparent);
  border-radius: 10px;
}
.success-contact-label {
  display: block;
  font-family: var(--ag-font-body);
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--ag-text-secondary);
  margin-bottom: 2px;
}
.success-contact-value {
  font-family: var(--ag-font-body);
  font-size: 15px;
  font-weight: 600;
  color: var(--ag-text-primary);
}

.success-notice-card {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 20px;
  background: color-mix(in srgb, var(--ag-warning) 6%, transparent);
  border-radius: 14px;
  border: 1px solid color-mix(in srgb, var(--ag-warning) 12%, transparent);
}
.success-notice-title {
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 700;
  color: var(--ag-warning);
  margin-bottom: 4px;
}
.success-notice-desc {
  font-family: var(--ag-font-body);
  font-size: 13px;
  line-height: 18px;
  color: var(--ag-text-secondary);
}

.success-shipping-card {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 20px;
  background: var(--ag-surface-container);
  border-radius: 14px;
  border: 1px solid rgba(116, 121, 108, 0.06);
}
.success-shipping-label {
  font-family: var(--ag-font-body);
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--ag-text-secondary);
  margin-bottom: 2px;
}
.success-shipping-method {
  font-family: var(--ag-font-body);
  font-size: 15px;
  color: var(--ag-text-primary);
}
.success-shipping-note {
  font-family: var(--ag-font-body);
  font-size: 12px;
  color: var(--ag-text-secondary);
  margin-top: 2px;
}

.success-green-tech {
  background: white;
  border-radius: 14px;
  border: 1px solid rgba(116, 121, 108, 0.08);
  overflow: hidden;
}
.success-green-content { padding: 32px; }
@media (min-width: 768px) { .success-green-content { padding: 40px; } }
.success-green-badge {
  display: inline-flex;
  padding: 4px 12px;
  background: color-mix(in srgb, var(--ag-primary-500) 8%, transparent);
  color: var(--ag-primary-500);
  border-radius: 9999px;
  font-family: var(--ag-font-body);
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.08em;
  margin-bottom: 12px;
  text-transform: uppercase;
}
.success-green-title {
  font-family: var(--ag-font-display);
  font-size: 24px;
  font-weight: 500;
  color: var(--ag-text-primary);
  margin-bottom: 24px;
}
.success-steps-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
}
@media (min-width: 768px) {
  .success-steps-grid { grid-template-columns: 1fr 1fr; gap: 24px; }
}
.success-step { padding: 12px; }
.success-step-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: color-mix(in srgb, var(--ag-primary-500) 8%, transparent);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--ag-primary-500);
  margin-bottom: 10px;
}
.success-step-title {
  font-family: var(--ag-font-body);
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 0.05em;
  color: var(--ag-text-primary);
  margin-bottom: 6px;
}
.success-step-desc {
  font-family: var(--ag-font-body);
  font-size: 13px;
  line-height: 18px;
  color: var(--ag-text-secondary);
}

.success-actions {
  max-width: var(--ag-container-max, 1280px);
  margin: 64px auto;
  padding: 0 var(--ag-margin-desktop, 64px) 80px;
  text-align: center;
}
@media (max-width: 768px) {
  .success-actions { padding: 0 var(--ag-margin-mobile, 20px) 48px; }
}
.success-actions-title {
  font-family: var(--ag-font-display);
  font-size: 22px;
  font-weight: 500;
  color: var(--ag-text-primary);
  margin-bottom: 20px;
}
.success-actions-group { display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; }
.success-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 14px 28px;
  border-radius: 9999px;
  font-family: var(--ag-font-body);
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s;
}
.success-btn-primary {
  background: var(--ag-primary-500);
  color: white;
}
.success-btn-primary:hover { background: var(--ag-primary-600); }
.success-btn-secondary {
  border: 1px solid rgba(116, 121, 108, 0.25);
  color: var(--ag-text-primary);
}
.success-btn-secondary:hover { background: var(--ag-primary-50); }
</style>
