<template>
  <MarketplaceLayout>
    <div class="garden-page">
      <section class="hero-section">
        <div class="hero-card">
          <div class="hero-content">
            <span class="hero-label">Bảng điều khiển Làm vườn</span>
            <h1 class="hero-title">Tình trạng Khu vườn của Bạn</h1>
            <p class="hero-desc">Chào mừng trở lại, {{ heroStats.userName }}. Khu vườn của bạn hiện có {{ heroStats.activeSpecimens }} mẫu vật đang được theo dõi.</p>
            <div class="hero-badges">
              <div class="badge badge-green">
                <span class="material-symbols-outlined badge-icon">eco</span>
                <span class="badge-text">Ngón tay xanh: {{ heroStats.greenFingers }}%</span>
              </div>
              <div class="badge badge-terracotta">
                <span class="material-symbols-outlined badge-icon">calendar_today</span>
                <span class="badge-text">{{ heroStats.activeSpecimens }} Mẫu vật Đang hoạt động</span>
              </div>
            </div>
          </div>
          <div class="hero-ring">
            <svg class="ring-svg" viewBox="0 0 100 100">
              <circle class="ring-bg" cx="50" cy="50" r="45" />
              <circle class="ring-progress" cx="50" cy="50" r="45"
                :stroke-dashoffset="ringOffset" />
            </svg>
            <div class="ring-label">
              <span class="ring-grade">{{ heroStats.grade }}</span>
              <span class="ring-tier">{{ heroStats.tier }}</span>
            </div>
          </div>
        </div>
      </section>

      <div class="page-grid">
        <div class="main-col">
          <div class="section-header">
            <div>
              <h2 class="section-title">Theo dõi Mẫu vật</h2>
              <p class="section-subtitle">Chỉ số sức sống thời gian thực cho bộ sưu tập của bạn.</p>
            </div>
            <button class="btn-primary">
              <span class="material-symbols-outlined">add</span>
              Mẫu vật Mới
            </button>
          </div>

          <div class="specimen-grid">
            <div v-for="(specimen, index) in specimens" :key="specimen.id" class="specimen-card">
              <div class="specimen-image-wrap">
                <img :src="specimen.image" :alt="specimen.name" class="specimen-image" />
                <div class="specimen-badge" :class="specimen.status === 'hydrated' ? 'badge-hydrated' : 'badge-thirsty'">
                  <span class="material-symbols-outlined">{{ specimen.status === 'hydrated' ? 'water_drop' : 'priority_high' }}</span>
                  <span>{{ specimen.status === 'hydrated' ? 'Đủ nước' : 'Khát nước' }}</span>
                </div>
              </div>
              <h3 class="specimen-name">{{ specimen.name }}</h3>
              <p class="specimen-id">Mã số: {{ specimen.code }} &bull; {{ specimen.location }}</p>
              <div class="progress-list">
                <div class="progress-item">
                  <div class="progress-header">
                    <span>Đếm ngược Tưới nước</span>
                    <span class="progress-value" :class="{ 'progress-value-error': specimen.hydration.error }">{{ specimen.hydration.label }}</span>
                  </div>
                  <div class="progress-bar">
                    <div class="progress-fill" :class="specimen.hydration.error ? 'progress-fill-error' : 'progress-fill-primary'"
                      :style="{ width: displayHydration[index] + '%' }"></div>
                  </div>
                </div>
                <div class="progress-item">
                  <div class="progress-header">
                    <span>Chu kỳ Dinh dưỡng</span>
                    <span class="progress-value" :class="{ 'progress-value-secondary': !specimen.nutrient.muted && !specimen.nutrient.error }">{{ specimen.nutrient.label }}</span>
                  </div>
                  <div class="progress-bar">
                    <div class="progress-fill" :class="specimen.nutrient.muted ? 'progress-fill-muted' : 'progress-fill-secondary'"
                      :style="{ width: displayNutrient[index] + '%' }"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <section class="wishlist-section">
            <div class="section-header">
              <h2 class="section-title">Danh sách Mong muốn Mẫu vật</h2>
              <Link :href="route('agriverse.shop.wishlist.index')" class="section-link">Xem tất cả Yêu thích</Link>
            </div>
            <div class="wishlist-grid" v-if="wishlist.length">
              <div v-for="item in wishlist" :key="item.id" class="wishlist-item">
                <div class="wishlist-img-wrap">
                  <img :src="item.image" :alt="item.name" class="wishlist-img" />
                  <div class="wishlist-overlay">
                    <span>Mua ngay</span>
                  </div>
                </div>
              </div>
              <Link :href="route('agriverse.shop.products.index')" class="wishlist-add">
                <span class="material-symbols-outlined wishlist-add-icon">add_circle</span>
                <span class="wishlist-add-text">Duyệt Vườn ươm</span>
              </Link>
            </div>
            <div v-else class="wishlist-grid">
              <Link :href="route('agriverse.shop.products.index')" class="wishlist-add">
                <span class="material-symbols-outlined wishlist-add-icon">add_circle</span>
                <span class="wishlist-add-text">Thêm sản phẩm yêu thích</span>
              </Link>
            </div>
          </section>
        </div>

        <aside class="sidebar-col">
          <div class="sidebar-sticky">
            <div class="sidebar-card">
              <div class="sidebar-card-header">
                <span class="material-symbols-outlined">auto_awesome</span>
                <h3 class="sidebar-card-title">Thông tin Khí hậu</h3>
              </div>

              <div class="climate-banner" v-if="climateInfo">
                <div class="climate-location">
                  <span class="material-symbols-outlined">location_on</span>
                  <span class="climate-location-text">{{ climateInfo.location }}</span>
                </div>
                <p class="climate-desc">{{ climateInfo.description }}</p>
              </div>

              <div class="advice-list">
                <div v-for="insight in climateInsightsList" :key="insight.title" class="advice-item">
                  <div class="advice-icon" :class="'advice-icon-' + insight.iconStyle">
                    <span class="material-symbols-outlined">{{ insight.icon }}</span>
                  </div>
                  <div>
                    <h4 class="advice-title">{{ insight.title }}</h4>
                    <p class="advice-desc">{{ insight.desc }}</p>
                  </div>
                </div>
              </div>

              <button class="btn-chat">
                Trò chuyện với Botanist AI
                <span class="material-symbols-outlined">arrow_forward</span>
              </button>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </MarketplaceLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import MarketplaceLayout from '@agriverse/Layouts/MarketplaceLayout.vue'

