<template>
  <MarketplaceLayout>
    <main>
      <section class="relative w-full overflow-hidden product-hero-section">
        <!-- 3D Model Viewer -->
        <div v-if="product.model_3d_url" class="absolute inset-0 z-0">
          <model-viewer
            :src="product.model_3d_url"
            alt="3D Model"
            camera-controls
            touch-action="pan-y"
            auto-rotate
            auto-rotate-delay="1000"
            rotation-per-second="15deg"
            camera-orbit="45deg 70deg 120%"
            min-camera-orbit="auto auto 30%"
            max-camera-orbit="Infinity Infinity 300%"
            field-of-view="30deg"
            shadow-intensity="0.4"
            shadow-softness="0.6"
            environment-image="neutral"
            exposure="1.0"
            style="width:100%;height:100%;background:transparent;"
          />
        </div>
        <!-- Fallback background -->
        <div v-else class="w-full h-full" style="background: linear-gradient(135deg, var(--ag-primary-800), var(--ag-primary-600)); display: flex; align-items: center; justify-content: center;">
          <span style="font-family: var(--ag-font-display); font-size: 120px; color: rgba(255,255,255,0.1);">{{ product.name.charAt(0).toUpperCase() }}</span>
        </div>
        <div class="absolute inset-0" style="background: linear-gradient(to bottom, rgba(252, 249, 248, 0) 0%, rgba(252, 249, 248, 0.3) 40%, rgba(252, 249, 248, 1) 100%); z-index: 1;"></div>
        <div class="absolute bottom-0 left-0 w-full z-10" style="padding: 0 var(--ag-margin-mobile, 20px) 48px;">
          <div style="max-width: var(--ag-container-max, 1280px); margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">
            <div style="flex: 1;">
              <div style="display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap;">
                <span class="tag tag-secondary">Loại quý hiếm</span>
                <span class="tag tag-primary">Nguồn gốc bền vững</span>
              </div>
              <h1 class="title-lg">{{ product.name }}</h1>
              <p class="subtitle" v-if="product.category">{{ product.category }}</p>
            </div>
            <div class="pricing-card">
              <div class="pricing-header">
                <span class="pricing-label">Giá bán</span>
                <div class="pricing-main">
                  <span class="pricing-current">{{ formatPrice(product.price) }}<span style="font-size: 14px; text-decoration: underline;">₫</span></span>
                  <span v-if="product.compare_price" class="pricing-old">{{ formatPrice(product.compare_price) }}₫</span>
                </div>
              </div>
              <p class="pricing-note">Miễn phí giao hàng cho đơn trên 200.000₫</p>
              <div class="pricing-actions">
                <div class="qty-selector">
                  <button @click="decrementQty" class="qty-btn">−</button>
                  <span class="qty-value">{{ quantity }}</span>
                  <button @click="incrementQty" class="qty-btn">+</button>
                </div>
                <button @click="addToCart" class="add-btn">
                  <span class="material-symbols-outlined" style="font-size: 20px;">add_shopping_cart</span>
                  Thêm vào giỏ
                </button>
                <button @click="toggleWishlist"
                  class="wishlist-btn"
                  :title="product.wishlisted ? 'Bỏ yêu thích' : 'Thêm yêu thích'">
                  <span class="material-symbols-outlined"
                    :class="product.wishlisted ? 'text-[var(--ag-danger)]' : ''"
                    :style="`font-variation-settings: 'FILL' ${product.wishlisted ? 1 : 0}`">
                    favorite
                  </span>
                </button>
                <button v-if="product.seller && isAuthenticated" @click="chatWithSeller"
                  class="chat-btn"
                  title="Nhắn tin với người bán">
                  <span class="material-symbols-outlined" style="font-size: 20px;">chat</span>
                </button>
              </div>
              <button @click="openARViewer" class="ar-btn">
                <span class="material-symbols-outlined" style="font-size: 20px;">view_in_ar</span>
                Xem trong không gian của bạn
              </button>
            </div>
          </div>
        </div>
      </section>

      <section class="content-section">
        <div class="content-grid">
          <div class="content-main">
            <h2 class="section-title">Một Tác Phẩm Điêu Khắc Sống</h2>
            <div class="narrative-text" v-if="product.description" v-html="product.description"></div>
            <div class="narrative-text" v-else>
              <p>Được tôn vinh bởi những tán lá hình vĩ cầm ấn tượng, đây là tác phẩm trung tâm hoàn hảo cho không gian nội thất hiện đại. Các mẫu vật của chúng tôi được canh tác trong 18 tháng dưới ánh sáng tự nhiên cường độ cao để đảm bảo sự phát triển thân cây khỏe mạnh và sắc tố xanh ngọc bích đậm đà.</p>
              <p>Mỗi cây được chọn lọc thủ công bởi các nhà làm vườn bậc thầy của chúng tôi và được vận chuyển trong thùng Eco-Crate độc quyền, đảm bảo cây đến nơi trong tình trạng nguyên vẹn với không chất thải nhựa.</p>
            </div>

            <div class="bento-grid">
              <div class="bento-card">
                <span class="material-symbols-outlined bento-icon">light_mode</span>
                <h4 class="bento-title">Ánh sáng</h4>
                <p class="bento-desc">Ánh sáng gián tiếp, sáng. Tránh ánh nắng trực tiếp buổi chiều.</p>
              </div>
              <div class="bento-card">
                <span class="material-symbols-outlined bento-icon" style="font-variation-settings: 'FILL' 1;">water_drop</span>
                <h4 class="bento-title">Độ ẩm</h4>
                <p class="bento-desc">Tưới hàng tuần. Để khô 5cm đất mặt trước khi tưới lại.</p>
              </div>
              <div class="bento-card">
                <span class="material-symbols-outlined bento-icon">humidity_mid</span>
                <h4 class="bento-title">Độ ẩm không khí</h4>
                <p class="bento-desc">Trung bình đến cao. Phun sương lá hai lần mỗi tuần.</p>
              </div>
              <div class="bento-card">
                <span class="material-symbols-outlined bento-icon">pets</span>
                <h4 class="bento-title">An toàn thú cưng</h4>
                <p class="bento-desc">Độc nhẹ. Để xa tầm với của thú cưng.</p>
              </div>
              <div class="bento-card">
                <span class="material-symbols-outlined bento-icon">thermostat</span>
                <h4 class="bento-title">Nhiệt độ</h4>
                <p class="bento-desc">Ổn định 18-24°C. Tránh xa gió lùa.</p>
              </div>
              <div class="bento-card">
                <span class="material-symbols-outlined bento-icon">eco</span>
                <h4 class="bento-title">Kích thước chậu</h4>
                <p class="bento-desc">Đi kèm chậu đất nung bền vững 25cm.</p>
              </div>
            </div>
          </div>

          <aside class="content-sidebar sidebar-sticky">
            <div class="stats-card" v-if="product.growth_stats && Object.keys(product.growth_stats).length">
              <h3 class="sidebar-title">Chỉ số phát triển</h3>
              <div class="stats-list">
                <div v-for="(stat, key) in product.growth_stats" :key="key" class="stat-item">
                  <div class="stat-header">
                    <span class="stat-label">{{ key }}</span>
                    <span class="stat-value">{{ stat.level }}</span>
                  </div>
                  <div class="stat-bar">
                    <div class="stat-fill" :style="{ width: stat.percent + '%' }"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="stats-card" v-else>
              <h3 class="sidebar-title">Chỉ số phát triển</h3>
              <div class="stats-list">
                <div class="stat-item">
                  <div class="stat-header">
                    <span class="stat-label">Độ cứng cáp</span>
                    <span class="stat-value">Cao</span>
                  </div>
                  <div class="stat-bar"><div class="stat-fill" style="width: 85%;"></div></div>
                </div>
                <div class="stat-item">
                  <div class="stat-header">
                    <span class="stat-label">Tốc độ tăng trưởng</span>
                    <span class="stat-value">Trung bình</span>
                  </div>
                  <div class="stat-bar"><div class="stat-fill" style="width: 55%;"></div></div>
                </div>
                <div class="stat-item">
                  <div class="stat-header">
                    <span class="stat-label">Lọc không khí</span>
                    <span class="stat-value">Xuất sắc</span>
                  </div>
                  <div class="stat-bar"><div class="stat-fill" style="width: 95%;"></div></div>
                </div>
              </div>
            </div>
            <div class="concierge-card">
              <span class="material-symbols-outlined" style="font-size: 32px; color: var(--ag-secondary-500);">verified</span>
              <div>
                <h5 class="concierge-title">Tư vấn chăm sóc đi kèm</h5>
                <p class="concierge-desc">Liên hệ với chuyên gia cây trồng 24/7 qua tin nhắn trong 90 ngày đầu. Chúng tôi luôn sẵn sàng đảm bảo cây của bạn phát triển tốt.</p>
              </div>
            </div>
          </aside>
        </div>
      </section>

      <!-- Digital Passport -->
      <section v-if="product.passport_logs?.length" class="content-section">
        <div class="max-w-[1280px] mx-auto">
          <h2 class="section-title">Vòng đời sản phẩm</h2>
          <p class="text-sm text-[var(--ag-text-secondary)] mb-8">Hành trình của sản phẩm từ khi tạo đến khi đến tay bạn.</p>
          <div class="space-y-4">
            <div v-for="(log, i) in product.passport_logs" :key="log.id" class="flex gap-4">
              <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                  :class="i === 0 ? 'bg-[var(--ag-primary-500)] text-white' : 'bg-[var(--ag-bg)] text-[var(--ag-text-muted)] border border-[var(--ag-border)]'">
                  <span class="material-symbols-outlined text-sm">{{ passportIcon(log.action) }}</span>
                </div>
                <div v-if="i < product.passport_logs.length - 1" class="w-0.5 flex-1 bg-[var(--ag-border)] mt-1"></div>
              </div>
              <div class="pb-6">
                <div class="text-base font-semibold text-[var(--ag-text-primary)]">{{ passportLabel(log.action) }}</div>
                <div class="text-sm text-[var(--ag-text-muted)] mt-0.5">{{ log.created_at }}</div>
                <div v-if="log.performer" class="text-sm text-[var(--ag-text-secondary)] mt-0.5">Bởi: {{ log.performer.name }}</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="reviews-section">
        <div style="max-width: var(--ag-container-max, 1280px); margin: 0 auto; padding: 0 var(--ag-margin-desktop, 64px);">
          <div class="reviews-header">
            <div>
              <h2 class="reviews-title">Cảm nhận của người sưu tầm</h2>
              <div class="rating-row">
                <div class="stars" v-if="reviews.length">
                  <span v-for="i in 5" :key="i" class="material-symbols-outlined star-icon" :class="i <= Math.round(averageRating) ? 'star-filled' : 'star-empty'">star</span>
                </div>
                <span class="rating-text">{{ averageRating }} trung bình ({{ reviews.length }} đánh giá)</span>
              </div>
            </div>
            <button class="write-review-btn">
              <span class="material-symbols-outlined" style="font-size: 16px;">edit</span>
              Viết đánh giá
            </button>
          </div>
          <div class="reviews-grid" v-if="reviews.length">
            <div v-for="review in reviews" :key="review.id" class="review-card">
              <div class="review-header">
                <span class="review-author">{{ review.user?.name || 'Ẩn danh' }}</span>
                <span class="review-badge">Đã mua hàng</span>
              </div>
              <p class="review-quote" v-if="review.comment">"{{ review.comment }}"</p>
            </div>
          </div>
          <div class="reviews-grid" v-else>
            <div class="review-card">
              <div class="review-header">
                <span class="review-author">Nguyễn Văn A</span>
                <span class="review-badge">Đã mua hàng</span>
              </div>
              <p class="review-quote">"Chất lượng cây vượt xa mong đợi. Đã ra hai lá mới sau một tháng."</p>
            </div>
            <div class="review-card">
              <div class="review-header">
                <span class="review-author">Trần Thị B</span>
                <span class="review-badge">Đã mua hàng</span>
              </div>
              <p class="review-quote">"Bao bì thật tuyệt vời. Không một giọt đất nào bị đổ trong quá trình vận chuyển."</p>
            </div>
            <div class="review-card">
              <div class="review-header">
                <span class="review-author">Lê Văn C</span>
                <span class="review-badge">Đã mua hàng</span>
              </div>
              <p class="review-quote">"Dịch vụ thực sự đẳng cấp. Hướng dẫn chăm sóc rất chi tiết và hữu ích."</p>
            </div>
          </div>
        </div>
      </section>

      <div class="sticky-bar" :class="{ 'sticky-bar-visible': showStickyBar }">
        <div style="max-width: var(--ag-container-max, 1280px); margin: 0 auto; padding: 0 var(--ag-margin-desktop, 64px); display: flex; justify-content: space-between; align-items: center;">
          <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, var(--ag-primary-600), var(--ag-primary-400)); display: flex; align-items: center; justify-content: center;">
              <span style="font-family: var(--ag-font-display); font-size: 20px; color: rgba(255,255,255,0.3);">{{ product.name.charAt(0).toUpperCase() }}</span>
            </div>
            <div>
              <h4 class="sticky-name">{{ product.name }}</h4>
              <p class="sticky-price" v-if="product.price">{{ formatPrice(product.price) }}₫</p>
            </div>
          </div>
          <div style="display: flex; gap: 10px;">
            <button @click="openARViewer" class="sticky-ar">
              <span class="material-symbols-outlined" style="font-size: 18px;">view_in_ar</span>
            </button>
            <button @click="addToCart" class="sticky-add-cart">Thêm vào giỏ</button>
            <button @click="buyNow" class="sticky-buy">Mua ngay</button>
          </div>
        </div>
      </div>
    </main>

    <!-- AR Modal -->
    <div v-if="arModalOpen" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4" @click.self="arModalOpen = false">
      <div class="bg-white rounded-3xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl" @click.stop>
        <div class="flex items-center justify-between p-5 border-b border-stone-100">
          <h3 class="text-sm font-bold text-stone-800">Xem trong không gian của bạn</h3>
          <button @click="arModalOpen = false" class="w-8 h-8 rounded-full hover:bg-stone-100 flex items-center justify-center transition-colors">
            <span class="material-symbols-outlined text-lg">close</span>
          </button>
        </div>
        <div class="p-5">
          <ARViewer :model-url="arModelUrl" />
        </div>
      </div>
    </div>
  </MarketplaceLayout>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, defineAsyncComponent } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import MarketplaceLayout from '@agriverse/Layouts/MarketplaceLayout.vue';
