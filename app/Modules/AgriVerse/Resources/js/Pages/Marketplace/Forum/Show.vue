<template>
  <MarketplaceLayout>
    <div class="forum-detail">
      <!-- Back -->
      <Link :href="route('agriverse.shop.forum.index')" class="forum-back-link">
        <span class="material-symbols-outlined">arrow_back</span>
        Quay lại diễn đàn
      </Link>

      <!-- Post -->
      <article class="forum-post">
        <div class="forum-post-header">
          <span v-if="post.category" class="forum-post-cat">{{ post.category.name }}</span>
          <h1 class="forum-post-title">{{ post.title }}</h1>
          <div class="forum-post-meta">
            <div class="forum-avatar">
              <span>{{ post.user?.name?.charAt(0)?.toUpperCase() || '?' }}</span>
            </div>
            <div>
              <div class="forum-post-author">{{ post.user?.name }}</div>
              <div class="forum-post-date">{{ post.created_at }}</div>
            </div>
          </div>
        </div>

        <div class="forum-post-body">
          <p class="forum-post-content">{{ post.content }}</p>
        </div>

        <!-- Actions -->
        <div class="forum-post-actions">
          <button @click="toggleLike" class="forum-action-btn" :class="{ 'forum-action-btn--liked': post.is_liked }">
            <span class="material-symbols-outlined" :class="{ 'liked-icon': post.is_liked }">favorite</span>
            <span>{{ post.likes_count }}</span>
          </button>
          <button class="forum-action-btn" @click="scrollToComments">
            <span class="material-symbols-outlined">chat_bubble</span>
            <span>{{ post.comments_count }}</span>
          </button>
          <div v-if="isOwner" class="forum-post-owner-actions">
            <Link v-if="canEdit" :href="route('agriverse.shop.forum.edit', post.id)" class="forum-action-btn forum-action-btn--edit">
              <span class="material-symbols-outlined">edit</span>
              <span>Sửa</span>
            </Link>
            <button @click="confirmDelete" class="forum-action-btn forum-action-btn--delete">
              <span class="material-symbols-outlined">delete</span>
              <span>Xóa</span>
            </button>
          </div>
        </div>

        <!-- Delete confirmation modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-[var(--ag-text-primary)]/30 backdrop-blur-sm" @click.self="showDeleteModal = false">
          <div class="bg-white rounded-3xl p-6 max-w-sm w-full mx-4 shadow-2xl">
            <div class="w-12 h-12 rounded-full bg-[var(--ag-danger)]/10 text-[var(--ag-danger)] flex items-center justify-center mb-4 mx-auto">
              <span class="material-symbols-outlined">warning</span>
            </div>
            <h3 class="text-lg font-bold text-center text-[var(--ag-text-primary)] mb-2">Xóa bài viết?</h3>
            <p class="text-sm text-center text-[var(--ag-text-secondary)] mb-6">Bài viết sẽ bị xóa vĩnh viễn. Hành động này không thể hoàn tác.</p>
            <div class="flex gap-3">
              <button @click="showDeleteModal = false" class="flex-1 h-11 rounded-2xl border-2 border-[var(--ag-border)] text-[var(--ag-text-secondary)] text-sm font-semibold hover:bg-[var(--ag-bg)] transition-all">Hủy</button>
              <button @click="handleDelete" class="flex-1 h-11 rounded-2xl bg-[var(--ag-danger)] text-white text-sm font-semibold hover:bg-[var(--ag-danger)]/80 transition-all">Xóa</button>
            </div>
          </div>
        </div>
      </article>

      <!-- Comments -->
      <div class="forum-comments" ref="commentsRef">
        <h2 class="forum-comments-title">Bình luận ({{ post.comments_count }})</h2>

        <!-- Comment form -->
        <div v-if="$page.props.auth?.user" class="forum-comment-form">
          <textarea v-model="newComment" class="forum-comment-input" placeholder="Viết bình luận..." rows="3" maxlength="2000"></textarea>
          <div class="forum-comment-form-footer">
            <span class="forum-comment-count">{{ newComment.length }}/2000</span>
            <button @click="submitComment" class="forum-comment-btn" :disabled="!newComment.trim() || submitting">
              Gửi bình luận
            </button>
          </div>
        </div>

        <!-- Comment list -->
        <div v-if="comments.length" class="forum-comment-list">
          <div v-for="c in comments" :key="c.id" class="forum-comment-item">
            <div class="forum-avatar">
              <span>{{ c.user?.name?.charAt(0)?.toUpperCase() || '?' }}</span>
            </div>
            <div class="forum-comment-body">
              <div class="forum-comment-header">
                <span class="forum-comment-author">{{ c.user?.name }}</span>
                <span class="forum-comment-time">{{ c.created_at }}</span>
              </div>
              <p class="forum-comment-text">{{ c.content }}</p>
            </div>
          </div>
        </div>
        <div v-else class="forum-comments-empty">
          <p>Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
        </div>
      </div>
    </div>
  </MarketplaceLayout>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import axios from 'axios';