const props = defineProps({
  specimens: { type: Array, default: () => [] },
  wishlist: { type: Array, default: () => [] },
  heroStats: {
    type: Object,
    default: () => ({
      greenFingers: 0,
      activeSpecimens: 0,
      grade: 'C',
      tier: 'Cấp Mới',
      userName: 'Bạn',
    }),
  },
})

const defaultSpecimens = computed(() => {
  if (props.specimens.length > 0) {
    return props.specimens.map(s => ({
      ...s,
      image: s.image_url || s.image,
      hydration: {
        value: s.hydration_value,
        label: s.hydration_label,
        error: Boolean(s.hydration_error),
      },
      nutrient: {
        value: s.nutrient_value,
        label: s.nutrient_label,
        error: Boolean(s.nutrient_error),
        muted: Boolean(s.nutrient_muted),
      },
    }))
  }
  return [
    {
      id: 1, name: 'Monstera Deliciosa', code: 'BH-09224', location: 'Living Room',
      image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuC9WLTKhlaWjBjifoYvcYy_yUtwhxrR2W3v1Hs-gPcHkLJ6PtEoJ2RkQJ3D6Jbvly0Sgro7DxT4l7eIpi9o8YeqhPWqZon25fr_UlySezGrzLjsE-6dvQLdVVgsqd7DBt4aHszsN3L2U1UXjkmDq85U1IWQ3HhoT9S76ppTUY1uHu77vvOi3C-WFRJwsTsX0Rru43spRcoxhtHmQbcbqRdZqDdU-NleSR-QW2MpdjAnPe-6hcF_qzX2ocNxmPeQUHisDy7Cg4MzHdQ',
      status: 'hydrated', hydration: { value: 65, label: '48h còn lại', error: false }, nutrient: { value: 82, label: '12 ngày còn lại', error: false, muted: false },
    },
    {
      id: 2, name: 'Ficus Lyrata', code: 'BH-11054', location: 'Studio',
      image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBg2GAi60_R3IKCF553FTQeHBhyF6ZK6cFvEIpr6g_FSccxpZS6sac28gKTY-aw2XucW1W-CC3Y4ezT0eFnLZ_03uwFGfmijYaoCBodfEiH78QVmx-73gO5-0THH307IAKwubK-d-cKujLKMqkraItbocGrF9kSw9br7bNg4g3RhsxixUHFeTFSmdLFPl1jNcDvg4x0vbbvxNxFcctfklTogjBeqKBWe2iKWBXCOVja0iqP9ss_oSZjBZBsbSF8BQNh2clNKZAFa4o',
      status: 'thirsty', hydration: { value: 100, label: 'Quá hạn: 4h', error: true }, nutrient: { value: 15, label: 'Sẵn sàng Bón phân', error: false, muted: true },
    },
  ]
})

