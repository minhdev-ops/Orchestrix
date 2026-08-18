<template>
  <component
    :is="tag"
    :class="[baseClass, variantClass, sizeClass, { 'opacity-50 cursor-not-allowed': disabled }]"
    v-bind="attrs"
    @click="$emit('click', $event)"
  >
    <i v-if="loading" class="pi pi-spin pi-spinner mr-2" />
    <slot />
  </component>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: { type: String, default: 'primary' },
  size: { type: String, default: 'md' },
  disabled: { type: Boolean, default: false },
  loading: { type: Boolean, default: false },
  href: { type: String, default: null },
})

defineEmits(['click'])

const tag = computed(() => props.href ? 'a' : 'button')
const attrs = computed(() => props.href ? { href: props.href } : { type: 'button', disabled: props.disabled || props.loading })

const baseClass = 'inline-flex items-center justify-center font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2'

const variants = {
  primary: 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500',
  secondary: 'bg-gray-100 text-gray-700 hover:bg-gray-200 focus:ring-gray-500',
  danger: 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
  ghost: 'text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:ring-gray-500',
  outline: 'border border-emerald-600 text-emerald-600 hover:bg-emerald-50 focus:ring-emerald-500',
}

const sizes = {
  sm: 'px-3 py-1.5 text-sm',
  md: 'px-4 py-2 text-sm',
  lg: 'px-6 py-3 text-base',
}

const variantClass = variants[props.variant] || variants.primary
const sizeClass = sizes[props.size] || sizes.md
</script>
