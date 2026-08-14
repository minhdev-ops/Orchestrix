<template>
  <div class="w-full">
    <label v-if="label" :for="inputId" class="block text-sm font-medium text-stone-700 mb-1.5">
      {{ label }}
      <span v-if="required" class="text-danger ml-0.5">*</span>
    </label>
    <div :class="['relative rounded-lg border transition-colors', wrapperClass]">
      <div v-if="$slots.prepend || prependIcon" class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <i v-if="prependIcon" :class="['pi', prependIcon, 'text-stone-400 text-sm']" />
        <slot name="prepend" />
      </div>
      <input
        :id="inputId"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        :class="[inputClass, { 'pl-9': $slots.prepend || prependIcon, 'pr-9': $slots.append || (type === 'password' && showToggle) }]"
        v-bind="$attrs"
        @input="$emit('update:modelValue', $event.target.value)"
        @blur="$emit('blur', $event)"
      />
      <div v-if="$slots.append || (type === 'password' && showToggle)" class="absolute inset-y-0 right-0 flex items-center pr-3">
        <slot name="append" />
        <button v-if="type === 'password' && showToggle" type="button" tabindex="-1" class="text-stone-400 hover:text-stone-600 focus:outline-none" @click="showPassword = !showPassword">
          <i :class="['pi', showPassword ? 'pi-eye-slash' : 'pi-eye', 'text-sm']" />
        </button>
      </div>
    </div>
    <p v-if="error" class="mt-1.5 text-sm text-danger flex items-center gap-1">
      <i class="pi pi-exclamation-circle text-xs" /> {{ error }}
    </p>
    <p v-else-if="hint" class="mt-1.5 text-sm text-stone-400">{{ hint }}</p>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  modelValue: { type: [String, Number], default: '' },
  label: { type: String, default: null },
  placeholder: { type: String, default: null },
  type: { type: String, default: 'text' },
  error: { type: String, default: null },
  hint: { type: String, default: null },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  readonly: { type: Boolean, default: false },
  prependIcon: { type: String, default: null },
  showToggle: { type: Boolean, default: false },
  size: { type: String, default: 'md' },
})

defineEmits(['update:modelValue', 'blur'])

const showPassword = ref(false)
const inputId = computed(() => `input-${Math.random().toString(36).slice(2, 9)}`)

const sizes = { sm: 'py-1.5 px-3 text-sm', md: 'py-2.5 px-3 text-sm', lg: 'py-3 px-4 text-base' }

const wrapperClass = computed(() => [
  props.error ? 'border-danger ring-1 ring-danger/20' : 'border-stone-200 focus-within:border-primary focus-within:ring-1 focus-within:ring-primary/20',
  props.disabled ? 'bg-stone-50 opacity-60' : 'bg-white',
])

const inputClass = computed(() => [
  'w-full bg-transparent text-stone-900 placeholder-stone-400 focus:outline-none',
  sizes[props.size] || sizes.md,
  props.disabled ? 'cursor-not-allowed' : '',
])
</script>