const specimens = defaultSpecimens



const climateInsights = ref([
  { icon: 'light_mode', iconStyle: 'green', title: 'Tối ưu Sương sáng', desc: 'Phát hiện mức ánh sáng khí quyển thấp. Di chuyển cây Ficus của bạn đến gần cửa sổ hướng nam hôm nay.' },
  { icon: 'thermostat', iconStyle: 'terracotta', title: 'Cảnh báo Ngủ đông', desc: 'Nhiệt độ bên ngoài giảm xuống 8°C.' },
  { icon: 'psychology', iconStyle: 'muted', title: 'Mẹo AI Chăm sóc', desc: 'Cây Monstera của bạn đang phát triển nhanh hơn 15%.' },
])

const climateInfo = computed(() => {
  if (props.heroStats.activeSpecimens === 0) return null
  return {
    location: 'Việt Nam',
    description: props.heroStats.greenFingers >= 70
      ? 'Điều kiện lý tưởng cho cây xanh. Tiếp tục duy trì chế độ chăm sóc hiện tại.'
      : props.heroStats.greenFingers >= 40
        ? 'Một số mẫu vật cần được chú ý hơn. Kiểm tra lịch tưới nước và dinh dưỡng.'
        : 'Nhiều mẫu vật cần được chăm sóc ngay. Hãy kiểm tra từng mẫu vật trong danh sách.',
  }
})

const climateInsightsList = computed(() => {
  const insights = []
  if (props.heroStats.activeSpecimens > 0) {
    const thirstyCount = props.specimens.filter(s => s.status === 'thirsty' || s.hydration_error).length
    if (thirstyCount > 0) {
      insights.push({ icon: 'water_drop', iconStyle: 'terracotta', title: `${thirstyCount} Mẫu vật Khát nước`, desc: `${thirstyCount} mẫu vật cần được tưới nước ngay lập tức.` })
    }
    if (props.heroStats.greenFingers >= 80) {
      insights.push({ icon: 'eco', iconStyle: 'green', title: 'Vườn khỏe mạnh', desc: 'Điểm số ngón tay xanh của bạn rất tốt. Tiếp tục duy trì!' })
    }
  }
  insights.push({ icon: 'psychology', iconStyle: 'muted', title: 'Mẹo AI Chăm sóc', desc: `Bạn đang theo dõi ${props.heroStats.activeSpecimens} mẫu vật. Kiểm tra định kỳ mỗi tuần.` })
  return insights
})

const ringCircumference = 283
const ringOffset = ref(ringCircumference)
const displayHydration = ref([])
const displayNutrient = ref([])

onMounted(() => {
  setTimeout(() => {
    ringOffset.value = ringCircumference - (ringCircumference * props.heroStats.greenFingers / 100)
    displayHydration.value = specimens.value.map(s => s.hydration.value)
    displayNutrient.value = specimens.value.map(s => s.nutrient.value)
  }, 300)
})
</script>

