<template>
  <MarketplaceLayout>
    <!-- Hero Section -->
    <header class="hero-section">
      <div class="hero-bg">
        <div class="hero-img">
          <span class="hero-img-placeholder">{{ heroEmoji }}</span>
        </div>
        <div class="hero-gradient-overlay"></div>
        <div class="hero-scanline"></div>
      </div>

      <div class="hero-data-panel">
        <div class="hero-data-card" :class="{ 'hero-data-pulse': airQuality && airQuality.aqi > 0 }">
          <p class="hero-data-label">Chất lượng không khí</p>
          <template v-if="airQuality">
            <p class="hero-data-value" :style="{ color: airQuality.color }">
              AQI: {{ airQuality.aqi }}
            </p>
            <p class="hero-data-sub">{{ airLevelLabel }}</p>
            <p v-if="airQuality.humidity" class="hero-data-sub">Độ ẩm: {{ airQuality.humidity }}%</p>
            <p v-if="airQuality.temperature" class="hero-data-sub">{{ airQuality.temperature }}°C</p>
          </template>
          <template v-else-if="geoError">
            <p class="hero-data-value" style="color: var(--ag-text-muted);">Không xác định</p>
          </template>
          <template v-else>
            <p class="hero-data-value" style="color: var(--ag-text-muted);">Đang tải...</p>
          </template>
        </div>
      </div>

      <div class="hero-content">
        <div class="hero-content-inner">
          <div class="hero-badge-row">
            <span class="hero-badge-line"></span>
            <span class="hero-badge-text">Bộ sưu tập đặc biệt: Bonsai Việt</span>
          </div>
          <h1 class="hero-title">
            Tinh hoa <span class="hero-title-accent">Bonsai Việt</span>.
          </h1>
          <p class="hero-description">
            Cây cảnh bonsai tinh tuyển, được chăm sóc theo tiêu chuẩn nghệ nhân, kiểm định chất lượng nghiêm ngặt.
            Nâng tầm không gian sống của bạn với những tác phẩm nghệ thuật từ thiên nhiên.
          </p>
          <div class="hero-actions">
            <Link :href="route('agriverse.shop.products.index')" class="hero-btn-primary">
              Khám phá bộ sưu tập bonsai
              <span class="material-symbols-outlined text-sm">sensors</span>
            </Link>
            <Link :href="route('agriverse.shop.stores.index')" class="hero-btn-secondary">
              Gian hàng
            </Link>
          </div>
        </div>
      </div>

      <div class="hero-scroll-indicator">
        <span class="hero-scroll-text">Cuộn</span>
        <div class="hero-scroll-line">
          <div class="hero-scroll-dot"></div>
        </div>
      </div>
    </header>

    <!-- Stats Bar -->
    <div class="stats-bar">
      <div class="stats-grid">
        <div class="stat-item">
          <div class="stat-value">{{ categories?.length || 0 }}</div>
          <div class="stat-label">Danh mục</div>
        </div>
        <div class="stat-item">
          <div class="stat-value">{{ featuredProducts?.length || 0 }}</div>
          <div class="stat-label">Sản phẩm</div>
        </div>
        <div class="stat-item">
          <div class="stat-value">{{ stores?.length || 0 }}</div>
          <div class="stat-label">Gian hàng</div>
        </div>
      </div>
    </div>

    <!-- Categories -->
    <section v-if="categories?.length" class="section-categories">
      <div class="section-container">
        <div class="section-header">
          <div>
            <h2 class="section-title">Danh mục cây cảnh</h2>
            <p class="section-subtitle">Khám phá theo sở thích của bạn</p>
          </div>
          <Link :href="route('agriverse.shop.products.index')" class="section-link">
            Xem tất cả <span class="material-symbols-outlined text-sm">arrow_forward</span>
          </Link>
        </div>
        <div class="categories-grid">
          <Link v-for="cat in categories" :key="cat.id"
            :href="route('agriverse.shop.products.index', { category: cat.slug })"
            class="category-card">
            <div class="category-icon">
              <span class="material-symbols-outlined category-icon-symbol">{{ categoryIcon(cat.name) }}</span>
            </div>
            <h3 class="category-name">{{ cat.name }}</h3>
            <p class="category-count">{{ cat.products_count ?? 0 }} sản phẩm</p>
          </Link>
        </div>
      </div>
    </section>

    <!-- Featured Products (Bento Grid) -->
    <section v-if="featuredProducts?.length" class="section-featured">
      <div class="section-container">
        <div class="section-header">
          <div>
            <h2 class="section-title">Cây cảnh nổi bật</h2>
            <p class="section-subtitle">Tuyển chọn từ các vườn ươm uy tín trên toàn quốc</p>
          </div>
          <Link :href="route('agriverse.shop.products.index')" class="section-link">
            Xem tất cả <span class="material-symbols-outlined text-sm">arrow_forward</span>
          </Link>
        </div>

        <div class="featured-grid">
          <div v-for="product in featuredProducts.slice(0, 4)" :key="product.id" class="product-card">
            <Link :href="route('agriverse.shop.products.show', product.id)" class="product-card-link">
              <div class="product-card-img">
                <img v-if="product.image" :src="product.image" :alt="product.name" class="w-full h-full object-cover" />
                <div v-else class="product-card-placeholder">
                  <span>{{ product.name.charAt(0).toUpperCase() }}</span>
                </div>
                <div class="product-card-badges">
                  <span v-if="product.compare_price && product.compare_price > product.price" class="product-badge-discount">
                    -{{ Math.round((1 - product.price / product.compare_price) * 100) }}%
                  </span>
                </div>
              </div>
              <div class="product-card-body">
                <div class="product-card-top">
                  <h3 class="product-card-name">{{ product.name }}</h3>
                  <span class="product-card-price">{{ formatPrice(product.price) }}₫</span>
                </div>
                <div class="product-card-meta">
                  <div class="product-card-meta-item">
                    <span class="material-symbols-outlined">height</span>
                    <span>30-50 cm</span>
                  </div>
                  <div class="product-card-meta-item">
                    <span class="material-symbols-outlined">schedule</span>
                    <span>3-5 năm tuổi</span>
                  </div>
                </div>
              </div>
            </Link>
          </div>
        </div>
      </div>
    </section>

    <!-- Commitment Section -->
    <section class="section-commitment">
      <div class="section-container">
        <div class="commitment-grid">
          <div class="commitment-media">
            <div class="commitment-blur-bg"></div>
            <div class="commitment-frame">
              <div class="commitment-frame-bg">
                <span class="text-8xl text-[var(--ag-primary-300)]/30">{{ heroEmoji }}</span>
              </div>
            </div>
            <div class="commitment-stats-card">
              <div class="commitment-stats-inner">
                <span class="material-symbols-outlined text-primary text-2xl">verified</span>
                <span class="commitment-stats-number">100%</span>
              </div>
                <p class="commitment-stats-text">Cây cảnh bonsai thuần Việt, chăm sóc bởi nghệ nhân làng nghề.</p>
            </div>
          </div>
          <div class="commitment-content">
            <span class="commitment-badge">Cam kết của chúng tôi</span>
            <h2 class="commitment-title">Nâng tầm không gian sống<br/>với bonsai Việt Nam.</h2>
            <p class="commitment-desc">
              Chúng tôi kết hợp tinh hoa nghệ thuật bonsai truyền thống với quy trình chăm sóc hiện đại
              để mang đến những tác phẩm cây cảnh bonsai đẹp nhất cho không gian của bạn.
            </p>
            <div class="commitment-features">
              <div class="commitment-feature">
                <div class="commitment-feature-icon">
                  <span class="material-symbols-outlined">eco</span>
                </div>
                <div>
                  <h4 class="commitment-feature-title">Nghệ nhân tạo tác</h4>
                  <p class="commitment-feature-desc">Mỗi cây bonsai đều được tạo tác bởi nghệ nhân lành nghề với tâm huyết và kinh nghiệm.</p>
                </div>
              </div>
              <div class="commitment-feature">
                <div class="commitment-feature-icon">
                  <span class="material-symbols-outlined">analytics</span>
                </div>
                <div>
                  <h4 class="commitment-feature-title">Kiểm định sức khỏe</h4>
                  <p class="commitment-feature-desc">Mỗi cây cảnh đều được kiểm tra sức khỏe trước khi đến tay người yêu cây.</p>
                </div>
              </div>
              <div class="commitment-feature">
                <div class="commitment-feature-icon">
                  <span class="material-symbols-outlined">box_edit</span>
                </div>
                <div>
                  <h4 class="commitment-feature-title">Đóng gói chuyên nghiệp</h4>
                  <p class="commitment-feature-desc">Bao bì chuyên dụng cho cây cảnh, đảm bảo cây luôn xanh tốt khi đến tay bạn.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="section-cta">
      <div class="cta-card">
        <div class="cta-card-inner">
          <div class="cta-bg-icon">
            <span class="material-symbols-outlined text-[240px]">biotech</span>
          </div>
          <div class="cta-content">
            <h2 class="cta-title">Chuyên gia tư vấn.<br/>Kiến thức khoa học.</h2>
            <p class="cta-desc">Kết nối với nghệ nhân bonsai của chúng tôi để được tư vấn về cách chọn và chăm sóc cây cảnh phù hợp nhất với không gian của bạn.</p>
            <Link :href="route('agriverse.shop.stores.index')" class="cta-btn">
              Nói chuyện với chuyên gia
              <span class="material-symbols-outlined">support_agent</span>
            </Link>
          </div>
        </div>
      </div>
    </section>
  </MarketplaceLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import MarketplaceLayout from '@agriverse/Layouts/MarketplaceLayout.vue';
