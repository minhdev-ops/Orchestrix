<template>
  <MarketplaceLayout>
    <main class="quiz-page">
      <div class="quiz-container">
        <div class="quiz-header">
          <span class="quiz-label">Công cụ Tìm Mẫu vật</span>
          <h1 class="quiz-title" id="quiz-title">{{ stepTitle }}</h1>

          <div class="progress-wrap">
            <div class="progress-track">
              <div class="progress-thumb" :style="{ width: progressPercent + '%' }"></div>
            </div>
            <div class="progress-counter">{{ progressLabel }}</div>
          </div>
        </div>

        <div v-for="q in questions" :key="q.step" v-show="step === q.step" class="quiz-step">
          <h2 class="step-question">{{ q.question_text }}</h2>
          <div v-if="q.choice_type === 'grid'" class="choice-grid choice-grid-3">
            <button v-for="(choice, ci) in q.choices" :key="ci" class="choice-card" @click="goToStep(q.step + 1)">
              <div class="choice-icon-wrap">
                <span class="material-symbols-outlined choice-icon">{{ choice.icon }}</span>
              </div>
              <span class="choice-label">{{ choice.label }}</span>
              <p v-if="choice.desc" class="choice-desc">{{ choice.desc }}</p>
            </button>
          </div>
          <div v-else-if="q.choice_type === 'image'" class="choice-grid choice-grid-2">
            <button v-for="(choice, ci) in q.choices" :key="ci" class="choice-image-card" @click="goToStep(q.step + 1)">
              <div class="choice-image" :class="choice.image_class"></div>
              <div class="choice-image-overlay">
                <span class="choice-image-label">{{ choice.label }}</span>
                <span v-if="choice.sub" class="choice-image-sub">{{ choice.sub }}</span>
              </div>
            </button>
          </div>
          <div v-else class="choice-rows">
            <button v-for="(choice, ci) in q.choices" :key="ci" class="choice-row" @click="goToStep(q.step + 1)">
              <div>
                <span class="choice-row-label">{{ choice.label }}</span>
                <span v-if="choice.desc" class="choice-row-desc">{{ choice.desc }}</span>
              </div>
              <span class="choice-row-arrow material-symbols-outlined">arrow_forward</span>
            </button>
          </div>
        </div>

        <div v-show="step === 4" class="quiz-step quiz-step-center">
          <div class="spinner-wrap">
            <div class="spinner-ring"></div>
            <span class="material-symbols-outlined spinner-icon">psychology</span>
          </div>
          <h2 class="step-question">Đang phân tích khả năng tương thích thực vật...</h2>
          <p class="step-loading-desc">Đang tuyển chọn từ kho lưu trữ quý hiếm của chúng tôi.</p>
        </div>

        <div v-show="step === 'results'" class="quiz-step">
          <div class="results-header">
            <h2 class="results-title">Mẫu vật được Tuyển chọn</h2>
            <p class="results-desc">Dựa trên môi trường và trình độ của bạn, chúng tôi đề xuất ba cặp đôi này.</p>
          </div>
          <div class="results-grid">
            <div class="result-card">
              <div class="result-image result-image-1">
                <span class="result-badge result-badge-secondary">Phát hiện Hiếm</span>
              </div>
              <div class="result-body">
                <h3 class="result-name">Monstera Thai Constellation</h3>
                <p class="result-desc">Một mẫu vật biến thể tuyệt đẹp phát triển tốt trong ánh sáng lọc sáng.</p>
                <button class="result-btn">
                  <span class="material-symbols-outlined">add</span>
                  Thêm nhanh &mdash; $185
                </button>
              </div>
            </div>
            <div class="result-card">
              <div class="result-image result-image-2">
                <span class="result-badge result-badge-primary">Ưa ẩm</span>
              </div>
              <div class="result-body">
                <h3 class="result-name">Alocasia 'Polly'</h3>
                <p class="result-desc">Cây Mặt nạ Châu Phi ấn tượng cho môi trường độ ẩm cao.</p>
                <button class="result-btn">
                  <span class="material-symbols-outlined">add</span>
                  Thêm nhanh &mdash; $45
                </button>
              </div>
            </div>
            <div class="result-card">
              <div class="result-image result-image-3">
                <span class="result-badge result-badge-tertiary">Cứng cáp</span>
              </div>
              <div class="result-body">
                <h3 class="result-name">Whale Fin Sansevieria</h3>
                <p class="result-desc">Một viên ngọc kiến trúc phát triển chậm, chịu được điều kiện ánh sáng yếu.</p>
                <button class="result-btn">
                  <span class="material-symbols-outlined">add</span>
                  Thêm nhanh &mdash; $62
                </button>
              </div>
            </div>
          </div>
          <div class="retake-wrap">
            <button class="retake-btn" @click="resetQuiz">
              <span class="material-symbols-outlined">restart_alt</span>
              Làm lại
            </button>
          </div>
        </div>
      </div>
    </main>

    <footer class="quiz-footer">
      <div class="quiz-footer-container">
        <div class="quiz-footer-grid">
          <div class="quiz-footer-brand">
            <div class="quiz-footer-logo">AgriVerse</div>
            <p class="quiz-footer-copy">&copy; 2024 AgriVerse. Vun đắp một tương lai xanh hơn thông qua nghề làm vườn chính xác.</p>
          </div>
          <div class="quiz-footer-col">
            <h4 class="quiz-footer-heading">Liên kết Nhanh</h4>
            <a href="#" class="quiz-footer-link">Câu chuyện của chúng tôi</a>
            <a href="#" class="quiz-footer-link">Vận chuyển &amp; Đổi trả</a>
            <a href="#" class="quiz-footer-link">Bán sỉ</a>
          </div>
          <div class="quiz-footer-col">
            <h4 class="quiz-footer-heading">Tài nguyên</h4>
            <a href="#" class="quiz-footer-link">Chính sách Bảo mật</a>
            <a href="#" class="quiz-footer-link">Liên hệ</a>
            <a href="#" class="quiz-footer-link">Báo cáo Bền vững</a>
          </div>
        </div>
      </div>
    </footer>
  </MarketplaceLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import MarketplaceLayout from '@agriverse/Layouts/MarketplaceLayout.vue'

