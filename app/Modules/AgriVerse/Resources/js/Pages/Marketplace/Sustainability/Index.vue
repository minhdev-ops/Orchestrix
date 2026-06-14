<template>
  <MarketplaceLayout>
    <main class="sustainability">
      <section class="sustainability__hero">
        <div class="sustainability__hero-text">
          <span class="sustainability__hero-label">{{ report?.title || 'Báo cáo Thường niên 2024' }}</span>
          <h1 class="sustainability__hero-title">Bắt nguồn từ <span class="sustainability__hero-accent">Minh bạch</span></h1>
          <p class="sustainability__hero-desc">{{ report?.description || 'Vượt lên trên vẻ đẹp thẩm mỹ của những tán lá hiếm là cam kết nghiêm ngặt về phục hồi sinh thái.' }}</p>
          <div class="sustainability__hero-actions">
            <button v-if="report?.pdf_url" class="sustainability__btn sustainability__btn--primary" @click="downloadPdf">Tải PDF</button>
            <button v-if="report?.methodology_description" class="sustainability__btn sustainability__btn--outline" @click="showMethodology">Phương pháp</button>
          </div>
        </div>
        <div class="sustainability__hero-image">
          <div class="sustainability__hero-img-wrap">
            <img :src="report?.hero_image_url || 'https://lh3.googleusercontent.com/aida-public/AB6AXuBWarD2zXjbfLDjZPfB6Pan5YmPT_I__sNh51QO1gnObBQEeBwRu_TxxHMgZycVXuGsXZL7NIkx0xL9TQj2qz3Q218q-gr0DMh31p5wE_cCX7zcsHb7g5JdQlvsAaBmFpmnZcVasD-_vJEn9Aq63P5R7iZIj0zFbP6AO8XjcPxSZFtaTYtWOmVxhJcL9gJmND6A-9_fMzYdhBzK1UIk9bN91JHifPWSmjGHsiknnONorbdhnmPwBe38Ro4W2O_GUrwmLVfssDtYmxY'" alt="Minh bạch thực vật" class="sustainability__hero-img" />
          </div>
          <div class="sustainability__hero-quote">
            <p class="sustainability__quote-text">{{ report?.quote || '"Chính xác trong từng cánh hoa."' }}</p>
            <p class="sustainability__quote-author">{{ report?.quote_author || '— TS. Elena Vance, Trưởng phòng Nghiên cứu Thực vật' }}</p>
          </div>
        </div>
      </section>

      <section class="sustainability__metrics">
        <h2 class="sustainability__section-title">Chỉ số Tác động Trực tiếp</h2>
        <div class="sustainability__metrics-grid">
          <div class="sustainability__metric-card sustainability__metric-card--offset">
            <div class="sustainability__metric-head">
              <span class="material-symbols-outlined sustainability__metric-icon">co2</span>
              <span v-if="report?.carbon_trend" class="sustainability__metric-trend">{{ report.carbon_trend }}</span>
            </div>
            <div class="sustainability__metric-body">
              <span class="sustainability__metric-label">Bù đắp Carbon</span>
              <div class="sustainability__metric-value-wrap">
                <span class="sustainability__metric-number" ref="carbonRef">{{ displayCarbon.toLocaleString() }}</span>
                <span class="sustainability__metric-unit">Tấn</span>
              </div>
            </div>
          </div>

          <div class="sustainability__metric-card sustainability__metric-card--reforest">
            <div class="sustainability__metric-card-bg"></div>
            <div class="sustainability__metric-head">
              <span class="material-symbols-outlined sustainability__metric-icon sustainability__metric-icon--secondary">forest</span>
              <span class="sustainability__metric-trend sustainability__metric-trend--secondary">{{ report?.reforestation_status || 'Tăng trưởng đã xác nhận' }}</span>
            </div>
            <div class="sustainability__metric-body">
              <span class="sustainability__metric-label">Tổng số Cây trồng lại</span>
              <div class="sustainability__metric-value-wrap">
                <span class="sustainability__metric-number" ref="reforestRef">{{ displayReforest.toLocaleString() }}</span>
                <span class="sustainability__metric-unit">Cây non</span>
              </div>
            </div>
          </div>

          <div class="sustainability__metric-card sustainability__metric-card--packaging">
            <div class="sustainability__metric-head">
              <span class="material-symbols-outlined sustainability__metric-icon sustainability__metric-icon--tertiary">package_2</span>
              <div class="sustainability__metric-badge">
                <span class="sustainability__metric-badge-text">{{ Math.round(report?.packaging_sustainable_percent || 98) }}%</span>
              </div>
            </div>
            <div class="sustainability__metric-body">
              <span class="sustainability__metric-label">Bao bì Sinh thái</span>
              <div class="sustainability__metric-value-wrap">
                <span class="sustainability__metric-number">{{ report?.packaging_sustainable_percent || '98.4' }}</span>
                <span class="sustainability__metric-unit">Bền vững</span>
              </div>
              <div class="sustainability__metric-progress">
                <div class="sustainability__metric-progress-fill" :style="{ width: Math.round(report?.packaging_sustainable_percent || 98) + '%' }"></div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="sustainability__supply">
        <div class="sustainability__supply-card">
          <div class="sustainability__supply-head">
            <div>
              <h2 class="sustainability__supply-title">Chuỗi Cung ứng Đạo đức</h2>
              <p class="sustainability__supply-desc">{{ report?.ethical_description || 'Theo dõi nguồn gốc mẫu vật với độ chính xác của blockchain.' }}</p>
            </div>
            <div class="sustainability__supply-stats">
              <div>
                <span class="sustainability__supply-stat-label">Nguồn gốc đã theo dõi</span>
                <span class="sustainability__supply-stat-value">{{ report?.supply_regions_tracked || '24 Khu vực' }}</span>
              </div>
              <div>
                <span class="sustainability__supply-stat-label">Xếp hạng Kiểm toán</span>
                <span class="sustainability__supply-stat-value">{{ report?.audit_rating || 'A+ (SGS)' }}</span>
              </div>
            </div>
          </div>

          <div class="sustainability__map">
            <div class="sustainability__map-bg"></div>
            <div class="sustainability__map-dot sustainability__map-dot--amazon" @mouseenter="activeDot = 'amazon'" @mouseleave="activeDot = null">
              <div class="sustainability__map-dot-ping"></div>
              <div class="sustainability__map-dot-core"></div>
              <div v-if="activeDot === 'amazon'" class="sustainability__map-tooltip">
                <span class="sustainability__map-tooltip-region">LƯU VỰC AMAZON</span>
                <p class="sustainability__map-tooltip-desc">Philodendron Biến thể Hiếm</p>
                <div class="sustainability__map-tooltip-verified">
                  <span class="material-symbols-outlined">verified</span>
                  Hợp tác xã Cộng đồng
                </div>
              </div>
            </div>
            <div class="sustainability__map-dot sustainability__map-dot--madagascar" @mouseenter="activeDot = 'madagascar'" @mouseleave="activeDot = null">
              <div class="sustainability__map-dot-core sustainability__map-dot-core--secondary"></div>
              <div v-if="activeDot === 'madagascar'" class="sustainability__map-tooltip">
                <span class="sustainability__map-tooltip-region">MADAGASCAR</span>
                <p class="sustainability__map-tooltip-desc">Sen đá Đặc hữu</p>
              </div>
            </div>
            <div class="sustainability__map-dot sustainability__map-dot--center">
              <div class="sustainability__map-dot-core sustainability__map-dot-core--dim"></div>
            </div>
            <div class="sustainability__map-watermark">Mạng lưới Cung ứng Toàn cầu</div>
          </div>
        </div>
      </section>

      <section class="sustainability__logistics">
        <div class="sustainability__logistics-list">
          <h2 class="sustainability__section-title">Hậu cần Xanh</h2>
          <div class="sustainability__logistics-items">
            <div class="sustainability__logistics-item">
              <div class="sustainability__logistics-icon sustainability__logistics-icon--primary">
                <span class="material-symbols-outlined">electric_bolt</span>
              </div>
              <div>
                <h4 class="sustainability__logistics-item-title">Giao hàng EV Chặng cuối</h4>
                <p class="sustainability__logistics-item-desc">82% tổng số lượt giao hàng đô thị được thực hiện bằng xe điện.</p>
              </div>
            </div>
            <div class="sustainability__logistics-item">
              <div class="sustainability__logistics-icon sustainability__logistics-icon--secondary">
                <span class="material-symbols-outlined">fluid_med</span>
              </div>
              <div>
                <h4 class="sustainability__logistics-item-title">Vườn ươm Tích cực Nước</h4>
                <p class="sustainability__logistics-item-desc">Thu hồi 1,2 triệu gallon nước mưa hàng năm để tưới tiêu.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="sustainability__circular">
          <div class="sustainability__circular-ring">
            <svg class="sustainability__circular-svg" viewBox="0 0 100 100">
              <circle class="sustainability__circular-track" cx="50" cy="50" r="45" fill="transparent" stroke-width="8" />
              <circle class="sustainability__circular-fill" cx="50" cy="50" r="45" fill="transparent" stroke-width="8" stroke-dasharray="282.7" :stroke-dashoffset="282.7 - (282.7 * (report?.circularity_percent || 70) / 100)" />
            </svg>
            <div class="sustainability__circular-center">
              <span class="sustainability__circular-pct">{{ report?.circularity_percent || 70 }}%</span>
              <span class="sustainability__circular-label">Tuần hoàn</span>
            </div>
          </div>
          <h3 class="sustainability__circular-title">Chu trình Tài nguyên Tuần hoàn</h3>
          <p class="sustainability__circular-desc">{{ report?.circular_description || 'Mục tiêu của chúng tôi là 100% tuần hoàn vào năm 2026.' }}</p>
        </div>
      </section>

      <section class="sustainability__editorial">
        <h2 class="sustainability__editorial-quote">"Tương lai của nghề làm vườn không chỉ là giữ cho cây xanh, mà còn giữ cho chúng trung thực."</h2>
        <div class="sustainability__editorial-divider"></div>
        <p class="sustainability__editorial-text">Tại AgriVerse, chúng tôi tin rằng sự xa xỉ không thể tách rời khỏi trách nhiệm. Mỗi mẫu vật chúng tôi tuyển chọn là một điểm dữ liệu trong sứ mệnh chứng minh rằng thương mại toàn cầu có thể là một lực lượng tích cực cho môi trường. Báo cáo này là lộ trình—và thẻ điểm—của chúng tôi với thế giới.</p>
      </section>
    </main>

    <footer class="sustainability__footer">
      <div class="sustainability__footer-container">
        <div class="sustainability__footer-grid">
          <div class="sustainability__footer-brand">
            <div class="sustainability__footer-logo">AgriVerse</div>
            <p class="sustainability__footer-copy">&copy; {{ report?.year || '2024' }} AgriVerse. Vun đắp một tương lai xanh hơn thông qua nghề làm vườn chính xác.</p>
            <div class="sustainability__footer-social">
              <span class="material-symbols-outlined">eco</span>
              <span class="material-symbols-outlined">public</span>
              <span class="material-symbols-outlined">compost</span>
            </div>
          </div>
          <div class="sustainability__footer-col">
            <h4 class="sustainability__footer-heading">Tổ chức</h4>
            <a href="#" class="sustainability__footer-link">Câu chuyện của chúng tôi</a>
            <a href="#" class="sustainability__footer-link">Bán sỉ</a>
            <a href="#" class="sustainability__footer-link">Báo cáo Bền vững</a>
          </div>
          <div class="sustainability__footer-col">
            <h4 class="sustainability__footer-heading">Hỗ trợ</h4>
            <a href="#" class="sustainability__footer-link">Vận chuyển &amp; Đổi trả</a>
            <a href="#" class="sustainability__footer-link">Liên hệ</a>
            <a href="#" class="sustainability__footer-link">Chính sách Bảo mật</a>
          </div>
          <div class="sustainability__footer-col sustainability__footer-col--signup">
            <h4 class="sustainability__footer-heading">Tạp chí</h4>
            <p class="sustainability__footer-desc">Đăng ký để nhận bản tin "Tình trạng Đất" hàng tháng của chúng tôi.</p>
            <div class="sustainability__footer-input-wrap">
              <input type="email" class="sustainability__footer-input" placeholder="Địa chỉ email" />
              <button class="sustainability__footer-btn">
                <span class="material-symbols-outlined">arrow_forward</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </footer>
  </MarketplaceLayout>
