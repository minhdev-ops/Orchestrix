<template>
  <div class="ar-page">
    <!-- LOADING -->
    <div v-if="loading" class="loading-overlay">
      <div class="loading-spinner"></div>
      <div class="loading-text">{{ loadingText }}</div>
      <div v-if="loadError" class="loading-error">{{ loadError }}</div>
    </div>

    <!-- TOPBAR -->
    <div class="topbar">
      <button class="topbar-back" @click="goBack">
        <span class="material-symbols-outlined" style="font-size: 20px;">arrow_back</span>
      </button>
      <div class="topbar-title">{{ viewMode === '3d' ? 'Xem mô hình 3D' : 'Xem trong không gian' }}</div>
      <div v-if="viewMode === 'ar'" class="topbar-badge">AR</div>
      <div v-if="viewMode === '3d'" class="topbar-actions">
        <button class="topbar-btn" @click="resetCamera" title="Đặt lại">
          <span class="material-symbols-outlined" style="font-size: 18px;">restart_alt</span>
        </button>
        <button class="topbar-btn" @click="toggleFullscreen" title="Toàn màn hình">
          <span class="material-symbols-outlined" style="font-size: 18px;">fullscreen</span>
        </button>
      </div>
    </div>

    <!-- 3D VIEW MODE -->
    <template v-if="viewMode === '3d'">
      <div class="viewer-area">
        <model-viewer
          ref="modelViewerRef"
          :src="modelUrl"
          alt="3D Model"
          camera-controls
          touch-action="pan-y"
          auto-rotate
          auto-rotate-delay="1000"
          rotation-per-second="20deg"
          camera-orbit="45deg 70deg 120%"
          min-camera-orbit="auto auto 30%"
          max-camera-orbit="Infinity Infinity 300%"
          field-of-view="30deg"
          shadow-intensity="0.6"
          shadow-softness="0.8"
          environment-image="neutral"
          exposure="1.0"
          style="background: transparent;"
          @load="onModelLoaded"
        />

        <div class="viewer-controls">
          <button class="ctrl-btn" :class="{ active: autoRotating }" @click="toggleAutoRotate" title="Tự xoay">
            <span class="material-symbols-outlined" style="font-size: 18px;">360</span>
          </button>
          <button class="ctrl-btn" @click="zoomIn" title="Phóng to">
            <span class="material-symbols-outlined" style="font-size: 18px;">zoom_in</span>
          </button>
          <button class="ctrl-btn" @click="zoomOut" title="Thu nhỏ">
            <span class="material-symbols-outlined" style="font-size: 18px;">zoom_out</span>
          </button>
          <button class="ctrl-btn" @click="resetCamera" title="Đặt lại">
            <span class="material-symbols-outlined" style="font-size: 18px;">restart_alt</span>
          </button>
        </div>

        <div class="viewer-badge">Kéo để xoay · Cuộn để zoom · Chuột phải để di chuyển</div>
      </div>

      <!-- SIDEBAR -->
      <div class="sidebar">
        <div class="sidebar-header">
          <div class="sidebar-tag">3D MODEL</div>
          <h1 class="sidebar-name">{{ product.name || 'Cây Bonsai' }}</h1>
          <div class="sidebar-store" v-if="product.store">
            <div class="store-avatar">{{ product.store.name?.charAt(0) || 'V' }}</div>
            {{ product.store.name }}
          </div>
        </div>

        <div class="sidebar-stats">
          <div class="stat">
            <div class="stat-val">GLB</div>
            <div class="stat-label">Định dạng</div>
          </div>
          <div class="stat">
            <div class="stat-val">{{ modelSize }}</div>
            <div class="stat-label">Kích thước</div>
          </div>
          <div class="stat">
            <div class="stat-val">360°</div>
            <div class="stat-label">Xoay tự do</div>
          </div>
        </div>

        <div class="sidebar-info">
          <div class="info-title">Thông tin</div>
          <div class="info-grid">
            <div class="info-item">
              <div class="info-key">Kích thước</div>
              <div class="info-val">{{ product.height || '30cm' }}</div>
            </div>
            <div class="info-item">
              <div class="info-key">Giá</div>
              <div class="info-val">{{ formatPrice(product.price) }}₫</div>
            </div>
            <div class="info-item">
              <div class="info-key">Tình trạng</div>
              <div class="info-val">{{ product.stock > 0 ? 'Còn hàng' : 'Hết hàng' }}</div>
            </div>
            <div class="info-item">
              <div class="info-key">Danh mục</div>
              <div class="info-val">{{ product.categories?.[0]?.name || 'Cây cảnh' }}</div>
            </div>
          </div>
        </div>

        <div class="sidebar-desc" v-if="product.description">
          {{ product.description }}
        </div>

        <div class="sidebar-actions">
          <button class="action-btn primary" @click="switchToAR">
            <span class="material-symbols-outlined" style="font-size: 18px;">view_in_ar</span>
            Xem trong không gian
          </button>
          <Link v-if="product.id" :href="route('agriverse.shop.products.show', product.id)" class="action-btn outline">
            <span class="material-symbols-outlined" style="font-size: 18px;">arrow_back</span>
            Quay lại
          </Link>
        </div>
      </div>
    </template>

    <!-- AR CAMERA MODE -->
    <template v-if="viewMode === 'ar'">
      <video ref="videoRef" autoplay playsinline muted class="ar-video"></video>
      <canvas ref="threeCanvasRef" class="ar-canvas"></canvas>

      <button class="gyro-btn" :class="{ active: gyroEnabled }" @click="toggleGyro" title="Bật/tắt gyro">
        <span class="material-symbols-outlined" style="font-size: 22px;">screen_rotation</span>
      </button>

      <div class="ar-bottom">
        <div class="gyro-status" :class="{ active: gyroEnabled }">
          {{ gyroEnabled ? 'Gyro đang hoạt động - xoay thiết bị để xem' : 'Nhấn nút để xoay theo thiết bị' }}
        </div>
        <div class="ar-info-bar">
          <div class="ar-info">
            <div class="ar-name">{{ product.name || 'Cây Bonsai' }}</div>
            <div class="ar-price">{{ formatPrice(product.price) }}₫</div>
          </div>
          <button class="ar-detail-btn" @click="switchTo3D">
            <span class="material-symbols-outlined" style="font-size: 16px;">view_in_ar</span>
            Xem 3D
          </button>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch, nextTick } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import '@google/model-viewer';