<style scoped>
.garden-page {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 64px;
  padding-top: 120px;
  padding-bottom: 0;
}
@media (max-width: 768px) {
  .garden-page { padding: 80px 24px 0; }
}

.hero-section { margin-bottom: 64px; }
.hero-card {
  background: var(--ag-bg-card, #ffffff);
  border-radius: var(--ag-radius-xl);
  padding: 40px;
  border: 1px solid color-mix(in srgb, var(--ag-border) 30%, transparent);
  display: flex;
  flex-direction: column;
  gap: 48px;
  align-items: center;
  box-shadow: var(--ag-shadow-sm, 0 1px 3px rgba(0,0,0,0.04));
}
@media (min-width: 768px) {
  .hero-card { flex-direction: row; justify-content: space-between; }
}
.hero-content { max-width: 520px; }
.hero-label {
  font-family: var(--ag-font-body);
  font-size: 12px;
  font-weight: 700;
  color: var(--ag-primary);
  text-transform: uppercase;
  letter-spacing: 0.2em;
  display: block;
  margin-bottom: 16px;
}
.hero-title {
  font-family: var(--ag-font-display);
  font-size: 48px;
  line-height: 1.1;
  color: var(--ag-on-surface);
  margin-bottom: 24px;
}
@media (max-width: 768px) { .hero-title { font-size: 36px; } }
.hero-desc {
  font-family: var(--ag-font-body);
  font-size: 18px;
  line-height: 1.6;
  color: var(--ag-on-surface-variant);
  margin-bottom: 32px;
}
.hero-badges { display: flex; flex-wrap: wrap; gap: 16px; }
.badge {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  padding: 12px 24px;
  border-radius: var(--ag-radius-full);
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
}
.badge-green {
  background: color-mix(in srgb, var(--ag-primary) 5%, transparent);
  border: 1px solid color-mix(in srgb, var(--ag-primary) 10%, transparent);
}
.badge-green .badge-icon,
.badge-green .badge-text { color: var(--ag-primary); }
.badge-terracotta {
  background: color-mix(in srgb, var(--ag-secondary) 5%, transparent);
  border: 1px solid color-mix(in srgb, var(--ag-secondary) 10%, transparent);
}
.badge-terracotta .badge-icon,
.badge-terracotta .badge-text { color: var(--ag-secondary); }
.badge-icon { font-size: 20px; font-variation-settings: 'FILL' 1; }
.badge-text { font-size: 14px; font-weight: 600; }

.hero-ring {
  position: relative;
  width: 224px;
  height: 224px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.ring-svg {
  width: 100%;
  height: 100%;
  transform: rotate(-90deg);
}
.ring-bg {
  fill: transparent;
  stroke: var(--ag-surface-container);
  stroke-width: 6;
}
.ring-progress {
  fill: transparent;
  stroke: var(--ag-primary);
  stroke-width: 10;
  stroke-linecap: round;
  stroke-dasharray: 283;
  transition: stroke-dashoffset 1s ease-out;
}
.ring-label {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
}
.ring-grade {
  font-family: var(--ag-font-display);
  font-size: 48px;
  font-style: italic;
  font-weight: 500;
  color: var(--ag-primary);
  line-height: 1;
}
.ring-tier {
  font-family: var(--ag-font-body);
  font-size: 12px;
  font-weight: 600;
  color: var(--ag-on-surface-variant);
  text-transform: uppercase;
  letter-spacing: 0.1em;
  margin-top: 4px;
}

.page-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 40px;
}
@media (min-width: 1024px) {
  .page-grid { grid-template-columns: 8fr 4fr; }
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 32px;
}
.section-title {
  font-family: var(--ag-font-display);
  font-size: 30px;
  color: var(--ag-on-surface);
}
.section-subtitle {
  font-family: var(--ag-font-body);
  font-size: 14px;
  color: var(--ag-on-surface-variant);
  margin-top: 4px;
}
.section-link {
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  color: var(--ag-primary);
  text-decoration: none;
}
.section-link:hover { text-decoration: underline; text-decoration-color: color-mix(in srgb, var(--ag-primary) 30%, transparent); }

.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 32px;
  background: var(--ag-primary);
  color: white;
  border: none;
  border-radius: 8px;
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: var(--ag-shadow-sm, 0 1px 3px rgba(0,0,0,0.06));
}
.btn-primary:hover { background: color-mix(in srgb, var(--ag-primary) 90%, black); }

