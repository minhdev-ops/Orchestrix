<template>
  <MarketplaceLayout>
    <div class="create-page">
      <Link :href="route('agriverse.shop.forum.index')" class="create-back-link">
        <span class="material-symbols-outlined">arrow_back</span>
        Quay lại diễn đàn
      </Link>

      <div class="create-card">
        <h1 class="create-title">{{ isEdit ? 'Chỉnh sửa bài viết' : 'Tạo bài viết mới' }}</h1>
        <p class="create-desc">{{ isEdit ? 'Cập nhật nội dung bài viết của bạn' : 'Chia sẻ kinh nghiệm, câu hỏi hoặc ý tưởng với cộng đồng' }}</p>

        <form @submit.prevent="submit" class="create-form">
          <div class="create-field">
            <label class="create-label">Danh mục</label>
            <select v-model="form.category_id" class="create-select" required>
              <option value="">-- Chọn danh mục --</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
          </div>

          <div class="create-field">
            <label class="create-label">Tiêu đề</label>
            <input v-model="form.title" class="create-input" placeholder="Nhập tiêu đề bài viết..." maxlength="200" required />
            <span class="create-count">{{ form.title.length }}/200</span>
          </div>

          <div class="create-field">
            <label class="create-label">Nội dung</label>
            <textarea v-model="form.content" class="create-textarea" placeholder="Viết nội dung bài viết..." rows="8" required></textarea>
          </div>

          <div class="create-actions">
            <Link :href="isEdit ? route('agriverse.shop.forum.show', post.id) : route('agriverse.shop.forum.index')" class="create-cancel-btn">{{ isEdit ? 'Hủy' : 'Hủy' }}</Link>
            <button type="submit" class="create-submit-btn" :disabled="submitting">
              {{ submitting ? 'Đang gửi...' : (isEdit ? 'Cập nhật' : 'Đăng bài') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </MarketplaceLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import MarketplaceLayout from '@agriverse/Layouts/MarketplaceLayout.vue';

const props = defineProps({
  categories: Array,
  post: Object,
});

const isEdit = computed(() => !!props.post);

const form = ref({
  category_id: props.post?.category_id || '',
  title: props.post?.title || '',
  content: props.post?.content || '',
});
const submitting = ref(false);

function submit() {
  if (!form.value.category_id || !form.value.title.trim() || !form.value.content.trim()) return;
  submitting.value = true;
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
  const data = { ...form.value, _token: csrfToken };
  if (isEdit.value) {
    router.put(route('agriverse.shop.forum.update', props.post.id), data, {
      onSuccess: () => { submitting.value = false; },
      onError: () => { submitting.value = false; },
      onFinish: () => { submitting.value = false; },
    });
  } else {
    router.post(route('agriverse.shop.forum.store'), data, {
      onSuccess: () => { submitting.value = false; },
      onError: () => { submitting.value = false; },
      onFinish: () => { submitting.value = false; },
    });
  }
}
</script>

<style scoped>
.create-page {
  max-width: 720px;
  margin: 0 auto;
  padding: 104px 64px 80px;
}
@media (max-width: 768px) {
  .create-page { padding: 88px 20px 60px; }
}
.create-back-link {
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
.create-back-link:hover { color: var(--ag-primary-500); }

.create-card {
  background: white;
  border: 1px solid color-mix(in srgb, var(--ag-border) 60%, transparent);
  border-radius: 20px;
  padding: 40px;
}
.create-title {
  font-family: var(--ag-font-display);
  font-size: 28px;
  font-weight: 500;
  color: var(--ag-text-primary);
  margin-bottom: 8px;
}
.create-desc {
  font-family: var(--ag-font-body);
  font-size: 15px;
  color: var(--ag-text-secondary);
  margin-bottom: 32px;
}
.create-form { display: flex; flex-direction: column; gap: 24px; }
.create-field { position: relative; }
.create-label {
  display: block;
  font-family: var(--ag-font-body);
  font-size: 13px;
  font-weight: 600;
  color: var(--ag-text-primary);
  margin-bottom: 6px;
}
.create-select, .create-input {
  width: 100%;
  height: 44px;
  padding: 0 14px;
  border: 1px solid var(--ag-border);
  border-radius: 10px;
  font-family: var(--ag-font-body);
  font-size: 14px;
  color: var(--ag-text-primary);
  outline: none;
  background: white;
  box-sizing: border-box;
  transition: border-color 0.2s;
}
.create-select:focus, .create-input:focus { border-color: var(--ag-primary-500); }
.create-textarea {
  width: 100%;
  min-height: 200px;
  padding: 14px;
  border: 1px solid var(--ag-border);
  border-radius: 10px;
  font-family: var(--ag-font-body);
  font-size: 14px;
  line-height: 1.6;
  color: var(--ag-text-primary);
  outline: none;
  resize: vertical;
  box-sizing: border-box;
  transition: border-color 0.2s;
}
.create-textarea:focus { border-color: var(--ag-primary-500); }
.create-count {
  position: absolute;
  right: 12px;
  bottom: 10px;
  font-size: 11px;
  color: var(--ag-text-muted);
}
.create-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  padding-top: 8px;
}
.create-cancel-btn {
  padding: 10px 24px;
  border-radius: 8px;
  border: 1px solid var(--ag-border);
  font-family: var(--ag-font-body);
  font-size: 13px;
  font-weight: 600;
  color: var(--ag-text-secondary);
  text-decoration: none;
  transition: all 0.2s;
}
.create-cancel-btn:hover { border-color: var(--ag-primary-500); color: var(--ag-primary-500); }
.create-submit-btn {
  padding: 10px 32px;
  border-radius: 8px;
  border: none;
  background: var(--ag-primary-500);
  color: white;
  font-family: var(--ag-font-body);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}
.create-submit-btn:hover { background: var(--ag-primary-600); }
.create-submit-btn:disabled { opacity: 0.5; cursor: not-allowed; }
</style>