import { useToast } from 'primevue/usetoast';
import { useChat } from '@agriverse/Composables/useChat';
import webApi from '@agriverse/services/webApi';
import '@google/model-viewer';

const ARViewer = defineAsyncComponent(() => import('@agriverse/Components/ARViewer.vue'));

const toast = useToast();
const page = usePage();
const { openPanel } = useChat();
const isAuthenticated = computed(() => !!page.props.auth?.user);

const props = defineProps({
  product: { type: Object, default: () => ({}) },
  relatedProducts: { type: Array, default: () => [] },
});

const quantity = ref(1);
const arModalOpen = ref(false);
const arModelUrl = computed(() => props.product?.model_3d_url || '');
const reviews = computed(() => props.product?.reviews || []);
const averageRating = computed(() => {
  const r = reviews.value;
  if (!r.length) return 0;
  return (r.reduce((s, r) => s + (r.rating || 0), 0) / r.length).toFixed(1);
});

const showStickyBar = ref(false);
let scrollHandler = null;

onMounted(() => {
  scrollHandler = () => {
    showStickyBar.value = window.scrollY > 800;
  };
  window.addEventListener('scroll', scrollHandler, { passive: true });
});

onBeforeUnmount(() => {
  if (scrollHandler) window.removeEventListener('scroll', scrollHandler);
});