.specimen-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 32px;
}
@media (min-width: 768px) {
  .specimen-grid { grid-template-columns: 1fr 1fr; }
}
.specimen-card {
  background: var(--ag-bg-card, #ffffff);
  border-radius: var(--ag-radius-xl);
  padding: 24px;
  border: 1px solid color-mix(in srgb, var(--ag-border) 30%, transparent);
  transition: box-shadow 0.3s;
}
.specimen-card:hover { box-shadow: var(--ag-shadow-md, 0 4px 12px rgba(0,0,0,0.06)); }
.specimen-image-wrap {
  height: 224px;
  margin-bottom: 24px;
  border-radius: 8px;
  overflow: hidden;
  background: var(--ag-surface-container);
  position: relative;
}
.specimen-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.7s;
  display: block;
}
.specimen-card:hover .specimen-image { transform: scale(1.05); }
.specimen-badge {
  position: absolute;
  top: 16px;
  right: 16px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: var(--ag-radius-full);
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  box-shadow: var(--ag-shadow-sm, 0 1px 3px rgba(0,0,0,0.06));
  backdrop-filter: blur(4px);
}
.badge-hydrated {
  background: rgba(255,255,255,0.95);
  color: var(--ag-primary);
  border: 1px solid color-mix(in srgb, var(--ag-primary) 10%, transparent);
}
.badge-thirsty {
  background: rgba(255,218,214,0.95);
  color: var(--ag-danger);
  border: 1px solid color-mix(in srgb, var(--ag-error) 10%, transparent);
}
.specimen-name {
  font-family: var(--ag-font-display);
  font-size: 24px;
  color: var(--ag-on-surface);
  margin-bottom: 4px;
}
.specimen-id {
  font-family: var(--ag-font-body);
  font-size: 11px;
  font-weight: 700;
  color: var(--ag-on-surface-variant);
  opacity: 0.7;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  margin-bottom: 20px;
}

.progress-list { display: flex; flex-direction: column; gap: 20px; }
.progress-header {
  display: flex;
  justify-content: space-between;
  font-family: var(--ag-font-body);
  font-size: 12px;
  font-weight: 600;
  margin-bottom: 8px;
}
.progress-header span:first-child { color: var(--ag-on-surface-variant); }
.progress-value {
  font-style: italic;
  color: var(--ag-primary);
}
.progress-value-secondary { color: var(--ag-secondary); }
.progress-value-error { color: var(--ag-error); font-weight: 700; }
.progress-bar {
  height: 6px;
  width: 100%;
  background: var(--ag-surface-container);
  border-radius: var(--ag-radius-full);
  overflow: hidden;
}
.progress-fill {
  height: 100%;
  border-radius: var(--ag-radius-full);
  transition: width 0.6s ease;
}
.progress-fill-primary { background: var(--ag-primary); }
.progress-fill-secondary { background: var(--ag-secondary); }
.progress-fill-error { background: var(--ag-error); }
.progress-fill-muted { background: var(--ag-on-surface-variant); opacity: 0.3; }

.wishlist-section { margin-top: 64px; }
.wishlist-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 24px;
}
@media (min-width: 640px) {
  .wishlist-grid { grid-template-columns: repeat(3, 1fr); }
}
.wishlist-item {
  aspect-ratio: 1;
  border-radius: var(--ag-radius-xl);
  overflow: hidden;
  position: relative;
  background: var(--ag-surface-container);
}
.wishlist-img-wrap {
  width: 100%;
  height: 100%;
  position: relative;
  overflow: hidden;
}
.wishlist-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 1s;
  display: block;
}
.wishlist-item:hover .wishlist-img { transform: scale(1.1); }
.wishlist-overlay {
  position: absolute;
  inset: 0;
  background: color-mix(in srgb, var(--ag-primary) 20%, transparent);
  opacity: 0;
  transition: opacity 0.3s;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(2px);
}
.wishlist-item:hover .wishlist-overlay { opacity: 1; }
.wishlist-overlay span {
  color: white;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.15em;
}
.wishlist-add {
  aspect-ratio: 1;
  border: 2px dashed var(--ag-border);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  border-radius: var(--ag-radius-xl);
  transition: background 0.3s;
}
.wishlist-add:hover { background: var(--ag-surface-container); }
.wishlist-add-icon {
  font-size: 32px;
  color: var(--ag-border);
  margin-bottom: 12px;
}
.wishlist-add-text {
  font-family: var(--ag-font-body);
  font-size: 12px;
  font-weight: 700;
  color: var(--ag-on-surface-variant);
  text-transform: uppercase;
  letter-spacing: 0.1em;
  text-align: center;
  padding: 0 16px;
}

