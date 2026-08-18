<template>
  <div class="overflow-x-auto rounded-xl border border-stone-200">
    <table class="min-w-full divide-y divide-stone-200">
      <thead class="bg-stone-50">
        <tr>
          <th
            v-for="col in columns"
            :key="col.key"
            :class="['px-4 py-3 text-left text-xs font-semibold text-stone-500 uppercase tracking-wider', col.class]"
            @click="col.sortable !== false && sort(col.key)"
          >
            <span class="inline-flex items-center gap-1 cursor-pointer select-none" :class="{ 'text-primary': sortKey === col.key }">
              {{ col.label }}
              <i v-if="col.sortable !== false && sortKey === col.key" :class="['pi text-xs', sortDir === 'asc' ? 'pi-sort-amount-up-alt' : 'pi-sort-amount-down']" />
            </span>
          </th>
          <th v-if="$slots.actions" class="px-4 py-3 text-right text-xs font-semibold text-stone-500 uppercase tracking-wider">
            <slot name="actions-header" />
          </th>
        </tr>
      </thead>
      <tbody class="divide-y divide-stone-100 bg-white">
        <tr v-for="(row, i) in sortedRows" :key="rowKey ? row[rowKey] : i" class="hover:bg-stone-50 transition-colors" :class="{ 'opacity-60': row.disabled }">
          <td v-for="col in columns" :key="col.key" :class="['px-4 py-3 text-sm text-stone-700', col.cellClass]">
            <slot :name="`cell-${col.key}`" :row="row" :value="resolve(row, col.key)">
              {{ resolve(row, col.key) }}
            </slot>
          </td>
          <td v-if="$slots.actions" class="px-4 py-3 text-right">
            <slot name="actions" :row="row" />
          </td>
        </tr>
        <tr v-if="!rows.length">
          <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-4 py-8 text-center text-stone-400">
            <slot name="empty"><i class="pi pi-inbox mr-2" />Không có dữ liệu</slot>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  columns: { type: Array, required: true },
  rows: { type: Array, default: () => [] },
  rowKey: { type: String, default: null },
})

const sortKey = ref(null)
const sortDir = ref('asc')

function resolve(obj, path) {
  return path.split('.').reduce((acc, part) => acc?.[part], obj)
}

function sort(key) {
  if (sortKey.value === key) {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortKey.value = key
    sortDir.value = 'asc'
  }
}

const sortedRows = computed(() => {
  if (!sortKey.value) return props.rows
  return [...props.rows].sort((a, b) => {
    const aVal = resolve(a, sortKey.value)
    const bVal = resolve(b, sortKey.value)
    if (aVal == null) return 1
    if (bVal == null) return -1
    const cmp = typeof aVal === 'string' ? aVal.localeCompare(bVal) : aVal - bVal
    return sortDir.value === 'asc' ? cmp : -cmp
  })
})
</script>
