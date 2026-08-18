<template>
  <MarketplaceLayout>
    <main class="diagnostic">
      <section class="diagnostic__header">
        <h1 class="diagnostic__title">Phòng Chẩn Đoán Sức Khỏe Cây Trồng</h1>
        <p class="diagnostic__subtitle">Sử dụng phân tích mẫu vật tiên tiến bằng AI để phát hiện thiếu hụt dinh dưỡng, sâu bệnh và stress môi trường. Làm vườn chính xác bắt đầu từ dữ liệu chính xác.</p>
      </section>

      <div class="diagnostic__grid">
        <div class="diagnostic__left">
          <div class="diagnostic__upload" @click="handleUpload">
            <input id="specimen-upload" type="file" class="diagnostic__upload-input" accept="image/jpeg,image/png,image/webp" @change="onFileChange" />
            <label for="specimen-upload" class="diagnostic__upload-label">
              <template v-if="!previewUrl">
                <div class="diagnostic__upload-icon">
                  <span class="material-symbols-outlined">photo_camera</span>
                </div>
                <h3 class="diagnostic__upload-title">Tải Ảnh Mẫu Vật</h3>
                <p class="diagnostic__upload-desc">Đảm bảo chụp độ phân giải cao, từ trên xuống phần tán lá bị ảnh hưởng để xử lý thần kinh chính xác.</p>
              </template>
              <template v-else>
                <img :src="previewUrl" alt="Ảnh mẫu vật đã chọn" class="diagnostic__upload-preview" />
                <p class="diagnostic__upload-desc">Nhấn để chọn ảnh khác</p>
              </template>
            </label>
            <div class="diagnostic__corner diagnostic__corner--tl"></div>
            <div class="diagnostic__corner diagnostic__corner--tr"></div>
            <div class="diagnostic__corner diagnostic__corner--bl"></div>
            <div class="diagnostic__corner diagnostic__corner--br"></div>
          </div>

          <div class="diagnostic__checklist">
            <div class="diagnostic__checklist-header">
              <h2 class="diagnostic__checklist-title">Danh Sách Triệu Chứng</h2>
              <span class="diagnostic__checklist-badge">Ghi Đè Thủ Công</span>
            </div>
            <div class="diagnostic__checklist-grid">
              <label v-for="symptom in symptomNames" :key="symptom" class="diagnostic__symptom-item">
                <input type="checkbox" class="diagnostic__checkbox" v-model="selectedSymptoms" :value="symptom" />
                <span class="diagnostic__symptom-label">{{ symptom }}</span>
              </label>
            </div>
          </div>
        </div>

        <div class="diagnostic__right">
          <div class="diagnostic__status">
            <div class="diagnostic__status-inner">
              <div class="diagnostic__status-indicator">
                <span class="diagnostic__pulse"></span>
                <span class="diagnostic__status-text">{{ statusText }}</span>
              </div>
              <h2 class="diagnostic__status-title">Trạng Thái Hệ Thống</h2>
              <div class="diagnostic__metrics">
                <div class="diagnostic__metric">
                  <div class="diagnostic__metric-head">
                    <span class="diagnostic__metric-label">Sức Sống Tổng Thể</span>
                    <span class="diagnostic__metric-value diagnostic__metric-value--primary">64%</span>
                  </div>
                  <div class="diagnostic__bar">
                    <div class="diagnostic__bar-fill diagnostic__bar-fill--primary" style="width: 64%"></div>
                  </div>
                </div>
                <div class="diagnostic__metric">
                  <div class="diagnostic__metric-head">
                    <span class="diagnostic__metric-label">Mật Độ Diệp Lục</span>
                    <span class="diagnostic__metric-value diagnostic__metric-value--secondary">42%</span>
                  </div>
                  <div class="diagnostic__bar">
                    <div class="diagnostic__bar-fill diagnostic__bar-fill--secondary" style="width: 42%"></div>
                  </div>
                </div>
              </div>
              <div class="diagnostic__minicards">
                <div class="diagnostic__minicard">
                  <span class="material-symbols-outlined diagnostic__minicard-icon diagnostic__minicard-icon--primary">water_drop</span>
                  <p class="diagnostic__minicard-label">Độ Ẩm</p>
                  <p class="diagnostic__minicard-value">Dưới Mức Tối Ưu</p>
                </div>
                <div class="diagnostic__minicard">
                  <span class="material-symbols-outlined diagnostic__minicard-icon diagnostic__minicard-icon--secondary">thermostat</span>
                  <p class="diagnostic__minicard-label">Stress Nhiệt</p>
                  <p class="diagnostic__minicard-value">Trung Bình</p>
                </div>
              </div>
            </div>
            <div class="diagnostic__status-bg"></div>
          </div>

          <div class="diagnostic__report" ref="reportRef">
            <div class="diagnostic__report-header">
              <div class="diagnostic__report-icon">
                <span class="material-symbols-outlined">psychology</span>
              </div>
              <div>
                <h3 class="diagnostic__report-title">Báo Cáo Chẩn Đoán{{ result ? `: #${result.id}` : '' }}</h3>
                <p class="diagnostic__report-subtitle">{{ reportSubtitle }}</p>
              </div>
            </div>
            <div class="diagnostic__report-body">
              <div v-if="isAnalyzing" class="diagnostic__analyzing">
                <span class="material-symbols-outlined diagnostic__analyzing-icon">auto_awesome</span>
                <p>AI đang phân tích mẫu vật...</p>
              </div>
              <template v-else-if="result">
                <p class="diagnostic__report-desc">{{ result.description }}</p>
                <ul class="diagnostic__treatment-list">
                  <li v-for="treatment in result.treatments" :key="treatment" class="diagnostic__treatment-item">
                    <span class="material-symbols-outlined diagnostic__treatment-icon">check_circle</span>
                    <span>{{ treatment }}</span>
                  </li>
                </ul>
                <div class="diagnostic__prevention">
                  <h4 class="diagnostic__prevention-title">Phòng Ngừa</h4>
                  <ul class="diagnostic__treatment-list">
                    <li v-for="item in result.prevention" :key="item" class="diagnostic__treatment-item">
                      <span class="material-symbols-outlined diagnostic__treatment-icon">shield</span>
                      <span>{{ item }}</span>
                    </li>
                  </ul>
                </div>
              </template>
              <p v-else-if="errorMessage" class="diagnostic__report-error">{{ errorMessage }}</p>
              <p v-else class="diagnostic__report-desc">Tải lên ảnh lá cây để bắt đầu chẩn đoán. Hệ thống AI sẽ phát hiện bệnh và đề xuất biện pháp xử lý.</p>
            </div>
            <button class="diagnostic__cta" @click="handleUpload" :disabled="isAnalyzing">
              {{ isAnalyzing ? 'Đang Phân Tích...' : 'Chẩn Đoán Ngay' }}
              <span class="material-symbols-outlined">arrow_forward</span>
            </button>
          </div>
        </div>
      </div>

      <section class="diagnostic__gallery">
        <h2 class="diagnostic__gallery-title">Thư Viện Mẫu Vật</h2>
        <div class="diagnostic__gallery-grid">
          <div v-for="specimen in gallery" :key="specimen.name" class="diagnostic__gallery-card">
            <div class="diagnostic__gallery-image">
              <img :src="specimen.image" :alt="specimen.name" />
              <span :class="['diagnostic__gallery-badge', `diagnostic__gallery-badge--${specimen.statusClass}`]">{{ specimen.status }}</span>
            </div>
            <div class="diagnostic__gallery-info">
              <h4 class="diagnostic__gallery-name">{{ specimen.name }}</h4>
              <p class="diagnostic__gallery-desc">{{ specimen.description }}</p>
            </div>
          </div>
        </div>
      </section>
    </main>
  </MarketplaceLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'