</template>

<!-- fix -->
<script setup>
import { ref, computed, onMounted } from 'vue'
import MarketplaceLayout from '@agriverse/Layouts/MarketplaceLayout.vue'

const props = defineProps({
  report: { type: Object, default: () => null },
})

const activeDot = ref(null)
const carbonRef = ref(null)
const reforestRef = ref(null)

const carbonTarget = computed(() => props.report?.carbon_offset_actual || 2482)
const reforestTarget = computed(() => props.report?.reforestation_total || 45100)
const displayCarbon = ref(0)
const displayReforest = ref(0)

function animateNumber(currentRef, target, duration = 2000) {
  const start = performance.now()
  const startValue = currentRef.value

  function step(now) {
    const elapsed = now - start
    const progress = Math.min(elapsed / duration, 1)
    const eased = 1 - Math.pow(1 - progress, 3)
    currentRef.value = Math.floor(startValue + (target - startValue) * eased)

    if (progress < 1) {
      requestAnimationFrame(step)
    } else {
      currentRef.value = target
    }
  }

  requestAnimationFrame(step)
}

onMounted(() => {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          if (entry.target === carbonRef.value) {
            animateNumber(displayCarbon, carbonTarget.value)
          }
          if (entry.target === reforestRef.value) {
            animateNumber(displayReforest, reforestTarget.value)
          }
          observer.unobserve(entry.target)
        }
      })
    },
    { threshold: 0.5 }
  )

  if (carbonRef.value) observer.observe(carbonRef.value)
  if (reforestRef.value) observer.observe(reforestRef.value)
})