const props = defineProps({
  product: { type: Object, default: () => ({}) },
  modelUrl: { type: String, default: '/models/bonsai.glb' },
});

const page = usePage();

const viewMode = ref('3d');
const loading = ref(true);
const loadingText = ref('Đang tải mô hình 3D...');
const loadError = ref('');
const autoRotating = ref(true);
const modelViewerRef = ref(null);
const modelSize = ref('27MB');

const videoRef = ref(null);
const threeCanvasRef = ref(null);

let threeScene = null;
let threeCamera = null;
let threeRenderer = null;
let threeAnimFrame = null;
let camDist = 0.8;
let camTheta = Math.PI / 6;
let camPhi = Math.PI / 3;
let cameraTarget = null;

const gyroEnabled = ref(false);
let gyroAlpha = 0, gyroBeta = 0, gyroGamma = 0;
let gyroRefAlpha = null, gyroRefBeta = null, gyroRefGamma = null;
let gyroStream = null;

function goBack() {
  if (viewMode.value === 'ar') {
    viewMode.value = '3d';
    cleanupAR();
    return;
  }
  window.history.back();
}

function formatPrice(price) {
  return new Intl.NumberFormat('vi-VN').format(price || 0);
}

function onModelLoaded() {
  loading.value = false;
  loadingText.value = '';
}

function resetCamera() {
  const mv = modelViewerRef.value;
  if (mv) {
    mv.cameraOrbit = '45deg 70deg 120%';
    mv.fieldOfView = '30deg';
  }
}