function formatPrice(price) {
  return new Intl.NumberFormat('vi-VN').format(price || 0);
}

function incrementQty() { quantity.value = Math.min(quantity.value + 1, props.product.stock || 99); }
function decrementQty() { quantity.value = Math.max(1, quantity.value - 1); }

async function addToCart() {
  try {
    await webApi.post('/agriverse/api/cart/add', { product_id: props.product.id, quantity: quantity.value });
    toast.add({ severity: 'success', summary: 'Đã thêm vào giỏ hàng', life: 2000 });
  } catch {
    toast.add({ severity: 'error', summary: 'Vui lòng đăng nhập', life: 2000 });
  }
}

async function buyNow() {
  try {
    await webApi.post('/agriverse/api/cart/add', { product_id: props.product.id, quantity: quantity.value });
    window.location.href = '/agriverse/thanh-toan';
  } catch {
    toast.add({ severity: 'error', summary: 'Vui lòng đăng nhập', life: 2000 });
  }
}

function passportIcon(action) {
  const icons = {
    product_submitted: 'upload_file',
    product_approved: 'verified',
    ordered: 'shopping_cart',
    order_confirmed: 'check_circle',
    order_shipped: 'local_shipping',
    order_delivered: 'inventory_2',
    order_completed: 'handshake',
    order_cancelled: 'cancel',
  };
  return icons[action] || 'circle';
}