import MarketplaceLayout from '@agriverse/Layouts/MarketplaceLayout.vue'

const props = defineProps({
  symptoms: { type: Array, default: () => [] },
})

const selectedSymptoms = ref([])
const reportRef = ref(null)
const statusText = ref('Sẵn Sàng Chẩn Đoán')
const previewUrl = ref('')
const selectedFile = ref(null)
const isAnalyzing = ref(false)
const result = ref(null)
const errorMessage = ref('')

const symptomNames = computed(() => {
  if (props.symptoms.length > 0) return props.symptoms
  return ['Vàng Lá', 'Rụng Lá', 'Đầu Lá Nâu', 'Chậm Phát Triển', 'Lá Đốm', 'Mốc Trắng']
})

const reportSubtitle = computed(() => {
  if (!result.value) return 'Chưa có kết quả phân tích'
  const confidence = Math.round((result.value.confidence ?? 0) * 100)
  return `${result.value.disease_name} • ${result.value.plant_name} • Độ tin cậy ${confidence}% • Mức độ: ${result.value.severity}`
})

const gallery = [
  {
    name: 'Mẫu Monstera 01',
    description: 'Xử lý tuần tự cho tình trạng khô mép lá thông qua hiệu chỉnh độ ẩm.',
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBJnawWEtrFD0Un65WivRFjXy10Q1cPWGZp82AVcdUbes6wj-GVAzyqYyRNHWJ2NiMuHfX_1I1y_NBYxwZxjQEBbQTXYUSMkd-jurU0WgsJYeZ2sUDmzckdqtwbP0BxldNU4KDOYgDPVoaP12YhQJI8upn-0G8eO1--8KlBGZAjyyae2oQ3mh48xjvy8cWT1H-cMw4B1RcK-wHnDiaFnJxcqe4T91CL1K4dCbNtR68yUDYz28Uq5MXXGtFqAdt42Rl_SSXOYcwqc7Y',
    status: 'Đã Xử Lý',
    statusClass: 'resolved'
  },
  {
    name: 'Ca Epipremnum 42',
    description: 'Đang theo dõi các mô hình khảm virus. Quy trình cách ly đang hoạt động.',
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAfPsL3M1qstaOTi518GGRbofjVq8_awXFrWu3x36m8_Ng2tzQGtLaruqV94vRYpvZ5Rykx3-jwjIqV8dcge_O_ibrbiPsFRAiD0q7PGNbJ9sz3xgGT8EZnGuchKUf5Vr296eEUhgA74xEihVO52AyaM5QYXHfNlAIZeeW7L5eBXc6-NMVqA5O_7qT8-fu4iwzgTVsgvT42hcGJnx1TIUDcw1uuanNzrTiVg_O3jQDCzSc9kyXzAGHYBXTawlP2b7L50uvRt00IE50',
    status: 'Ca Đang Xử Lý',
    statusClass: 'active'
  },
  {
    name: 'Sansevieria Băng A',
    description: 'Mẫu vật cơ sở cho nghiên cứu kiến trúc tế bào chịu ánh sáng yếu.',
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAHAmzF7DX4IX-ZEGrNql_dgFan9kc6oU8AqnGsSXHjOxXIC5IrpBBc4uk1s0akyTtayB9mIpmHUrMZoQHgC5L2EveocO6GPJoRTsR3e21IjmazeTNpEfKZq0mbkPOOJFcmnD2771ifgVVbSsd5IvSIyru_9CBBsOv6eAllRuOVlj1_n_QlvZH4h_ZE2ZUYy8OQnWinyFH7OQMQ7sLUC-nj6Rsd5a3EinfmakuugaO2zYI-HwdAIpjf8RLLRFzywkFhtBn2TF3yNzo',
    status: 'Khỏe Mạnh',
    statusClass: 'healthy'
  }
]

