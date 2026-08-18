<template>
  <Head :title="'Nhóm: ' + group.name" />

  <div class="space-y-6">
    <div class="flex items-center gap-3">
      <Link :href="route('admin.agriverse.chat-groups.index')" class="text-gray-400 hover:text-gray-600">
        <i class="pi pi-arrow-left text-xl" />
      </Link>
      <h1 class="text-2xl font-bold text-gray-900">{{ group.name }}</h1>
      <UiBadge :variant="group.type === 'support' ? 'primary' : group.type === 'general' ? 'info' : 'success'">{{ group.type }}</UiBadge>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <UiCard>
        <template #header><h2 class="font-semibold text-gray-900">Thông tin</h2></template>
        <div class="space-y-3 text-sm">
          <div class="flex justify-between"><span class="text-gray-500">Tên:</span><span class="font-medium">{{ group.name }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Loại:</span><span>{{ group.type }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Kích hoạt:</span><i :class="group.is_active ? 'pi pi-check text-emerald-500' : 'pi pi-times text-red-400'" /></div>
          <div class="flex justify-between"><span class="text-gray-500">Ngày tạo:</span><span>{{ group.created_at }}</span></div>
        </div>
      </UiCard>

      <UiCard>
        <template #header><h2 class="font-semibold text-gray-900">Thành viên ({{ group.members?.length || 0 }})</h2></template>
        <div v-if="group.members?.length" class="space-y-2">
          <div v-for="member in group.members" :key="member.id" class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg">
            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
              {{ member.user?.name?.charAt(0) || '?' }}
            </div>
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-900">{{ member.user?.name }}</p>
              <p class="text-xs text-gray-400">{{ member.role || 'member' }}</p>
            </div>
          </div>
        </div>
        <p v-else class="text-sm text-gray-400">Chưa có thành viên.</p>
      </UiCard>

      <UiCard>
        <template #header><h2 class="font-semibold text-gray-900">Thống kê</h2></template>
        <div class="space-y-3 text-sm">
          <div class="flex justify-between"><span class="text-gray-500">Số thành viên:</span><span class="font-medium">{{ group.members_count || 0 }}</span></div>
          <div class="flex justify-between"><span class="text-gray-500">Tin nhắn:</span><span class="font-medium">{{ group.messages_count || 0 }}</span></div>
        </div>
      </UiCard>
    </div>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'
import UiBadge from '@/Components/ui/UiBadge.vue'

const props = defineProps({
  group: { type: Object, required: true },
})
</script>

<style scoped>
</style>