function passportLabel(action) {
  const labels = {
    product_submitted: 'Sản phẩm được tạo',
    product_approved: 'Sản phẩm được duyệt',
    ordered: 'Đơn hàng được đặt',
    order_confirmed: 'Đơn hàng đã được xác nhận',
    order_shipped: 'Đơn hàng đã được giao cho vận chuyển',
    order_delivered: 'Đơn hàng đã được giao thành công',
    order_completed: 'Người mua đã xác nhận nhận hàng',
    order_cancelled: 'Đơn hàng đã bị hủy',
  };
  return labels[action] || action;
}

async function toggleWishlist() {
  const was = props.product.wishlisted;
  props.product.wishlisted = !was;
  try {
    const { data } = await webApi.post(`/agriverse/api/wishlist/${props.product.id}/toggle`);
    props.product.wishlisted = data.wishlisted;
  } catch {
    props.product.wishlisted = was;
    toast.add({ severity: 'error', summary: 'Vui lòng đăng nhập', life: 2000 });
  }
}

async function chatWithSeller() {
  try {
    const { data } = await webApi.post('/agriverse/api/chat/start', { product_id: props.product.id })
    if (data.conversation) {
      openPanel(data.conversation.id, data.conversation.product)
    }
  } catch (e) {
    if (e.response?.status === 422) {
      toast.add({ severity: 'warn', summary: e.response.data.error || 'Không thể nhắn tin', life: 3000 })
    } else {
      toast.add({ severity: 'error', summary: 'Vui lòng đăng nhập', life: 2000 })
    }
  }
}