const props = defineProps({
  questions: { type: Array, default: () => [] },
})

const step = ref(1)
const totalSteps = computed(() => Math.max(props.questions.length + 1, 4))

const stepTitle = computed(() => {
  if (step.value === 'results') return "Chúng tôi đã tìm thấy cặp đôi của bạn."
  return 'Hãy cho chúng tôi biết về khu vườn của bạn.'
})

const progressPercent = computed(() => {
  if (step.value === 'results') return 100
  return (step.value / totalSteps.value) * 100
})

const progressLabel = computed(() => {
  if (step.value === 4) return 'Đang hoàn tất...'
  if (step.value === 'results') return 'Đã tìm thấy'
  return `Bước ${step.value} trên ${totalSteps.value}`
})

const currentQuestion = computed(() => {
  return props.questions.find(q => q.step === step.value)
})

function goToStep(next) {
  if (next === totalSteps.value) {
    step.value = totalSteps.value
    setTimeout(() => {
      step.value = 'results'
    }, 2500)
  } else {
    step.value = next
  }
}

function resetQuiz() {
  step.value = 1
}
</script>

<style scoped>
.quiz-page {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 64px;
  padding-top: 80px;
  padding-bottom: 96px;
  min-height: 100vh;
}
@media (max-width: 768px) {
  .quiz-page { padding: 64px 24px 80px; }
}
.quiz-container { max-width: 768px; margin: 0 auto; }

.quiz-header { text-align: center; margin-bottom: 48px; }
.quiz-label {
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--ag-secondary);
  display: block;
  margin-bottom: 8px;
}
.quiz-title {
  font-family: var(--ag-font-display);
  font-size: 48px;
  font-weight: 500;
  letter-spacing: -0.02em;
  color: var(--ag-on-surface);
  margin-bottom: 32px;
}
@media (max-width: 768px) { .quiz-title { font-size: 36px; } }

