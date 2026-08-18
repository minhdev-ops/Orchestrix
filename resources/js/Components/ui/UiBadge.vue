<template>
  <span :class="['inline-flex items-center font-medium rounded-full', variantClass, sizeClass]">
    <i v-if="icon" :class="['pi', icon, 'mr-1']" />
    <slot />
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: { type: String, default: 'default' },
  size: { type: String, default: 'sm' },
  icon: { type: String, default: null },
})

const variants = {
  default: 'bg-stone-100 text-stone-700',
  primary: 'bg-primary-50 text-primary-700',
  success: 'bg-emerald-50 text-emerald-700',
  warning: 'bg-amber-50 text-amber-700',
  danger: 'bg-red-50 text-red-700',
  info: 'bg-sky-50 text-sky-700',
}

const sizes = {
  sm: 'px-2 py-0.5 text-xs',
  md: 'px-2.5 py-1 text-sm',
  lg: 'px-3 py-1.5 text-sm',
}

const variantClass = variants[props.variant] || variants.default
const sizeClass = sizes[props.size] || sizes.sm
</script>