import { useToast } from 'primevue/usetoast';
import axios from 'axios';

const toast = useToast();

const props = defineProps({
  categories: Array,
  featuredProducts: Array,
  stores: Array,
});

const airQuality = ref(null);
const geoError = ref(false);

const airLevelLabel = computed(() => {
  if (!airQuality.value) return '';
  const map = {
    good: 'Tốt',
    moderate: 'Trung bình',
    unhealthy_sensitive: 'Kém (Nhạy cảm)',
    unhealthy: 'Kém',
    very_unhealthy: 'Rất kém',
    hazardous: 'Nguy hiểm',
  };
  return map[airQuality.value.level] || '';
});

const heroEmoji = computed(() => {
  const emojis = ['🌲', '🎍', '🌿', '🌱', '🍀', '🪴'];
  return emojis[Math.floor(Math.random() * emojis.length)];
});

onMounted(async () => {
  // Immediately fetch Hanoi data so user sees something right away
  await fetchAirQuality(21.0285, 105.8542);

  // Then try to get precise location for better data
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      async (pos) => {
        await fetchAirQuality(pos.coords.latitude, pos.coords.longitude);
      },
      () => {
        // Silently keep Hanoi data — no error needed
      },
      { timeout: 8000, enableHighAccuracy: false }
    );
  }
});

