<template>
  <div class="space-y-2">
    <div v-if="!arSupported" class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-700">
      Trình duyệt của bạn không hỗ trợ AR. Vui lòng truy cập từ thiết bị di động (Android Chrome / iOS Safari).
    </div>
    <div v-if="modelUrl" class="aspect-square rounded-2xl overflow-hidden bg-stone-50">
      <model-viewer
        :src="modelUrl"
        ar
        ar-modes="scene-viewer webxr quick-look"
        camera-controls
        auto-rotate
        class="w-full h-full"
        shadow-intensity="1"
        environment-image="neutral"
        loading="eager"
      />
    </div>
    <div v-else class="aspect-square rounded-2xl bg-stone-50 flex items-center justify-center">
      <span class="text-xs text-stone-400">Chưa có mô hình AR</span>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import '@google/model-viewer';

const props = defineProps({
  modelUrl: { type: String, default: '' },
});

const arSupported = ref(false);

function checkARSupport() {
  arSupported.value = !!(
    navigator.xr ||
    /Android|iPhone|iPad|iPod/i.test(navigator.userAgent)
  );
}

onMounted(checkARSupport);
</script>
