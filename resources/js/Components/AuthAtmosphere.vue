<template>
  <div class="absolute inset-0 overflow-hidden pointer-events-none">
    <div
      class="absolute top-[10%] right-[10%] w-[30vw] h-[30vw] border border-blue-500 rounded-full"
      :style="{ opacity: 0.1 }"
    />
    <div
      class="absolute top-20 left-[15%] w-64 h-64 bg-blue-50 blur-[120px] rounded-full"
      :style="{
        transform: `translateY(${blob1Y}px)`,
      }"
    />
    <div
      class="absolute bottom-20 right-[15%] w-80 h-80 bg-slate-100 blur-[140px] rounded-full"
      :style="{
        transform: `translateY(${blob2Y}px)`,
      }"
    />
    <div
      v-for="(particle, i) in particles"
      :key="i"
      class="absolute w-1 h-1 bg-blue-400 rounded-full"
      :style="{
        left: `${20 * i}%`,
        top: `${particle.top}%`,
        opacity: particle.opacity,
        transform: `translateY(${particle.offset}px)`,
      }"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const blob1Y = ref(0);
const blob2Y = ref(0);
const particles = ref(
  Array.from({ length: 5 }, (_, i) => ({
    top: 15 * i + 5,
    opacity: 0.3,
    offset: 0,
  }))
);

let animFrame = null;
let startTime = 0;

function animate(timestamp) {
  if (!startTime) startTime = timestamp;
  const elapsed = (timestamp - startTime) / 1000;

  blob1Y.value = Math.sin(elapsed * 0.5) * 20;
  blob2Y.value = Math.sin(elapsed * 0.4 + 1) * 20;

  particles.value.forEach((p, i) => {
    const phase = i * 1.2;
    p.offset = Math.sin(elapsed * 0.6 + phase) * 15;
    p.opacity = 0.2 + Math.sin(elapsed * 0.5 + phase) * 0.15;
  });

  animFrame = requestAnimationFrame(animate);
}

onMounted(() => {
  animFrame = requestAnimationFrame(animate);
});

onUnmounted(() => {
  if (animFrame) cancelAnimationFrame(animFrame);
});
</script>