function openARViewer() {
  if (props.product?.id) {
    router.visit(route('agriverse.shop.ar-viewer', props.product.id));
  } else {
    arModalOpen.value = true;
  }
}

function closeARViewer() {
  arModalOpen.value = false;
}
</script>

<style scoped>
.product-hero-section {
  height: 716px;
}
@media (max-width: 640px) {
  .product-hero-section { height: 520px; }
}
.tag {
  padding: 4px 12px;
  border-radius: 9999px;
  font-family: var(--ag-font-body);
  font-size: 12px;
  font-weight: 600;
}
.tag-secondary {
  background: rgba(139, 79, 39, 0.08);
  color: var(--ag-secondary-500);
}
.tag-primary {
  background: rgba(72, 103, 48, 0.08);
  color: var(--ag-primary-500);
}
.title-lg {
  font-family: var(--ag-font-display);
  font-size: clamp(2.25rem, 1.7rem + 1.4vw, 3rem);
  font-weight: 500;
  line-height: 1.15;
  letter-spacing: -0.02em;
  color: var(--ag-text-primary);
  margin-bottom: 8px;
}
.subtitle {
  font-family: var(--ag-font-display);
  font-size: 24px;
  font-weight: 500;
  color: var(--ag-primary-500);
  opacity: 0.8;
  font-style: italic;
}
.pricing-card {
  background: rgba(255,255,255,0.88);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  padding: 24px;
  border-radius: 16px;
  border: 1px solid rgba(255,255,255,0.6);
  box-shadow: 0 8px 32px -4px rgba(0,0,0,0.08);
  width: 100%;
}
@media (max-width: 640px) {
  .pricing-card { padding: 16px; }
  .pricing-current { font-size: 26px; }
  .pricing-actions { gap: 8px; }
  .add-btn { height: 36px; font-size: 13px; }
  .qty-btn { width: 36px; height: 36px; }
  .wishlist-btn, .chat-btn { width: 40px; height: 40px; }
  .ar-btn { height: 36px; font-size: 12px; }
}
@media (min-width: 768px) {
  .pricing-card { width: 320px; padding: 24px; }
  section.product-hero-section { height: 870px !important; }
  section.relative.w-full.overflow-hidden > div:first-child > span { font-size: 160px; }
  div.absolute.bottom-0.left-0.w-full { padding: 0 var(--ag-margin-desktop, 64px) 48px; }
  div.absolute.bottom-0.left-0.w-full > div { flex-direction: row; justify-content: space-between; align-items: flex-end; }
}
.pricing-header { margin-bottom: 16px; }
.pricing-label {
  font-family: var(--ag-font-body);
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--ag-text-secondary);
  display: block;
  margin-bottom: 8px;
}
.pricing-main { display: flex; align-items: baseline; gap: 12px; }
.pricing-current {
  font-family: var(--ag-font-display);
  font-size: 32px;
  font-weight: 500;
  line-height: 40px;
  color: var(--ag-primary-500);
}
.pricing-old {
  font-family: var(--ag-font-body);
  font-size: 14px;
  color: var(--ag-text-secondary);
  text-decoration: line-through;
}
.pricing-note {
  font-family: var(--ag-font-body);
  font-size: 12px;
  color: var(--ag-text-secondary);
  margin-bottom: 24px;
}
.pricing-actions { display: flex; gap: 12px; }
.qty-selector {
  display: flex;
  align-items: center;
  border: 1px solid var(--ag-border);
  border-radius: 8px;
  overflow: hidden;
  background: white;
}
.qty-btn {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  background: transparent;
  font-size: 18px;
  font-weight: 600;
  color: var(--ag-text-secondary);
  cursor: pointer;
  transition: all 0.2s;
}
.qty-btn:hover { background: color-mix(in srgb, var(--ag-primary-500) 8%, transparent); color: var(--ag-primary-500); }
.qty-value {
  width: 48px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  color: var(--ag-text-primary);
  border-left: 1px solid var(--ag-border);
  border-right: 1px solid var(--ag-border);
}
.add-btn {
  flex: 1;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  border-radius: 8px;
  border: none;
  background: var(--ag-primary-500);
  color: white;
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}
.add-btn:hover { background: var(--ag-primary-600); }
.add-btn:active { transform: scale(0.97); }
.wishlist-btn {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid color-mix(in srgb, var(--ag-border) 15%, transparent);
  background: white;
  color: var(--ag-text-secondary);
  cursor: pointer;
  transition: all 0.3s;
  flex-shrink: 0;
}
.wishlist-btn:hover {
  border-color: var(--ag-danger);
  color: var(--ag-danger);
  box-shadow: 0 2px 8px rgba(220, 38, 38, 0.08);
}
.wishlist-btn:active { transform: scale(0.9); }
.chat-btn {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid color-mix(in srgb, var(--ag-border) 15%, transparent);
  background: white;
  color: var(--ag-primary-500);
  cursor: pointer;
  transition: all 0.3s;
  flex-shrink: 0;
}
.chat-btn:hover {
  border-color: var(--ag-primary-500);
  background: color-mix(in srgb, var(--ag-primary-500) 8%, transparent);
  box-shadow: 0 2px 8px rgba(72, 103, 48, 0.08);
}
.chat-btn:active { transform: scale(0.9); }
.ar-btn {
  margin-top: 10px;
  width: 100%;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  border-radius: 8px;
  border: 1px solid rgba(72, 103, 48, 0.2);
  background: white;
  color: var(--ag-primary-500);
  font-family: var(--ag-font-body);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  padding: 0 16px;
  white-space: nowrap;
  flex-shrink: 0;
}
.ar-btn:hover {
  background: color-mix(in srgb, var(--ag-primary-500) 6%, transparent);
  border-color: var(--ag-primary-500);
  box-shadow: 0 2px 8px color-mix(in srgb, var(--ag-primary-500) 12%, transparent);
}
.ar-btn:active { transform: scale(0.97); }

