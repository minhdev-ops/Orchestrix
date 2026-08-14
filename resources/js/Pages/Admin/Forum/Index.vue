<template>
  <Head title="Quản lý diễn đàn - AgriVerse Admin" />

  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Quản lý diễn đàn</h1>
    </div>

    <UiCard>
      <DataTable :value="posts.data" striped-rows paginator :rows="10" :total-records="posts.total" :first="offset" @page="onPage" class="text-sm">
        <Column field="id" header="ID" sortable style="width:80px" />
        <Column field="title" header="Tiêu đề" sortable>
          <template #body="{ data }">
            <Link :href="route('admin.agriverse.forum.show', data.id)" class="text-emerald-600 hover:text-emerald-800 font-medium">{{ data.title }}</Link>
          </template>
        </Column>
        <Column field="user.name" header="Tác giả" sortable />
        <Column field="category.name" header="Danh mục" sortable />
        <Column field="comments_count" header="Bình luận" sortable>
          <template #body="{ data }">
            <span class="flex items-center gap-1"><i class="pi pi-comment text-gray-400" /> {{ data.comments_count || 0 }}</span>
          </template>
        </Column>
        <Column field="views" header="Lượt xem" sortable>
          <template #body="{ data }">
            <span class="flex items-center gap-1"><i class="pi pi-eye text-gray-400" /> {{ data.views || 0 }}</span>
          </template>
        </Column>
        <Column field="is_pinned" header="Ghim" sortable>
          <template #body="{ data }">
            <i :class="data.is_pinned ? 'pi pi-pin text-amber-500' : 'pi pi-pin text-gray-300'" />
          </template>
        </Column>
        <Column field="status" header="Trạng thái" sortable>
          <template #body="{ data }">
            <UiBadge :variant="data.status === 'published' ? 'success' : data.status === 'draft' ? 'warning' : 'danger'">{{ data.status }}</UiBadge>
          </template>
        </Column>
        <Column field="created_at" header="Ngày tạo" sortable />
        <Column header="Thao tác" style="width:120px">
          <template #body="{ data }">
            <div class="flex gap-2">
              <Link :href="route('admin.agriverse.forum.show', data.id)" class="text-blue-600 hover:text-blue-800"><i class="pi pi-eye" /></Link>
              <button class="text-red-600 hover:text-red-800" @click="deletePost(data.id)"><i class="pi pi-trash" /></button>
            </div>
          </template>
        </Column>
      </DataTable>
    </UiCard>
  </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import UiCard from '@/Components/ui/UiCard.vue'
import UiBadge from '@/Components/ui/UiBadge.vue'

const props = defineProps({
  posts: { type: Object, required: true },
})

const offset = (props.posts.current_page - 1) * props.posts.per_page

function onPage(event) {
  router.get(route('admin.agriverse.forum.index'), { page: event.page + 1 }, { preserveState: true })
}

function deletePost(id) {
  if (confirm('Xóa bài viết này?')) {
    router.delete(route('admin.agriverse.forum.destroy', id))
  }
}
</script>

<style scoped>
</style>