import MarketplaceLayout from '@agriverse/Layouts/MarketplaceLayout.vue';
import { useChatSocket } from '@agriverse/Composables/useChatSocket';

const { on, off, connect } = useChatSocket();
const { props: pageProps } = usePage();

const props = defineProps({
  post: Object,
  comments: Array,
});

const newComment = ref('');
const submitting = ref(false);
const commentsRef = ref(null);
const showDeleteModal = ref(false);

const currentUser = computed(() => pageProps.auth?.user);
const isOwner = computed(() => currentUser.value && currentUser.value.id === props.post.user?.id);

const canEdit = computed(() => {
  if (!isOwner.value) return false;
  const created = new Date(props.post.created_at_raw || props.post.created_at);
  const now = new Date();
  const hoursDiff = (now - created) / (1000 * 60 * 60);
  return hoursDiff <= 24;
});

// Real-time via WebSocket — connect and listen for forum events
onMounted(() => {
  connect()
  on('message', handleForumEvent)
})

onBeforeUnmount(() => {
  off('message', handleForumEvent)
})

function handleForumEvent(data) {
  if (!data || !data.content) return
  try {
    const payload = JSON.parse(data.content)
    if (payload.event === 'comment' && String(payload.post_id) === String(props.post.id)) {
      comments.value.push({
        id: 'ws-' + Date.now(),
        content: payload.content,
        user: { id: payload.user_id, name: payload.user_name },
        created_at: 'Vừa xong',
      })
      post.comments_count++
    }
    if (payload.event === 'like' && String(payload.post_id) === String(props.post.id)) {
      post.likes_count = payload.like_count
    }
  } catch {
    // not a forum event
  }
}

async function toggleLike() {
  try {
    const { data } = await axios.post(`/agriverse/api/forum/${props.post.id}/like`)
    post.is_liked = data.liked
    post.likes_count = data.likes_count
  } catch {
    // ignore
  }
}

async function submitComment() {
  const text = newComment.value.trim()
  if (!text || submitting.value) return
  submitting.value = true
  try {
    const { data } = await axios.post(`/agriverse/api/forum/${props.post.id}/comments`, { content: text })
    comments.value.push(data.comment)
    post.comments_count++
    newComment.value = ''
  } catch {
    // ignore
  } finally {
    submitting.value = false
  }
}

function scrollToComments() {
  commentsRef.value?.scrollIntoView({ behavior: 'smooth' })
}

function confirmDelete() {
  showDeleteModal.value = true
}

function handleDelete() {
  router.delete(route('agriverse.shop.forum.destroy', props.post.id), {
    preserveScroll: true,
    onSuccess: () => { showDeleteModal.value = false },
  })
}
</script>

<style scoped>
.forum-detail {
  max-width: 900px;
  margin: 0 auto;
  padding: 104px 64px 80px;
}
@media (max-width: 768px) {
  .forum-detail { padding: 88px 20px 60px; }
}
.forum-back-link {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-family: var(--ag-font-body);
  font-size: 14px;
  color: var(--ag-text-secondary);
  text-decoration: none;
  margin-bottom: 32px;
  transition: color 0.2s;
}
.forum-back-link:hover { color: var(--ag-primary-500); }