.content-section {
  max-width: var(--ag-container-max, 1280px);
  margin: 0 auto;
  padding: 80px var(--ag-margin-desktop, 64px);
}
@media (max-width: 768px) {
  .content-section { padding: 48px var(--ag-margin-mobile, 20px); }
}
.content-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 64px;
}
@media (min-width: 1024px) {
  .content-grid { grid-template-columns: 7fr 5fr; gap: 64px; }
}
.content-main { min-width: 0; }
.section-title {
  font-family: var(--ag-font-display);
  font-size: 32px;
  font-weight: 500;
  line-height: 40px;
  color: var(--ag-text-primary);
  margin-bottom: 24px;
}
.narrative-text {
  font-family: var(--ag-font-body);
  font-size: 18px;
  line-height: 28px;
  color: var(--ag-text-secondary);
}
.narrative-text p { margin-bottom: 24px; }
.bento-grid {
  margin-top: 64px;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 24px;
}
@media (min-width: 768px) {
  .bento-grid { grid-template-columns: repeat(3, 1fr); }
}
.bento-card {
  background: var(--ag-surface-container-low);
  padding: 32px;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  transition: all 0.3s;
  border: 1px solid transparent;
}
.bento-card:hover {
  background: white;
  border-color: color-mix(in srgb, var(--ag-border) 30%, transparent);
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.bento-icon {
  font-size: 36px;
  color: var(--ag-primary-500);
  margin-bottom: 16px;
}
.bento-title {
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  letter-spacing: 0.05em;
  color: var(--ag-text-primary);
  margin-bottom: 8px;
}
.bento-desc {
  font-family: var(--ag-font-body);
  font-size: 12px;
  line-height: 16px;
  color: var(--ag-text-secondary);
}

.sidebar-sticky {
  position: sticky;
  top: 100px;
  align-self: start;
}
.stats-card {
  background: white;
  padding: 40px;
  border-radius: 16px;
  border: 1px solid var(--ag-surface-container-high);
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
  margin-bottom: 48px;
}
.sidebar-title {
  font-family: var(--ag-font-display);
  font-size: 24px;
  font-weight: 500;
  line-height: 32px;
  color: var(--ag-text-primary);
  margin-bottom: 24px;
}
.stats-list { display: flex; flex-direction: column; gap: 24px; }
.stat-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
}
.stat-label {
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  color: var(--ag-text-primary);
}
.stat-value {
  font-family: var(--ag-font-body);
  font-size: 12px;
  color: var(--ag-primary-500);
}
.stat-bar {
  height: 6px;
  width: 100%;
  background: var(--ag-surface-container-high);
  border-radius: 9999px;
  overflow: hidden;
}
.stat-fill {
  height: 100%;
  background: var(--ag-primary-500);
  border-radius: 9999px;
  transition: width 1s ease;
}
.concierge-card {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  padding: 24px;
  background: rgba(139, 79, 39, 0.04);
  border-radius: 12px;
  border: 1px solid rgba(139, 79, 39, 0.1);
}
.concierge-title {
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  letter-spacing: 0.05em;
  color: var(--ag-secondary-500);
  margin-bottom: 4px;
}
.concierge-desc {
  font-family: var(--ag-font-body);
  font-size: 12px;
  line-height: 16px;
  color: var(--ag-text-secondary);
}