function toggleAutoRotate() {
  autoRotating.value = !autoRotating.value;
  const mv = modelViewerRef.value;
  if (mv) mv.autoRotate = autoRotating.value;
}

function zoomIn() {
  const mv = modelViewerRef.value;
  if (mv) mv.fieldOfView = Math.max(10, parseFloat(mv.getFieldOfView()) - 5) + 'deg';
}

function zoomOut() {
  const mv = modelViewerRef.value;
  if (mv) mv.fieldOfView = Math.min(90, parseFloat(mv.getFieldOfView()) + 5) + 'deg';
}

function toggleFullscreen() {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen();
  } else {
    document.exitFullscreen();
  }
}

async function switchToAR() {
  viewMode.value = 'ar';
  await nextTick();
  initAR();
}

function switchTo3D() {
  viewMode.value = '3d';
  cleanupAR();
}

async function initAR() {
  loading.value = true;
  loadingText.value = 'Đang mở camera...';
  loadError.value = '';

  try {
    const isMobileAR = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
    const stream = await navigator.mediaDevices.getUserMedia({
      video: {
        facingMode: 'environment',
        width: { ideal: isMobileAR ? 640 : 1280 },
        height: { ideal: isMobileAR ? 480 : 720 },
      }
    });
    gyroStream = stream;
    if (videoRef.value) {
      videoRef.value.srcObject = stream;
      await videoRef.value.play();
    }
  } catch (err) {
    loadError.value = 'Không mở được camera: ' + err.message;
    loading.value = false;
    return;
  }

  try {
    const THREE = await import('three');
    const { GLTFLoader } = await import('three/addons/loaders/GLTFLoader.js');

    const canvas = threeCanvasRef.value;
    threeScene = new THREE.Scene();

    threeCamera = new THREE.PerspectiveCamera(50, window.innerWidth / window.innerHeight, 0.01, 100);
    cameraTarget = new THREE.Vector3(0, 0.15, 0);

    threeCamera.position.set(
      camDist * Math.sin(camPhi) * Math.sin(camTheta),
      camDist * Math.cos(camPhi),
      camDist * Math.sin(camPhi) * Math.cos(camTheta)
    );
    threeCamera.lookAt(cameraTarget);

    const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent) || window.innerWidth < 768;
    threeRenderer = new THREE.WebGLRenderer({
      canvas,
      alpha: true,
      antialias: !isMobile,
      powerPreference: isMobile ? 'low-power' : 'high-performance',
    });
    threeRenderer.setSize(window.innerWidth, window.innerHeight);
    threeRenderer.setPixelRatio(isMobile ? Math.min(window.devicePixelRatio, 1.5) : Math.min(window.devicePixelRatio, 2));
    threeRenderer.outputColorSpace = THREE.SRGBColorSpace;
    threeRenderer.toneMapping = THREE.ACESFilmicToneMapping;
    threeRenderer.toneMappingExposure = isMobile ? 1.0 : 1.1;
    threeRenderer.shadowMap.enabled = !isMobile;
    if (!isMobile) {
      threeRenderer.shadowMap.type = THREE.PCFSoftShadowMap;
    }

    threeScene.add(new THREE.AmbientLight(0xfff5e6, 0.4));

    const sunLight = new THREE.DirectionalLight(0xfff8ee, 1.2);
    sunLight.position.set(2, 3, 1.5);
    if (!isMobile) {
      sunLight.castShadow = true;
      sunLight.shadow.mapSize.set(1024, 1024);
      sunLight.shadow.camera.near = 0.1;
      sunLight.shadow.camera.far = 10;
      sunLight.shadow.camera.left = -1;
      sunLight.shadow.camera.right = 1;
      sunLight.shadow.camera.top = 1;
      sunLight.shadow.camera.bottom = -1;
      sunLight.shadow.bias = -0.001;
      sunLight.shadow.radius = 4;
    }
    threeScene.add(sunLight);

    threeScene.add(new THREE.HemisphereLight(0xc9daf8, 0xd4c4a8, 0.5));

    if (!isMobile) {
      const fillLight = new THREE.DirectionalLight(0xd4e5f7, 0.3);
      fillLight.position.set(-1.5, 1, -1);
      threeScene.add(fillLight);

      const shadowMat = new THREE.ShadowMaterial({ opacity: 0.25 });
      const shadowPlane = new THREE.Mesh(new THREE.PlaneGeometry(2, 2), shadowMat);
      shadowPlane.rotation.x = -Math.PI / 2;
      shadowPlane.position.y = 0.001;
      shadowPlane.receiveShadow = true;
      threeScene.add(shadowPlane);
    }

    const loader = new GLTFLoader();
    loader.load(props.modelUrl, (gltf) => {
      const model = gltf.scene;
      const REAL_HEIGHT_M = 0.30;
      const MODEL_HEIGHT = 98.739;
      const scaleFactor = REAL_HEIGHT_M / MODEL_HEIGHT;
      model.scale.set(scaleFactor, scaleFactor, scaleFactor);

      const box = new THREE.Box3().setFromObject(model);
      model.position.y -= box.min.y;

      model.traverse((child) => {
        if (child.isMesh) {
          if (!isMobile) {
            child.castShadow = true;
            child.receiveShadow = true;
          }
          if (isMobile && child.material) {
            child.material.flatShading = true;
          }
        }
      });

      threeScene.add(model);
      loading.value = false;

      let dragging = false;
      let lastX = 0, lastY = 0;

      canvas.addEventListener('pointerdown', (e) => {
        dragging = true;
        lastX = e.clientX;
        lastY = e.clientY;
      });
      canvas.addEventListener('pointermove', (e) => {
        if (!dragging) return;
        const dx = (e.clientX - lastX) * 0.005;
        const dy = (e.clientY - lastY) * 0.005;
        lastX = e.clientX;
        lastY = e.clientY;
        camTheta -= dx;
        camPhi = Math.max(0.2, Math.min(Math.PI - 0.2, camPhi - dy));
      });
      canvas.addEventListener('pointerup', () => { dragging = false; });
      canvas.addEventListener('pointerleave', () => { dragging = false; });

      canvas.addEventListener('wheel', (e) => {
        camDist = Math.max(0.3, Math.min(2.0, camDist + e.deltaY * 0.001));
        e.preventDefault();
      }, { passive: false });

      function animate() {
        threeAnimFrame = requestAnimationFrame(animate);

        if (gyroEnabled.value && gyroRefAlpha !== null) {
          const dAlpha = (gyroAlpha - gyroRefAlpha) * Math.PI / 180;
          const dBeta = (gyroBeta - gyroRefBeta) * Math.PI / 180;
          camTheta = -dAlpha;
          camPhi = Math.PI / 3 + dBeta * 0.5;
          camPhi = Math.max(0.2, Math.min(Math.PI - 0.2, camPhi));
        }

        threeCamera.position.set(
          cameraTarget.x + camDist * Math.sin(camPhi) * Math.sin(camTheta),
          cameraTarget.y + camDist * Math.cos(camPhi),
          cameraTarget.z + camDist * Math.sin(camPhi) * Math.cos(camTheta)
        );
        threeCamera.lookAt(cameraTarget);

        threeRenderer.render(threeScene, threeCamera);
      }
      animate();
    }, undefined, () => {
      loadError.value = 'Lỗi tải mô hình 3D';
      loading.value = false;
    });

    window.addEventListener('resize', () => {
      if (threeCamera && threeRenderer) {
        threeCamera.aspect = window.innerWidth / window.innerHeight;
        threeCamera.updateProjectionMatrix();
        threeRenderer.setSize(window.innerWidth, window.innerHeight);
      }
    });
  } catch (err) {
    loadError.value = 'Lỗi khởi tạo 3D: ' + err.message;
    loading.value = false;
  }
}