function downloadPdf() {
  if (props.report?.pdf_url) {
    window.open(props.report.pdf_url, '_blank')
  }
}

function showMethodology() {
  if (props.report?.methodology_description) {
    alert(props.report.methodology_description)
  }
}


<style scoped>
.sustainability {
  font-family: var(--ag-font-body);
  max-width: 1280px;
  margin: 0 auto;
  padding: 48px 64px 96px;
  color: var(--ag-on-surface);
}

.sustainability__hero {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 48px;
  margin-bottom: 96px;
}

@media (min-width: 768px) {
  .sustainability__hero {
    flex-direction: row;
  }
}

.sustainability__hero-text {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.sustainability__hero-label {
  font-size: 14px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: var(--ag-secondary);
}

.sustainability__hero-title {
  font-family: var(--ag-font-display);
  font-size: 48px;
  font-weight: 500;
  line-height: 1.1;
  letter-spacing: -0.02em;
  margin: 0;
}

@media (min-width: 768px) {
  .sustainability__hero-title {
    font-size: 64px;
  }
}

.sustainability__hero-accent {
  font-style: italic;
  color: var(--ag-primary);
}

.sustainability__hero-desc {
  font-size: 24px;
  font-weight: 500;
  line-height: 32px;
  color: var(--ag-on-surface-variant);
  max-width: 576px;
  margin: 0;
}

.sustainability__hero-actions {
  display: flex;
  gap: 16px;
  padding-top: 16px;
}

.sustainability__btn {
  padding: 12px 32px;
  border-radius: 9999px;
  font-size: 14px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0.05em;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
}

.sustainability__btn--primary {
  background: var(--ag-primary);
  color: #fff;
  box-shadow: 0 10px 15px -3px color-mix(in srgb, var(--ag-primary) 20%, transparent);
}

.sustainability__btn--primary:hover {
  filter: brightness(0.9);
}

.sustainability__btn--outline {
  border: 1px solid var(--ag-outline);
  color: var(--ag-on-surface);
  background: transparent;
}

.sustainability__btn--outline:hover {
  background: var(--ag-surface-variant);
}

.sustainability__hero-image {
  flex: 1;
  position: relative;
}

.sustainability__hero-img-wrap {
  aspect-ratio: 4 / 5;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
  rotate: 2deg;
  transition: rotate 0.7s;
}

.sustainability__hero-img-wrap:hover {
  rotate: 0deg;
}

.sustainability__hero-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.sustainability__hero-quote {
  position: absolute;
  bottom: -24px;
  left: -24px;
  background: color-mix(in srgb, #fff 70%, transparent);
  backdrop-filter: blur(12px);
  border: 1px solid color-mix(in srgb, #fff 30%, transparent);
  padding: 24px;
  border-radius: 16px;
  box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
  max-width: 288px;
}

.sustainability__quote-text {
  font-family: var(--ag-font-display);
  font-size: 24px;
  font-weight: 500;
  font-style: italic;
  line-height: 32px;
  color: var(--ag-primary);
  margin: 0;
}

.sustainability__quote-author {
  font-size: 12px;
  line-height: 16px;
  color: var(--ag-on-surface-variant);
  margin: 8px 0 0;
}

.sustainability__section-title {
  font-family: var(--ag-font-display);
  font-size: 32px;
  font-weight: 500;
  line-height: 40px;
  text-align: center;
  margin: 0 0 48px;
}

.sustainability__metrics {
  margin-bottom: 96px;
}

.sustainability__metrics-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 24px;
}

@media (min-width: 768px) {
  .sustainability__metrics-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

.sustainability__metric-card {
  padding: 32px;
  border-radius: 32px;
  border: 1px solid var(--ag-outline-variant);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  position: relative;
  overflow: hidden;
  transition: background 0.5s;
}

.sustainability__metric-card--offset {
  background: var(--ag-surface-container-low);
}

.sustainability__metric-card--offset:hover {
  background: var(--ag-primary-light);
}

.sustainability__metric-card--reforest {
  background: #fff;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.sustainability__metric-card-bg {
  position: absolute;
  top: 0;
  right: 0;
  width: 128px;
  height: 128px;
  background: color-mix(in srgb, var(--ag-primary) 5%, transparent);
  border-radius: 50%;
  margin-right: -64px;
  margin-top: -64px;
  transition: transform 0.7s;
}

.sustainability__metric-card--reforest:hover .sustainability__metric-card-bg {
  transform: scale(1.5);
}

.sustainability__metric-card--packaging {
  background: var(--ag-tertiary-fixed);
}

.sustainability__metric-head {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.sustainability__metric-icon {
  font-size: 40px;
  color: var(--ag-primary);
}

.sustainability__metric-icon--secondary {
  color: var(--ag-secondary);
}

.sustainability__metric-icon--tertiary {
  color: var(--ag-tertiary);
}

.sustainability__metric-trend {
  font-size: 14px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0.05em;
  color: var(--ag-primary);
}

.sustainability__metric-trend--secondary {
  color: var(--ag-secondary);
}

.sustainability__metric-badge {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 2px solid var(--ag-tertiary);
  display: flex;
  align-items: center;
  justify-content: center;
}

.sustainability__metric-badge-text {
  font-size: 10px;
  font-weight: 700;
  color: var(--ag-tertiary);
}

.sustainability__metric-body {
  margin-top: 32px;
}

.sustainability__metric-label {
  font-size: 14px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: var(--ag-on-surface-variant);
}

.sustainability__metric-value-wrap {
  display: flex;
  align-items: baseline;
  gap: 8px;
}

.sustainability__metric-number {
  font-family: var(--ag-font-display);
  font-size: 48px;
  font-weight: 500;
  line-height: 56px;
  letter-spacing: -0.02em;
  color: var(--ag-on-surface);
}

.sustainability__metric-unit {
  font-size: 14px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0.05em;
  color: var(--ag-on-surface-variant);
}

.sustainability__metric-progress {
  width: 100%;
  height: 6px;
  background: color-mix(in srgb, var(--ag-on-tertiary-fixed) 10%, transparent);
  border-radius: 9999px;
  margin-top: 16px;
  overflow: hidden;
}

.sustainability__metric-progress-fill {
  height: 100%;
  background: var(--ag-tertiary);
  border-radius: 9999px;
}

.sustainability__supply {
  margin-bottom: 96px;
}

.sustainability__supply-card {
  background: var(--ag-on-surface);
  border-radius: 48px;
  padding: 48px;
  color: #fff;
  overflow: hidden;
  position: relative;
}

.sustainability__supply-head {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: flex-start;
  gap: 32px;
  margin-bottom: 48px;
}

@media (min-width: 768px) {
  .sustainability__supply-head {
    flex-direction: row;
    align-items: flex-end;
  }
}

.sustainability__supply-title {
  font-family: var(--ag-font-display);
  font-size: 48px;
  font-weight: 500;
  line-height: 56px;
  letter-spacing: -0.02em;
  color: var(--ag-primary-fixed);
  margin: 0 0 16px;
}

.sustainability__supply-desc {
  font-size: 18px;
  line-height: 28px;
  color: var(--ag-on-surface-variant);
  max-width: 576px;
  margin: 0;
}

.sustainability__supply-stats {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 32px;
}

.sustainability__supply-stat-label {
  font-size: 14px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: color-mix(in srgb, var(--ag-on-surface-variant) 60%, transparent);
}

.sustainability__supply-stat-value {
  font-family: var(--ag-font-display);
  font-size: 24px;
  font-weight: 500;
  line-height: 32px;
  display: block;
}

.sustainability__map {
  position: relative;
  width: 100%;
  aspect-ratio: 21 / 9;
  background: color-mix(in srgb, var(--ag-surface-dim) 10%, transparent);
  border-radius: 16px;
  border: 1px solid color-mix(in srgb, #fff 10%, transparent);
  overflow: hidden;
}

.sustainability__map-bg {
  position: absolute;
  inset: 0;
  opacity: 0.4;
  mix-blend-mode: overlay;
  background: radial-gradient(circle at center, color-mix(in srgb, var(--ag-primary-fixed) 20%, transparent), transparent);
}

.sustainability__map-watermark {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  pointer-events: none;
  font-family: var(--ag-font-display);
  font-size: 24px;
  font-weight: 500;
  line-height: 32px;
  opacity: 0.2;
  color: var(--ag-on-surface-variant);
}

.sustainability__map-dot {
  position: absolute;
  z-index: 10;
}

.sustainability__map-dot--amazon {
  top: 30%;
  left: 20%;
}

.sustainability__map-dot--madagascar {
  top: 50%;
  left: 70%;
}

.sustainability__map-dot--center {
  top: 40%;
  left: 50%;
}

.sustainability__map-dot-ping {
  position: absolute;
  width: 16px;
  height: 16px;
  background: var(--ag-primary-fixed);
  border-radius: 50%;
  animation: ping 1.5s infinite;
}

@keyframes ping {
  75%, 100% {
    transform: scale(2);
    opacity: 0;
  }
}

.sustainability__map-dot-core {
  width: 16px;
  height: 16px;
  background: var(--ag-primary-fixed);
  border-radius: 50%;
  position: relative;
  cursor: pointer;
}

.sustainability__map-dot-core--secondary {
  background: var(--ag-secondary-fixed);
}

.sustainability__map-dot-core--dim {
  background: var(--ag-primary-fixed-dim);
}

.sustainability__map-tooltip {
  position: absolute;
  bottom: 100%;
  left: 50%;
  transform: translateX(-50%);
  margin-bottom: 16px;
  background: #fff;
  padding: 16px;
  border-radius: 12px;
  color: var(--ag-on-surface);
  box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
  min-width: 200px;
  z-index: 20;
}

.sustainability__map-tooltip-region {
  font-size: 14px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0.05em;
  color: var(--ag-secondary);
}

.sustainability__map-tooltip-desc {
  font-size: 16px;
  line-height: 24px;
  margin: 4px 0 8px;
}

.sustainability__map-tooltip-verified {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  line-height: 16px;
  color: var(--ag-on-surface-variant);
}

.sustainability__map-tooltip-verified .material-symbols-outlined {
  font-size: 14px;
}

.sustainability__logistics {
  display: grid;
  grid-template-columns: 1fr;
  gap: 24px;
  margin-bottom: 96px;
}

@media (min-width: 768px) {
  .sustainability__logistics {
    grid-template-columns: 1fr 1fr;
  }
}

.sustainability__logistics-list {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.sustainability__logistics-list .sustainability__section-title {
  text-align: left;
}

.sustainability__logistics-items {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.sustainability__logistics-item {
  display: flex;
  align-items: center;
  gap: 24px;
  padding: 24px;
  border-radius: 16px;
  background: var(--ag-surface-container);
  transition: background 0.2s;
}

.sustainability__logistics-item:hover {
  background: var(--ag-surface-container-high);
}

.sustainability__logistics-icon {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.sustainability__logistics-icon .material-symbols-outlined {
  font-size: 32px;
}

.sustainability__logistics-icon--primary {
  background: var(--ag-primary-fixed);
  color: var(--ag-primary-dark);
}

.sustainability__logistics-icon--secondary {
  background: var(--ag-secondary-fixed);
  color: var(--ag-secondary);
}

.sustainability__logistics-item-title {
  font-family: var(--ag-font-display);
  font-size: 24px;
  font-weight: 500;
  line-height: 32px;
  color: var(--ag-on-surface);
  margin: 0 0 4px;
}

.sustainability__logistics-item-desc {
  font-size: 16px;
  line-height: 24px;
  color: var(--ag-on-surface-variant);
  margin: 0;
}

.sustainability__circular {
  background: #fff;
  padding: 32px;
  border-radius: 32px;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
  border: 1px solid var(--ag-outline-variant);
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.sustainability__circular-ring {
  position: relative;
  width: 192px;
  height: 192px;
  margin-bottom: 32px;
}

.sustainability__circular-svg {
  width: 100%;
  height: 100%;
  transform: rotate(-90deg);
}

.sustainability__circular-track {
  stroke: var(--ag-surface-container);
}

.sustainability__circular-fill {
  stroke: var(--ag-primary);
  transition: stroke-dashoffset 1s;
}

.sustainability__circular-center {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.sustainability__circular-pct {
  font-family: var(--ag-font-display);
  font-size: 32px;
  font-weight: 500;
  line-height: 40px;
  color: var(--ag-on-surface);
}

.sustainability__circular-label {
  font-size: 12px;
  line-height: 16px;
  color: var(--ag-on-surface-variant);
}

.sustainability__circular-title {
  font-family: var(--ag-font-display);
  font-size: 24px;
  font-weight: 500;
  line-height: 32px;
  margin: 0 0 16px;
}

.sustainability__circular-desc {
  font-size: 16px;
  line-height: 24px;
  color: var(--ag-on-surface-variant);
  max-width: 384px;
  margin: 0;
}

.sustainability__editorial {
  max-width: 768px;
  margin: 0 auto;
  text-align: center;
  display: flex;
  flex-direction: column;
  gap: 32px;
  align-items: center;
}

.sustainability__editorial-quote {
  font-family: var(--ag-font-display);
  font-size: 48px;
  font-weight: 500;
  line-height: 56px;
  letter-spacing: -0.02em;
  font-style: italic;
  margin: 0;
}

.sustainability__editorial-divider {
  width: 96px;
  height: 1px;
  background: var(--ag-primary);
}

.sustainability__editorial-text {
  font-size: 18px;
  line-height: 28px;
  color: var(--ag-on-surface-variant);
  margin: 0;
}

.sustainability__footer {
  background: var(--ag-on-surface);
  color: #fff;
  margin-top: 96px;
  padding: 80px 0;
}

.sustainability__footer-container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 64px;
}

@media (max-width: 768px) {
  .sustainability__footer-container { padding: 0 20px; }
}

.sustainability__footer-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 48px;
}

@media (min-width: 768px) {
  .sustainability__footer-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

.sustainability__footer-brand {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.sustainability__footer-logo {
  font-family: var(--ag-font-display);
  font-size: 32px;
  font-weight: 500;
  font-style: italic;
  color: var(--ag-primary-fixed);
}

.sustainability__footer-copy {
  font-size: 14px;
  line-height: 20px;
  opacity: 0.8;
  color: var(--ag-surface-variant);
}

.sustainability__footer-social {
  display: flex;
  gap: 16px;
}

.sustainability__footer-social .material-symbols-outlined {
  opacity: 0.6;
  cursor: pointer;
  transition: opacity 0.2s;
}

.sustainability__footer-social .material-symbols-outlined:hover {
  opacity: 1;
}

.sustainability__footer-col {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.sustainability__footer-heading {
  font-size: 14px;
  font-weight: 600;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: var(--ag-primary-fixed-dim);
  margin: 0 0 8px;
}

.sustainability__footer-link {
  font-size: 16px;
  line-height: 24px;
  color: var(--ag-surface-variant);
  opacity: 0.8;
  text-decoration: none;
  transition: opacity 0.2s;
}

.sustainability__footer-link:hover {
  opacity: 1;
  color: var(--ag-primary-fixed);
}

.sustainability__footer-desc {
  font-size: 12px;
  line-height: 16px;
  color: var(--ag-surface-variant);
  opacity: 0.8;
}

.sustainability__footer-input-wrap {
  position: relative;
  display: flex;
}

.sustainability__footer-input {
  width: 100%;
  background: rgba(255,255,255,0.1);
  border: 1px solid rgba(255,255,255,0.2);
  border-radius: 9999px;
  padding: 12px 48px 12px 24px;
  font-family: var(--ag-font-body);
  font-size: 16px;
  color: #fff;
  outline: none;
  box-sizing: border-box;
}

.sustainability__footer-input::placeholder {
  color: rgba(255,255,255,0.3);
}

.sustainability__footer-input:focus {
  border-color: var(--ag-primary-fixed);
}

.sustainability__footer-btn {
  position: absolute;
  right: 8px;
  top: 50%;
  transform: translateY(-50%);
  background: var(--ag-primary-fixed);
  color: var(--ag-on-primary-fixed);
  border: none;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.sustainability__footer-btn .material-symbols-outlined {
  font-size: 18px;
}
</style>