function onFileChange(e) {
  const file = e.target.files && e.target.files[0]
  if (!file) return

  if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
    errorMessage.value = 'Định dạng ảnh không hợp lệ. Vui lòng chọn JPG, PNG hoặc WEBP.'
    return
  }

  selectedFile.value = file
  previewUrl.value = URL.createObjectURL(file)
  errorMessage.value = ''
  result.value = null
}

async function handleUpload() {
  if (!selectedFile.value) {
    errorMessage.value = 'Vui lòng chọn ảnh mẫu vật trước khi chẩn đoán.'
    return
  }

  isAnalyzing.value = true
  errorMessage.value = ''
  result.value = null
  statusText.value = 'AI: Đang Phát Hiện Dị Thường Tán Lá...'

  const formData = new FormData()
  formData.append('image', selectedFile.value)
  formData.append('symptoms', selectedSymptoms.value.join(', '))

  try {
    const { data } = await axios.post('/agriverse/api/plant-doctor/diagnose', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    result.value = data.diagnosis
    statusText.value = 'Hoàn Tất Phân Tích'
    if (reportRef.value) {
      reportRef.value.scrollIntoView({ behavior: 'smooth', block: 'center' })
      reportRef.value.classList.add('diagnostic__report--highlight')
      setTimeout(() => {
        reportRef.value.classList.remove('diagnostic__report--highlight')
      }, 1000)
    }
  } catch (err) {
    statusText.value = 'Chẩn Đoán Thất Bại'
    const detail = err.response?.data?.error || err.response?.data?.message
    errorMessage.value = detail
      ? `Chẩn đoán thất bại: ${detail}`
      : 'Không thể kết nối đến hệ thống AI. Vui lòng thử lại sau.'
  } finally {
    isAnalyzing.value = false
  }
}
</script>

<style scoped>
.diagnostic {
  font-family: var(--ag-font-body);
  max-width: 1280px;
  margin: 0 auto;
  padding: 48px 64px;
  color: var(--ag-on-surface);
}

.diagnostic__header {
  margin-bottom: 64px;
}

.diagnostic__title {
  font-family: var(--ag-font-display);
  font-size: 48px;
  font-weight: 500;
  line-height: 56px;
  letter-spacing: -0.02em;
  color: var(--ag-primary);
  margin: 0 0 16px;
}

.diagnostic__subtitle {
  font-size: 18px;
  line-height: 28px;
  color: var(--ag-on-surface-variant);
  max-width: 576px;
  margin: 0;
}

.diagnostic__grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 24px;
}