function cleanupAR() {
  if (threeAnimFrame) {
    cancelAnimationFrame(threeAnimFrame);
    threeAnimFrame = null;
  }
  if (threeRenderer) {
    threeRenderer.dispose();
    threeRenderer = null;
  }
  threeScene = null;
  threeCamera = null;

  if (gyroStream) {
    gyroStream.getTracks().forEach(t => t.stop());
    gyroStream = null;
  }
  if (gyroEnabled.value) {
    window.removeEventListener('deviceorientation', handleOrientation);
    gyroEnabled.value = false;
  }
}

function handleOrientation(e) {
  gyroAlpha = e.alpha || 0;
  gyroBeta = e.beta || 0;
  gyroGamma = e.gamma || 0;
  if (gyroRefAlpha === null) {
    gyroRefAlpha = gyroAlpha;
    gyroRefBeta = gyroBeta;
    gyroRefGamma = gyroGamma;
  }
}

async function toggleGyro() {
  if (!gyroEnabled.value) {
    if (typeof DeviceOrientationEvent !== 'undefined' &&
        typeof DeviceOrientationEvent.requestPermission === 'function') {
      try {
        const p = await DeviceOrientationEvent.requestPermission();
        if (p !== 'granted') return;
      } catch { return; }
    }
    window.addEventListener('deviceorientation', handleOrientation);
    gyroEnabled.value = true;
    gyroRefAlpha = null;
  } else {
    window.removeEventListener('deviceorientation', handleOrientation);
    gyroEnabled.value = false;
  }
}