.sidebar-col { position: relative; }
.sidebar-sticky { position: sticky; top: 112px; }
.sidebar-card {
  background: var(--ag-bg-card, #ffffff);
  border-radius: var(--ag-radius-xl);
  padding: 32px;
  border: 1px solid color-mix(in srgb, var(--ag-border) 30%, transparent);
  box-shadow: var(--ag-shadow-sm, 0 1px 3px rgba(0,0,0,0.04));
}
.sidebar-card-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 32px;
}
.sidebar-card-header .material-symbols-outlined {
  color: var(--ag-primary);
  font-size: 24px;
  font-variation-settings: 'FILL' 1;
}
.sidebar-card-title {
  font-family: var(--ag-font-display);
  font-size: 24px;
  font-style: italic;
  color: var(--ag-on-surface);
}

.climate-banner {
  padding: 20px;
  background: var(--ag-bg);
  border-radius: 8px;
  border: 1px solid color-mix(in srgb, var(--ag-border) 20%, transparent);
  margin-bottom: 32px;
}
.climate-location {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
}
.climate-location .material-symbols-outlined {
  color: var(--ag-primary);
  font-size: 16px;
}
.climate-location-text {
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 700;
  color: var(--ag-on-surface);
}
.climate-desc {
  font-family: var(--ag-font-body);
  font-size: 12px;
  font-style: italic;
  color: var(--ag-on-surface-variant);
  line-height: 1.5;
}

.advice-list { display: flex; flex-direction: column; gap: 32px; }
.advice-item { display: flex; gap: 16px; }
.advice-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.advice-icon .material-symbols-outlined { font-size: 20px; }
.advice-icon-green {
  background: color-mix(in srgb, var(--ag-primary) 5%, transparent);
  color: var(--ag-primary);
  border: 1px solid color-mix(in srgb, var(--ag-primary) 10%, transparent);
}
.advice-icon-terracotta {
  background: color-mix(in srgb, var(--ag-secondary) 5%, transparent);
  color: var(--ag-secondary);
  border: 1px solid color-mix(in srgb, var(--ag-secondary) 10%, transparent);
}
.advice-icon-muted {
  background: color-mix(in srgb, var(--ag-on-surface-variant) 5%, transparent);
  color: var(--ag-on-surface-variant);
  border: 1px solid color-mix(in srgb, var(--ag-on-surface-variant) 10%, transparent);
}
.advice-title {
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 700;
  color: var(--ag-on-surface);
  margin-bottom: 4px;
}
.advice-desc {
  font-family: var(--ag-font-body);
  font-size: 12px;
  color: var(--ag-on-surface-variant);
  line-height: 1.5;
}

.btn-chat {
  width: 100%;
  margin-top: 40px;
  padding: 16px;
  background: var(--ag-primary);
  color: white;
  border: none;
  border-radius: 8px;
  font-family: var(--ag-font-body);
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  transition: all 0.2s;
  box-shadow: var(--ag-shadow-sm, 0 1px 3px rgba(0,0,0,0.06));
}
.btn-chat:hover { background: color-mix(in srgb, var(--ag-primary) 90%, black); }
.btn-chat .material-symbols-outlined {
  font-size: 16px;
  transition: transform 0.3s;
}
.btn-chat:hover .material-symbols-outlined { transform: translateX(4px); }
</style>