async function fetchAirQuality(lat, lng) {
  try {
    const { data } = await axios.get('/agriverse/khong-khi', {
      params: { lat, lng },
    });
    if (data && data.aqi) {
      airQuality.value = data;
    } else {
      geoError.value = true;
    }
  } catch {
    geoError.value = true;
  }
}

const categoryIconMap = {
  'bonsai': 'forest',
  'cây phong thủy': 'yard',
  'sen đá': 'spa',
  'cây thủy sinh': 'water',
  'cây ăn quả': 'nutrition',
  'phụ kiện': 'handyman',
  'chậu cảnh': 'pottery',
  'xương rồng': 'local_florist',
  'cây lá màu': 'palette',
  'lan': 'local_florist',
  'cây nội thất': 'cottage',
  'cây leo': 'trending_up',
  'hạt giống': 'seed',
}

function categoryIcon(name) {
  const key = (name || '').toLowerCase()
  for (const [k, icon] of Object.entries(categoryIconMap)) {
    if (key.includes(k)) return icon
  }
  return 'eco'
}

function formatPrice(price) {
  return new Intl.NumberFormat('vi-VN').format(price || 0);
}

function addToCart(productId) {
  router.post(route('agriverse.api.cart.add'), { product_id: productId, quantity: 1 }, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => toast.add({ severity: 'success', summary: 'Đã thêm vào giỏ hàng', life: 2000 }),
    onError: () => toast.add({ severity: 'error', summary: 'Vui lòng đăng nhập', life: 2000 }),
  });
}
</script>

<style scoped>
/* ============================================================
   HOME PAGE — Botanical Heritage Design System
   Reference: homepage_lumina_botanicals_updated
   ============================================================ */