.progress-wrap { max-width: 100%; }
.progress-track {
  width: 100%;
  height: 4px;
  background: var(--ag-surface-container);
  border-radius: 9999px;
  overflow: hidden;
}
.progress-thumb {
  height: 100%;
  background: var(--ag-primary);
  border-radius: 9999px;
  transition: width 0.7s ease-in-out;
}
.progress-counter {
  margin-top: 12px;
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  letter-spacing: 0.05em;
  color: var(--ag-outline);
  text-align: left;
}

.quiz-step {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
.quiz-step-center {
  text-align: center;
  padding: 48px 0;
}
.step-question {
  font-family: var(--ag-font-display);
  font-size: 32px;
  font-weight: 500;
  color: var(--ag-on-surface-variant);
  text-align: center;
  margin-bottom: 40px;
}
.step-loading-desc {
  font-family: var(--ag-font-body);
  font-size: 16px;
  line-height: 24px;
  color: var(--ag-on-surface-variant);
}

.choice-grid {
  display: grid;
  gap: 24px;
}
.choice-grid-3 { grid-template-columns: 1fr; }
@media (min-width: 768px) {
  .choice-grid-3 { grid-template-columns: repeat(3, 1fr); }
}
.choice-grid-2 { grid-template-columns: 1fr; }
@media (min-width: 768px) {
  .choice-grid-2 { grid-template-columns: repeat(2, 1fr); }
}

.choice-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 32px;
  background: white;
  border-radius: 12px;
  border: 1px solid transparent;
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
  cursor: pointer;
  text-align: center;
  transition: all 0.3s;
}
.choice-card:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.06);
  border-color: color-mix(in srgb, var(--ag-primary) 20%, transparent);
}
.choice-icon-wrap {
  width: 80px;
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--ag-primary-fixed);
  border-radius: 50%;
  margin-bottom: 24px;
  transition: transform 0.3s;
}
.choice-card:hover .choice-icon-wrap { transform: scale(1.1); }
.choice-icon {
  font-size: 36px;
  color: var(--ag-primary);
}
.choice-label {
  font-family: var(--ag-font-display);
  font-size: 24px;
  font-weight: 500;
  color: var(--ag-on-surface);
  margin-bottom: 8px;
}
.choice-desc {
  font-family: var(--ag-font-body);
  font-size: 16px;
  line-height: 24px;
  color: var(--ag-on-surface-variant);
}

.choice-image-card {
  position: relative;
  overflow: hidden;
  aspect-ratio: 4 / 3;
  border-radius: 16px;
  background: var(--ag-surface-container);
  cursor: pointer;
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.choice-image {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  transition: transform 0.7s;
}
.choice-image-card:hover .choice-image { transform: scale(1.05); }
.choice-image-humidity {
  background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDQDqI4eWo4KI5OSD_wHo3XjGeQQYTFTvVZDo5GDSE5O43oT6hacAeyyESfr1ikbQWmSxljVl7jlY0PVnzvFJRqfk_0o94Bg443Aod1Pp6ojglqnDpX4r6FfHJpCX8Ym8_NuQH49Ge0D-UwopGdoJxPIF_wrJbqsrofxSRz5qTFZjn112eV7OGm_TBVouNxZRFbSbWDXVu63U0g-y9rI8Ow9VlwEYXTxJqhz7_n2xacTmqqRYh2TdbNRewePH4uQaDYoVMUBHzv-CE');
}
.choice-image-dry {
  background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDVSI8871NzeDrO7ulEKK7j0kvXaCY3rBQAjxMtpiEdT81AhH3KxMA1lsPYA3Lw44Ho04cjsuDX0HFkLkOELi4CuBNandVZ0iIQEXb1EiI8XPYIiWkF0isTATBOW1findRk7ue7t-jEQlRQZlHJjR3FF1o4na_6ofPqnPIcBzOLqCLmsObfC2idL3-lVqIWAnRsIQ89ZeP-fxK4dbixQROcUBX0q4kmfkP0fKJdzz4HyF35gsSrHLKg6jbDp5H_NxOG9hOKWPBK2hA');
}
.choice-image-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(27, 28, 28, 0.8), transparent);
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 32px;
  text-align: left;
}
.choice-image-label {
  font-family: var(--ag-font-display);
  font-size: 24px;
  font-weight: 500;
  color: white;
}
.choice-image-sub {
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  letter-spacing: 0.05em;
  color: rgba(255, 255, 255, 0.7);
}