.forum-post {
  background: white;
  border: 1px solid color-mix(in srgb, var(--ag-border) 60%, transparent);
  border-radius: 20px;
  padding: 32px;
  margin-bottom: 32px;
}
.forum-post-header { margin-bottom: 24px; }
.forum-post-cat {
  display: inline-block;
  padding: 3px 12px;
  border-radius: 9999px;
  background: color-mix(in srgb, var(--ag-primary-500) 8%, transparent);
  color: var(--ag-primary-500);
  font-size: 12px;
  font-weight: 600;
  font-family: var(--ag-font-body);
  margin-bottom: 12px;
}
.forum-post-title {
  font-family: var(--ag-font-display);
  font-size: 32px;
  font-weight: 500;
  line-height: 40px;
  color: var(--ag-text-primary);
  margin-bottom: 16px;
}
.forum-post-meta {
  display: flex;
  align-items: center;
  gap: 10px;
}
.forum-post-author {
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  color: var(--ag-text-primary);
}
.forum-post-date {
  font-size: 12px;
  color: var(--ag-text-muted);
}
.forum-post-body {
  padding-bottom: 24px;
  border-bottom: 1px solid color-mix(in srgb, var(--ag-border) 50%, transparent);
  margin-bottom: 16px;
}
.forum-post-content {
  font-family: var(--ag-font-body);
  font-size: 16px;
  line-height: 28px;
  color: var(--ag-text-primary);
  white-space: pre-wrap;
}
.forum-post-actions {
  display: flex;
  gap: 16px;
}
.forum-action-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border: 1px solid var(--ag-border);
  border-radius: 9999px;
  background: white;
  font-family: var(--ag-font-body);
  font-size: 13px;
  font-weight: 600;
  color: var(--ag-text-secondary);
  cursor: pointer;
  transition: all 0.2s;
}
.forum-action-btn:hover { border-color: var(--ag-primary-500); color: var(--ag-primary-500); }
.forum-action-btn .material-symbols-outlined { font-size: 20px; }
.forum-action-btn--liked {
  color: #e41e3f;
  border-color: #fecaca;
  background: #fef2f2;
}
.forum-action-btn--liked .liked-icon {
  font-variation-settings: 'FILL' 1;
}
.forum-post-owner-actions {
  display: flex;
  gap: 8px;
  margin-left: auto;
}
@media (max-width: 640px) {
  .forum-post-owner-actions { margin-left: 0; }
  .forum-post-actions { flex-wrap: wrap; }
}
.forum-action-btn--edit:hover {
  border-color: var(--ag-primary-500);
  color: var(--ag-primary-500);
}
.forum-action-btn--delete:hover {
  border-color: #dc2626;
  color: #dc2626;
  background: #fef2f2;
}

.forum-comments-title {
  font-family: var(--ag-font-display);
  font-size: 20px;
  font-weight: 500;
  color: var(--ag-text-primary);
  margin-bottom: 24px;
}
.forum-comment-form {
  background: white;
  border: 1px solid var(--ag-border);
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 24px;
}
.forum-comment-input {
  width: 100%;
  border: 1px solid var(--ag-border);
  border-radius: 12px;
  padding: 12px;
  font-family: var(--ag-font-body);
  font-size: 14px;
  outline: none;
  resize: none;
  color: var(--ag-text-primary);
  box-sizing: border-box;
}
.forum-comment-input:focus { border-color: var(--ag-primary-500); }
.forum-comment-form-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 12px;
}
.forum-comment-count {
  font-size: 12px;
  color: var(--ag-text-muted);
}
.forum-comment-btn {
  padding: 8px 20px;
  border: none;
  border-radius: 8px;
  background: var(--ag-primary-500);
  color: white;
  font-family: var(--ag-font-body);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}
.forum-comment-btn:hover { background: var(--ag-primary-600); }
.forum-comment-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.forum-comment-list { display: flex; flex-direction: column; gap: 16px; }
.forum-comment-item {
  display: flex;
  gap: 12px;
  padding: 16px;
  background: white;
  border-radius: 12px;
  border: 1px solid color-mix(in srgb, var(--ag-border) 40%, transparent);
}
.forum-comment-body { flex: 1; }
.forum-comment-header {
  display: flex;
  align-items: baseline;
  gap: 8px;
  margin-bottom: 4px;
}
.forum-comment-author {
  font-family: var(--ag-font-body);
  font-size: 13px;
  font-weight: 600;
  color: var(--ag-primary-500);
}
.forum-comment-time { font-size: 11px; color: var(--ag-text-muted); }
.forum-comment-text {
  font-family: var(--ag-font-body);
  font-size: 14px;
  line-height: 20px;
  color: var(--ag-text-primary);
}
.forum-comments-empty {
  text-align: center;
  padding: 40px;
  color: var(--ag-text-muted);
  font-size: 14px;
}

.forum-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: color-mix(in srgb, var(--ag-primary-500) 10%, transparent);
  color: var(--ag-primary-500);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 600;
  flex-shrink: 0;
}
</style>