/* ===== Hero ===== */
.hero-section {
  position: relative;
  width: 100%;
  height: 100vh;
  height: 100dvh;
  min-height: 600px;
  display: flex;
  align-items: center;
  overflow: hidden;
}
@media (max-width: 640px) {
  .hero-section { min-height: 560px; }
}
.hero-bg {
  position: absolute;
  inset: 0;
  z-index: 0;
}
.hero-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.hero-img-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  font-size: 120px;
  opacity: 0.15;
  background: linear-gradient(135deg, var(--ag-primary-50), var(--ag-surface), var(--ag-primary-100));
  animation: subtle-zoom 20s ease-in-out infinite alternate;
}
.hero-gradient-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to bottom, rgba(252, 249, 248, 0.4) 0%, rgba(252, 249, 248, 0.95) 40%, rgba(252, 249, 248, 1) 100%);
}
.hero-scanline {
  position: absolute;
  inset: 0;
  background: linear-gradient(to bottom, transparent 50%, rgba(135, 169, 107, 0.05) 50%);
  background-size: 100% 4px;
  opacity: 0.2;
  pointer-events: none;
}

.hero-data-panel {
  position: absolute;
  top: 96px;
  right: 16px;
  z-index: 20;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
@media (min-width: 1024px) {
  .hero-data-panel { top: 128px; right: 64px; gap: 16px; }
}
@media (max-width: 640px) {
  .hero-data-panel { top: 20px; right: 12px; }
  .hero-data-card { padding: 10px; }
  .hero-data-label { font-size: 9px; }
  .hero-data-value { font-size: 12px; }
  .hero-data-sub { font-size: 11px; }
  .text-2xl { font-size: 18px; }
}
.hero-data-card {
  background: rgba(255,255,255,0.6);
  backdrop-filter: blur(12px);
  border: 1px solid color-mix(in srgb, var(--ag-outline) 20%, transparent);
  padding: 16px;
  border-radius: 12px;
  color: var(--ag-text-primary);
  font-family: var(--ag-font-body);
  box-shadow: var(--ag-shadow-sm);
}
.hero-data-pulse {
  animation: pulse 2s ease-in-out infinite;
}
.hero-data-label {
  font-size: 11px;
  opacity: 0.6;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  margin-bottom: 4px;
}
.hero-data-value {
  font-size: 14px;
  font-weight: 600;
}
.hero-data-sub {
  font-size: 14px;
}
.text-2xl {
  font-size: 24px;
  font-family: var(--ag-font-display);
}

.hero-content {
  position: relative;
  z-index: 10;
  width: 100%;
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 64px;
}
@media (max-width: 768px) {
  .hero-content { padding: 0 24px; }
}
@media (max-width: 640px) {
  .hero-content-inner { max-width: 100%; }
  .hero-badge-row { margin-bottom: 16px; }
  .hero-badge-line { width: 24px; }
  .hero-badge-text { font-size: 11px; letter-spacing: 0.12em; }
  .hero-description { font-size: 16px; line-height: 24px; margin-bottom: 28px; }
  .hero-btn-primary, .hero-btn-secondary { padding: 14px 24px; font-size: 13px; }
}
.hero-content-inner {
  max-width: 600px;
}
.hero-badge-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
}
.hero-badge-line {
  width: 32px;
  height: 1px;
  background: var(--ag-primary-500);
}
.hero-badge-text {
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: var(--ag-primary-500);
}
.hero-title {
  font-family: var(--ag-font-display);
  font-size: 48px;
  font-weight: 500;
  line-height: 56px;
  letter-spacing: -0.02em;
  color: var(--ag-text-primary);
  margin-bottom: 24px;
}
@media (max-width: 768px) {
  .hero-title {
    font-size: 36px;
    line-height: 42px;
    letter-spacing: -0.01em;
  }
}
.hero-title-accent {
  color: var(--ag-primary-500);
  font-style: italic;
}
.hero-description {
  font-family: var(--ag-font-body);
  font-size: 18px;
  line-height: 28px;
  color: var(--ag-text-secondary);
  margin-bottom: 40px;
  max-width: 560px;
}
.hero-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
}
.hero-btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 16px 32px;
  background: var(--ag-primary-500);
  color: white;
  border-radius: 12px;
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 14px -2px color-mix(in srgb, var(--ag-primary-500) 30%, transparent);
}
.hero-btn-primary:hover {
  background: var(--ag-primary-600);
  box-shadow: 0 8px 24px -4px color-mix(in srgb, var(--ag-primary-500) 40%, transparent);
}
.hero-btn-primary:active { transform: scale(0.95); }
.hero-btn-secondary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 16px 32px;
  border: 1px solid rgba(116, 121, 108, 0.3);
  color: var(--ag-text-primary);
  border-radius: 12px;
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.hero-btn-secondary:hover {
  background: color-mix(in srgb, var(--ag-primary-500) 6%, transparent);
  border-color: var(--ag-primary-500);
  color: var(--ag-primary-500);
}
.hero-btn-secondary:active { transform: scale(0.95); }