.choice-rows {
  display: flex;
  flex-direction: column;
  gap: 16px;
  max-width: 600px;
  margin: 0 auto;
}
.choice-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px;
  background: white;
  border: 1px solid var(--ag-outline-variant);
  border-radius: 12px;
  cursor: pointer;
  text-align: left;
  transition: border-color 0.3s;
}
.choice-row:hover { border-color: var(--ag-primary); }
.choice-row-label {
  display: block;
  font-family: var(--ag-font-display);
  font-size: 24px;
  font-weight: 500;
  color: var(--ag-on-surface);
}
.choice-row-desc {
  display: block;
  font-family: var(--ag-font-body);
  font-size: 16px;
  line-height: 24px;
  color: var(--ag-on-surface-variant);
}
.choice-row-arrow {
  color: var(--ag-primary);
  opacity: 0;
  transition: opacity 0.3s;
}
.choice-row:hover .choice-row-arrow { opacity: 1; }

.spinner-wrap {
  position: relative;
  width: 96px;
  height: 96px;
  margin: 0 auto 32px;
}
.spinner-ring {
  position: absolute;
  inset: 0;
  border: 4px solid color-mix(in srgb, var(--ag-primary) 12%, transparent);
  border-radius: 50%;
}
.spinner-ring::after {
  content: '';
  position: absolute;
  inset: -4px;
  border: 4px solid transparent;
  border-top-color: var(--ag-primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}
.spinner-icon {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 36px;
  color: var(--ag-primary);
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.results-header { text-align: center; margin-bottom: 48px; }
.results-title {
  font-family: var(--ag-font-display);
  font-size: 32px;
  font-weight: 500;
  color: var(--ag-on-surface);
  margin-bottom: 8px;
}
.results-desc {
  font-family: var(--ag-font-body);
  font-size: 16px;
  line-height: 24px;
  color: var(--ag-on-surface-variant);
}
.results-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 32px;
}
@media (min-width: 768px) {
  .results-grid { grid-template-columns: repeat(3, 1fr); }
}
.result-card {
  background: white;
  border-radius: 16px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
  transition: box-shadow 0.3s;
}
.result-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
.result-image {
  height: 256px;
  background-size: cover;
  background-position: center;
  position: relative;
}
.result-image-1 {
  background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuABGtJCKgfOuTbHbgmDNq5_Nb9u0VNsBBQBjDinZfNrzrb-mRswSw146yp9xbdaCjxiAdq6me9o2nnVgc-pjw4IIpPRN5YKxk8N5Vo9HaKJgyC9pRwWyV9qjj-RpPU31F32FBSTQltGkbUa6TzWWrSzWrx4IWUE8Bpj9g_eywOVpJZ8yz0XdAf2fxcl4C4K1EQNxkgK2ETcBbZ_1M1DZ1SehOjWBonqHadRdal_D7C2dQRCOfkjrG_GwBziqIFQw2C-5I9W-EmvAzg');
}
.result-image-2 {
  background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDSexPZOkUWHWYFqf2P-_uSCk7739VQl6RRkQsBRERkgzvvr1R2taEa_e1wwEpc55ysxXwbiB0__xVpd5TMFhnC7T4UPpNgyqJ2kjVcbUBertVsr2kqekwAHlbk52vI3Qm5FY2U_TnOVEZtp5qzbV1ezXauZYPWGYNpGe_GrocT10928Iucx2sz4Q8h9_ehfheZKTW3fJKesKqRv8KNv8end_NxYDU_H8aq4IXPZdLTUNmfELXzhptOl84fxvx0zli6PRQKZ8VzzEo');
}
.result-image-3 {
  background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDV_r9DKtkRr4Mvft4ZdaZmaUEikXI4GO8JoYQaG-jzHO2FRYm6OZYUMeCpLWKtLPZlVaGNsP0Hw8e5sY7o5oHfHs-AkLQRwo8_XhaCAv2ya8aEi2JBUs1GnaCdDKcNXXSk5RK8ZjZYCJRtxRuZWKXeOmBiBocQv-52ePSACYdTJ9-5UO8J1znl89ytaPJwKPKSfr72Qx89MnOZsljNjpZTvxOWb5t7cj0LoEeMJ22Yzb0lnujLNa28IOnVeVQ9s-r1_3W9tIfvK28');
}
.result-badge {
  position: absolute;
  top: 16px;
  left: 16px;
  padding: 4px 12px;
  border-radius: 9999px;
  font-family: var(--ag-font-body);
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}
.result-badge-secondary {
  background: var(--ag-secondary-fixed);
  color: var(--ag-on-secondary-fixed-variant);
}
.result-badge-primary {
  background: var(--ag-primary-fixed);
  color: var(--ag-primary-dark);
}
.result-badge-tertiary {
  background: var(--ag-tertiary-fixed, #e5e2db);
  color: var(--ag-on-tertiary-fixed, #1c1c18);
}
.result-body {
  padding: 24px;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}
.result-name {
  font-family: var(--ag-font-display);
  font-size: 24px;
  font-weight: 500;
  color: var(--ag-on-surface);
  margin-bottom: 4px;
}
.result-desc {
  font-family: var(--ag-font-body);
  font-size: 16px;
  line-height: 24px;
  color: var(--ag-on-surface-variant);
  flex-grow: 1;
  margin-bottom: 24px;
}
.result-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px;
  background: var(--ag-primary);
  color: white;
  border: none;
  border-radius: 8px;
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  letter-spacing: 0.05em;
  cursor: pointer;
  transition: all 0.2s;
}
.result-btn:hover { background: var(--ag-primary-dark); }
.result-btn:active { transform: scale(0.95); }

.retake-wrap { text-align: center; padding-top: 32px; }
.retake-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: none;
  border: none;
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  letter-spacing: 0.05em;
  color: var(--ag-outline);
  cursor: pointer;
  transition: color 0.2s;
}
.retake-btn:hover { color: var(--ag-primary); }

.quiz-footer {
  background: var(--ag-on-surface);
  color: #fff;
  margin-top: 0;
  padding: 80px 0;
}
.quiz-footer-container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 64px;
}
@media (max-width: 768px) {
  .quiz-footer-container { padding: 0 20px; }
}
.quiz-footer-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 48px;
}
@media (min-width: 768px) {
  .quiz-footer-grid {
    grid-template-columns: 2fr 1fr 1fr;
  }
}
.quiz-footer-brand {
  display: flex;
  flex-direction: column;
  gap: 24px;
}
.quiz-footer-logo {
  font-family: var(--ag-font-display);
  font-size: 32px;
  font-weight: 500;
  font-style: italic;
  color: var(--ag-primary-fixed);
}
.quiz-footer-copy {
  font-size: 14px;
  line-height: 20px;
  opacity: 0.8;
  max-width: 320px;
  color: var(--ag-surface-variant);
}
.quiz-footer-col {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.quiz-footer-heading {
  font-size: 24px;
  font-weight: 500;
  font-family: var(--ag-font-display);
  color: var(--ag-inverse-on-surface);
  margin: 0 0 8px;
}
.quiz-footer-link {
  font-size: 14px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0.05em;
  color: var(--ag-surface-variant);
  opacity: 0.8;
  text-decoration: none;
  transition: opacity 0.2s;
}
.quiz-footer-link:hover {
  opacity: 1;
  color: var(--ag-primary-fixed);
}
</style>