.reviews-section {
  background: var(--ag-surface-container-low);
  padding: 120px 0;
}
.reviews-header {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 64px;
  gap: 24px;
}
@media (min-width: 768px) {
  .reviews-header { flex-direction: row; }
}
.reviews-title {
  font-family: var(--ag-font-display);
  font-size: clamp(2.25rem, 1.7rem + 1.4vw, 2rem);
  font-weight: 500;
  line-height: 1.2;
  color: var(--ag-text-primary);
  margin-bottom: 8px;
}
.rating-row { display: flex; align-items: center; gap: 8px; }
.stars { display: flex; gap: 2px; }
.star-icon { font-size: 20px; }
.star-filled { color: var(--ag-primary-500); font-variation-settings: 'FILL' 1; }
.star-empty { color: var(--ag-text-muted); }
.rating-text {
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  color: var(--ag-text-secondary);
}
.write-review-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 32px;
  border: 1px solid var(--ag-text-muted);
  border-radius: 10px;
  background: transparent;
  color: var(--ag-text-secondary);
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}
.write-review-btn:hover { background: rgba(72, 103, 48, 0.04); color: var(--ag-primary-500); border-color: var(--ag-primary-500); }
.reviews-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 24px;
}
@media (min-width: 768px) {
  .reviews-grid { grid-template-columns: repeat(3, 1fr); }
}
.review-card {
  background: white;
  padding: 32px;
  border-radius: 16px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.03);
  border: 1px solid rgba(116, 121, 108, 0.06);
}
.review-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}
.review-author {
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  color: var(--ag-text-primary);
}
.review-badge {
  font-family: var(--ag-font-body);
  font-size: 12px;
  color: var(--ag-text-secondary);
}
.review-quote {
  font-family: var(--ag-font-display);
  font-size: 20px;
  font-weight: 500;
  font-style: italic;
  color: var(--ag-primary-500);
  line-height: 28px;
}

