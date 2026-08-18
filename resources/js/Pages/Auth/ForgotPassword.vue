<template>
  <div class="login-page">
    <div class="login-container">
      <div class="login-card">
        <Link :href="route('agriverse.shop.home')" class="login-logo">
          <div class="login-logo-mark">
            <span class="material-symbols-outlined text-white" style="font-size: 20px;">eco</span>
          </div>
          <span class="login-logo-text">AgriVerse</span>
        </Link>

        <h1 class="login-title">Quên mật khẩu</h1>
        <p class="login-subtitle">Nhập email của bạn để nhận liên kết đặt lại mật khẩu</p>

        <div v-if="status" class="login-success-msg">
          {{ status }}
        </div>

        <form @submit.prevent="submit" class="login-form">
          <div v-if="errorMsg" class="login-error">{{ errorMsg }}</div>

          <div>
            <label class="login-label">Email</label>
            <input v-model="form.email" type="email" required autofocus
              class="login-input"
              placeholder="your@email.com" />
          </div>

          <!-- CAPTCHA -->
          <div v-if="captchaSiteKey" ref="captchaContainer" class="login-captcha"></div>

          <button type="submit" :disabled="form.processing" class="login-submit mt-2">
            <span v-if="form.processing" class="login-spinner" />
            {{ form.processing ? 'Đang gửi...' : 'Gửi liên kết' }}
          </button>
        </form>

        <div class="login-divider">
          <Link :href="route('login')" class="login-register-link flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Trở lại đăng nhập
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const props = defineProps({
  status: String,
});

const page = usePage();
const captchaContainer = ref(null);
const captchaSiteKey = import.meta.env.VITE_RECAPTCHA_SITE_KEY || '';

const errorMsg = computed(() => {
  const errors = page.props.errors || {};
  return Object.values(errors).flat().join(', ');
});

const form = useForm({
  email: '',
  captcha_token: '',
});

let recaptchaWidgetId = null;

onMounted(() => {
  if (!captchaSiteKey) return;
  if (window.grecaptcha) {
    renderCaptcha();
    return;
  }
  window._recaptchaOnload = renderCaptcha;
  const script = document.createElement('script');
  script.src = 'https://www.google.com/recaptcha/api.js?onload=_recaptchaOnload&render=explicit';
  script.async = true;
  document.head.appendChild(script);
});

function renderCaptcha() {
  if (!window.grecaptcha || !captchaContainer.value) return;
  recaptchaWidgetId = window.grecaptcha.render(captchaContainer.value, {
    sitekey: captchaSiteKey,
  });
}

onBeforeUnmount(() => {
  if (window.grecaptcha && recaptchaWidgetId !== null) {
    window.grecaptcha.reset(recaptchaWidgetId);
  }
});

function getCaptchaToken() {
  if (!captchaSiteKey || !window.grecaptcha || recaptchaWidgetId === null) return Promise.resolve(null);
  const response = window.grecaptcha.getResponse(recaptchaWidgetId);
  return Promise.resolve(response);
}

async function submit() {
  if (captchaSiteKey) {
    form.processing = true;
    try {
      const token = await getCaptchaToken();
      if (token) form.captcha_token = token;
    } catch {
      form.processing = false;
      return;
    }
  }

  form.post(route('password.email'), {
    onFinish: () => {
      if (window.grecaptcha && recaptchaWidgetId !== null) {
        window.grecaptcha.reset(recaptchaWidgetId);
        form.captcha_token = '';
      }
    }
  });
}
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: var(--ag-bg);
}
.login-container {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 48px 20px;
}
.login-card {
  width: 100%;
  max-width: 420px;
  background: var(--ag-bg-card);
  border-radius: var(--ag-radius-2xl);
  border: 1px solid var(--ag-border);
  padding: 40px;
}
.login-logo {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  margin-bottom: 40px;
  text-decoration: none;
}
.login-logo-mark {
  width: 40px;
  height: 40px;
  border-radius: var(--ag-radius-lg);
  background: var(--ag-primary-500);
  display: flex;
  align-items: center;
  justify-content: center;
}
.login-logo-text {
  font-family: var(--ag-font-display);
  font-size: 22px;
  font-weight: 500;
  color: var(--ag-primary-500);
  font-style: italic;
  letter-spacing: -0.02em;
}
.login-title {
  font-family: var(--ag-font-display);
  font-size: 24px;
  font-weight: 500;
  color: var(--ag-on-surface);
  text-align: center;
  margin-bottom: 4px;
}
.login-subtitle {
  font-family: var(--ag-font-body);
  font-size: 14px;
  color: var(--ag-on-surface-variant);
  text-align: center;
  margin-bottom: 32px;
}
.login-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.login-error {
  padding: 12px 16px;
  border-radius: var(--ag-radius-lg);
  background: color-mix(in srgb, var(--ag-error) 8%, transparent);
  color: var(--ag-error);
  font-family: var(--ag-font-body);
  font-size: 13px;
  font-weight: 500;
  line-height: 1.4;
}
.login-success-msg {
  padding: 12px 16px;
  border-radius: var(--ag-radius-lg);
  background: color-mix(in srgb, var(--ag-primary) 8%, transparent);
  color: var(--ag-primary-600);
  font-family: var(--ag-font-body);
  font-size: 13px;
  font-weight: 500;
  line-height: 1.4;
  margin-bottom: 20px;
  text-align: center;
}
.login-label {
  display: block;
  font-family: var(--ag-font-body);
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: var(--ag-on-surface-variant);
  margin-bottom: 6px;
}
.login-input {
  width: 100%;
  height: 48px;
  padding: 0 16px;
  border-radius: var(--ag-radius-xl);
  border: 2px solid var(--ag-border);
  font-family: var(--ag-font-body);
  font-size: 14px;
  color: var(--ag-on-surface);
  background: var(--ag-bg-card);
  outline: none;
  transition: var(--ag-transition-base);
  box-sizing: border-box;
}
.login-input:hover { border-color: var(--ag-outline-variant); }
.login-input:focus {
  border-color: var(--ag-primary);
  box-shadow: 0 0 0 4px color-mix(in srgb, var(--ag-primary) 10%, transparent);
}
.login-input::placeholder { color: var(--ag-border); }
.login-submit {
  width: 100%;
  height: 48px;
  border-radius: var(--ag-radius-2xl);
  background: var(--ag-primary-500);
  color: white;
  font-family: var(--ag-font-body);
  font-size: 14px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: var(--ag-transition-base);
}
.login-submit:hover {
  background: var(--ag-primary-600);
  box-shadow: 0 4px 14px -2px color-mix(in srgb, var(--ag-primary-500) 30%, transparent);
}
.login-submit:active { transform: scale(0.97); }
.login-submit:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
.login-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.login-divider {
  margin-top: 24px;
  padding-top: 24px;
  border-top: 1px solid var(--ag-border);
  text-align: center;
  font-family: var(--ag-font-body);
  font-size: 13px;
}
.login-register-link {
  color: var(--ag-on-surface-variant);
  font-weight: 600;
  text-decoration: none;
  transition: color 0.2s;
}
.login-register-link:hover { color: var(--ag-primary); }
</style>