.hero-scroll-indicator {
  position: absolute;
  bottom: 40px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  opacity: 0.5;
}
.hero-scroll-text {
  font-family: var(--ag-font-body);
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.1em;
}
.hero-scroll-line {
  width: 1px;
  height: 48px;
  background: color-mix(in srgb, var(--ag-primary-500) 30%, transparent);
  position: relative;
  overflow: hidden;
}
.hero-scroll-dot {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 50%;
  background: var(--ag-primary-500);
  animation: scroll-indicator 2s infinite;
}

/* Stats Bar */
.stats-bar {
  max-width: 1280px;
  margin: -28px auto 0;
  padding: 0 24px;
  position: relative;
  z-index: 10;
}
.stats-grid {
  background: white;
  border-radius: 12px;
  border: 1px solid var(--ag-border);
  padding: 20px 32px;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
}
@media (max-width: 640px) {
  .stats-grid { padding: 16px 12px; }
  .stat-item { padding: 0 8px; }
  .stat-value { font-size: 20px; }
  .stat-label { font-size: 10px; }
}
.stat-item {
  text-align: center;
  padding: 0 24px;
  border-right: 1px solid color-mix(in srgb, var(--ag-border) 60%, transparent);
}
.stat-item:last-child { border-right: none; }
.stat-value {
  font-family: var(--ag-font-display);
  font-size: 28px;
  font-weight: 500;
  color: var(--ag-text-primary);
}
.stat-label {
  font-family: var(--ag-font-body);
  font-size: 11px;
  font-weight: 600;
  color: var(--ag-text-secondary);
  margin-top: 2px;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

/* Section Common */
.section-container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 64px;
}
@media (max-width: 768px) {
  .section-container { padding: 0 24px; }
}
.section-header {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 40px;
}
@media (min-width: 768px) {
  .section-header {
    flex-direction: row;
    align-items: flex-end;
  }
}
.section-title {
  font-family: var(--ag-font-display);
  font-size: 32px;
  font-weight: 500;
  line-height: 40px;
  color: var(--ag-text-primary);
  margin-bottom: 8px;
}
@media (max-width: 640px) {
  .section-title { font-size: 24px; line-height: 30px; margin-bottom: 4px; }
  .section-subtitle { font-size: 13px; line-height: 18px; }
}
.section-subtitle {
  font-family: var(--ag-font-body);
  font-size: 16px;
  line-height: 24px;
  color: var(--ag-text-secondary);
}
.section-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  color: var(--ag-primary-500);
  text-decoration: none;
  transition: all 0.3s ease;
  margin-top: 12px;
}
@media (min-width: 768px) {
  .section-link { margin-top: 0; }
}
.section-link:hover { gap: 16px; }

