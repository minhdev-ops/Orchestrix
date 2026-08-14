<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="close">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" @click="close" />
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-hidden">
          <div v-if="$slots.header || title" class="flex items-center justify-between px-6 py-4 border-b border-stone-100">
            <h3 class="text-lg font-semibold text-stone-900">{{ title }}</h3>
            <button type="button" class="p-1 text-stone-400 hover:text-stone-600 rounded-lg hover:bg-stone-100 transition-colors" @click="close">
              <i class="pi pi-times text-lg" />
            </button>
          </div>
          <div class="px-6 py-4 overflow-y-auto">
            <slot />
          </div>
          <div v-if="$slots.footer" class="flex items-center justify-end gap-3 px-6 py-4 border-t border-stone-100 bg-stone-50">
            <slot name="footer" />
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
const props = defineProps({
  modelValue: { type: Boolean, default: false },
  title: { type: String, default: null },
  closable: { type: Boolean, default: true },
})

const emit = defineEmits(['update:modelValue'])

function close() {
  if (props.closable) emit('update:modelValue', false)
}
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: all 0.2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
.modal-enter-from > div:last-child, .modal-leave-to > div:last-child { transform: scale(0.95); }
</style>