@media (min-width: 1024px) {
  .diagnostic__grid {
    grid-template-columns: 7fr 5fr;
  }
}

.diagnostic__left,
.diagnostic__right {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.diagnostic__upload {
  position: relative;
  background: #fff;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.5s;
  overflow: hidden;
  height: 400px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' stroke='%23486730' stroke-width='3' stroke-dasharray='12%2c 12' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e");
}

.diagnostic__upload:hover {
  background: var(--ag-surface-container-low);
}

.diagnostic__upload-preview {
  max-width: 100%;
  max-height: 280px;
  border-radius: 8px;
  object-fit: contain;
  margin-bottom: 12px;
}

.diagnostic__upload-input {
  display: none;
}

.diagnostic__upload-label {
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.diagnostic__upload-icon {
  margin-bottom: 24px;
  padding: 24px;
  border-radius: 50%;
  background: color-mix(in srgb, var(--ag-primary) 10%, transparent);
  color: var(--ag-primary);
  transition: transform 0.5s;
}

.diagnostic__upload:hover .diagnostic__upload-icon {
  transform: scale(1.1);
}

.diagnostic__upload-icon .material-symbols-outlined {
  font-size: 48px;
}

.diagnostic__upload-title {
  font-family: var(--ag-font-display);
  font-size: 24px;
  font-weight: 500;
  line-height: 32px;
  color: var(--ag-on-surface);
  margin: 0 0 8px;
}

.diagnostic__upload-desc {
  font-size: 16px;
  line-height: 24px;
  color: var(--ag-on-surface-variant);
  max-width: 288px;
  padding: 0 16px;
  margin: 0;
}

.diagnostic__corner {
  position: absolute;
  width: 32px;
  height: 32px;
}

.diagnostic__corner--tl {
  top: 16px;
  left: 16px;
  border-top: 2px solid color-mix(in srgb, var(--ag-primary) 40%, transparent);
  border-left: 2px solid color-mix(in srgb, var(--ag-primary) 40%, transparent);
}

.diagnostic__corner--tr {
  top: 16px;
  right: 16px;
  border-top: 2px solid color-mix(in srgb, var(--ag-primary) 40%, transparent);
  border-right: 2px solid color-mix(in srgb, var(--ag-primary) 40%, transparent);
}

.diagnostic__corner--bl {
  bottom: 16px;
  left: 16px;
  border-bottom: 2px solid color-mix(in srgb, var(--ag-primary) 40%, transparent);
  border-left: 2px solid color-mix(in srgb, var(--ag-primary) 40%, transparent);
}

.diagnostic__corner--br {
  bottom: 16px;
  right: 16px;
  border-bottom: 2px solid color-mix(in srgb, var(--ag-primary) 40%, transparent);
  border-right: 2px solid color-mix(in srgb, var(--ag-primary) 40%, transparent);
}

.diagnostic__checklist {
  background: #fff;
  padding: 32px;
  border-radius: 12px;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
  border: 1px solid color-mix(in srgb, var(--ag-outline-variant) 30%, transparent);
}

.diagnostic__checklist-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 32px;
}

.diagnostic__checklist-title {
  font-family: var(--ag-font-display);
  font-size: 24px;
  font-weight: 500;
  line-height: 32px;
  color: var(--ag-primary);
  margin: 0;
}

.diagnostic__checklist-badge {
  font-size: 14px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: var(--ag-tertiary);
  background: var(--ag-tertiary-fixed);
  padding: 4px 12px;
  border-radius: 9999px;
  white-space: nowrap;
}

.diagnostic__checklist-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

@media (min-width: 768px) {
  .diagnostic__checklist-grid {
    grid-template-columns: 1fr 1fr 1fr;
  }
}

.diagnostic__symptom-item {
  display: flex;
  align-items: center;
  padding: 16px;
  border-radius: 8px;
  background: var(--ag-surface-container-low);
  border: 1px solid transparent;
  transition: all 0.2s;
  cursor: pointer;
}

.diagnostic__symptom-item:hover {
  border-color: color-mix(in srgb, var(--ag-primary) 30%, transparent);
}

.diagnostic__symptom-item:hover .diagnostic__symptom-label {
  color: var(--ag-primary);
}

.diagnostic__checkbox {
  border-radius: 4px;
  color: var(--ag-primary);
  margin-right: 12px;
  border-color: var(--ag-outline-variant);
  accent-color: var(--ag-primary);
}

.diagnostic__symptom-label {
  font-size: 16px;
  line-height: 24px;
  transition: color 0.2s;
}

.diagnostic__status {
  background: #fff;
  padding: 32px;
  border-radius: 12px;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
  border: 1px solid color-mix(in srgb, var(--ag-outline-variant) 30%, transparent);
  overflow: hidden;
  position: relative;
}

.diagnostic__status-inner {
  position: relative;
  z-index: 10;
}

.diagnostic__status-indicator {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 24px;
}

.diagnostic__pulse {
  display: block;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: var(--ag-secondary-fixed);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.diagnostic__status-text {
  font-size: 14px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: var(--ag-on-surface-variant);
}

.diagnostic__status-title {
  font-family: var(--ag-font-display);
  font-size: 32px;
  font-weight: 500;
  line-height: 40px;
  color: var(--ag-on-surface);
  margin: 0 0 24px;
}

.diagnostic__metrics {
  display: flex;
  flex-direction: column;
  gap: 24px;
  margin-bottom: 32px;
}

.diagnostic__metric-head {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
}

.diagnostic__metric-label {
  font-size: 14px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0.05em;
  color: var(--ag-tertiary);
}

.diagnostic__metric-value {
  font-size: 14px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0.05em;
}

.diagnostic__metric-value--primary {
  color: var(--ag-primary);
}

.diagnostic__metric-value--secondary {
  color: var(--ag-secondary);
}

.diagnostic__bar {
  height: 8px;
  width: 100%;
  background: var(--ag-surface-container-high);
  border-radius: 9999px;
  overflow: hidden;
}

.diagnostic__bar-fill {
  height: 100%;
  border-radius: 9999px;
  transition: width 1s;
}

.diagnostic__bar-fill--primary {
  background: var(--ag-primary);
}

.diagnostic__bar-fill--secondary {
  background: var(--ag-secondary);
}

.diagnostic__minicards {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.diagnostic__minicard {
  padding: 16px;
  border-radius: 8px;
  border: 1px solid color-mix(in srgb, var(--ag-outline-variant) 20%, transparent);
  background: var(--ag-surface-container-lowest);
}

.diagnostic__minicard-icon {
  margin-bottom: 8px;
  font-size: 24px;
}

.diagnostic__minicard-icon--primary {
  color: var(--ag-primary);
}

.diagnostic__minicard-icon--secondary {
  color: var(--ag-secondary);
}

.diagnostic__minicard-label {
  font-size: 12px;
  line-height: 16px;
  color: var(--ag-tertiary);
  text-transform: uppercase;
  margin: 0;
}

.diagnostic__minicard-value {
  font-size: 14px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0.05em;
  color: var(--ag-on-surface);
  margin: 0;
}

.diagnostic__status-bg {
  position: absolute;
  right: -48px;
  bottom: -48px;
  width: 192px;
  height: 192px;
  border-radius: 50%;
  border: 16px solid color-mix(in srgb, var(--ag-primary-fixed) 20%, transparent);
  z-index: 0;
}

.diagnostic__report {
  background: var(--ag-primary);
  color: #fff;
  padding: 32px;
  border-radius: 12px;
  box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
  transition: all 0.7s;
}

.diagnostic__report--highlight {
  box-shadow: 0 0 0 4px var(--ag-primary-fixed);
  transform: scale(1.05);
}

.diagnostic__report-header {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  margin-bottom: 24px;
}

.diagnostic__report-icon {
  padding: 12px;
  background: color-mix(in srgb, #fff 20%, transparent);
  border-radius: 8px;
}

.diagnostic__report-icon .material-symbols-outlined {
  font-size: 32px;
}

.diagnostic__report-title {
  font-family: var(--ag-font-display);
  font-size: 24px;
  font-weight: 500;
  line-height: 32px;
  margin: 0;
}

.diagnostic__report-subtitle {
  font-size: 16px;
  line-height: 24px;
  color: var(--ag-primary-fixed);
  margin: 0;
}

.diagnostic__report-body {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 32px;
}

.diagnostic__analyzing {
  display: flex;
  align-items: center;
  gap: 12px;
}

.diagnostic__analyzing-icon {
  font-size: 24px;
  animation: pulse 2s infinite;
}

.diagnostic__report-error {
  font-size: 15px;
  line-height: 24px;
  color: #ffe3e3;
  background: color-mix(in srgb, #b3261e 60%, transparent);
  padding: 12px 16px;
  border-radius: 8px;
  margin: 0;
}

.diagnostic__prevention {
  border-top: 1px solid color-mix(in srgb, #fff 20%, transparent);
  padding-top: 16px;
}

.diagnostic__prevention-title {
  font-size: 13px;
  font-weight: 600;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: var(--ag-primary-fixed);
  margin: 0 0 8px;
}

.diagnostic__cta:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.diagnostic__report-desc {
  font-size: 16px;
  line-height: 24px;
  line-height: 1.625;
  margin: 0;
}

.diagnostic__treatment-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.diagnostic__treatment-item {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 14px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0.05em;
}

.diagnostic__treatment-icon {
  font-size: 14px;
  color: var(--ag-primary-fixed);
}

.diagnostic__cta {
  width: 100%;
  background: #fff;
  color: var(--ag-primary);
  padding: 16px 0;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  line-height: 20px;
  letter-spacing: 0.05em;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: background 0.2s;
}

.diagnostic__cta:hover {
  background: var(--ag-primary-fixed);
}

.diagnostic__gallery {
  margin-top: 96px;
}

.diagnostic__gallery-title {
  font-family: var(--ag-font-display);
  font-size: 48px;
  font-weight: 500;
  line-height: 56px;
  letter-spacing: -0.02em;
  text-align: center;
  color: var(--ag-on-surface);
  margin: 0 0 48px;
}

.diagnostic__gallery-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 24px;
}

@media (min-width: 768px) {
  .diagnostic__gallery-grid {
    grid-template-columns: 1fr 1fr;
  }
}

@media (min-width: 1024px) {
  .diagnostic__gallery-grid {
    grid-template-columns: 1fr 1fr 1fr;
  }
}

.diagnostic__gallery-card {
  overflow: hidden;
  border-radius: 12px;
  background: #fff;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
  border: 1px solid color-mix(in srgb, var(--ag-outline-variant) 20%, transparent);
  transition: all 0.5s;
}

.diagnostic__gallery-card:hover {
  box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
}

.diagnostic__gallery-card:hover .diagnostic__gallery-image img {
  transform: scale(1.1);
}

.diagnostic__gallery-image {
  height: 256px;
  overflow: hidden;
  position: relative;
}

.diagnostic__gallery-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.7s;
}

.diagnostic__gallery-badge {
  position: absolute;
  top: 16px;
  right: 16px;
  padding: 4px 12px;
  background: color-mix(in srgb, #fff 90%, transparent);
  backdrop-filter: blur(12px);
  border-radius: 9999px;
  font-size: 12px;
  line-height: 16px;
  font-weight: 600;
  letter-spacing: 0.05em;
}

.diagnostic__gallery-badge--resolved {
  color: var(--ag-primary);
}

.diagnostic__gallery-badge--active {
  color: var(--ag-secondary);
}

.diagnostic__gallery-badge--healthy {
  color: var(--ag-primary);
}

.diagnostic__gallery-info {
  padding: 24px;
}

.diagnostic__gallery-name {
  font-family: var(--ag-font-display);
  font-size: 24px;
  font-weight: 500;
  line-height: 32px;
  color: var(--ag-on-surface);
  margin: 0 0 8px;
}

.diagnostic__gallery-desc {
  font-size: 16px;
  line-height: 24px;
  color: var(--ag-on-surface-variant);
  margin: 0;
}
</style>