/* Categories */
.section-categories {
  padding: 96px 0;
}
@media (max-width: 640px) {
  .section-categories { padding: 56px 0; }
}
.categories-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}
@media (max-width: 768px) {
  .categories-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
}
.category-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 24px 16px;
  border-radius: 16px;
  background: white;
  border: 1px solid rgba(116, 121, 108, 0.08);
  text-decoration: none;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
  min-height: 44px;
}
@media (max-width: 640px) {
  .category-card { padding: 16px 12px; gap: 8px; }
  .category-icon { width: 44px; height: 44px; }
  .category-icon-symbol { font-size: 20px; }
  .category-name { font-size: 12px; }
}
.category-card:hover {
  border-color: color-mix(in srgb, var(--ag-primary-500) 25%, transparent);
  box-shadow: 0 10px 30px -8px rgba(44, 44, 44, 0.05);
  transform: translateY(-2px);
}
.category-icon {
  width: 56px;
  height: 56px;
  border-radius: 14px;
  background: color-mix(in srgb, var(--ag-primary-500) 8%, transparent);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.category-card:hover .category-icon {
  background: var(--ag-primary-500);
  transform: scale(1.1);
}
.category-icon-symbol {
  font-size: 24px;
  color: var(--ag-primary-500);
  transition: color 0.3s;
}
.category-card:hover .category-icon-symbol { color: white; }
.category-name {
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  color: var(--ag-text-primary);
  text-align: center;
  transition: color 0.3s;
}
.category-card:hover .category-name { color: var(--ag-primary-500); }
.category-count {
  font-family: var(--ag-font-body);
  font-size: 12px;
  color: var(--ag-text-secondary);
  text-align: center;
}

/* Featured Products (Bento Grid) */
.section-featured {
  padding: 96px 0;
}
@media (max-width: 640px) {
  .section-featured { padding: 56px 0; }
}
.featured-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 32px 24px;
}
@media (min-width: 640px) {
  .featured-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (min-width: 1024px) {
  .featured-grid { grid-template-columns: repeat(4, 1fr); }
}

.product-card {
  background: transparent;
  border-radius: 0;
  overflow: visible;
  transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  border: none;
  box-shadow: none;
  cursor: pointer;
}
.product-card:hover {
  transform: translateY(-4px);
}
.product-card-link {
  text-decoration: none;
  display: flex;
  flex-direction: column;
  height: 100%;
}
.product-card-img {
  position: relative;
  aspect-ratio: 4 / 5;
  border-radius: var(--ag-radius-lg);
  overflow: hidden;
  background: var(--ag-surface-container-low);
  margin-bottom: 24px;
}
@media (max-width: 640px) {
  .product-card-img { margin-bottom: 16px; }
}
.product-card-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.product-card:hover .product-card-placeholder {
  transform: scale(1.05);
}
.product-card-placeholder span {
  font-family: var(--ag-font-display);
  font-size: 56px;
  font-weight: 500;
  color: var(--ag-neutral-400);
}
.product-card-badges {
  position: absolute;
  top: 12px;
  left: 12px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.product-badge-discount {
  padding: 3px 10px;
  background: rgba(139, 79, 39, 0.12);
  color: var(--ag-secondary-500);
  border-radius: 9999px;
  font-family: var(--ag-font-body);
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.03em;
  backdrop-filter: blur(4px);
}
.product-card-body {
  padding: 0;
  display: flex;
  flex-direction: column;
  flex: 1;
}
.product-card-top {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  margin-bottom: 8px;
}
.product-card-name {
  font-family: var(--ag-font-display);
  font-size: 22px;
  font-weight: 500;
  color: var(--ag-text-primary);
  margin: 0 0 4px;
}
@media (max-width: 640px) {
  .product-card-name { font-size: 18px; }
  .product-card-price { font-size: 17px; }
}
.product-card-price {
  font-family: var(--ag-font-display);
  font-size: 20px;
  font-weight: 500;
  color: var(--ag-primary-500);
  white-space: nowrap;
}
.product-card-meta {
  display: flex;
  gap: 16px;
  border-top: 1px solid color-mix(in srgb, var(--ag-border) 50%, transparent);
  padding-top: 16px;
  margin-top: auto;
}
.product-card-meta-item {
  display: flex;
  align-items: center;
  gap: 6px;
}
.product-card-meta-item .material-symbols-outlined {
  font-size: 16px;
  color: var(--ag-primary-500);
}
.product-card-meta-item span:last-child {
  font-family: var(--ag-font-body);
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: var(--ag-text-secondary);
}

/* Commitment Section */
.section-commitment {
  padding: 128px 0;
  background: #f0f4eb;
  position: relative;
  overflow: hidden;
}
.commitment-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 64px;
  align-items: center;
}
@media (min-width: 1024px) {
  .commitment-grid {
    grid-template-columns: 1fr 1fr;
    gap: 96px;
  }
}
.commitment-media {
  position: relative;
  order: 2;
}
@media (min-width: 1024px) {
  .commitment-media { order: 1; }
}
.commitment-blur-bg {
  position: absolute;
  top: -48px;
  left: -48px;
  width: 256px;
  height: 256px;
  background: rgba(72, 103, 48, 0.08);
  border-radius: 50%;
  filter: blur(64px);
  animation: pulse 3s ease-in-out infinite;
}
.commitment-frame {
  position: relative;
  z-index: 10;
  border-radius: 32px;
  overflow: hidden;
  box-shadow: 0 24px 48px -12px rgba(44, 44, 44, 0.15);
  border: 4px solid white;
}
.commitment-frame-bg {
  aspect-ratio: 4 / 5;
  background: linear-gradient(135deg, var(--ag-primary-600), var(--ag-primary-800));
  display: flex;
  align-items: center;
  justify-content: center;
}
.commitment-stats-card {
  position: absolute;
  bottom: -32px;
  right: -32px;
  background: white;
  padding: 32px;
  border-radius: 16px;
  box-shadow: 0 10px 30px -8px rgba(44, 44, 44, 0.1);
  max-width: 280px;
  border: 1px solid rgba(72, 103, 48, 0.15);
}
@media (max-width: 640px) {
  .commitment-stats-card { right: 16px; padding: 20px; max-width: 220px; }
  .commitment-stats-number { font-size: 28px; }
  .commitment-stats-text { font-size: 12px; }
}
.commitment-stats-inner {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 8px;
}
.commitment-stats-number {
  font-family: var(--ag-font-display);
  font-size: 36px;
  color: var(--ag-primary-500);
  display: block;
  line-height: 1;
}
.commitment-stats-text {
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  line-height: 20px;
  color: var(--ag-text-secondary);
}
.commitment-content {
  order: 1;
}
@media (min-width: 1024px) {
  .commitment-content { order: 2; }
}
.commitment-badge {
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  letter-spacing: 0.3em;
  text-transform: uppercase;
  color: var(--ag-primary-500);
  margin-bottom: 24px;
  display: block;
}
.commitment-title {
  font-family: var(--ag-font-display);
  font-size: 32px;
  font-weight: 500;
  line-height: 40px;
  color: var(--ag-text-primary);
  margin-bottom: 32px;
}
.commitment-desc {
  font-family: var(--ag-font-body);
  font-size: 18px;
  line-height: 28px;
  color: var(--ag-text-secondary);
  margin-bottom: 48px;
}
.commitment-features {
  display: flex;
  flex-direction: column;
  gap: 24px;
}
.commitment-feature {
  display: flex;
  gap: 32px;
  align-items: flex-start;
}
.commitment-feature-icon {
  flex-shrink: 0;
  width: 56px;
  height: 56px;
  border-radius: 12px;
  background: white;
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
  border: 1px solid rgba(72, 103, 48, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--ag-primary-500);
  transition: all 0.3s;
}
.commitment-feature:hover .commitment-feature-icon {
  background: var(--ag-primary-500);
  color: white;
}
.commitment-feature-title {
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0.03em;
  text-transform: uppercase;
  color: var(--ag-text-primary);
  margin-bottom: 4px;
}
.commitment-feature-desc {
  font-family: var(--ag-font-body);
  font-size: 16px;
  line-height: 24px;
  color: var(--ag-text-secondary);
}

/* CTA Section */
.section-cta {
  padding: 96px 0;
}
.cta-card {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 64px;
}
@media (max-width: 768px) {
  .cta-card { padding: 0 24px; }
}
.cta-card-inner {
  background: rgba(135, 169, 107, 0.08);
  border-radius: 40px;
  padding: 48px 24px;
  text-align: center;
  position: relative;
  overflow: hidden;
  border: 1px solid rgba(72, 103, 48, 0.1);
}
@media (min-width: 768px) {
  .cta-card-inner { padding: 96px; }
}
.cta-bg-icon {
  position: absolute;
  top: 0;
  right: 0;
  padding: 48px;
  opacity: 0.05;
  pointer-events: none;
}
@media (max-width: 640px) {
  .cta-bg-icon { display: none; }
}
.cta-content {
  position: relative;
  z-index: 10;
  max-width: 600px;
  margin: 0 auto;
}
.cta-title {
  font-family: var(--ag-font-display);
  font-size: 32px;
  font-weight: 500;
  line-height: 40px;
  color: var(--ag-text-primary);
  margin-bottom: 24px;
}
.cta-desc {
  font-family: var(--ag-font-body);
  font-size: 18px;
  line-height: 28px;
  color: var(--ag-text-secondary);
  margin-bottom: 40px;
}
.cta-btn {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  padding: 20px 48px;
  background: var(--ag-primary-500);
  color: white;
  border-radius: 9999px;
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s;
}
.cta-btn:hover {
  box-shadow: 0 10px 30px -4px color-mix(in srgb, var(--ag-primary-500) 30%, transparent);
}
.cta-btn:active { transform: scale(0.95); }

@keyframes subtle-zoom {
  from { transform: scale(1); }
  to { transform: scale(1.1); }
}
@keyframes scroll-indicator {
  0% { transform: translateY(-100%); opacity: 0; }
  50% { opacity: 1; }
  100% { transform: translateY(200%); opacity: 0; }
}
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.6; }
}
</style>