.sticky-bar {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  background: rgba(255,255,255,0.8);
  backdrop-filter: blur(16px);
  padding: 16px 0;
  border-top: 1px solid color-mix(in srgb, var(--ag-border) 30%, transparent);
  transform: translateY(100%);
  transition: transform 0.3s ease;
  z-index: 40;
}
.sticky-bar-visible { transform: translateY(0); }
.sticky-name {
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  color: var(--ag-text-primary);
}
.sticky-price {
  font-family: var(--ag-font-body);
  font-size: 12px;
  color: var(--ag-primary-500);
}
.sticky-add-cart {
  padding: 12px 32px;
  border: 1px solid var(--ag-primary-500);
  color: var(--ag-primary-500);
  background: transparent;
  border-radius: 10px;
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}
.sticky-add-cart:hover { background: rgba(72, 103, 48, 0.04); }
.sticky-buy {
  padding: 12px 48px;
  background: var(--ag-primary-500);
  color: white;
  border: none;
  border-radius: 10px;
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}
.sticky-buy:hover { background: var(--ag-primary-600); }
.sticky-buy:active { transform: scale(0.98); }
.sticky-ar {
  width: 44px;
  height: 44px;
  border: 1px solid var(--ag-border);
  background: white;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  color: var(--ag-primary-500);
}
.sticky-ar:hover {
  background: color-mix(in srgb, var(--ag-primary-500) 6%, transparent);
  border-color: var(--ag-primary-500);
}

@media (max-width: 768px) {
  .sticky-bar { display: none; }
}
</style>