onBeforeUnmount(() => {
  cleanupAR();
});
</script>

<style scoped>
.ar-page {
  width: 100vw;
  height: 100vh;
  overflow: hidden;
  background: var(--ag-bg);
  display: flex;
  flex-direction: column;
  position: relative;
}

/* LOADING */
.loading-overlay {
  position: fixed;
  inset: 0;
  background: var(--ag-bg);
  z-index: 200;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  transition: opacity 0.5s;
}
.loading-overlay:empty,
.loading-spinner + .loading-text:empty { opacity: 0; }
.loading-spinner {
  width: 36px;
  height: 36px;
  border: 3px solid color-mix(in srgb, var(--ag-primary-300) 20%, transparent);
  border-top-color: var(--ag-primary-500);
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.loading-text {
  margin-top: 14px;
  font: 500 13px/1 var(--ag-font-body);
  color: var(--ag-text-secondary);
}
.loading-error {
  margin-top: 16px;
  font: 500 14px/1.4 var(--ag-font-body);
  color: var(--ag-danger, #dc2626);
  padding: 0 20px;
  text-align: center;
}

/* TOPBAR */
.topbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 100;
  height: 56px;
  background: color-mix(in srgb, var(--ag-bg) 85%, transparent);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border-bottom: 1px solid color-mix(in srgb, var(--ag-border) 50%, transparent);
  display: flex;
  align-items: center;
  padding: 0 16px;
  gap: 12px;
}
.topbar-back {
  width: 36px;
  height: 36px;
  background: color-mix(in srgb, var(--ag-primary-500) 8%, transparent);
  border: 1px solid color-mix(in srgb, var(--ag-border) 30%, transparent);
  border-radius: 50%;
  color: var(--ag-text-primary);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.topbar-back:hover {
  background: color-mix(in srgb, var(--ag-primary-500) 14%, transparent);
  border-color: var(--ag-primary-500);
}
.topbar-title {
  flex: 1;
  font: 600 15px/1 var(--ag-font-body);
  color: var(--ag-text-primary);
}
.topbar-badge {
  font: 600 10px/1 var(--ag-font-body);
  color: white;
  background: var(--ag-primary-500);
  border-radius: 8px;
  padding: 5px 10px;
}
.topbar-actions {
  display: flex;
  gap: 6px;
}
.topbar-btn {
  width: 36px;
  height: 36px;
  background: color-mix(in srgb, var(--ag-primary-500) 8%, transparent);
  border: 1px solid color-mix(in srgb, var(--ag-border) 30%, transparent);
  border-radius: 8px;
  color: var(--ag-text-secondary);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.topbar-btn:hover {
  background: color-mix(in srgb, var(--ag-primary-500) 14%, transparent);
  color: var(--ag-primary-500);
  border-color: var(--ag-primary-500);
}

/* 3D VIEWER */
.viewer-area {
  flex: 1;
  position: relative;
  background: radial-gradient(ellipse at center, var(--ag-surface-container-low) 0%, var(--ag-bg) 70%);
  margin-top: 56px;
}
.viewer-area model-viewer {
  width: 100%;
  height: 100%;
  --poster-color: transparent;
}
.viewer-controls {
  position: absolute;
  bottom: 16px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 6px;
  background: color-mix(in srgb, var(--ag-bg) 80%, transparent);
  backdrop-filter: blur(12px);
  border: 1px solid color-mix(in srgb, var(--ag-border) 40%, transparent);
  border-radius: 12px;
  padding: 6px;
}
.ctrl-btn {
  width: 40px;
  height: 40px;
  background: transparent;
  border: 1px solid transparent;
  border-radius: 8px;
  color: var(--ag-text-secondary);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.ctrl-btn:hover {
  background: color-mix(in srgb, var(--ag-primary-500) 8%, transparent);
  color: var(--ag-primary-500);
}
.ctrl-btn.active {
  background: var(--ag-primary-500);
  color: white;
  border-color: var(--ag-primary-500);
}
.viewer-badge {
  position: absolute;
  bottom: 16px;
  right: 16px;
  font: 400 11px/1 var(--ag-font-body);
  color: var(--ag-text-muted);
  background: color-mix(in srgb, var(--ag-bg) 60%, transparent);
  padding: 5px 10px;
  border-radius: 6px;
  border: 1px solid color-mix(in srgb, var(--ag-border) 30%, transparent);
}

/* SIDEBAR */
.sidebar {
  width: 100%;
  max-height: 40vh;
  overflow-y: auto;
  background: var(--ag-bg);
  border-top: 1px solid var(--ag-border);
  padding-bottom: 20px;
}
@media (min-width: 1024px) {
  .ar-page { flex-direction: row; }
  .viewer-area {
    flex: 1;
    margin-top: 0;
    height: 100vh;
  }
  .sidebar {
    width: 360px;
    max-height: 100vh;
    border-top: none;
    border-left: 1px solid var(--ag-border);
    flex-shrink: 0;
  }
}
.sidebar-header {
  padding: 24px 24px 20px;
  border-bottom: 1px solid color-mix(in srgb, var(--ag-border) 50%, transparent);
}
.sidebar-tag {
  display: inline-block;
  background: color-mix(in srgb, var(--ag-primary-500) 10%, transparent);
  color: var(--ag-primary-500);
  font: 600 10px/1 var(--ag-font-body);
  padding: 4px 10px;
  border-radius: 6px;
  margin-bottom: 10px;
  letter-spacing: 0.5px;
}
.sidebar-name {
  font-family: var(--ag-font-display);
  font-size: 22px;
  font-weight: 500;
  line-height: 1.2;
  color: var(--ag-text-primary);
  margin-bottom: 8px;
}
.sidebar-store {
  display: flex;
  align-items: center;
  gap: 8px;
  font: 400 13px/1 var(--ag-font-body);
  color: var(--ag-text-muted);
}
.store-avatar {
  width: 22px;
  height: 22px;
  background: var(--ag-primary-500);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font: 600 10px/1 var(--ag-font-body);
  color: white;
}
.sidebar-stats {
  display: flex;
  padding: 16px 24px;
  gap: 16px;
  border-bottom: 1px solid color-mix(in srgb, var(--ag-border) 50%, transparent);
}
.sidebar-stats .stat {
  text-align: center;
  flex: 1;
}
.sidebar-stats .stat-val {
  font: 700 16px/1 var(--ag-font-body);
  color: var(--ag-text-primary);
}
.sidebar-stats .stat-label {
  font: 400 11px/1 var(--ag-font-body);
  color: var(--ag-text-muted);
  margin-top: 4px;
}
.sidebar-info {
  padding: 16px 24px;
  border-bottom: 1px solid color-mix(in srgb, var(--ag-border) 50%, transparent);
}
.info-title {
  font: 600 11px/1 var(--ag-font-body);
  color: var(--ag-text-muted);
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-bottom: 12px;
}
.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}
.info-item {
  background: var(--ag-surface-container-low);
  border-radius: 10px;
  padding: 10px 12px;
}
.info-key {
  font: 400 11px/1 var(--ag-font-body);
  color: var(--ag-text-muted);
  margin-bottom: 4px;
}
.info-val {
  font: 600 13px/1 var(--ag-font-body);
  color: var(--ag-text-primary);
}
.sidebar-desc {
  padding: 16px 24px;
  font: 400 13px/1.6 var(--ag-font-body);
  color: var(--ag-text-secondary);
  border-bottom: 1px solid color-mix(in srgb, var(--ag-border) 50%, transparent);
}
.sidebar-actions {
  padding: 20px 24px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.action-btn {
  width: 100%;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  border-radius: 10px;
  font: 600 14px/1 var(--ag-font-body);
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
}
.action-btn.primary {
  background: var(--ag-primary-500);
  color: white;
  border: none;
}
.action-btn.primary:hover {
  background: var(--ag-primary-600);
  box-shadow: 0 4px 14px -2px color-mix(in srgb, var(--ag-primary-500) 40%, transparent);
}
.action-btn.outline {
  background: transparent;
  color: var(--ag-text-secondary);
  border: 1px solid var(--ag-border);
}
.action-btn.outline:hover {
  background: color-mix(in srgb, var(--ag-primary-500) 6%, transparent);
  border-color: var(--ag-primary-500);
  color: var(--ag-primary-500);
}

/* AR MODE */
.ar-video {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  z-index: 0;
}
.ar-canvas {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1;
}
.gyro-btn {
  position: fixed;
  bottom: 120px;
  right: 16px;
  z-index: 100;
  width: 48px;
  height: 48px;
  background: color-mix(in srgb, var(--ag-bg) 60%, transparent);
  backdrop-filter: blur(10px);
  border: 1px solid color-mix(in srgb, var(--ag-border) 40%, transparent);
  border-radius: 50%;
  color: var(--ag-text-primary);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.gyro-btn.active {
  background: color-mix(in srgb, var(--ag-primary-500) 30%, transparent);
  border-color: var(--ag-primary-500);
  color: var(--ag-primary-500);
}
.ar-bottom {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 100;
  background: linear-gradient(0deg, rgba(0,0,0,0.7) 0%, transparent 100%);
  padding: 20px 16px 24px;
}
.gyro-status {
  text-align: center;
  margin-bottom: 8px;
  font: 400 11px/1 var(--ag-font-body);
  color: rgba(255,255,255,0.4);
}
.gyro-status.active {
  color: var(--ag-primary-300);
}
.ar-info-bar {
  background: rgba(255,255,255,0.95);
  backdrop-filter: blur(10px);
  border-radius: 14px;
  padding: 14px 16px;
  display: flex;
  align-items: center;
  gap: 12px;
}
.ar-info {
  flex: 1;
}
.ar-name {
  font: 600 14px/1.2 var(--ag-font-body);
  color: var(--ag-text-primary);
}
.ar-price {
  font: 700 15px/1 var(--ag-font-body);
  color: var(--ag-primary-500);
  margin-top: 2px;
}
.ar-detail-btn {
  background: var(--ag-primary-500);
  color: white;
  border: none;
  border-radius: 10px;
  padding: 10px 16px;
  font: 600 13px/1 var(--ag-font-body);
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: all 0.2s;
}
.ar-detail-btn:hover {
  background: var(--ag-primary-600);
}
</style>
