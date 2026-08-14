<template>
  <Head :title="post.title" />

  <div class="space-y-6">
    <div class="flex items-center gap-3">
      <Link :href="route('admin.agriverse.forum.index')" class="text-gray-400 hover:text-gray-600">
        <i class="pi pi-arrow-left text-xl" />
      </Link>
      <h1 class="text-2xl font-bold text-gray-900">{{ post.title }}</h1>
      <UiBadge :variant="post.status === 'published' ? 'success' : 'warning'">{{ post.status }}</UiBadge>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2 space-y-6">
        <UiCard>
          <template #header>
            <div class="flex items-center justify-between">
              <h2 class="font-semibold text-gray-900">Nội dung</h2>
              <span class="text-xs text-gray-400"><i class="pi pi-eye mr-1" />{{ post.views || 0 }} lượt xem</span>
            </div>
          </template>
          <div class="text-sm text-gray-700 whitespace-pre-line">{{ post.content }}</div>
        </UiCard>

        <UiCard v-if="post.comments?.length">
          <template #header><h2 class="font-semibold text-gray-900">Bình luận ({{ post.comments.length }})</h2></template>
          <div class="space-y-4">
            <div v-for="comment in post.comments" :key="comment.id" class="flex gap-3 p-3 bg-gray-50 rounded-lg">
              <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 flex-shrink-0">
                {{ comment.user?.name?.charAt(0) || '?' }}
              </div>
              <div class="flex-1">
                <div class="flex items-center gap-2">
                  <span class="text-sm font-medium text-gray-900">{{ comment.user?.name }}</span>
                  <span class="text-xs text-gray-400">{{ comment.created_at }}</span>
                </div>
                <p class="text-sm text-gray-700 mt-1">{{ comment.content }}</p>
                <div v-if="comment.status !== 'approved'" class="mt-2">
                  <UiBadge variant="warning">Chờ duyệt</UiBadge>
                  <button class="text-xs text-emerald-600 hover:text-emerald-800 ml-2" @click="approveComment(comment.id)">Duyệt</button>
                </div>
              </div>
            </div>
          </div>
        </UiCard>
      </div>

      <div class="space-y-6">
        <UiCard>
          <template #header><h2 class="font-semibold text-gray-900">Thông tin</h2></template>
          <div class="space-y-3 text-sm">
            <div><span class="text-gray-500">Tác giả:</span><p class="font-medium">{{ post.user?.name }}</p></div>
            <div><span class="text-gray-500">Danh mục:</span><p>{{ post.category?.name || '—' }}</p></div>
            <div><span class="text-gray-500">Trạng thái:</span><UiBadge :variant="post.status === 'published' ? 'success' : 'warning'">{{ post.status }}</UiBadge></div>
            <div><span class="text-gray-500">Ghim:</span><i :class="post.is_pinned ? 'pi pi-check text-emerald-500' : 'pi pi-times text-gray-400'" /></div>
            <div><span class="text-gray-500">Ngày tạo:</span><p>{{ post.created_at }}</p></div>
          </div>
        </UiCard>

        <div class="flex flex-col gap-2">
          <Button v-if="post.status === 'published'" severity="warn" @click="toggleStatus('draft')">
            <i class="pi pi-file-edit mr-1" /> Chuyển nháp
          </Button>
          <Button v-else severity="success" @click="toggleStatus('published')">
            <i class="pi pi-check-circle mr-1" /> Đăng bài
          </Button>
          <Button severity="danger" @click="deletePost">
            <i class="pi pi-trash mr-1" /> Xóa bài viết
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'
import UiBadge from '@/Components/ui/UiBadge.vue'

const props = defineProps({
  post: { type: Object, required: true },
})

function toggleStatus(status) {
  router.put(route('admin.agriverse.forum.update-status', props.post.id), { status })
}

function approveComment(id) {
  router.put(route('admin.agriverse.forum.approve-comment', id))
}

function deletePost() {
  // TODO: Replace with PrimeVue useConfirm() for non-blocking UX
  if (confirm('Xóa bài viết này?')) {
    router.delete(route('admin.agriverse.forum.destroy', props.post.id))
  }
}
</script>

<style scoped>
</style>
